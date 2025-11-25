<?php
/**
 * Culqi QR Callback Handler
 *
 * Maneja los callbacks y webhooks de Culqi para WHMCS
 *
 * @package CulqiQR
 * @author iBlack14
 */

// Require libraries needed for gateway module functions
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

// Detect module name from filename
$gatewayModuleName = basename(__FILE__, '.php');

// Fetch gateway configuration parameters
$gatewayParams = getGatewayVariables($gatewayModuleName);

// Die if module is not active
if (!$gatewayParams['type']) {
    die("Module Not Activated");
}

/**
 * Verificar estado del pago (llamado desde JavaScript)
 */
if (isset($_GET['action']) && $_GET['action'] === 'check') {
    header('Content-Type: application/json');

    $invoiceId = isset($_GET['invoice_id']) ? (int)$_GET['invoice_id'] : 0;
    $orderId = isset($_GET['order_id']) ? $_GET['order_id'] : '';

    if (!$invoiceId || !$orderId) {
        echo json_encode(array('status' => 'error', 'message' => 'Invalid parameters'));
        exit;
    }

    try {
        // Consultar estado en Culqi
        $culqiApi = new CulqiQR_API(
            $gatewayParams['secretKey'],
            $gatewayParams['environment']
        );

        $order = $culqiApi->getOrder($orderId);

        if (isset($order['payment_status'])) {
            $status = $order['payment_status'];

            // Actualizar estado en la base de datos
            culqiqr_updateTransactionStatus($invoiceId, $orderId, $status);

            if ($status === 'paid') {
                // Marcar factura como pagada
                $transactionId = isset($order['charges'][0]['id']) ? $order['charges'][0]['id'] : $orderId;

                addInvoicePayment(
                    $invoiceId,
                    $transactionId,
                    $order['amount'] / 100, // Convertir de céntimos
                    0, // Fees
                    $gatewayModuleName
                );

                logTransaction($gatewayParams['name'], $order, 'Success');

                echo json_encode(array('status' => 'paid', 'transaction_id' => $transactionId));
            } elseif ($status === 'failed' || $status === 'expired') {
                logTransaction($gatewayParams['name'], $order, 'Failed');
                echo json_encode(array('status' => 'failed'));
            } else {
                echo json_encode(array('status' => 'pending'));
            }
        } else {
            echo json_encode(array('status' => 'pending'));
        }
    } catch (Exception $e) {
        logTransaction($gatewayParams['name'], $_GET, 'Error: ' . $e->getMessage());
        echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
    }

    exit;
}

/**
 * Webhook de Culqi
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del webhook
    $rawPayload = file_get_contents('php://input');
    $payload = json_decode($rawPayload, true);

    // Validar firma del webhook (si está configurada)
    $signature = isset($_SERVER['HTTP_X_CULQI_SIGNATURE']) ? $_SERVER['HTTP_X_CULQI_SIGNATURE'] : '';

    if ($gatewayParams['testMode']) {
        logTransaction($gatewayParams['name'], array(
            'payload' => $payload,
            'signature' => $signature,
        ), 'Webhook Received');
    }

    if (!$payload || !isset($payload['type'])) {
        http_response_code(400);
        die('Invalid payload');
    }

    // Procesar diferentes tipos de eventos
    switch ($payload['type']) {
        case 'order.status.changed':
            handleOrderStatusChanged($payload, $gatewayParams);
            break;

        case 'charge.succeeded':
            handleChargeSucceeded($payload, $gatewayParams);
            break;

        case 'charge.failed':
            handleChargeFailed($payload, $gatewayParams);
            break;

        default:
            if ($gatewayParams['testMode']) {
                logTransaction($gatewayParams['name'], $payload, 'Unhandled webhook type: ' . $payload['type']);
            }
            break;
    }

    http_response_code(200);
    echo 'OK';
    exit;
}

/**
 * Manejar cambio de estado de orden
 */
