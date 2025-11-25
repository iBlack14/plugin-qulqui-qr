<?php
/**
 * Diagnóstico completo de conectividad con Culqi
 * Subir este archivo a la raíz de WHMCS y acceder vía navegador
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>";
echo "<title>Diagnóstico Culqi - WHMCS</title>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
h1 { color: #00a19c; border-bottom: 3px solid #00a19c; padding-bottom: 10px; }
h2 { color: #333; margin-top: 30px; background: #f0f0f0; padding: 10px; border-left: 4px solid #00a19c; }
.success { color: #28a745; font-weight: bold; }
.error { color: #dc3545; font-weight: bold; }
.warning { color: #ffc107; font-weight: bold; }
.info { background: #e7f3ff; padding: 10px; border-left: 4px solid #2196F3; margin: 10px 0; }
table { width: 100%; border-collapse: collapse; margin: 10px 0; }
table td { padding: 8px; border-bottom: 1px solid #ddd; }
table td:first-child { font-weight: bold; width: 30%; }
pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; border: 1px solid #ddd; }
.test-box { background: #fff; border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 4px; }
</style></head><body><div class='container'>";

echo "<h1>🔍 Diagnóstico Completo - Culqi QR para WHMCS</h1>";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>Servidor:</strong> " . $_SERVER['SERVER_NAME'] . "</p>";

// ==========================================
// TEST 1: Información del servidor
// ==========================================
echo "<h2>1️⃣ Información del Servidor</h2>";
echo "<table>";
echo "<tr><td>PHP Version:</td><td>" . phpversion() . "</td></tr>";
echo "<tr><td>Sistema Operativo:</td><td>" . PHP_OS . "</td></tr>";
echo "<tr><td>Server Software:</td><td>" . $_SERVER['SERVER_SOFTWARE'] . "</td></tr>";
echo "<tr><td>Document Root:</td><td>" . $_SERVER['DOCUMENT_ROOT'] . "</td></tr>";
echo "</table>";

// ==========================================
// TEST 2: Extensiones PHP necesarias
// ==========================================
echo "<h2>2️⃣ Extensiones PHP</h2>";
echo "<table>";

$extensions = [
    'curl' => 'cURL (Requerido para API)',
    'json' => 'JSON (Requerido)',
    'openssl' => 'OpenSSL (Requerido para HTTPS)',
    'mbstring' => 'MBString (Recomendado)',
];

foreach ($extensions as $ext => $desc) {
    $loaded = extension_loaded($ext);
    $status = $loaded ? "<span class='success'>✅ Habilitado</span>" : "<span class='error'>❌ Deshabilitado</span>";
    echo "<tr><td>$desc</td><td>$status</td></tr>";
}
echo "</table>";

if (extension_loaded('curl')) {
    $curl_version = curl_version();
    echo "<div class='info'>";
    echo "<strong>Información de cURL:</strong><br>";
    echo "Versión: " . $curl_version['version'] . "<br>";
    echo "SSL Versión: " . $curl_version['ssl_version'] . "<br>";
    echo "Protocolos: " . implode(', ', $curl_version['protocols']) . "<br>";
    echo "</div>";
}

// ==========================================
// TEST 3: Configuración PHP relevante
// ==========================================
echo "<h2>3️⃣ Configuración PHP</h2>";
echo "<table>";
echo "<tr><td>allow_url_fopen:</td><td>" . (ini_get('allow_url_fopen') ? "<span class='success'>✅ Habilitado</span>" : "<span class='error'>❌ Deshabilitado</span>") . "</td></tr>";
echo "<tr><td>max_execution_time:</td><td>" . ini_get('max_execution_time') . " segundos</td></tr>";
echo "<tr><td>memory_limit:</td><td>" . ini_get('memory_limit') . "</td></tr>";
$open_basedir = ini_get('open_basedir');
echo "<tr><td>open_basedir:</td><td>" . ($open_basedir ? $open_basedir : "<span class='success'>Sin restricciones</span>") . "</td></tr>";
$disable_functions = ini_get('disable_functions');
echo "<tr><td>disable_functions:</td><td>" . ($disable_functions ? $disable_functions : "<span class='success'>Ninguna</span>") . "</td></tr>";
echo "</table>";

// ==========================================
// TEST 4: Resolución DNS
// ==========================================
echo "<h2>4️⃣ Test de Resolución DNS</h2>";

$hosts = [
    'api-sandbox.culqi.com' => 'API Sandbox de Culqi',
    'api.culqi.com' => 'API Producción de Culqi',
    'google.com' => 'Google (referencia)',
    'cloudflare.com' => 'Cloudflare (referencia)',
];

echo "<table>";
foreach ($hosts as $host => $desc) {
    $start = microtime(true);
    $ip = gethostbyname($host);
    $time = round((microtime(true) - $start) * 1000, 2);

    if ($ip != $host) {
        echo "<tr><td>$desc ($host)</td><td><span class='success'>✅ $ip</span> ({$time}ms)</td></tr>";
    } else {
        echo "<tr><td>$desc ($host)</td><td><span class='error'>❌ No puede resolver</span></td></tr>";
    }
}
echo "</table>";

// ==========================================
// TEST 5: Test de conectividad con cURL
// ==========================================
echo "<h2>5️⃣ Test de Conectividad cURL</h2>";

$endpoints = [
    'https://api-sandbox.culqi.com/v2/' => 'API Sandbox',
    'https://api.culqi.com/v2/' => 'API Producción',
    'https://www.google.com' => 'Google (referencia)',
];

foreach ($endpoints as $url => $name) {
    echo "<div class='test-box'>";
    echo "<h3>$name</h3>";
    echo "<strong>URL:</strong> $url<br>";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_VERBOSE, false);

    $start = microtime(true);
    $response = curl_exec($ch);
    $time = round((microtime(true) - $start) * 1000, 2);

    $error = curl_error($ch);
    $errno = curl_errno($ch);
    $info = curl_getinfo($ch);

    curl_close($ch);

    if ($errno) {
        echo "<span class='error'>❌ Error: $error (Código: $errno)</span><br>";
        echo "<strong>Tiempo:</strong> {$time}ms<br>";
    } else {
        echo "<span class='success'>✅ Conexión exitosa</span><br>";
        echo "<strong>HTTP Code:</strong> " . $info['http_code'] . "<br>";
        echo "<strong>Tiempo total:</strong> {$time}ms<br>";
        echo "<strong>Content Type:</strong> " . ($info['content_type'] ?: 'N/A') . "<br>";
        echo "<strong>Tamaño respuesta:</strong> " . strlen($response) . " bytes<br>";
    }

    echo "</div>";
}

// ==========================================
// TEST 6: Test con API Key (si se proporciona)
// ==========================================
echo "<h2>6️⃣ Test con API Key de Culqi</h2>";
echo "<div class='info'>";
echo "<strong>Nota:</strong> Para probar con tu API Key, agrega al final de la URL:<br>";
echo "<code>?secret_key=sk_test_xxxxx</code>";
echo "</div>";

$secret_key = isset($_GET['secret_key']) ? $_GET['secret_key'] : '';

if (!empty($secret_key)) {
    echo "<div class='test-box'>";
    echo "<h3>Probando con tu Secret Key</h3>";

    // Test: Listar órdenes
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api-sandbox.culqi.com/v2/orders');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $secret_key,
        'Content-Type: application/json',
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $errno = curl_errno($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($errno) {
        echo "<span class='error'>❌ Error de conexión: $error</span><br>";
    } else {
        if ($http_code == 200 || $http_code == 401) {
            echo "<span class='success'>✅ Conexión exitosa con API</span><br>";
            echo "<strong>HTTP Code:</strong> $http_code<br>";

            if ($http_code == 401) {
                echo "<span class='warning'>⚠️ API Key inválida o expirada</span><br>";
            } else {
                echo "<span class='success'>✅ API Key válida</span><br>";
            }

            echo "<strong>Respuesta:</strong><br>";
            echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
        } else {
            echo "<span class='error'>❌ Error HTTP: $http_code</span><br>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
    }

    echo "</div>";
} else {
    echo "<div class='test-box'>";
    echo "<p>No se proporcionó Secret Key. Para probar con tu key:</p>";
    echo "<p><a href='?secret_key=sk_test_TU_KEY_AQUI'>Agregar Secret Key al test</a></p>";
    echo "</div>";
}

// ==========================================
// TEST 7: Test de funciones de red
// ==========================================
echo "<h2>7️⃣ Funciones de Red Disponibles</h2>";
echo "<table>";

$network_functions = [
    'curl_init' => 'cURL',
    'fsockopen' => 'fsockopen',
    'file_get_contents' => 'file_get_contents',
    'fopen' => 'fopen',
    'gethostbyname' => 'gethostbyname',
    'dns_get_record' => 'dns_get_record',
];

foreach ($network_functions as $func => $name) {
    $available = function_exists($func);
    $status = $available ? "<span class='success'>✅ Disponible</span>" : "<span class='error'>❌ No disponible</span>";
    echo "<tr><td>$name</td><td>$status</td></tr>";
}
echo "</table>";

// ==========================================
// TEST 8: Verificar archivo del módulo
// ==========================================
echo "<h2>8️⃣ Verificación de Archivos WHMCS</h2>";

$whmcs_files = [
    '../modules/gateways/culqiqr.php' => 'Módulo principal',
    '../modules/gateways/callback/culqiqr.php' => 'Callback handler',
];

echo "<table>";
foreach ($whmcs_files as $file => $desc) {
    $exists = file_exists($file);
    $status = $exists ? "<span class='success'>✅ Existe</span>" : "<span class='error'>❌ No encontrado</span>";
    echo "<tr><td>$desc</td><td>$status";

    if ($exists) {
        $size = filesize($file);
        $perms = substr(sprintf('%o', fileperms($file)), -4);
        echo " (Tamaño: " . number_format($size) . " bytes, Permisos: $perms)";
    }

    echo "</td></tr>";
}
echo "</table>";

// ==========================================
// RESUMEN Y RECOMENDACIONES
// ==========================================
echo "<h2>📋 Resumen y Recomendaciones</h2>";

$dns_works = (gethostbyname('api-sandbox.culqi.com') != 'api-sandbox.culqi.com');
$curl_works = extension_loaded('curl');

echo "<div class='test-box'>";
echo "<h3>Estado General:</h3>";

if ($dns_works && $curl_works) {
    echo "<p class='success'>✅ El servidor tiene las capacidades necesarias</p>";
} else {
    echo "<p class='error'>❌ Se encontraron problemas que deben resolverse</p>";

    if (!$curl_works) {
        echo "<p class='error'>🔴 CRÍTICO: cURL no está habilitado. Contacta a tu proveedor de hosting.</p>";
    }

    if (!$dns_works) {
        echo "<p class='error'>🔴 CRÍTICO: No puede resolver api-sandbox.culqi.com</p>";
        echo "<p><strong>Posibles causas:</strong></p>";
        echo "<ul>";
        echo "<li>Firewall del servidor bloqueando DNS</li>";
        echo "<li>DNS del servidor mal configurado</li>";
        echo "<li>Restricciones de red del hosting</li>";
        echo "</ul>";
        echo "<p><strong>Solución:</strong></p>";
        echo "<ul>";
        echo "<li>Contacta a MegaWeb (soporte técnico)</li>";
        echo "<li>Pídeles que habiliten conexiones a api-sandbox.culqi.com y api.culqi.com</li>";
        echo "<li>Menciona que necesitas acceso al puerto 443 (HTTPS)</li>";
        echo "</ul>";
    }
}

echo "</div>";

// ==========================================
// Información de contacto
// ==========================================
echo "<div class='info'>";
echo "<h3>📞 Información para Soporte Técnico</h3>";
echo "<p>Si necesitas contactar al soporte de tu hosting, proporciona esta información:</p>";
echo "<ul>";
echo "<li><strong>Servidor:</strong> " . $_SERVER['SERVER_NAME'] . "</li>";
echo "<li><strong>IP del servidor:</strong> " . $_SERVER['SERVER_ADDR'] . "</li>";
echo "<li><strong>PHP Version:</strong> " . phpversion() . "</li>";
echo "<li><strong>Problema:</strong> No puede resolver/conectar a api-sandbox.culqi.com</li>";
echo "<li><strong>Dominios requeridos:</strong> api-sandbox.culqi.com, api.culqi.com (Puerto 443)</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<p style='text-align: center; color: #666; margin-top: 30px;'>";
echo "Diagnóstico generado el " . date('Y-m-d H:i:s') . " | ";
echo "<a href='" . $_SERVER['PHP_SELF'] . "'>Recargar test</a>";
echo "</p>";

echo "</div></body></html>";
?>
