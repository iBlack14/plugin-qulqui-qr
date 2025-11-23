<?php
/**
 * QR Code Generator
 *
 * @package CulqiQR
 */

defined('ABSPATH') || exit;

/**
 * Culqi_QR_Generator class
 */
class Culqi_QR_Generator {

    /**
     * Generate QR code
     *
     * @param array $args
     * @return array|WP_Error
     */
    public static function generate($args = array()) {
        $defaults = array(
            'amount' => 0,
            'currency' => get_option('culqi_qr_currency', 'PEN'),
            'description' => '',
            'order_id' => null,
            'metadata' => array(),
        );

        $args = wp_parse_args($args, $defaults);

        // Validate amount
        if (empty($args['amount']) || $args['amount'] <= 0) {
            return new WP_Error('invalid_amount', __('Invalid amount', 'culqi-qr'));
        }

        // Check if demo mode is enabled
        if (class_exists('Culqi_QR_Demo') && Culqi_QR_Demo::is_enabled()) {
            return Culqi_QR_Demo::generate_demo_qr($args);
        }

        // Create QR via API
        $result = culqi_qr()->api()->create_qr($args);

        if (is_wp_error($result)) {
            return $result;
        }

        // Save transaction to database
        self::save_transaction($result, $args);

        // Generate QR code image
        $qr_image = self::generate_qr_image($result['id']);

        return array(
            'qr_code' => $qr_image,
            'payment_id' => $result['id'],
            'amount' => $args['amount'],
            'currency' => $args['currency'],
            'status' => 'pending',
            'data' => $result,
        );
    }

    /**
     * Save transaction to database
     *
     * @param array $result
     * @param array $args
     * @return int|false
     */
    private static function save_transaction($result, $args) {
        global $wpdb;

        $table = $wpdb->prefix . 'culqi_qr_transactions';

        return $wpdb->insert(
            $table,
            array(
                'payment_id' => $result['id'],
                'order_id' => $args['order_id'],
                'amount' => $args['amount'],
                'currency' => $args['currency'],
                'status' => 'pending',
                'qr_code' => isset($result['qr_code']) ? $result['qr_code'] : '',
                'metadata' => wp_json_encode($args['metadata']),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ),
            array('%s', '%d', '%f', '%s', '%s', '%s', '%s', '%s', '%s')
        );
    }

    /**
     * Generate QR code image
     *
     * @param string $payment_id
     * @return string
     */
    private static function generate_qr_image($payment_id) {
        // Use Google Charts API or local QR library
        $base_url = 'https://api.qrserver.com/v1/create-qr-code/';

        $params = array(
            'size' => '300x300',
            'data' => $payment_id,
        );

        return add_query_arg($params, $base_url);
    }

    /**
     * Get transaction by payment ID
     *
     * @param string $payment_id
     * @return object|null
     */
    public static function get_transaction($payment_id) {
        global $wpdb;

        $table = $wpdb->prefix . 'culqi_qr_transactions';

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table WHERE payment_id = %s",
                $payment_id
            )
        );
    }

    /**
     * Update transaction status
     *
     * @param string $payment_id
     * @param string $status
     * @return int|false
     */
    public static function update_status($payment_id, $status) {
        global $wpdb;

        $table = $wpdb->prefix . 'culqi_qr_transactions';

        return $wpdb->update(
            $table,
            array(
                'status' => $status,
                'updated_at' => current_time('mysql'),
            ),
            array('payment_id' => $payment_id),
            array('%s', '%s'),
            array('%s')
        );
    }
}
