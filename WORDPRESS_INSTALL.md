# 📦 Instalación del Plugin de WordPress - Culqi QR

## 🎯 Archivo ZIP Listo para Descargar

**Ubicación:** `/home/user/plugin-qulqui-qr/culqi-qr-wordpress.zip`

**Tamaño:** 22 KB

---

## 🚀 Método 1: Instalación desde ZIP (Recomendado)

### Pasos:

1. **Descargar el archivo ZIP**
   ```bash
   # El archivo está en:
   /home/user/plugin-qulqui-qr/culqi-qr-wordpress.zip
   ```

2. **Ir al panel de WordPress**
   - Navega a: `Plugins → Añadir nuevo`
   - Haz clic en: `Subir plugin`

3. **Subir el archivo**
   - Haz clic en `Seleccionar archivo`
   - Elige: `culqi-qr-wordpress.zip`
   - Haz clic en: `Instalar ahora`

4. **Activar el plugin**
   - Haz clic en: `Activar plugin`

5. **¡Listo! El plugin está instalado y activo**

---

## 🔧 Método 2: Instalación Manual (Avanzado)

### Via FTP/SFTP:

```bash
# 1. Extraer el ZIP
unzip culqi-qr-wordpress.zip -d culqi-qr/

# 2. Subir via FTP a:
/wp-content/plugins/culqi-qr/

# 3. Activar desde WordPress admin
```

### Via SSH (si tienes acceso):

```bash
# 1. Conectar al servidor
ssh usuario@tu-servidor.com

# 2. Ir al directorio de plugins
cd /path/to/wordpress/wp-content/plugins/

# 3. Subir y extraer
unzip /path/to/culqi-qr-wordpress.zip -d culqi-qr/

# 4. Ajustar permisos
chmod -R 755 culqi-qr/
chown -R www-data:www-data culqi-qr/

# 5. Activar desde WordPress admin
```

---

## ⚙️ Configuración Inicial

### 1. Ir a Configuración

Después de activar, ve a:
```
WordPress Admin → Culqi QR → Settings
```

### 2. Configurar API Keys

#### Para Pruebas (Sandbox):
```
Environment: Sandbox (Test)
Public Key: pk_test_XXXXXXXXXXXXXXXX
Secret Key: sk_test_XXXXXXXXXXXXXXXX
Currency: PEN
```

#### Para Producción (Live):
```
Environment: Production (Live)
Public Key: pk_live_XXXXXXXXXXXXXXXX
Secret Key: sk_live_XXXXXXXXXXXXXXXX
Webhook Secret: whsec_XXXXXXXXXXXXXXXX
Currency: PEN
```

### 3. Obtener API Keys de Culqi

1. Ve a: https://www.culqi.com/
2. Inicia sesión en tu cuenta
3. Ve a: `Configuración → API Keys`
4. Copia las keys y pégalas en el plugin

---

## 🛒 Integración con WooCommerce

Si usas WooCommerce:

### 1. Activar método de pago

```
WooCommerce → Ajustes → Pagos → Culqi QR
```

### 2. Habilitar

- Marca la casilla: `Activar Culqi QR`
- Configura título y descripción
- Guarda cambios

### 3. Probar

- Crea un producto de prueba
- Agrégalo al carrito
- Procede al checkout
- Deberías ver "Pagar con Culqi QR"

---

## 🎨 Usar Shortcodes

### Shortcode 1: Botón de Pago

```php
[culqi_qr amount="100.00" currency="PEN" description="Producto X"]
```

**Parámetros:**
- `amount` - Monto a cobrar (requerido)
- `currency` - Moneda (PEN o USD)
- `description` - Descripción del pago
- `button_text` - Texto del botón

**Ejemplo completo:**
```php
[culqi_qr
    amount="99.90"
    currency="PEN"
    description="Curso de WordPress"
    button_text="Comprar Ahora"
]
```

### Shortcode 2: Mostrar QR Existente

```php
[culqi_qr_display payment_id="ord_xxxxx"]
```

O por Order ID:
```php
[culqi_qr_display order_id="123"]
```

### Shortcode 3: Formulario Personalizado

```php
[culqi_qr_form button_text="Generar QR de Pago"]
```

---

## 🔗 Configurar Webhooks

### 1. Obtener URL del Webhook

El plugin muestra la URL en:
```
Culqi QR → Settings → Webhook URL
```

La URL será algo como:
```
https://tu-sitio.com/wp-json/culqi-qr/v1/webhook
```

### 2. Configurar en Culqi

