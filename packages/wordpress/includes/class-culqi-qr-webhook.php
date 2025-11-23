<?php
/**
 * Webhook Handler
 *
 * @package CulqiQR
 */

defined('ABSPATH') || exit;

/**
 * Culqi_QR_Webhook class
 */
class Culqi_QR_Webhook {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_webhook_endpoint'));
    }

    /**
     * Register webhook endpoint
     */
    public function register_webhook_endpoint() {
        register_rest_route('culqi-qr/v1', '/webhook', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_webhook'),
            'permission_callback' => '__return_true',
        ));
    }

    /**
     * Handle webhook
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function handle_webhook($request) {
        $payload = $request->get_body();
        $signature = $request->get_header('X-Culqi-Signature');

        // Validate signature
        if (!culqi_qr()->api()->validate_webhook($payload, $signature)) {
            culqi_qr()->logger()->error('Invalid webhook signature');

            return new WP_REST_Response(
                array('error' => 'Invalid signature'),
                401
            );
        }

        $data = json_decode($payload, true);

        if (!$data) {
            culqi_qr()->logger()->error('Invalid webhook payload');

            return new WP_REST_Response(
                array('error' => 'Invalid payload'),
                400
            );
        }

        // Log webhook
        culqi_qr()->logger()->info('Webhook received', $data);

        // Process webhook based on event type
        $event = isset($data['event']) ? $data['event'] : '';

        switch ($event) {
            case 'order.paid':
                $this->handle_order_paid($data);
                break;

            case 'order.expired':
                $this->handle_order_expired($data);
                break;

            case 'charge.succeeded':
                $this->handle_charge_succeeded($data);
                break;

            case 'charge.failed':
                $this->handle_charge_failed($data);
                break;

            default:
                culqi_qr()->logger()->warning('Unknown webhook event', array('event' => $event));
        }

        return new WP_REST_Response(array('success' => true), 200);
    }

    /**
     * Handle order paid event
     *
     * @param array $data
     */
    private function handle_order_paid($data) {
        $payment_id = isset($data['data']['id']) ? $data['data']['id'] : '';

        if (empty($payment_id)) {
            return;
        }

        // Update transaction status
        Culqi_QR_Generator::update_status($payment_id, 'completed');

        // Get transaction
        $transaction = Culqi_QR_Generator::get_transaction($payment_id);

        if ($transaction && $transaction->order_id) {
            // Update WooCommerce order if exists
            if (function_exists('wc_get_order')) {
                $order = wc_get_order($transaction->order_id);

                if ($order) {
                    $order->payment_complete($payment_id);
                    $order->add_order_note(__('Payment completed via Culqi QR', 'culqi-qr'));
                }
            }

            // Fire action for custom integrations
            do_action('culqi_qr_payment_completed', $transaction, $data);
        }

        culqi_qr()->logger()->info('Payment completed', array('payment_id' => $payment_id));
    }

    /**
     * Handle order expired event
     *
     * @param array $data
     */
    private function handle_order_expired($data) {
        $payment_id = isset($data['data']['id']) ? $data['data']['id'] : '';

        if (empty($payment_id)) {
            return;
        }

        // Update transaction status
        Culqi_QR_Generator::update_status($payment_id, 'expired');

        // Fire action
        do_action('culqi_qr_payment_expired', $payment_id, $data);

        culqi_qr()->logger()->info('Payment expired', array('payment_id' => $payment_id));
    }

    /**
     * Handle charge succeeded event
     *
     * @param array $data
     */
    private function handle_charge_succeeded($data) {
        $this->handle_order_paid($data);
    }

    /**
     * Handle charge failed event
     *
     * @param array $data
     */
    private function handle_charge_failed($data) {
        $payment_id = isset($data['data']['id']) ? $data['data']['id'] : '';

        if (empty($payment_id)) {
            return;
        }

        // Update transaction status
        Culqi_QR_Generator::update_status($payment_id, 'failed');

        // Fire action
        do_action('culqi_qr_payment_failed', $payment_id, $data);

        culqi_qr()->logger()->error('Payment failed', array('payment_id' => $payment_id));
    }
}
