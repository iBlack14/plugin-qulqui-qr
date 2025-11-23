<?php
/**
 * Admin functionality
 *
 * @package CulqiQR
 */

defined('ABSPATH') || exit;

/**
 * Culqi_QR_Admin class
 */
class Culqi_QR_Admin {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    /**
     * Add admin menu
     */
    public function add_menu() {
        add_menu_page(
            __('Culqi QR', 'culqi-qr'),
            __('Culqi QR', 'culqi-qr'),
            'manage_options',
            'culqi-qr',
            array($this, 'render_dashboard'),
            'dashicons-smartphone',
            56
        );

        add_submenu_page(
            'culqi-qr',
            __('Dashboard', 'culqi-qr'),
            __('Dashboard', 'culqi-qr'),
            'manage_options',
            'culqi-qr',
            array($this, 'render_dashboard')
        );

        add_submenu_page(
            'culqi-qr',
            __('Transactions', 'culqi-qr'),
            __('Transactions', 'culqi-qr'),
            'manage_options',
            'culqi-qr-transactions',
            array($this, 'render_transactions')
        );

        add_submenu_page(
            'culqi-qr',
            __('Settings', 'culqi-qr'),
            __('Settings', 'culqi-qr'),
            'manage_options',
            'culqi-qr-settings',
            array($this, 'render_settings')
        );
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_scripts($hook) {
        if (strpos($hook, 'culqi-qr') === false) {
            return;
        }

        // TODO: Enqueue admin CSS and JS
    }

    /**
     * Render dashboard page
     */
    public function render_dashboard() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Culqi QR Dashboard', 'culqi-qr'); ?></h1>
            <p><?php esc_html_e('Welcome to Culqi QR! Configure your settings to start accepting payments.', 'culqi-qr'); ?></p>

            <div class="card">
                <h2><?php esc_html_e('Quick Start', 'culqi-qr'); ?></h2>
                <ol>
                    <li><?php esc_html_e('Go to Settings and configure your API keys', 'culqi-qr'); ?></li>
                    <li><?php esc_html_e('Enable Culqi QR in WooCommerce payment methods', 'culqi-qr'); ?></li>
                    <li><?php esc_html_e('Start accepting payments!', 'culqi-qr'); ?></li>
                </ol>
                <p>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=culqi-qr-settings')); ?>" class="button button-primary">
                        <?php esc_html_e('Go to Settings', 'culqi-qr'); ?>
                    </a>
                </p>
            </div>
        </div>
        <?php
    }

