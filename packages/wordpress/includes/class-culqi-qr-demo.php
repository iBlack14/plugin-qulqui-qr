<?php
/**
 * Demo Mode for Testing
 *
 * @package CulqiQR
 */

defined('ABSPATH') || exit;

/**
 * Culqi_QR_Demo class
 */
class Culqi_QR_Demo {

    /**
     * Check if demo mode is enabled
     *
     * @return bool
     */
    public static function is_enabled() {
        return 'yes' === get_option('culqi_qr_demo_mode', 'no');
    }

    /**
     * Generate fake QR for demo
     *
     * @param array $data
     * @return array
     */
    public static function generate_demo_qr($data) {
        $payment_id = 'demo_' . uniqid();

        // Generate a fake QR code URL
        $qr_text = "DEMO: Pago de {$data['currency']} {$data['amount']}";
        $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qr_text);

        return array(
            'qr_code' => $qr_url,
            'payment_id' => $payment_id,
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'status' => 'pending',
            'demo' => true,
        );
    }

    /**
     * Add demo mode setting
     */
    public static function add_setting_field() {
        add_settings_field(
            'culqi_qr_demo_mode',
            __('Modo Demo', 'culqi-qr'),
            array(__CLASS__, 'render_demo_field'),
            'culqi-qr-settings',
            'default'
        );
    }

    /**
     * Render demo mode field
     */
    public static function render_demo_field() {
        $enabled = get_option('culqi_qr_demo_mode', 'no');
        ?>
        <input type="checkbox" name="culqi_qr_demo_mode" value="yes" <?php checked($enabled, 'yes'); ?> />
        <label>
            <?php esc_html_e('Activar modo demo (genera QRs de prueba sin conectar a Culqi)', 'culqi-qr'); ?>
        </label>
        <p class="description">
            ⚠️ <?php esc_html_e('Usar solo para pruebas. Los pagos NO serán reales.', 'culqi-qr'); ?>
        </p>
        <?php
    }
}
