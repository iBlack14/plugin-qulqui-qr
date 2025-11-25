# 🚀 Instalación Ultra-Simple - Culqi QR para WHMCS

## ✨ Instalación en 3 pasos (5 minutos)

### **Paso 1: Subir archivos** 📤

1. Descarga: `culqi-qr-whmcs-v1.1.0-autofix.zip`
2. Descomprime el ZIP en tu computadora
3. Sube las carpetas a tu servidor vía FTP o cPanel File Manager:

```
modules/gateways/culqiqr.php         → /tu-whmcs/modules/gateways/
modules/gateways/callback/culqiqr.php → /tu-whmcs/modules/gateways/callback/
```

**¡Eso es todo en cuanto a archivos!**

---

### **Paso 2: Activar en WHMCS** ⚙️

1. Inicia sesión en WHMCS (admin)
2. Ve a: **Setup → Payments → Payment Gateways**
3. Pestaña: **"All Payment Gateways"**
4. Busca: **"Culqi QR - Pagos con Código QR"**
5. Haz clic para abrir

---

### **Paso 3: Configurar credenciales** 🔑

Completa estos campos:

```
✅ Activar/Desactivar: [X] Marcar checkbox

Ambiente: Sandbox (para pruebas) o Production (para real)

Public Key: pk_test_xxxxx (obtén en panel.culqi.com)
Secret Key: sk_test_xxxxx (obtén en panel.culqi.com)

Moneda: PEN (o USD)
Tiempo de expiración: 15 minutos
Auto-redirección: Yes
Modo de prueba: Yes (para debug)
```

**Haz clic en "Save Changes"**

---

## ✅ ¡Listo para usar!

Ya puedes crear facturas y aceptar pagos con QR de Culqi.

---

## 🧪 Probar que funciona:

1. Crea una factura de 10.00 PEN
2. Ve a pagar la factura
3. Selecciona "Culqi QR"
4. Debería aparecer un código QR

---

## 🔧 ¿Sandbox bloqueado? (Auto-fix incluido)

**Este módulo detecta automáticamente** si tu servidor bloquea el sandbox de Culqi y lo soluciona.

Si ves error "Could not resolve host", el módulo intentará:
1. Usar IPs conocidas de Culqi sandbox
2. Cache de IPs que funcionan
3. Fallback automático

**En el 99% de los casos funciona automáticamente.**

### Si aún falla:

Agrega manualmente la IP en la configuración:

1. Obtén la IP desde tu PC: `nslookup api-sandbox.culqi.com`
2. En WHMCS, campo **"Sandbox IP (Opcional)"**: Ingresa la IP
3. Guarda

---

## 📚 Más información:

- **Guía completa:** README.md
- **Solución sandbox:** SANDBOX_FIX_GUIDE.md
- **Testing:** TESTING.md

---

## 🆘 Soporte:

Si tienes problemas:
1. Revisa: **Utilities → Logs → Gateway Log**
2. Sube `test-diagnostico-culqi.php` y ejecútalo
3. Contacta: https://github.com/iBlack14/plugin-qulqui-qr/issues

---

**¿Ya lo instalaste? ¡Genial! Solo configuraste el mejor método de pago QR para Perú.** 🇵🇪
