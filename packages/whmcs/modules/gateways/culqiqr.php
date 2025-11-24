<?php
/**
 * Culqi QR Payment Gateway Module for WHMCS
 *
 * Este módulo permite procesar pagos a través de códigos QR de Culqi en WHMCS.
 *
 * @package CulqiQR
 * @author iBlack14
 * @copyright Copyright (c) 2024
 * @license MIT
 * @version 1.0.0
 * @link https://github.com/iBlack14/plugin-qulqui-qr
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Definir metadata del módulo de gateway
 *
 * @return array
 */
function culqiqr_MetaData()
{
    return array(
        'DisplayName' => 'Culqi QR',
        'APIVersion' => '1.1',
        'DisableLocalCredtCardInput' => true,
        'TokenisedStorage' => false,
    );
}

/**
 * Definir parámetros de configuración del gateway
 *
 * @return array
 */
function culqiqr_config()
{
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Culqi QR - Pagos con Código QR',
        ),
        'environment' => array(
            'FriendlyName' => 'Ambiente',
            'Type' => 'dropdown',
            'Options' => array(
                'sandbox' => 'Sandbox (Pruebas)',
                'production' => 'Producción',
            ),
            'Default' => 'sandbox',
            'Description' => 'Seleccione el ambiente de Culqi',
        ),
        'publicKey' => array(
            'FriendlyName' => 'Public Key',
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
            'Description' => 'Ingrese su Culqi Public Key',
        ),
        'secretKey' => array(
            'FriendlyName' => 'Secret Key',
            'Type' => 'password',
            'Size' => '50',
            'Default' => '',
            'Description' => 'Ingrese su Culqi Secret Key',
        ),
        'currency' => array(
            'FriendlyName' => 'Moneda',
            'Type' => 'dropdown',
            'Options' => array(
                'PEN' => 'Soles Peruanos (PEN)',
                'USD' => 'Dólares Americanos (USD)',
            ),
            'Default' => 'PEN',
            'Description' => 'Seleccione la moneda para los pagos',
        ),
        'expirationMinutes' => array(
            'FriendlyName' => 'Tiempo de expiración (minutos)',
            'Type' => 'text',
            'Size' => '5',
            'Default' => '15',
            'Description' => 'Tiempo en minutos antes de que expire el QR',
        ),
        'autoRedirect' => array(
            'FriendlyName' => 'Auto-redirección',
            'Type' => 'yesno',
            'Default' => 'yes',
            'Description' => 'Redirigir automáticamente después del pago exitoso',
        ),
        'testMode' => array(
            'FriendlyName' => 'Modo de prueba',
            'Type' => 'yesno',
            'Default' => 'no',
            'Description' => 'Activar para registrar información de debug en el log del módulo',
        ),
    );
}

/**
 * Generar link de pago
 *
 * @param array $params Parámetros del gateway
 * @return string HTML del formulario de pago
 */
