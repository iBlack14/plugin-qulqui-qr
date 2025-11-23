<?php
/**
 * Elementor Integration
 *
 * @package CulqiQR
 */

defined('ABSPATH') || exit;

/**
 * Culqi_QR_Elementor class
 *
 * Placeholder for future Elementor integration
 */
class Culqi_QR_Elementor {

    /**
     * Constructor
     */
    public function __construct() {
        // TODO: Implement Elementor widgets
        // Only load if Elementor is active
        if (!did_action('elementor/loaded')) {
            return;
        }
    }
}
