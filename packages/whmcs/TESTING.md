# 🧪 Guía de Verificación - Culqi QR para WHMCS

## ✅ Lista de Verificación Completa

### 1️⃣ Verificar instalación de archivos

Conecta a tu servidor vía FTP/SSH y verifica que los archivos estén en su lugar:

```bash
# Verificar archivo principal del gateway
ls -la /path/to/whmcs/modules/gateways/culqiqr.php

# Verificar archivo de callback
ls -la /path/to/whmcs/modules/gateways/callback/culqiqr.php
```

✅ **Ambos archivos deben existir**

---

### 2️⃣ Activar el módulo en WHMCS

1. Inicia sesión en tu panel de administración de WHMCS
2. Ve a **Setup** → **Payments** → **Payment Gateways**
3. Haz clic en la pestaña **All Payment Gateways**
4. Busca **"Culqi QR - Pagos con Código QR"**
5. Haz clic para abrirlo

**¿Aparece el módulo?**
- ✅ **SÍ** → Continúa al paso 3
- ❌ **NO** → Revisa que el archivo `culqiqr.php` esté en `/modules/gateways/`

---

### 3️⃣ Configurar credenciales de prueba

Completa los siguientes campos:

```
┌─────────────────────────────────────────────┐
│ Activar/Desactivar: ✓ (checkbox marcado)   │
│ Ambiente: Sandbox                           │
│ Public Key: pk_test_xxxxxxxxxxxxx           │
│ Secret Key: sk_test_xxxxxxxxxxxxx           │
│ Moneda: PEN                                 │
│ Tiempo de expiración: 15                   │
│ Auto-redirección: ✓                         │
│ Modo de prueba: ✓ (para ver logs)          │
└─────────────────────────────────────────────┘
```

**¿Dónde conseguir las API Keys de prueba?**

