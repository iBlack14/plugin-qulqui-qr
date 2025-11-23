<?php
/**
 * Culqi Connection Tester
 *
 * @package CulqiQR
 */

defined('ABSPATH') || exit;

/**
 * Test Culqi API connectivity
 */
function culqi_qr_test_connection() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Culqi QR - Test de Conexión', 'culqi-qr'); ?></h1>

        <?php
        $tests = array();

        // Test 1: DNS Resolution
        $host = 'api-dev.culqi.com';
        $ip = gethostbyname($host);
        $tests['dns'] = array(
            'name' => 'Resolución DNS',
            'result' => ($ip !== $host),
            'message' => $ip !== $host
                ? "✅ DNS resuelve correctamente a: {$ip}"
                : "❌ No puede resolver {$host}"
        );

        // Test 2: cURL Available
        $tests['curl'] = array(
            'name' => 'cURL Disponible',
            'result' => function_exists('curl_version'),
            'message' => function_exists('curl_version')
                ? '✅ cURL está instalado: ' . curl_version()['version']
                : '❌ cURL no está instalado'
        );

        // Test 3: allow_url_fopen
        $tests['fopen'] = array(
            'name' => 'allow_url_fopen',
            'result' => ini_get('allow_url_fopen'),
            'message' => ini_get('allow_url_fopen')
                ? '✅ allow_url_fopen está habilitado'
                : '❌ allow_url_fopen está deshabilitado'
        );

        // Test 4: Try connection to Culqi
        $response = wp_remote_get('https://api-dev.culqi.com', array('timeout' => 10));
        $tests['culqi'] = array(
            'name' => 'Conexión a Culqi',
            'result' => !is_wp_error($response),
            'message' => !is_wp_error($response)
                ? '✅ Puede conectarse a Culqi (Status: ' . wp_remote_retrieve_response_code($response) . ')'
                : '❌ Error: ' . $response->get_error_message()
        );

        // Test 5: SSL
        $tests['ssl'] = array(
            'name' => 'Soporte SSL',
            'result' => extension_loaded('openssl'),
            'message' => extension_loaded('openssl')
                ? '✅ OpenSSL está instalado'
                : '❌ OpenSSL no está instalado'
        );

        // Display results
        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Test</th>
                    <th>Resultado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tests as $test) : ?>
                    <tr>
                        <td><strong><?php echo esc_html($test['name']); ?></strong></td>
                        <td>
                            <?php if ($test['result']) : ?>
                                <span style="color: green;">✅</span>
                            <?php else : ?>
                                <span style="color: red;">❌</span>
                            <?php endif; ?>
                            <?php echo esc_html($test['message']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php
        // Recommendations
        $all_passed = array_reduce($tests, function($carry, $test) {
            return $carry && $test['result'];
        }, true);
        ?>

        <div class="notice notice-<?php echo $all_passed ? 'success' : 'warning'; ?>" style="margin-top: 20px;">
            <h3><?php echo $all_passed ? '✅ Todo OK' : '⚠️ Hay Problemas'; ?></h3>
            <?php if (!$all_passed) : ?>
                <h4>Soluciones:</h4>
                <ol>
                    <?php if (!$tests['dns']['result']) : ?>
                        <li><strong>Problema DNS:</strong> Contacta a tu proveedor de hosting</li>
                    <?php endif; ?>

                    <?php if (!$tests['curl']['result']) : ?>
                        <li><strong>cURL faltante:</strong> Pídele a tu hosting que instale cURL</li>
                    <?php endif; ?>

                    <?php if (!$tests['culqi']['result']) : ?>
                        <li><strong>Conexión bloqueada:</strong>
                            <ul>
                                <li>Verifica que tu hosting permita conexiones salientes</li>
                                <li>Verifica que no haya firewall bloqueando</li>
                                <li>Contacta a tu hosting para desbloquear api-dev.culqi.com</li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <?php if (!$tests['ssl']['result']) : ?>
                        <li><strong>SSL faltante:</strong> Pídele a tu hosting que instale OpenSSL</li>
                    <?php endif; ?>
                </ol>
            <?php else : ?>
                <p>✅ Tu servidor puede conectarse a Culqi correctamente.</p>
            <?php endif; ?>
        </div>

        <hr>

        <h3>Información del Servidor</h3>
        <table class="wp-list-table widefat">
            <tr>
                <td><strong>PHP Version:</strong></td>
                <td><?php echo phpversion(); ?></td>
            </tr>
            <tr>
                <td><strong>WordPress Version:</strong></td>
                <td><?php echo get_bloginfo('version'); ?></td>
            </tr>
            <tr>
                <td><strong>Servidor:</strong></td>
                <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
            </tr>
            <tr>
                <td><strong>cURL Version:</strong></td>
                <td><?php echo function_exists('curl_version') ? curl_version()['version'] : 'No instalado'; ?></td>
            </tr>
        </table>
    </div>
    <?php
}

// Add menu item
add_action('admin_menu', function() {
    add_submenu_page(
        'culqi-qr',
        __('Test de Conexión', 'culqi-qr'),
        __('Test de Conexión', 'culqi-qr'),
        'manage_options',
        'culqi-qr-test',
        'culqi_qr_test_connection'
    );
});
