<?php
/**
 * Cache Handler
 *
 * @package CulqiQR
 */

defined('ABSPATH') || exit;

/**
 * Culqi_QR_Cache class
 */
class Culqi_QR_Cache {

    /**
     * Cache group
     *
     * @var string
     */
    const CACHE_GROUP = 'culqi_qr';

    /**
     * Get cached data
     *
     * @param string $key
     * @return mixed|false
     */
    public static function get($key) {
        if ('yes' !== get_option('culqi_qr_cache_enabled', 'yes')) {
            return false;
        }

        return wp_cache_get($key, self::CACHE_GROUP);
    }

    /**
     * Set cached data
     *
     * @param string $key
     * @param mixed  $data
     * @param int    $expire
     * @return bool
     */
    public static function set($key, $data, $expire = null) {
        if ('yes' !== get_option('culqi_qr_cache_enabled', 'yes')) {
            return false;
        }

        if (null === $expire) {
            $expire = (int) get_option('culqi_qr_cache_ttl', 3600);
        }

        return wp_cache_set($key, $data, self::CACHE_GROUP, $expire);
    }

    /**
     * Delete cached data
     *
     * @param string $key
     * @return bool
     */
    public static function delete($key) {
        return wp_cache_delete($key, self::CACHE_GROUP);
    }

    /**
     * Flush all cache
     *
     * @return bool
     */
    public static function flush() {
        return wp_cache_flush();
    }
}
