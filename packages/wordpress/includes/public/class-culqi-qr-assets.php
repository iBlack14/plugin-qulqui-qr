<?php
/**
 * Public Assets Handler
 *
 * @package CulqiQR
 */

defined('ABSPATH') || exit;

/**
 * Enqueue public scripts and styles
 */
function culqi_qr_enqueue_public_assets() {
    // CSS
    wp_enqueue_style(
        'culqi-qr-public',
        CULQI_QR_PLUGIN_URL . 'assets/css/public.css',
        array(),
        CULQI_QR_VERSION
    );

    // JavaScript
    wp_enqueue_script(
        'culqi-qr-public',
        CULQI_QR_PLUGIN_URL . 'assets/js/public.js',
        array('jquery'),
        CULQI_QR_VERSION,
        true
    );

    // Localize script
    wp_localize_script('culqi-qr-public', 'culqiQRData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'restUrl' => rest_url(),
        'nonce' => wp_create_nonce('culqi_qr_nonce'),
    ));
}

add_action('wp_enqueue_scripts', 'culqi_qr_enqueue_public_assets');

/**
 * AJAX handler to generate QR
 */
function culqi_qr_ajax_generate() {
    check_ajax_referer('culqi_qr_nonce', 'nonce');

    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $currency = isset($_POST['currency']) ? sanitize_text_field($_POST['currency']) : 'PEN';
    $description = isset($_POST['description']) ? sanitize_text_field($_POST['description']) : '';

    if (empty($amount) || $amount <= 0) {
        wp_send_json_error(__('Invalid amount', 'culqi-qr'));
    }

    $result = Culqi_QR_Generator::generate(array(
        'amount' => $amount,
        'currency' => $currency,
        'description' => $description,
    ));

    if (is_wp_error($result)) {
        wp_send_json_error($result->get_error_message());
    }

    wp_send_json_success($result);
}

add_action('wp_ajax_culqi_qr_generate', 'culqi_qr_ajax_generate');
add_action('wp_ajax_nopriv_culqi_qr_generate', 'culqi_qr_ajax_generate');