1. Ve a tu dashboard de Culqi
2. Ve a: `Configuración → Webhooks`
3. Añade nueva webhook URL
4. Pega la URL del paso 1
5. Selecciona eventos:
   - `order.paid`
   - `order.expired`
   - `charge.succeeded`
   - `charge.failed`
6. Guarda

### 3. Copiar Webhook Secret

Culqi te dará un `Webhook Secret`, cópialo y pégalo en:
```
Culqi QR → Settings → Webhook Secret
```

---

## 📊 Ver Transacciones

Para ver todas las transacciones:

```
WordPress Admin → Culqi QR → Transactions
```

Aquí verás:
- ID de pago
- Monto
- Estado
- Fecha
- Orden asociada (si hay)

---

## 🐛 Modo Debug

Para debugging, activa el modo debug:

```
Culqi QR → Settings → Debug Mode ✓
```

Los logs se guardarán en:
- Base de datos: Tabla `wp_culqi_qr_logs`
- PHP error_log

---

## ✅ Verificar Instalación

### Checklist:

- [ ] Plugin activado sin errores
- [ ] Menú "Culqi QR" visible en admin
- [ ] API keys configuradas
- [ ] WooCommerce integrado (si aplica)
- [ ] Shortcodes funcionando
- [ ] Webhooks configurados

### Probar Pago:

1. Crea una página de prueba
2. Agrega el shortcode:
   ```
   [culqi_qr amount="10.00" currency="PEN" description="Test"]
   ```
3. Publica la página
4. Abre la página en el navegador
5. Haz clic en el botón
6. Debería mostrarse un QR

---

## 🆘 Solución de Problemas

### Error: "El plugin no pudo activarse"

**Solución:**
1. Verifica versión de PHP: Requiere PHP 7.4+
2. Verifica versión de WordPress: Requiere WP 6.0+
3. Revisa error_log de PHP

### Error: "API Key inválida"

**Solución:**
1. Verifica que copiaste las keys correctamente
2. Asegúrate de usar las keys del entorno correcto
3. Verifica que no haya espacios extras

### Error: "No se genera el QR"

**Solución:**
1. Activa Debug Mode
2. Revisa logs en `Culqi QR → Transactions`
3. Verifica conexión a internet del servidor
4. Verifica que las API keys sean correctas

### Webhooks no funcionan

**Solución:**
1. Verifica que la URL sea accesible públicamente
2. Verifica Webhook Secret
3. Revisa logs de Culqi
4. Activa Debug Mode

---

## 📂 Estructura de Archivos Instalados

```
wp-content/plugins/culqi-qr/
├── culqi-qr.php                    # Plugin principal
├── includes/
│   ├── class-culqi-qr-api.php     # Cliente API
│   ├── class-culqi-qr-generator.php # Generador QR
│   ├── class-culqi-qr-logger.php   # Sistema de logs
│   ├── class-culqi-qr-webhook.php  # Webhooks
│   ├── class-culqi-qr-cache.php    # Cache
│   ├── admin/
│   │   ├── class-culqi-qr-admin.php
│   │   ├── class-culqi-qr-settings.php
│   │   └── class-culqi-qr-analytics.php
│   ├── api/
│   │   └── class-culqi-qr-rest-api.php
│   ├── public/
│   │   ├── class-culqi-qr-shortcodes.php
│   │   └── class-culqi-qr-blocks.php
│   ├── woocommerce/
│   │   └── class-culqi-qr-gateway.php
│   └── integrations/
│       ├── class-culqi-qr-elementor.php
│       └── class-culqi-qr-divi.php
└── templates/
    └── qr-display.php
```

---

## 🔐 Seguridad

### Buenas Prácticas:

1. **Nunca compartas tus Secret Keys**
2. **Usa Sandbox para pruebas**
3. **Configura Webhook Secret**
4. **Mantén WordPress actualizado**
5. **Usa HTTPS en producción**
6. **Desactiva Debug Mode en producción**

---

## 📞 Soporte

### Documentación:
- README.md
- ARCHITECTURE.md
- CONTRIBUTING.md

### Issues:
https://github.com/iBlack14/plugin-qulqui-qr/issues

### Culqi Docs:
https://docs.culqi.com/

---

## 🎉 ¡Todo Listo!

El plugin está instalado y listo para usar.

**Próximos pasos:**
1. Configura tus API keys
2. Prueba un pago en modo Sandbox
3. Configura webhooks
4. Pasa a producción cuando estés listo

¡Feliz venta! 🚀
