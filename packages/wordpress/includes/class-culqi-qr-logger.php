<?php
/**
 * Logger
 *
 * @package CulqiQR
 */

defined('ABSPATH') || exit;

/**
 * Culqi_QR_Logger class
 */
class Culqi_QR_Logger {

    /**
     * Log levels
     */
    const DEBUG = 'debug';
    const INFO = 'info';
    const WARNING = 'warning';
    const ERROR = 'error';

    /**
     * Log debug message
     *
     * @param string $message
     * @param array  $context
     */
    public function debug($message, $context = array()) {
        if ('yes' === get_option('culqi_qr_debug', 'no')) {
            $this->log(self::DEBUG, $message, $context);
        }
    }

    /**
     * Log info message
     *
     * @param string $message
     * @param array  $context
     */
    public function info($message, $context = array()) {
        $this->log(self::INFO, $message, $context);
    }

    /**
     * Log warning message
     *
     * @param string $message
     * @param array  $context
     */
    public function warning($message, $context = array()) {
        $this->log(self::WARNING, $message, $context);
    }

    /**
     * Log error message
     *
     * @param string $message
     * @param array  $context
     */
    public function error($message, $context = array()) {
        $this->log(self::ERROR, $message, $context);
    }

    /**
     * Log message
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     */
    private function log($level, $message, $context = array()) {
        global $wpdb;

        $table = $wpdb->prefix . 'culqi_qr_logs';

        $wpdb->insert(
            $table,
            array(
                'level' => $level,
                'message' => $message,
                'context' => wp_json_encode($context),
                'created_at' => current_time('mysql'),
            ),
            array('%s', '%s', '%s', '%s')
        );

        // Also log to error_log in debug mode
        if ('yes' === get_option('culqi_qr_debug', 'no')) {
            error_log(sprintf('[Culqi QR] [%s] %s %s', strtoupper($level), $message, wp_json_encode($context)));
        }
    }

    /**
     * Get logs
     *
     * @param array $args
     * @return array
     */
    public function get_logs($args = array()) {
        global $wpdb;

        $defaults = array(
            'level' => '',
            'limit' => 100,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC',
        );

        $args = wp_parse_args($args, $defaults);

        $table = $wpdb->prefix . 'culqi_qr_logs';

        $where = '1=1';
        if (!empty($args['level'])) {
            $where .= $wpdb->prepare(' AND level = %s', $args['level']);
        }

        $query = "SELECT * FROM $table WHERE $where ORDER BY {$args['orderby']} {$args['order']} LIMIT %d OFFSET %d";

        return $wpdb->get_results($wpdb->prepare($query, $args['limit'], $args['offset']));
    }

    /**
     * Clear old logs
     *
     * @param int $days
     * @return int|false
     */
    public function clear_old_logs($days = 30) {
        global $wpdb;

        $table = $wpdb->prefix . 'culqi_qr_logs';

        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $wpdb->query(
            $wpdb->prepare("DELETE FROM $table WHERE created_at < %s", $date)
        );
    }
}
