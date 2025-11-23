<?php
/**
 * REST API
 *
 * @package CulqiQR
 */

defined('ABSPATH') || exit;

/**
 * Culqi_QR_REST_API class
 */
class Culqi_QR_REST_API {

    /**
     * Namespace
     *
     * @var string
     */
    private $namespace = 'culqi-qr/v1';

    /**
     * Register REST API routes
     */
    public function register_routes() {
        // Generate QR endpoint
        register_rest_route($this->namespace, '/generate-qr', array(
            'methods' => 'POST',
            'callback' => array($this, 'generate_qr'),
            'permission_callback' => '__return_true',
        ));

        // Check payment status endpoint
        register_rest_route($this->namespace, '/check-status', array(
            'methods' => 'POST',
            'callback' => array($this, 'check_status'),
            'permission_callback' => '__return_true',
        ));
    }

    /**
     * Generate QR code
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function generate_qr($request) {
        $amount = $request->get_param('amount');
        $currency = $request->get_param('currency');
        $description = $request->get_param('description');

        if (empty($amount)) {
            return new WP_REST_Response(
                array('error' => __('Amount is required', 'culqi-qr')),
                400
            );
        }

        $result = Culqi_QR_Generator::generate(array(
            'amount' => $amount,
            'currency' => $currency ?: 'PEN',
            'description' => $description ?: '',
        ));

        if (is_wp_error($result)) {
            return new WP_REST_Response(
                array('error' => $result->get_error_message()),
                400
            );
        }

        return new WP_REST_Response($result, 200);
    }

    /**
     * Check payment status
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function check_status($request) {
        $payment_id = $request->get_param('payment_id');

        if (empty($payment_id)) {
            return new WP_REST_Response(
                array('error' => __('Payment ID is required', 'culqi-qr')),
                400
            );
        }

        $transaction = Culqi_QR_Generator::get_transaction($payment_id);

        if (!$transaction) {
            return new WP_REST_Response(
                array('error' => __('Transaction not found', 'culqi-qr')),
                404
            );
        }

        // Check status via API
        $result = culqi_qr()->api()->get_order($payment_id);

        if (is_wp_error($result)) {
            return new WP_REST_Response(
                array('error' => $result->get_error_message()),
                400
            );
        }

        return new WP_REST_Response(
            array(
                'status' => $transaction->status,
                'transaction' => $transaction,
                'api_data' => $result,
            ),
            200
        );
    }
}
