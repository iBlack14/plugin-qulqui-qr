<?php
/**
 * QR Display Template
 *
 * @package CulqiQR
 */

defined('ABSPATH') || exit;

/**
 * Variables available:
 * @var WC_Order $order
 * @var array    $qr_data
 * @var string   $instructions
 */
?>

<div class="culqi-qr-payment-container">
    <div class="culqi-qr-instructions">
        <?php if (!empty($instructions)) : ?>
            <p><?php echo wp_kses_post($instructions); ?></p>
        <?php endif; ?>
    </div>

    <div class="culqi-qr-display-wrapper">
        <div class="culqi-qr-code">
            <img
                src="<?php echo esc_url($qr_data['qr_code']); ?>"
                alt="<?php esc_attr_e('QR Code for payment', 'culqi-qr'); ?>"
                class="culqi-qr-image"
            />
        </div>

        <div class="culqi-qr-payment-info">
            <div class="info-row">
                <span class="label"><?php esc_html_e('Amount:', 'culqi-qr'); ?></span>
                <span class="value">
                    <?php echo esc_html($qr_data['currency'] . ' ' . number_format($qr_data['amount'], 2)); ?>
                </span>
            </div>

            <div class="info-row">
                <span class="label"><?php esc_html_e('Order:', 'culqi-qr'); ?></span>
                <span class="value">#<?php echo esc_html($order->get_order_number()); ?></span>
            </div>

            <div class="info-row">
                <span class="label"><?php esc_html_e('Status:', 'culqi-qr'); ?></span>
                <span class="value status-badge status-<?php echo esc_attr($qr_data['status']); ?>">
                    <?php echo esc_html(ucfirst($qr_data['status'])); ?>
                </span>
            </div>
        </div>

        <div class="culqi-qr-actions">
            <button
                type="button"
                class="button culqi-qr-check-status"
                data-payment-id="<?php echo esc_attr($qr_data['payment_id']); ?>"
            >
                <?php esc_html_e('Check Payment Status', 'culqi-qr'); ?>
            </button>

            <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="button">
                <?php esc_html_e('Refresh QR', 'culqi-qr'); ?>
            </a>
        </div>

        <div class="culqi-qr-help">
            <h4><?php esc_html_e('How to pay:', 'culqi-qr'); ?></h4>
            <ol>
                <li><?php esc_html_e('Open your Culqi app', 'culqi-qr'); ?></li>
                <li><?php esc_html_e('Scan the QR code above', 'culqi-qr'); ?></li>
                <li><?php esc_html_e('Confirm the payment', 'culqi-qr'); ?></li>
                <li><?php esc_html_e('Wait for confirmation', 'culqi-qr'); ?></li>
            </ol>
        </div>
    </div>

    <div class="culqi-qr-loading" style="display:none;">
        <div class="spinner"></div>
        <p><?php esc_html_e('Checking payment status...', 'culqi-qr'); ?></p>
    </div>
</div>

<style>
.culqi-qr-payment-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    text-align: center;
}

.culqi-qr-instructions {
    margin-bottom: 30px;
    font-size: 16px;
}

.culqi-qr-display-wrapper {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.culqi-qr-code {
    margin-bottom: 30px;
}

.culqi-qr-image {
    max-width: 300px;
    height: auto;
    border: 3px solid #f0f0f0;
    border-radius: 8px;
}

.culqi-qr-payment-info {
    margin: 20px 0;
    text-align: left;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-row .label {
    font-weight: 600;
    color: #666;
}

.info-row .value {
    color: #333;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-completed {
    background: #d4edda;
    color: #155724;
}

.status-failed {
    background: #f8d7da;
    color: #721c24;
}

.culqi-qr-actions {
    margin: 20px 0;
    display: flex;
    gap: 10px;
    justify-content: center;
}

.culqi-qr-help {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #f0f0f0;
    text-align: left;
}

.culqi-qr-help h4 {
    margin-bottom: 15px;
    color: #333;
}

.culqi-qr-help ol {
    padding-left: 20px;
}

.culqi-qr-help li {
    margin-bottom: 8px;
    color: #666;
}

.culqi-qr-loading {
    padding: 40px;
    text-align: center;
}

.culqi-qr-loading .spinner {
    width: 40px;
    height: 40px;
    margin: 0 auto 20px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
jQuery(document).ready(function($) {
    $('.culqi-qr-check-status').on('click', function() {
        var button = $(this);
        var paymentId = button.data('payment-id');
        var container = $('.culqi-qr-payment-container');

        // Show loading
        $('.culqi-qr-loading').show();
        button.prop('disabled', true);

        // Check status
        $.ajax({
            url: '<?php echo esc_url(home_url('/wp-json/culqi-qr/v1/check-status')); ?>',
            method: 'POST',
            data: {
                payment_id: paymentId
            },
            success: function(response) {
                if (response.status === 'completed') {
                    alert('<?php esc_html_e('Payment completed!', 'culqi-qr'); ?>');
                    window.location.reload();
                } else {
                    alert('<?php esc_html_e('Payment is still pending', 'culqi-qr'); ?>');
                }
            },
            error: function() {
                alert('<?php esc_html_e('Error checking payment status', 'culqi-qr'); ?>');
            },
            complete: function() {
                $('.culqi-qr-loading').hide();
                button.prop('disabled', false);
            }
        });
    });

    // Auto-refresh every 10 seconds
    setInterval(function() {
        $('.culqi-qr-check-status').trigger('click');
    }, 10000);
});
</script>
