<?php
/**
 * WooCommerce Payment Gateway
 *
 * @package CulqiQR
 */

defined('ABSPATH') || exit;

/**
 * Culqi_QR_Gateway class
 */
class Culqi_QR_Gateway extends WC_Payment_Gateway {

    /**
     * Constructor
     */
    public function __construct() {
        $this->id = 'culqi_qr';
        $this->icon = CULQI_QR_PLUGIN_URL . 'assets/images/culqi-logo.png';
        $this->has_fields = false;
        $this->method_title = __('Culqi QR', 'culqi-qr');
        $this->method_description = __('Accept payments via Culqi QR codes', 'culqi-qr');

        // Supports
        $this->supports = array(
            'products',
            'refunds',
        );

        // Load settings
        $this->init_form_fields();
        $this->init_settings();

        // Get settings
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->enabled = $this->get_option('enabled');

        // Hooks
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
        add_action('woocommerce_api_culqi_qr_callback', array($this, 'check_payment_callback'));
    }

    /**
     * Initialize form fields
     */
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'culqi-qr'),
                'type' => 'checkbox',
                'label' => __('Enable Culqi QR Payment', 'culqi-qr'),
                'default' => 'no',
            ),
            'title' => array(
                'title' => __('Title', 'culqi-qr'),
                'type' => 'text',
                'description' => __('Payment method title that customers will see during checkout', 'culqi-qr'),
                'default' => __('QR Payment', 'culqi-qr'),
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __('Description', 'culqi-qr'),
                'type' => 'textarea',
                'description' => __('Payment method description that customers will see during checkout', 'culqi-qr'),
                'default' => __('Scan the QR code with your Culqi app to complete the payment', 'culqi-qr'),
                'desc_tip' => true,
            ),
            'instructions' => array(
                'title' => __('Instructions', 'culqi-qr'),
                'type' => 'textarea',
                'description' => __('Instructions to show on the order receipt page', 'culqi-qr'),
                'default' => __('Please scan the QR code below to complete your payment', 'culqi-qr'),
                'desc_tip' => true,
            ),
        );
    }

    /**
     * Process payment
     *
     * @param int $order_id
     * @return array
     */
    public function process_payment($order_id) {
        $order = wc_get_order($order_id);

        // Mark as pending
        $order->update_status('pending', __('Awaiting QR payment', 'culqi-qr'));

        // Reduce stock
        wc_reduce_stock_levels($order_id);

        // Remove cart
        WC()->cart->empty_cart();

        // Return to receipt page
        return array(
            'result' => 'success',
            'redirect' => $this->get_return_url($order),
        );
    }

    /**
     * Receipt page
     *
     * @param int $order_id
     */
    public function receipt_page($order_id) {
        $order = wc_get_order($order_id);

        if (!$order) {
            return;
        }

        // Check if QR already generated
        $payment_id = $order->get_meta('_culqi_qr_payment_id');

        if (empty($payment_id)) {
            // Generate QR
            $result = Culqi_QR_Generator::generate(array(
                'amount' => $order->get_total(),
                'currency' => $order->get_currency(),
                'description' => sprintf(__('Order #%s', 'culqi-qr'), $order->get_order_number()),
                'order_id' => $order_id,
                'metadata' => array(
                    'order_id' => $order_id,
                    'customer_email' => $order->get_billing_email(),
                    'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                ),
            ));

            if (is_wp_error($result)) {
                wc_add_notice($result->get_error_message(), 'error');
                return;
            }

            // Save payment ID
            $order->update_meta_data('_culqi_qr_payment_id', $result['payment_id']);
            $order->save();
        } else {
            // Get existing QR
            $transaction = Culqi_QR_Generator::get_transaction($payment_id);

            if (!$transaction) {
                wc_add_notice(__('QR code not found', 'culqi-qr'), 'error');
                return;
            }

            $result = array(
                'qr_code' => $transaction->qr_code,
                'payment_id' => $payment_id,
                'status' => $transaction->status,
            );
        }

        // Display QR
        $this->display_qr($order, $result);
    }

    /**
     * Display QR code
     *
     * @param WC_Order $order
     * @param array    $qr_data
     */
    private function display_qr($order, $qr_data) {
        $instructions = $this->get_option('instructions');

        include CULQI_QR_PLUGIN_DIR . 'templates/qr-display.php';
    }

    /**
     * Check payment callback
     */
    public function check_payment_callback() {
        $payment_id = isset($_GET['payment_id']) ? sanitize_text_field($_GET['payment_id']) : '';

        if (empty($payment_id)) {
            wp_die(__('Invalid payment ID', 'culqi-qr'));
        }

        $transaction = Culqi_QR_Generator::get_transaction($payment_id);

        if (!$transaction) {
            wp_send_json_error(array('message' => __('Transaction not found', 'culqi-qr')));
        }

        // Check payment status via API
        $result = culqi_qr()->api()->get_payment($payment_id);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }

        $status = isset($result['status']) ? $result['status'] : 'pending';

        wp_send_json_success(array(
            'status' => $status,
            'transaction' => $transaction,
        ));
    }

    /**
     * Process refund
     *
     * @param int    $order_id
     * @param float  $amount
     * @param string $reason
     * @return bool|WP_Error
     */
    public function process_refund($order_id, $amount = null, $reason = '') {
        $order = wc_get_order($order_id);

        if (!$order) {
            return new WP_Error('invalid_order', __('Invalid order', 'culqi-qr'));
        }

        $payment_id = $order->get_meta('_culqi_qr_payment_id');

        if (empty($payment_id)) {
            return new WP_Error('no_payment_id', __('Payment ID not found', 'culqi-qr'));
        }

        // Process refund via API
        // Note: Culqi API refund implementation
        // This is a placeholder - implement according to Culqi's refund API

        culqi_qr()->logger()->info('Refund processed', array(
            'order_id' => $order_id,
            'payment_id' => $payment_id,
            'amount' => $amount,
            'reason' => $reason,
        ));

        return true;
    }
}