function culqiqr_link($params)
{
    // Parámetros del gateway
    $environment = $params['environment'];
    $publicKey = $params['publicKey'];
    $secretKey = $params['secretKey'];
    $currency = $params['currency'];
    $expirationMinutes = $params['expirationMinutes'];
    $testMode = $params['testMode'];

    // Parámetros de la factura
    $invoiceId = $params['invoiceid'];
    $description = $params['description'];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];

    // Información del cliente
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];

    // URLs del sistema
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];

    // Validar configuración
    if (empty($secretKey) || empty($publicKey)) {
        return '<div class="alert alert-danger">Error: Las credenciales de Culqi no están configuradas. Por favor contacte al administrador.</div>';
    }

    // Generar ID único para esta transacción
    $transactionId = uniqid('culqi_qr_' . $invoiceId . '_');

    // Crear orden en Culqi
    try {
        $culqiApi = new CulqiQR_API($secretKey, $environment);

        // Calcular timestamp de expiración
        $expirationTime = time() + ($expirationMinutes * 60);

        $orderData = array(
            'amount' => (int)($amount * 100), // Convertir a céntimos
            'currency_code' => $currency,
            'description' => "Factura #{$invoiceId} - {$description}",
            'order_number' => $invoiceId,
            'client_details' => array(
                'first_name' => $firstname,
                'last_name' => $lastname,
                'email' => $email,
                'phone_number' => $phone,
            ),
            'expiration_date' => $expirationTime,
            'metadata' => array(
                'invoice_id' => $invoiceId,
                'transaction_id' => $transactionId,
                'client_id' => $params['clientdetails']['userid'],
            ),
        );

        $order = $culqiApi->createOrder($orderData);

        if (isset($order['id']) && isset($order['qr_code'])) {
            // Guardar información en la base de datos
            culqiqr_saveTransaction($invoiceId, $transactionId, $order['id'], 'pending');

            // Generar HTML del código QR
            $qrCode = $order['qr_code'];
            $orderId = $order['id'];

            $html = '
            <div class="culqi-qr-payment-container" style="text-align: center; padding: 20px;">
                <div class="culqi-qr-header">
                    <h3>Escanea el código QR para pagar</h3>
                    <p>Usa tu aplicación de Culqi para escanear el código QR</p>
                </div>

                <div class="culqi-qr-code" style="margin: 20px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 400px;">
                    <img src="' . htmlspecialchars($qrCode) . '" alt="Código QR de pago" style="max-width: 100%; height: auto;" />
                </div>

                <div class="culqi-qr-info" style="margin: 20px 0;">
                    <p><strong>Monto a pagar:</strong> ' . $currency . ' ' . number_format($amount, 2) . '</p>
                    <p><strong>Factura:</strong> #' . $invoiceId . '</p>
                    <p style="font-size: 12px; color: #666;">El código QR expirará en ' . $expirationMinutes . ' minutos</p>
                </div>

                <div class="culqi-qr-status" id="culqi-payment-status-' . $orderId . '">
                    <div class="spinner" style="margin: 20px auto;">
                        <p>Esperando el pago...</p>
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Verificando pago...</span>
                        </div>
                    </div>
                </div>

                <div class="culqi-qr-instructions" style="margin-top: 30px; text-align: left; max-width: 500px; margin-left: auto; margin-right: auto;">
                    <h4>Instrucciones:</h4>
                    <ol>
                        <li>Abre tu aplicación de Culqi</li>
                        <li>Selecciona la opción de pagar con QR</li>
                        <li>Escanea el código QR mostrado arriba</li>
                        <li>Confirma el pago en tu aplicación</li>
                        <li>Espera la confirmación (serás redirigido automáticamente)</li>
                    </ol>
                </div>
            </div>

            <script>
            (function() {
                var checkInterval = 5000; // Verificar cada 5 segundos
                var maxAttempts = ' . (($expirationMinutes * 60) / 5) . '; // Intentos basados en tiempo de expiración
                var attempts = 0;
                var orderId = "' . $orderId . '";
                var invoiceId = "' . $invoiceId . '";

                function checkPaymentStatus() {
                    if (attempts >= maxAttempts) {
                        document.getElementById("culqi-payment-status-" + orderId).innerHTML =
                            \'<div class="alert alert-warning">El código QR ha expirado. Por favor, recargue la página para generar uno nuevo.</div>\';
                        return;
                    }

                    attempts++;

                    // Hacer petición AJAX para verificar el estado
                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", "modules/gateways/callback/culqiqr.php?action=check&invoice_id=" + invoiceId + "&order_id=" + orderId, true);
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            var response = JSON.parse(xhr.responseText);

                            if (response.status === "paid") {
                                document.getElementById("culqi-payment-status-" + orderId).innerHTML =
                                    \'<div class="alert alert-success"><strong>¡Pago exitoso!</strong> Redirigiendo...</div>\';

                                setTimeout(function() {
                                    window.location.href = "' . $returnUrl . '";
                                }, 2000);
                            } else if (response.status === "failed") {
                                document.getElementById("culqi-payment-status-" + orderId).innerHTML =
                                    \'<div class="alert alert-danger"><strong>Pago fallido.</strong> Por favor, intente nuevamente.</div>\';
                            } else {
                                // Continuar verificando
                                setTimeout(checkPaymentStatus, checkInterval);
                            }
                        }
                    };
                    xhr.send();
                }

                // Iniciar verificación
                setTimeout(checkPaymentStatus, checkInterval);
            })();
            </script>

            <style>
            .culqi-qr-payment-container {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            }
            .spinner-border {
                display: inline-block;
                width: 2rem;
                height: 2rem;
                vertical-align: text-bottom;
                border: 0.25em solid currentColor;
                border-right-color: transparent;
                border-radius: 50%;
                animation: spinner-border .75s linear infinite;
            }
            @keyframes spinner-border {
                to { transform: rotate(360deg); }
            }
            </style>
            ';

            return $html;
        } else {
            throw new Exception('Error al crear la orden en Culqi');
        }
    } catch (Exception $e) {
        if ($testMode) {
            logModuleCall('culqiqr', 'create_order_error', $params, $e->getMessage());
        }
        return '<div class="alert alert-danger">Error al generar el código QR: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

/**
 * Procesar reembolso
 *
 * @param array $params Parámetros del gateway
 * @return array Estado del reembolso
 */
function culqiqr_refund($params)
{
    $transactionId = $params['transid'];
    $refundAmount = $params['amount'];
    $secretKey = $params['secretKey'];
    $environment = $params['environment'];

    try {
        $culqiApi = new CulqiQR_API($secretKey, $environment);

        $refundData = array(
            'amount' => (int)($refundAmount * 100),
            'charge_id' => $transactionId,
            'reason' => 'requested_by_customer',
        );

        $refund = $culqiApi->createRefund($refundData);

        return array(
            'status' => 'success',
            'rawdata' => $refund,
            'transid' => $refund['id'],
        );
    } catch (Exception $e) {
        return array(
            'status' => 'declined',
            'rawdata' => $e->getMessage(),
        );
    }
}

/**
 * Guardar transacción en la base de datos
 *
 * @param int $invoiceId ID de la factura
 * @param string $transactionId ID de transacción único
 * @param string $orderId ID de orden de Culqi
 * @param string $status Estado de la transacción
 */
function culqiqr_saveTransaction($invoiceId, $transactionId, $orderId, $status)
{
    $table = 'mod_culqiqr_transactions';

    // Crear tabla si no existe
    $createTable = "CREATE TABLE IF NOT EXISTS `{$table}` (
        `id` int(10) NOT NULL AUTO_INCREMENT,
        `invoice_id` int(10) NOT NULL,
        `transaction_id` varchar(255) NOT NULL,
        `order_id` varchar(255) NOT NULL,
        `status` varchar(50) NOT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `invoice_id` (`invoice_id`),
        KEY `transaction_id` (`transaction_id`),
        KEY `order_id` (`order_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    full_query($createTable);

    // Insertar transacción
    $insert = "INSERT INTO `{$table}`
        (invoice_id, transaction_id, order_id, status, created_at, updated_at)
        VALUES
        ('{$invoiceId}', '{$transactionId}', '{$orderId}', '{$status}', NOW(), NOW())";

    full_query($insert);
}

/**
 * Clase API de Culqi
 */
class CulqiQR_API
{
    private $secretKey;
    private $apiBase;

    public function __construct($secretKey, $environment = 'sandbox')
    {
        $this->secretKey = $secretKey;
        $this->apiBase = ($environment === 'production')
            ? 'https://api.culqi.com/v2/'
            : 'https://api-sandbox.culqi.com/v2/';
    }

    /**
     * Crear orden
     */
    public function createOrder($data)
    {
        return $this->request('orders', 'POST', $data);
    }

    /**
     * Obtener orden
     */
    public function getOrder($orderId)
    {
        return $this->request('orders/' . $orderId, 'GET');
    }

    /**
     * Crear reembolso
     */
    public function createRefund($data)
    {
        return $this->request('refunds', 'POST', $data);
    }

    /**
     * Hacer petición a la API
     */
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
