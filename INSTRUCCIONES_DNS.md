# Cómo Actualizar el Dominio de la API

Si descubres el dominio correcto de Culqi, edita este archivo:

```
wp-content/plugins/culqi-qr/culqi-qr.php
```

Busca estas líneas (alrededor de línea 31):

```php
// API endpoints
define('CULQI_QR_API_BASE', 'https://api.culqi.com/v2/');
define('CULQI_QR_API_SANDBOX', 'https://api-dev.culqi.com/v2/');
```

Cambia a:

```php
// API endpoints
define('CULQI_QR_API_BASE', 'https://api.culqi.com/v2/');
define('CULQI_QR_API_SANDBOX', 'https://DOMINIO-CORRECTO-AQUI/v2/');
```

Guarda y prueba de nuevo.

---

## Dominios posibles para probar:

1. `https://api.culqi.com/v2/` (producción, pero con keys de test)
2. `https://sandbox-api.culqi.com/v2/`
3. `https://staging.culqi.com/v2/`
4. `https://test.culqi.com/v2/`
5. `https://dev.culqi.com/v2/`

Prueba cada uno en el Test de Conexión hasta que encuentres el correcto.
