<?php
/**
 * Culqi API Client
 *
 * @package CulqiQR
 */

defined('ABSPATH') || exit;

/**
 * Culqi_QR_API class
 */
class Culqi_QR_API {

    /**
     * API base URL
     *
     * @var string
     */
    private $api_base;

    /**
     * Public key
     *
     * @var string
     */
    private $public_key;

    /**
     * Secret key
     *
     * @var string
     */
    private $secret_key;

    /**
     * Environment
     *
     * @var string
     */
    private $environment;

    /**
     * Constructor
     */
    public function __construct() {
        $this->environment = get_option('culqi_qr_environment', 'sandbox');
        $this->public_key = get_option('culqi_qr_public_key', '');
        $this->secret_key = get_option('culqi_qr_secret_key', '');

        $this->api_base = ('production' === $this->environment)
            ? CULQI_QR_API_BASE
            : CULQI_QR_API_SANDBOX;
    }

    /**
     * Make API request
     *
     * @param string $endpoint
     * @param string $method
     * @param array  $data
     * @return array|WP_Error
     */
    private function request($endpoint, $method = 'GET', $data = array()) {
        $url = $this->api_base . $endpoint;

        $headers = array(
            'Authorization' => 'Bearer ' . $this->secret_key,
            'Content-Type' => 'application/json',
        );

        $args = array(
            'method' => $method,
            'headers' => $headers,
            'timeout' => 30,
        );

        if ('GET' !== $method && !empty($data)) {
            $args['body'] = wp_json_encode($data);
        }

        // Log request
        culqi_qr()->logger()->debug('API Request', array(
            'url' => $url,
            'method' => $method,
            'data' => $data,
        ));

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            culqi_qr()->logger()->error('API Error', array(
                'error' => $response->get_error_message(),
            ));
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $code = wp_remote_retrieve_response_code($response);

        $result = json_decode($body, true);

        // Log response
        culqi_qr()->logger()->debug('API Response', array(
            'code' => $code,
            'body' => $result,
        ));

        if ($code < 200 || $code >= 300) {
            return new WP_Error(
                'api_error',
                isset($result['user_message']) ? $result['user_message'] : __('API Error', 'culqi-qr'),
                array('status' => $code, 'response' => $result)
            );
        }

        return $result;
    }

    /**
     * Create QR code
     *
     * @param array $data
     * @return array|WP_Error
     */
    public function create_qr($data) {
        $defaults = array(
            'currency' => get_option('culqi_qr_currency', 'PEN'),
            'metadata' => array(),
        );

        $data = wp_parse_args($data, $defaults);

        // Validate required fields
        if (empty($data['amount'])) {
            return new WP_Error('missing_amount', __('Amount is required', 'culqi-qr'));
        }

        // Convert amount to cents
        $data['amount'] = (int) ($data['amount'] * 100);

        // Create order in Culqi
        $result = $this->request('orders', 'POST', $data);

        if (is_wp_error($result)) {
            return $result;
        }

        return $result;
    }

    /**
     * Get order status
     *
     * @param string $order_id
     * @return array|WP_Error
     */
    public function get_order($order_id) {
        return $this->request('orders/' . $order_id, 'GET');
    }

    /**
     * Get payment status
     *
     * @param string $payment_id
     * @return array|WP_Error
     */
    public function get_payment($payment_id) {
        return $this->request('charges/' . $payment_id, 'GET');
    }

    /**
     * Validate webhook signature
     *
     * @param string $payload
     * @param string $signature
     * @return bool
     */
    public function validate_webhook($payload, $signature) {
        $webhook_secret = get_option('culqi_qr_webhook_secret', '');

        if (empty($webhook_secret)) {
            return false;
        }

        $expected = hash_hmac('sha256', $payload, $webhook_secret);

        return hash_equals($expected, $signature);
    }
}