1. Ve a [https://panel.culqi.com](https://panel.culqi.com)
2. Inicia sesión
3. **Desarrollo** → **API Keys**
4. Copia las keys de **"Sandbox"**:
   - Public Key: `pk_test_xxxxxxxxxx`
   - Secret Key: `sk_test_xxxxxxxxxx`

**Guarda la configuración** → Clic en "Save Changes"

---

### 4️⃣ Verificar que el método de pago está disponible

1. Ve a **Setup** → **Payments** → **Payment Gateways**
2. En la pestaña **Manage Existing Gateways**
3. Deberías ver **"Culqi QR"** en la lista de gateways activos

✅ **El módulo está activo**

---

### 5️⃣ Crear una factura de prueba

**Opción A: Desde el admin**

1. Ve a **Clients** → Selecciona o crea un cliente
2. **Actions** → **Create New Invoice**
3. Completa:
   - Description: "Prueba pago QR"
   - Amount: 10.00 PEN
   - Due Date: Hoy
4. Clic en **Create Invoice**
5. Anota el **Invoice ID** (ej: #1234)

**Opción B: Crear cliente y factura de prueba**

1. **Clients** → **Add New Client**
2. Completa datos básicos:
   - First Name: Test
   - Last Name: Culqi
   - Email: test@example.com
3. Crea una factura para este cliente

---

### 6️⃣ Probar el pago como cliente

**Método 1: Desde el área de cliente**

1. Abre una ventana de incógnito
2. Ve a: `https://tudominio.com/whmcs/viewinvoice.php?id=1234`
   (Reemplaza 1234 con tu Invoice ID)
3. Deberías ver el botón **"Pay Now"** o **"Pagar Ahora"**
4. Selecciona **"Pago con QR (Culqi)"**
5. Haz clic en **"Pay Now"**

**Método 2: Login como cliente**

1. Inicia sesión con las credenciales del cliente
2. Ve a **Billing** → **My Invoices**
3. Selecciona la factura pendiente
4. Clic en **"Pay Now"**
5. Selecciona **"Pago con QR (Culqi)"**

---

### 7️⃣ Verificar generación del código QR

Después de hacer clic en "Pay Now", deberías ver:

```
┌──────────────────────────────────────────┐
│  Escanea el código QR para pagar         │
│                                          │
│  [CÓDIGO QR GRANDE]                      │
│                                          │
│  Monto a pagar: PEN 10.00               │
│  Factura: #1234                         │
│  El código QR expirará en 15 minutos    │
│                                          │
│  🔄 Esperando el pago...                │
│                                          │
│  Instrucciones:                         │
│  1. Abre tu aplicación de Culqi         │
│  2. Selecciona pagar con QR             │
│  3. Escanea el código QR                │
│  4. Confirma el pago                    │
└──────────────────────────────────────────┘
```

**Verificaciones:**

- ✅ ¿Se muestra el código QR?
- ✅ ¿El monto es correcto?
- ✅ ¿El número de factura es correcto?
- ✅ ¿Hay un spinner de "Esperando pago"?

**SI NO SE MUESTRA EL QR:**
- Ve al paso 8 para revisar los logs

---

### 8️⃣ Revisar logs de WHMCS

1. En el panel admin, ve a:
   **Utilities** → **Logs** → **Gateway Log**

2. Busca las entradas más recientes de **"culqiqr"**

3. Deberías ver algo como:

```
Gateway: culqiqr
Request: create_order
Data: {"amount":1000,"currency_code":"PEN",...}
Response: {"id":"ord_live_xxx","qr_code":"https://..."}
Result: Success
```

**¿Qué buscar?**

- ✅ **Success** → Todo bien
- ❌ **Error** → Revisa el mensaje de error

**Errores comunes:**

| Error | Causa | Solución |
|-------|-------|----------|
| `Invalid API Key` | API Keys incorrectas | Verifica que usas las keys correctas |
| `cURL Error` | Problema de conexión | Verifica que tu servidor puede conectar a api.culqi.com |
| `Amount is required` | Error en el código | Contacta soporte |

---

### 9️⃣ Probar el pago con la app de Culqi (Sandbox)

**Importante:** En modo sandbox, necesitas la app de Culqi configurada en modo de pruebas.

1. Abre la app de Culqi en tu móvil
2. Asegúrate de estar en modo **Sandbox/Pruebas**
3. Selecciona **"Pagar con QR"**
4. Escanea el código QR de la pantalla
5. Confirma el pago

**Después de confirmar:**

- La página debería actualizar automáticamente (cada 5 segundos)
- Debería aparecer: **"¡Pago exitoso! Redirigiendo..."**
- Serás redirigido a la página de confirmación

---

### 🔟 Verificar que el pago se registró

**En el área de admin:**

1. Ve a **Clients** → Busca tu cliente de prueba
2. **Transactions** → Deberías ver la transacción registrada
3. Ve a **Billing** → **Invoices** → Busca tu factura
4. El estado debería ser **"Paid"** (Pagada)

**En la base de datos:**

```sql
-- Ver transacciones de Culqi
SELECT * FROM mod_culqiqr_transactions
ORDER BY created_at DESC
LIMIT 5;

-- Ver facturas pagadas
SELECT id, status, total, paymentmethod
FROM tblinvoices
WHERE paymentmethod = 'culqiqr'
ORDER BY id DESC
LIMIT 5;
```

---

### 1️⃣1️⃣ Probar webhooks (Opcional pero recomendado)

**Configurar webhook en Culqi:**

1. Panel de Culqi → **Desarrollo** → **Webhooks**
2. Clic en **"Crear Webhook"**
3. URL: `https://tudominio.com/modules/gateways/callback/culqiqr.php`
4. Eventos:
   - ✅ order.status.changed
   - ✅ charge.succeeded
   - ✅ charge.failed
5. Guardar

**Verificar que funciona:**

1. En Culqi, ve a **Desarrollo** → **Webhooks**
2. Encuentra tu webhook
3. Haz clic en **"Ver logs"**
4. Deberías ver las peticiones enviadas
5. Estado: **200 OK** = Funcionando correctamente

---

### 1️⃣2️⃣ Probar reembolsos (Opcional)

1. En WHMCS admin, ve a **Billing** → **Transactions**
2. Encuentra la transacción de Culqi que quieres reembolsar
3. Haz clic en **"Refund Transaction"**
4. Ingresa el monto a reembolsar
5. Clic en **"Submit"**

**Verificar:**
- ✅ El reembolso se procesa en Culqi
- ✅ WHMCS registra el reembolso
- ✅ La factura se marca como "Refunded" o ajusta el balance

---

## 🐛 Solución de Problemas

### Problema 1: El módulo no aparece en Payment Gateways

**Causas posibles:**
- Archivo no está en la ruta correcta
- Permisos de archivo incorrectos
- Error de sintaxis PHP

**Solución:**

```bash
# Verificar que existe
ls -la /path/to/whmcs/modules/gateways/culqiqr.php

# Corregir permisos
chmod 644 /path/to/whmcs/modules/gateways/culqiqr.php

# Verificar errores de PHP
php -l /path/to/whmcs/modules/gateways/culqiqr.php
```

---

### Problema 2: Error "Module Not Activated"

**Causa:** El módulo no está activado en WHMCS

**Solución:**
1. Setup → Payments → Payment Gateways
2. Busca "Culqi QR"
3. Actívalo y guarda la configuración

---

### Problema 3: No se genera el código QR

**Causas posibles:**
- API Keys incorrectas
- Ambiente mal configurado (sandbox vs production)
- Problema de conectividad

**Solución:**

1. Activa "Modo de prueba" en la configuración
2. Intenta generar un QR de nuevo
3. Ve a Utilities → Logs → Gateway Log
4. Revisa el error específico

**Errores comunes:**

```
Error: "Invalid API Key"
→ Verifica que las keys sean correctas y del ambiente correcto

Error: "cURL error"
→ Tu servidor no puede conectar a api.culqi.com
→ Verifica firewall/proxy

Error: "Amount is required"
→ La factura no tiene monto
→ Verifica que la factura tenga un total > 0
```

---

### Problema 4: El pago no se registra automáticamente

**Causas posibles:**
- Webhooks no configurados
- URL del webhook incorrecta
- Firewall bloqueando peticiones de Culqi

**Solución:**

1. Configura los webhooks (ver paso 11)
2. Verifica que la URL sea accesible públicamente:
   ```bash
   curl -I https://tudominio.com/modules/gateways/callback/culqiqr.php?action=check&invoice_id=1&order_id=test
   ```
3. Revisa logs del webhook en panel de Culqi

---

### Problema 5: Error 404 en callback

**Causa:** Archivo callback no existe o está mal ubicado

**Solución:**

```bash
# Verificar que existe
ls -la /path/to/whmcs/modules/gateways/callback/culqiqr.php

# Debe estar en la carpeta callback/
# NO en la raíz de gateways/
```

---

## ✅ Checklist final

Marca cada item cuando lo hayas verificado:

- [ ] Archivos instalados en las rutas correctas
- [ ] Módulo aparece en Payment Gateways
- [ ] API Keys de sandbox configuradas
- [ ] Módulo activado y guardado
- [ ] Factura de prueba creada
- [ ] Código QR se genera correctamente
- [ ] QR muestra información correcta (monto, factura)
- [ ] Verificación automática funciona (spinner)
- [ ] Pago se procesa correctamente
- [ ] Factura se marca como pagada
- [ ] Transacción aparece en WHMCS
- [ ] Logs muestran "Success"
- [ ] Webhooks configurados (opcional)
- [ ] Reembolsos funcionan (opcional)

---

## 📊 Datos de prueba

### Para Sandbox de Culqi:

Puedes usar estos datos de prueba en la app de Culqi:

- **Ambiente:** Sandbox
- **Monto mínimo:** 1.00 PEN
- **Monedas:** PEN, USD

Consulta la [documentación de Culqi](https://docs.culqi.com/es/documentacion/pagos/testing) para más datos de prueba.

---

## 🎯 Próximos pasos

Una vez que todo funcione en sandbox:

1. **Solicita credenciales de producción** a Culqi
2. **Cambia el ambiente** a "Producción"
3. **Actualiza las API Keys** con las de producción
4. **Desactiva el modo de prueba**
5. **Haz una transacción real de prueba** con poco monto
6. **Configura webhooks de producción**
7. **Monitorea las primeras transacciones**

---

## 📞 Soporte

Si encuentras problemas:

1. **Revisa los logs:** Utilities → Logs → Gateway Log
2. **Verifica la documentación:** [README.md](README.md)
3. **Revisa issues conocidos:** [GitHub Issues](https://github.com/iBlack14/plugin-qulqui-qr/issues)
4. **Contacta soporte de Culqi:** [https://culqi.com/soporte](https://culqi.com/soporte)

---

## 🎉 ¡Éxito!

Si llegaste hasta aquí y todo funciona, **¡felicidades!** 🎊

Tu módulo de Culqi QR está correctamente instalado y funcionando.

Ahora puedes:
- Pasar a producción cuando estés listo
- Personalizar la apariencia del QR
- Configurar webhooks para notificaciones instantáneas
- Monitorear transacciones desde el dashboard de WHMCS

---

**¿Todo funcionó?** ⭐ [Dale una estrella al proyecto en GitHub](https://github.com/iBlack14/plugin-qulqui-qr)
