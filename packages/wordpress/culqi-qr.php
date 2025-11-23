<?php
/**
 * Plugin Name: Culqi QR - Pagos con QR
 * Plugin URI: https://github.com/iBlack14/plugin-qulqui-qr
 * Description: El plugin más completo para integrar pagos QR de Culqi en WordPress, WooCommerce y más. Multiplataforma y fácil de usar.
 * Version: 0.1.3
 * Author: iBlack14
 * Author URI: https://github.com/iBlack14
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: culqi-qr
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * WC requires at least: 7.0
 * WC tested up to: 8.5
 *
 * @package CulqiQR
 */

defined('ABSPATH') || exit;

// Plugin constants
define('CULQI_QR_VERSION', '0.1.3');
define('CULQI_QR_PLUGIN_FILE', __FILE__);
define('CULQI_QR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CULQI_QR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CULQI_QR_PLUGIN_BASENAME', plugin_basename(__FILE__));

// API endpoints
define('CULQI_QR_API_BASE', 'https://api.culqi.com/v2/');
define('CULQI_QR_API_SANDBOX', 'https://api-dev.culqi.com/v2/');

/**
 * Main plugin class
 */
final class Culqi_QR {

    /**
     * Plugin instance
     *
     * @var Culqi_QR
     */
    private static $instance = null;

    /**
     * Get plugin instance
     *
     * @return Culqi_QR
     */
    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->includes();
        $this->init();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        register_activation_hook(CULQI_QR_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(CULQI_QR_PLUGIN_FILE, array($this, 'deactivate'));

        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('init', array($this, 'init_blocks'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));
    }

    /**
     * Include required files
     */
    private function includes() {
        // Core classes
        require_once CULQI_QR_PLUGIN_DIR . 'includes/class-culqi-qr-api.php';
        require_once CULQI_QR_PLUGIN_DIR . 'includes/class-culqi-qr-generator.php';
        require_once CULQI_QR_PLUGIN_DIR . 'includes/class-culqi-qr-webhook.php';
        require_once CULQI_QR_PLUGIN_DIR . 'includes/class-culqi-qr-logger.php';
        require_once CULQI_QR_PLUGIN_DIR . 'includes/class-culqi-qr-cache.php';

        // Admin classes (load if they exist)
        if (is_admin()) {
            $this->load_file_if_exists('includes/admin/class-culqi-qr-admin.php');
            $this->load_file_if_exists('includes/admin/class-culqi-qr-settings.php');
            $this->load_file_if_exists('includes/admin/class-culqi-qr-analytics.php');
            $this->load_file_if_exists('includes/admin/class-culqi-qr-connection-test.php');
        }

        // Public classes
        require_once CULQI_QR_PLUGIN_DIR . 'includes/public/class-culqi-qr-shortcodes.php';
        require_once CULQI_QR_PLUGIN_DIR . 'includes/public/class-culqi-qr-assets.php';
        $this->load_file_if_exists('includes/public/class-culqi-qr-blocks.php');

        // WooCommerce integration
        if (class_exists('WooCommerce')) {
            require_once CULQI_QR_PLUGIN_DIR . 'includes/woocommerce/class-culqi-qr-gateway.php';
        }

        // Page builders (load if they exist)
        $this->load_file_if_exists('includes/integrations/class-culqi-qr-elementor.php');
        $this->load_file_if_exists('includes/integrations/class-culqi-qr-divi.php');
    }

    /**
     * Load file if it exists
     *
     * @param string $file Relative file path
     */
    private function load_file_if_exists($file) {
        $filepath = CULQI_QR_PLUGIN_DIR . $file;
        if (file_exists($filepath)) {
            require_once $filepath;
        }
    }

    /**
     * Initialize components
     */
    private function init() {
        // Initialize API client
        $this->api = new Culqi_QR_API();

        // Initialize logger
        $this->logger = new Culqi_QR_Logger();

        // Initialize webhook handler
        $this->webhook = new Culqi_QR_Webhook();

        // Initialize shortcodes
        new Culqi_QR_Shortcodes();

        // Initialize WooCommerce gateway
        if (class_exists('WooCommerce')) {
            add_filter('woocommerce_payment_gateways', array($this, 'add_gateway'));

            // Show notice if API keys not configured
            if (empty(get_option('culqi_qr_public_key')) || empty(get_option('culqi_qr_secret_key'))) {
                add_action('admin_notices', array($this, 'api_keys_notice'));
            }
        }
    }

