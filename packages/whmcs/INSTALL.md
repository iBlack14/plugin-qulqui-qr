# Guía de Instalación Rápida - Culqi QR para WHMCS

## 🚀 Instalación en 5 pasos

### 1️⃣ Subir archivos

Copia los siguientes archivos a tu instalación de WHMCS:

```bash
# Archivo principal del gateway
culqiqr.php → /modules/gateways/

# Archivo de callback
callback/culqiqr.php → /modules/gateways/callback/
```

### 2️⃣ Activar el módulo

1. Inicia sesión en WHMCS como administrador
2. Ve a **Setup** → **Payments** → **Payment Gateways**
3. Busca "Culqi QR" en la pestaña "All Payment Gateways"
4. Haz clic en "Culqi QR - Pagos con Código QR"

### 3️⃣ Configurar credenciales

Completa la configuración con tus datos de Culqi:

```
┌─────────────────────────────────────────────┐
│ Ambiente: Sandbox (para pruebas)            │
│ Public Key: pk_test_xxxxxxxxxxxxxxxx        │
│ Secret Key: sk_test_xxxxxxxxxxxxxxxx        │
│ Moneda: PEN                                 │
│ Tiempo de expiración: 15 minutos           │
│ Auto-redirección: Sí                        │
│ Modo de prueba: Sí (para debugging)        │
└─────────────────────────────────────────────┘
```

### 4️⃣ Obtener API Keys

**Para Sandbox (Pruebas):**
1. Ve a https://panel.culqi.com
2. Inicia sesión
3. Desarrollo → API Keys
4. Copia las keys de "Sandbox"

**Para Producción:**
1. En el panel de Culqi
2. Desarrollo → API Keys
3. Copia las keys de "Producción"

### 5️⃣ Configurar Webhooks (Opcional pero recomendado)

1. Panel de Culqi → Desarrollo → Webhooks
2. Crear Webhook
3. URL: `https://tudominio.com/modules/gateways/callback/culqiqr.php`
4. Eventos:
   - ✅ order.status.changed
   - ✅ charge.succeeded
   - ✅ charge.failed
5. Guardar

## ✅ Verificar instalación

### Test rápido:

1. Crea una factura de prueba en WHMCS
2. Ve al área de cliente
3. Intenta pagar la factura
4. Selecciona "Pago con QR (Culqi)"
5. Deberías ver un código QR generado

## 🔧 Solución rápida de problemas

| Problema | Solución |
|----------|----------|
| No aparece el método de pago | Verifica que el módulo esté activado |
| Error al generar QR | Revisa que las API Keys sean correctas |
| El pago no se registra | Configura los webhooks |
| Error "Module Not Activated" | Activa el módulo en Payment Gateways |

## 📞 ¿Necesitas ayuda?

- 📖 [README completo](README.md)
- 🐛 [Reportar un bug](https://github.com/iBlack14/plugin-qulqui-qr/issues)
- 📚 [Documentación de Culqi](https://docs.culqi.com/)

---

**¡Listo! Tu sistema WHMCS ahora acepta pagos con QR de Culqi** 🎉