    /**
     * Render transactions page
     */
    public function render_transactions() {
        global $wpdb;
        $table = $wpdb->prefix . 'culqi_qr_transactions';

        $transactions = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC LIMIT 50");
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Transactions', 'culqi-qr'); ?></h1>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e('ID', 'culqi-qr'); ?></th>
                        <th><?php esc_html_e('Payment ID', 'culqi-qr'); ?></th>
                        <th><?php esc_html_e('Amount', 'culqi-qr'); ?></th>
                        <th><?php esc_html_e('Currency', 'culqi-qr'); ?></th>
                        <th><?php esc_html_e('Status', 'culqi-qr'); ?></th>
                        <th><?php esc_html_e('Date', 'culqi-qr'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($transactions)) : ?>
                        <?php foreach ($transactions as $transaction) : ?>
                            <tr>
                                <td><?php echo esc_html($transaction->id); ?></td>
                                <td><code><?php echo esc_html($transaction->payment_id); ?></code></td>
                                <td><?php echo esc_html(number_format($transaction->amount, 2)); ?></td>
                                <td><?php echo esc_html($transaction->currency); ?></td>
                                <td>
                                    <span class="status-<?php echo esc_attr($transaction->status); ?>">
                                        <?php echo esc_html(ucfirst($transaction->status)); ?>
                                    </span>
                                </td>
                                <td><?php echo esc_html($transaction->created_at); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6"><?php esc_html_e('No transactions found', 'culqi-qr'); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * Render settings page
     */
    public function render_settings() {
        // Check if form was submitted
        if (isset($_POST['culqi_qr_settings_nonce']) && wp_verify_nonce($_POST['culqi_qr_settings_nonce'], 'culqi_qr_settings')) {
            update_option('culqi_qr_environment', sanitize_text_field($_POST['environment']));
            update_option('culqi_qr_public_key', sanitize_text_field($_POST['public_key']));
            update_option('culqi_qr_secret_key', sanitize_text_field($_POST['secret_key']));
            update_option('culqi_qr_webhook_secret', sanitize_text_field($_POST['webhook_secret']));
            update_option('culqi_qr_currency', sanitize_text_field($_POST['currency']));
            update_option('culqi_qr_debug', sanitize_text_field($_POST['debug']));

            echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved!', 'culqi-qr') . '</p></div>';
        }

        $environment = get_option('culqi_qr_environment', 'sandbox');
        $public_key = get_option('culqi_qr_public_key', '');
        $secret_key = get_option('culqi_qr_secret_key', '');
        $webhook_secret = get_option('culqi_qr_webhook_secret', '');
        $currency = get_option('culqi_qr_currency', 'PEN');
        $debug = get_option('culqi_qr_debug', 'no');
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Culqi QR Settings', 'culqi-qr'); ?></h1>

            <form method="post" action="">
                <?php wp_nonce_field('culqi_qr_settings', 'culqi_qr_settings_nonce'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="environment"><?php esc_html_e('Environment', 'culqi-qr'); ?></label>
                        </th>
                        <td>
                            <select name="environment" id="environment">
                                <option value="sandbox" <?php selected($environment, 'sandbox'); ?>>
                                    <?php esc_html_e('Sandbox (Test)', 'culqi-qr'); ?>
                                </option>
                                <option value="production" <?php selected($environment, 'production'); ?>>
                                    <?php esc_html_e('Production (Live)', 'culqi-qr'); ?>
                                </option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="public_key"><?php esc_html_e('Public Key', 'culqi-qr'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="public_key" id="public_key" value="<?php echo esc_attr($public_key); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e('Your Culqi public API key', 'culqi-qr'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="secret_key"><?php esc_html_e('Secret Key', 'culqi-qr'); ?></label>
                        </th>
                        <td>
                            <input type="password" name="secret_key" id="secret_key" value="<?php echo esc_attr($secret_key); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e('Your Culqi secret API key', 'culqi-qr'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="webhook_secret"><?php esc_html_e('Webhook Secret', 'culqi-qr'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="webhook_secret" id="webhook_secret" value="<?php echo esc_attr($webhook_secret); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e('Your Culqi webhook secret for signature validation', 'culqi-qr'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="currency"><?php esc_html_e('Currency', 'culqi-qr'); ?></label>
                        </th>
                        <td>
                            <select name="currency" id="currency">
                                <option value="PEN" <?php selected($currency, 'PEN'); ?>>PEN (Soles)</option>
                                <option value="USD" <?php selected($currency, 'USD'); ?>>USD (Dólares)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="debug"><?php esc_html_e('Debug Mode', 'culqi-qr'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" name="debug" id="debug" value="yes" <?php checked($debug, 'yes'); ?> />
                            <label for="debug"><?php esc_html_e('Enable debug logging', 'culqi-qr'); ?></label>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>

            <hr>

            <h2><?php esc_html_e('Webhook URL', 'culqi-qr'); ?></h2>
            <p><?php esc_html_e('Configure this URL in your Culqi dashboard:', 'culqi-qr'); ?></p>
            <code><?php echo esc_url(rest_url('culqi-qr/v1/webhook')); ?></code>
        </div>
        <?php
    }
}

// Initialize admin
if (is_admin()) {
    new Culqi_QR_Admin();
}