    /**
     * Add WooCommerce gateway
     *
     * @param array $gateways
     * @return array
     */
    public function add_gateway($gateways) {
        if (class_exists('Culqi_QR_Gateway')) {
            $gateways[] = 'Culqi_QR_Gateway';
        }
        return $gateways;
    }

    /**
     * Show admin notice if API keys not configured
     */
    public function api_keys_notice() {
        if (get_current_screen()->id !== 'woocommerce_page_wc-settings') {
            return;
        }
        ?>
        <div class="notice notice-warning">
            <p>
                <strong><?php esc_html_e('Culqi QR:', 'culqi-qr'); ?></strong>
                <?php
                printf(
                    esc_html__('Para que el método de pago funcione, configura tus API keys en %s', 'culqi-qr'),
                    '<a href="' . esc_url(admin_url('admin.php?page=culqi-qr-settings')) . '">Culqi QR → Settings</a>'
                );
                ?>
            </p>
        </div>
        <?php
    }

    /**
     * Initialize Gutenberg blocks
     */
    public function init_blocks() {
        if (function_exists('register_block_type') && class_exists('Culqi_QR_Blocks')) {
            new Culqi_QR_Blocks();
        }
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        $rest_api_file = CULQI_QR_PLUGIN_DIR . 'includes/api/class-culqi-qr-rest-api.php';
        if (file_exists($rest_api_file)) {
            require_once $rest_api_file;
            $rest_api = new Culqi_QR_REST_API();
            $rest_api->register_routes();
        }
    }

    /**
     * Load plugin textdomain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'culqi-qr',
            false,
            dirname(CULQI_QR_PLUGIN_BASENAME) . '/languages'
        );
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Create tables
        $this->create_tables();

        // Set default options
        $this->set_default_options();

        // Schedule cron jobs
        if (!wp_next_scheduled('culqi_qr_cleanup')) {
            wp_schedule_event(time(), 'daily', 'culqi_qr_cleanup');
        }

        // Flush rewrite rules
        flush_rewrite_rules();

        // Log activation
        error_log('Culqi QR Plugin activated - Version ' . CULQI_QR_VERSION);
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clear scheduled events
        wp_clear_scheduled_hook('culqi_qr_cleanup');

        // Flush rewrite rules
        flush_rewrite_rules();

        // Log deactivation
        error_log('Culqi QR Plugin deactivated');
    }

    /**
     * Create database tables
     */
    private function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Transactions table
        $table_name = $wpdb->prefix . 'culqi_qr_transactions';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            payment_id varchar(100) NOT NULL,
            order_id bigint(20),
            amount decimal(10,2) NOT NULL,
            currency varchar(3) NOT NULL,
            status varchar(20) NOT NULL,
            qr_code text,
            metadata longtext,
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY payment_id (payment_id),
            KEY order_id (order_id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        // Logs table
        $table_name = $wpdb->prefix . 'culqi_qr_logs';

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            level varchar(20) NOT NULL,
            message text NOT NULL,
            context longtext,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY level (level),
            KEY created_at (created_at)
        ) $charset_collate;";

        dbDelta($sql);
    }

    /**
     * Set default options
     */
    private function set_default_options() {
        $defaults = array(
            'culqi_qr_environment' => 'sandbox',
            'culqi_qr_public_key' => '',
            'culqi_qr_secret_key' => '',
            'culqi_qr_webhook_secret' => '',
            'culqi_qr_currency' => 'PEN',
            'culqi_qr_debug' => 'no',
            'culqi_qr_cache_enabled' => 'yes',
            'culqi_qr_cache_ttl' => '3600',
        );

        foreach ($defaults as $key => $value) {
            if (false === get_option($key)) {
                add_option($key, $value);
            }
        }
    }

    /**
     * Get API client
     *
     * @return Culqi_QR_API
     */
    public function api() {
        return $this->api;
    }

    /**
     * Get logger
     *
     * @return Culqi_QR_Logger
     */
    public function logger() {
        return $this->logger;
    }

    /**
     * Get webhook handler
     *
     * @return Culqi_QR_Webhook
     */
    public function webhook() {
        return $this->webhook;
    }
}

/**
 * Get main plugin instance
 *
 * @return Culqi_QR
 */
function culqi_qr() {
    return Culqi_QR::instance();
}

// Initialize plugin
culqi_qr();
