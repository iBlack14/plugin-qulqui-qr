<?php
/**
 * Obtener IP del sandbox de Culqi
 *
 * Este script obtiene la IP actual de api-sandbox.culqi.com
 * para usarla en el workaround del módulo WHMCS
 */

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>";
echo "<title>Obtener IP Sandbox Culqi</title>";
echo "<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
.container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
h1 { color: #00a19c; }
.success { background: #d4edda; color: #155724; padding: 15px; border-radius: 4px; border: 1px solid #c3e6cb; }
.error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; border: 1px solid #f5c6cb; }
.info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 4px; border: 1px solid #bee5eb; margin: 20px 0; }
.ip-box { background: #f8f9fa; padding: 20px; border-radius: 4px; border: 2px solid #00a19c; font-size: 24px; text-align: center; font-weight: bold; margin: 20px 0; }
code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
</style></head><body><div class='container'>";

echo "<h1>🔍 Obtener IP de Sandbox Culqi</h1>";

// Intentar obtener la IP
$host = 'api-sandbox.culqi.com';
$ip = gethostbyname($host);

if ($ip != $host) {
    // DNS funcionó
    echo "<div class='success'>";
    echo "<h2>✅ IP obtenida correctamente</h2>";
    echo "<p>Tu servidor puede resolver <code>$host</code></p>";
    echo "</div>";

    echo "<div class='ip-box'>";
    echo "IP: $ip";
    echo "</div>";

    echo "<div class='info'>";
    echo "<h3>📋 Cómo usar esta IP:</h3>";
    echo "<ol>";
    echo "<li>Copia la IP de arriba: <strong>$ip</strong></li>";
    echo "<li>Ve a WHMCS: <strong>Setup → Payments → Payment Gateways → Culqi QR</strong></li>";
    echo "<li>Busca el campo: <strong>\"Sandbox IP (Opcional)\"</strong></li>";
    echo "<li>Pega la IP: <code>$ip</code></li>";
    echo "<li>Guarda los cambios</li>";
    echo "</ol>";
    echo "<p><strong>Nota:</strong> Si tu módulo no tiene el campo \"Sandbox IP\", necesitas actualizar a la versión con el fix.</p>";
    echo "</div>";

    echo "<div class='info'>";
    echo "<h3>ℹ️ ¿Por qué necesito esto?</h3>";
    echo "<p>Algunos servidores bloquean el dominio <code>api-sandbox.culqi.com</code> pero permiten conexiones si usas la IP directamente.</p>";
    echo "<p>Este workaround le dice a tu servidor: <em>\"Cuando veas api-sandbox.culqi.com, usa esta IP en su lugar\"</em></p>";
    echo "</div>";

} else {
    // DNS falló
    echo "<div class='error'>";
    echo "<h2>❌ No se pudo obtener la IP</h2>";
    echo "<p>Tu servidor no puede resolver el dominio <code>$host</code></p>";
    echo "<p>Esto confirma que el sandbox está bloqueado.</p>";
    echo "</div>";

    echo "<div class='info'>";
    echo "<h3>🔧 Soluciones:</h3>";
    echo "<h4>Opción 1: Obtener la IP desde otro lugar</h4>";
    echo "<p>Desde tu computadora (no el servidor), ejecuta:</p>";
    echo "<p><strong>Windows:</strong></p>";
    echo "<pre>nslookup api-sandbox.culqi.com</pre>";
    echo "<p><strong>Linux/Mac:</strong></p>";
    echo "<pre>dig api-sandbox.culqi.com +short</pre>";
    echo "<p><strong>Online:</strong></p>";
    echo "<p>Ve a: <a href='https://www.nslookup.io/domains/api-sandbox.culqi.com/dns-records/' target='_blank'>https://www.nslookup.io/domains/api-sandbox.culqi.com/dns-records/</a></p>";
    echo "<hr>";
    echo "<h4>Opción 2: Usar producción</h4>";
    echo "<p>Si tienes keys de producción de Culqi, puedes usarlas directamente. El dominio <code>api.culqi.com</code> probablemente funciona.</p>";
    echo "<hr>";
    echo "<h4>Opción 3: Contactar a tu hosting</h4>";
    echo "<p>Pide a tu proveedor (MegaWeb) que desbloquee el dominio <code>api-sandbox.culqi.com</code></p>";
    echo "</div>";
}

// Intentar también con api.culqi.com (producción)
echo "<hr>";
echo "<h2>🧪 Test adicional: API de Producción</h2>";

$prod_host = 'api.culqi.com';
$prod_ip = gethostbyname($prod_host);

if ($prod_ip != $prod_host) {
    echo "<div class='success'>";
    echo "✅ <code>$prod_host</code> → <strong>$prod_ip</strong>";
    echo "<p>El dominio de producción <strong>SÍ funciona</strong>. Puedes usar producción si tienes keys.</p>";
    echo "</div>";
} else {
    echo "<div class='error'>";
    echo "❌ <code>$prod_host</code> → No puede resolver";
    echo "<p>El dominio de producción también está bloqueado.</p>";
    echo "</div>";
}

// Test de conectividad con cURL
echo "<hr>";
echo "<h2>🔌 Test de Conectividad</h2>";

if (function_exists('curl_init')) {
    echo "<p>Probando conexión con cURL a los dominios de Culqi...</p>";

    $domains = [
        'sandbox' => 'https://api-sandbox.culqi.com/v2/',
        'production' => 'https://api.culqi.com/v2/'
    ];

    foreach ($domains as $name => $url) {
        echo "<div style='margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 4px;'>";
        echo "<strong>" . ucfirst($name) . ":</strong> <code>$url</code><br>";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $start = microtime(true);
        $response = curl_exec($ch);
        $time = round((microtime(true) - $start) * 1000, 2);

        $error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            echo "<span style='color: #dc3545;'>❌ Error: $error</span>";
        } else {
            echo "<span style='color: #28a745;'>✅ Conexión exitosa</span> (HTTP $http_code, {$time}ms)";
        }

        echo "</div>";
    }
} else {
    echo "<div class='error'>❌ cURL no está habilitado en PHP</div>";
}

echo "<hr>";
echo "<p style='text-align: center; color: #666;'>";
echo "Script generado: " . date('Y-m-d H:i:s') . " | ";
echo "<a href='" . $_SERVER['PHP_SELF'] . "'>Recargar</a>";
echo "</p>";

echo "</div></body></html>";
?>
