<?php
/**
 * Shortcodes
 *
 * @package CulqiQR
 */

defined('ABSPATH') || exit;

/**
 * Culqi_QR_Shortcodes class
 */
class Culqi_QR_Shortcodes {

    /**
     * Constructor
     */
    public function __construct() {
        add_shortcode('culqi_qr', array($this, 'qr_button'));
        add_shortcode('culqi_qr_display', array($this, 'qr_display'));
        add_shortcode('culqi_qr_form', array($this, 'qr_form'));
    }

    /**
     * QR button shortcode
     *
     * Usage: [culqi_qr amount="100.00" currency="PEN" description="Payment"]
     *
     * @param array $atts
     * @return string
     */
    public function qr_button($atts) {
        $atts = shortcode_atts(array(
            'amount' => 0,
            'currency' => get_option('culqi_qr_currency', 'PEN'),
            'description' => '',
            'button_text' => __('Pay with QR', 'culqi-qr'),
            'button_class' => 'culqi-qr-button',
            'success_url' => '',
            'cancel_url' => '',
        ), $atts, 'culqi_qr');

        // Validate amount
        if (empty($atts['amount']) || $atts['amount'] <= 0) {
            return '<p class="culqi-qr-error">' . __('Invalid amount', 'culqi-qr') . '</p>';
        }

        // Enqueue scripts
        wp_enqueue_script('culqi-qr-public');
        wp_enqueue_style('culqi-qr-public');

        ob_start();
        ?>
        <div class="culqi-qr-wrapper">
            <button
                type="button"
                class="<?php echo esc_attr($atts['button_class']); ?>"
                data-amount="<?php echo esc_attr($atts['amount']); ?>"
                data-currency="<?php echo esc_attr($atts['currency']); ?>"
                data-description="<?php echo esc_attr($atts['description']); ?>"
                data-success-url="<?php echo esc_attr($atts['success_url']); ?>"
                data-cancel-url="<?php echo esc_attr($atts['cancel_url']); ?>"
            >
                <?php echo esc_html($atts['button_text']); ?>
            </button>
            <div class="culqi-qr-modal" style="display:none;">
                <div class="culqi-qr-modal-content">
                    <span class="culqi-qr-close">&times;</span>
                    <div class="culqi-qr-loading">
                        <?php esc_html_e('Generating QR code...', 'culqi-qr'); ?>
                    </div>
                    <div class="culqi-qr-content"></div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * QR display shortcode
     *
     * Usage: [culqi_qr_display order_id="123"]
     *
     * @param array $atts
     * @return string
     */
    public function qr_display($atts) {
        $atts = shortcode_atts(array(
            'order_id' => 0,
            'payment_id' => '',
            'size' => '300',
        ), $atts, 'culqi_qr_display');

        if (empty($atts['payment_id']) && empty($atts['order_id'])) {
            return '<p class="culqi-qr-error">' . __('Missing payment or order ID', 'culqi-qr') . '</p>';
        }

        // Get transaction
        if (!empty($atts['payment_id'])) {
            $transaction = Culqi_QR_Generator::get_transaction($atts['payment_id']);
        } else {
            global $wpdb;
            $table = $wpdb->prefix . 'culqi_qr_transactions';
            $transaction = $wpdb->get_row(
                $wpdb->prepare("SELECT * FROM $table WHERE order_id = %d", $atts['order_id'])
            );
        }

        if (!$transaction) {
            return '<p class="culqi-qr-error">' . __('Transaction not found', 'culqi-qr') . '</p>';
        }

        ob_start();
        ?>
        <div class="culqi-qr-display">
            <div class="culqi-qr-image">
                <img
                    src="<?php echo esc_url($transaction->qr_code); ?>"
                    alt="<?php esc_attr_e('QR Code', 'culqi-qr'); ?>"
                    width="<?php echo esc_attr($atts['size']); ?>"
                    height="<?php echo esc_attr($atts['size']); ?>"
                />
            </div>
            <div class="culqi-qr-status">
                <span class="status-<?php echo esc_attr($transaction->status); ?>">
                    <?php echo esc_html(ucfirst($transaction->status)); ?>
                </span>
            </div>
            <div class="culqi-qr-info">
                <p>
                    <strong><?php esc_html_e('Amount:', 'culqi-qr'); ?></strong>
                    <?php echo esc_html($transaction->currency . ' ' . number_format($transaction->amount, 2)); ?>
                </p>
                <p>
                    <strong><?php esc_html_e('Payment ID:', 'culqi-qr'); ?></strong>
                    <code><?php echo esc_html($transaction->payment_id); ?></code>
                </p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * QR form shortcode
     *
     * Usage: [culqi_qr_form]
     *
     * @param array $atts
     * @return string
     */
    public function qr_form($atts) {
        $atts = shortcode_atts(array(
            'button_text' => __('Generate QR', 'culqi-qr'),
            'success_url' => '',
        ), $atts, 'culqi_qr_form');

        wp_enqueue_script('culqi-qr-public');
        wp_enqueue_style('culqi-qr-public');

        ob_start();
        ?>
        <div class="culqi-qr-form-wrapper">
            <form class="culqi-qr-form" method="post">
                <div class="form-group">
                    <label for="culqi_amount">
                        <?php esc_html_e('Amount', 'culqi-qr'); ?> <span class="required">*</span>
                    </label>
                    <input
                        type="number"
                        id="culqi_amount"
                        name="amount"
                        step="0.01"
                        min="0.01"
                        required
                    />
                </div>

                <div class="form-group">
                    <label for="culqi_currency">
                        <?php esc_html_e('Currency', 'culqi-qr'); ?>
                    </label>
                    <select id="culqi_currency" name="currency">
                        <option value="PEN">PEN</option>
                        <option value="USD">USD</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="culqi_description">
                        <?php esc_html_e('Description', 'culqi-qr'); ?>
                    </label>
                    <input
                        type="text"
                        id="culqi_description"
                        name="description"
                        placeholder="<?php esc_attr_e('Optional', 'culqi-qr'); ?>"
                    />
                </div>

                <div class="form-group">
                    <button type="submit" class="culqi-qr-submit">
                        <?php echo esc_html($atts['button_text']); ?>
                    </button>
                </div>

                <div class="culqi-qr-result" style="display:none;"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}