function handleOrderStatusChanged($payload, $gatewayParams)
{
    if (!isset($payload['data']['object'])) {
        return;
    }

    $order = $payload['data']['object'];
    $orderId = $order['id'];
    $status = $order['payment_status'];

    // Obtener invoice_id de metadata
    $invoiceId = isset($order['metadata']['invoice_id']) ? (int)$order['metadata']['invoice_id'] : 0;

    if (!$invoiceId) {
        logTransaction($gatewayParams['name'], $payload, 'No invoice ID in metadata');
        return;
    }

    // Actualizar estado
    culqiqr_updateTransactionStatus($invoiceId, $orderId, $status);

    if ($status === 'paid') {
        // Obtener ID de cargo
        $transactionId = isset($order['charges'][0]['id']) ? $order['charges'][0]['id'] : $orderId;

        // Verificar si ya se procesó el pago
        $invoiceStatus = localAPI('GetInvoice', array('invoiceid' => $invoiceId));

        if ($invoiceStatus['status'] !== 'Paid') {
            addInvoicePayment(
                $invoiceId,
                $transactionId,
                $order['amount'] / 100,
                0,
                'culqiqr'
            );

            logTransaction($gatewayParams['name'], $order, 'Payment Success');

            // Enviar email de confirmación
            sendMessage('Invoice Payment Confirmation', $invoiceId);
        }
    } elseif ($status === 'failed' || $status === 'expired') {
        logTransaction($gatewayParams['name'], $order, 'Payment Failed: ' . $status);
    }
}

/**
 * Manejar cargo exitoso
 */
function handleChargeSucceeded($payload, $gatewayParams)
{
    if (!isset($payload['data']['object'])) {
        return;
    }

    $charge = $payload['data']['object'];
    $orderId = isset($charge['order']['id']) ? $charge['order']['id'] : '';

    if (!$orderId) {
        return;
    }

    // Obtener orden completa
    try {
        $culqiApi = new CulqiQR_API(
            $gatewayParams['secretKey'],
            $gatewayParams['environment']
        );

        $order = $culqiApi->getOrder($orderId);

        if (isset($order['metadata']['invoice_id'])) {
            $invoiceId = (int)$order['metadata']['invoice_id'];
            $transactionId = $charge['id'];

            // Verificar si ya se procesó
            $invoiceStatus = localAPI('GetInvoice', array('invoiceid' => $invoiceId));

            if ($invoiceStatus['status'] !== 'Paid') {
                addInvoicePayment(
                    $invoiceId,
                    $transactionId,
                    $charge['amount'] / 100,
                    0,
                    'culqiqr'
                );

                culqiqr_updateTransactionStatus($invoiceId, $orderId, 'paid');

                logTransaction($gatewayParams['name'], $charge, 'Charge Success');
            }
        }
    } catch (Exception $e) {
        logTransaction($gatewayParams['name'], $payload, 'Error: ' . $e->getMessage());
    }
}

/**
 * Manejar cargo fallido
 */
function handleChargeFailed($payload, $gatewayParams)
{
    if (!isset($payload['data']['object'])) {
        return;
    }

    $charge = $payload['data']['object'];
    $orderId = isset($charge['order']['id']) ? $charge['order']['id'] : '';

    if ($orderId) {
        try {
            $culqiApi = new CulqiQR_API(
                $gatewayParams['secretKey'],
                $gatewayParams['environment']
            );

            $order = $culqiApi->getOrder($orderId);

            if (isset($order['metadata']['invoice_id'])) {
                $invoiceId = (int)$order['metadata']['invoice_id'];
                culqiqr_updateTransactionStatus($invoiceId, $orderId, 'failed');
            }
        } catch (Exception $e) {
            logTransaction($gatewayParams['name'], $payload, 'Error: ' . $e->getMessage());
        }
    }

    logTransaction($gatewayParams['name'], $charge, 'Charge Failed');
}

/**
 * Actualizar estado de transacción
 */
function culqiqr_updateTransactionStatus($invoiceId, $orderId, $status)
{
    $table = 'mod_culqiqr_transactions';

    $update = "UPDATE `{$table}`
        SET status = '{$status}', updated_at = NOW()
        WHERE invoice_id = '{$invoiceId}' AND order_id = '{$orderId}'";

    full_query($update);
}

/**
 * Clase API de Culqi (duplicada para el callback)
 */
class CulqiQR_API
{
    private $secretKey;
    private $apiBase;

    public function __construct($secretKey, $environment = 'sandbox')
    {
        $this->secretKey = $secretKey;
        // Culqi usa el mismo endpoint para sandbox y producción
        // La diferencia está en las API keys (test vs live)
        $this->apiBase = 'https://api.culqi.com/v2/';
    }

    public function getOrder($orderId)
    {
        return $this->request('orders/' . $orderId, 'GET');
    }

    private function request($endpoint, $method = 'GET', $data = null)
    {
        $url = $this->apiBase . $endpoint;

        $headers = array(
            'Authorization: Bearer ' . $this->secretKey,
            'Content-Type: application/json',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }

        $result = json_decode($response, true);

        if ($httpCode < 200 || $httpCode >= 300) {
            $message = isset($result['user_message']) ? $result['user_message'] : 'API Error';
            throw new Exception($message);
        }

        return $result;
    }
}
