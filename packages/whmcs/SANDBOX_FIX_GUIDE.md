# 🔧 Guía de Solución para Sandbox Bloqueado

## 🎯 Problema identificado

Tu servidor puede conectarse a Culqi en **producción** pero bloquea el dominio **sandbox**:

- ✅ `api.culqi.com` (Producción) → **Funciona**
- ❌ `api-sandbox.culqi.com` (Sandbox) → **Bloqueado**

## ✅ Soluciones disponibles

---

### **Solución 1: Usar el módulo con workaround de IP (RECOMENDADA)**

He creado una versión modificada del módulo que incluye un workaround para este problema.

#### Paso 1: Obtener la IP actual del sandbox de Culqi

Desde tu computadora (NO el servidor), ejecuta uno de estos comandos:

**Opción A - Windows (PowerShell):**
```powershell
nslookup api-sandbox.culqi.com
```

**Opción B - Linux/Mac (Terminal):**
```bash
dig api-sandbox.culqi.com +short
```

**Opción C - Online:**
Ve a: https://www.nslookup.io/domains/api-sandbox.culqi.com/dns-records/

Copia la IP que te muestre (ejemplo: `52.222.136.9`)

#### Paso 2: Reemplazar el archivo del módulo

1. **Descarga** el archivo `culqiqr-sandbox-fix.php` de este repositorio

2. **Renómbralo** a `culqiqr.php`

3. **Reemplaza** el archivo existente en tu servidor:
   ```
   /modules/gateways/culqiqr.php
   ```

4. **Sube el archivo** vía cPanel File Manager o FTP

#### Paso 3: Configurar la IP en WHMCS

1. Ve a **Setup → Payments → Payment Gateways → Culqi QR**

2. Verás un nuevo campo: **"Sandbox IP (Opcional)"**

3. Ingresa la IP que obtuviste en el Paso 1, por ejemplo:
   ```
   52.222.136.9
   ```

4. **Guarda la configuración**

5. **¡Listo!** Ahora el módulo debería funcionar en sandbox

#### Cómo funciona el workaround:

El módulo modificado usa `CURLOPT_RESOLVE` para decirle a cURL:
```
"Cuando veas api-sandbox.culqi.com, usa esta IP directamente"
```

Esto evita la resolución DNS que está bloqueada.

---

### **Solución 2: Contactar a MegaWeb (Definitiva)**

Envía este ticket a soporte de MegaWeb:

```
Asunto: Desbloquear dominio api-sandbox.culqi.com

Hola equipo,

He realizado pruebas de conectividad y encontré que mi servidor
bloquea específicamente el dominio: api-sandbox.culqi.com

Diagnóstico:
- api.culqi.com ✅ Funciona (52.222.136.9)
- api-sandbox.culqi.com ❌ Error: Could not resolve host
- google.com ✅ Funciona
- cloudflare.com ✅ Funciona

Servidor: clientes.megaweb.pe
PHP: 8.1.33
Server: LiteSpeed

Necesito que habiliten conexiones salientes a:
- api-sandbox.culqi.com (Puerto 443, HTTPS)

Es el ambiente de pruebas de Culqi (pasarela de pagos oficial en Perú).

Diagnóstico completo disponible si lo requieren.

Gracias.
```

**Tiempo estimado de respuesta:** 24-48 horas

---

### **Solución 3: Usar producción directamente**

Si tienes cuenta verificada en Culqi con keys de producción:

1. Obtén tus keys de producción:
   - `pk_live_xxxxx`
   - `sk_live_xxxxx`

2. En WHMCS configura:
   - **Ambiente:** Production
   - **Public Key:** pk_live_xxxxx
   - **Secret Key:** sk_live_xxxxx

3. **Ventaja:** Funciona inmediatamente
4. **Desventaja:** Las pruebas generan transacciones reales

**Recomendación:** Haz pruebas con **1.00 PEN** (monto mínimo) y luego reembolsa.

---

### **Solución 4: Crear proxy PHP (Avanzada)**

Si las anteriores no funcionan, podemos crear un script proxy:

```
Tu módulo → proxy.php (en tu servidor) → api-sandbox.culqi.com
```

El proxy hace la petición desde otro servidor/servicio que no esté bloqueado.

**Complejidad:** Alta
**Requiere:** Servidor adicional o servicio externo

---

## 🧪 Verificar que funciona

Después de aplicar la Solución 1:

1. Crea una factura de prueba (10.00 PEN)
2. Intenta pagar seleccionando "Culqi QR"
3. Deberías ver el código QR generado

Si no funciona:
1. Ve a **Utilities → Logs → Gateway Log**
2. Busca entradas de "culqiqr"
3. Revisa el error

---

## 📊 Comparación de soluciones

| Solución | Dificultad | Tiempo | Permanente | Recomendado |
|----------|-----------|---------|------------|-------------|
| 1. Workaround con IP | ⭐ Fácil | 5 min | Temporal* | ✅ SÍ |
| 2. Contactar MegaWeb | ⭐ Fácil | 24-48h | ✅ Sí | ✅ SÍ |
| 3. Usar producción | ⭐ Fácil | 2 min | ✅ Sí | ⚠️ Solo si tienes keys |
| 4. Crear proxy | ⭐⭐⭐ Difícil | 1-2h | ✅ Sí | ❌ Última opción |

\* La IP puede cambiar, pero es poco frecuente (meses/años)

---

## ❓ FAQ

### ¿Por qué MegaWeb bloquea el sandbox?

Algunos hostings bloquean dominios con "sandbox", "test" o "dev" por políticas de seguridad para prevenir actividades maliciosas.

### ¿La IP del sandbox cambia?

Rara vez, pero puede pasar. Si algún día deja de funcionar, solo necesitas actualizar la IP en la configuración.

### ¿Es seguro usar el workaround?

Sí, es completamente seguro. Solo estamos diciéndole a cURL qué IP usar, la conexión sigue siendo HTTPS cifrada.

### ¿Puedo usar ambas soluciones (1 y 2)?

Sí, puedes usar el workaround mientras MegaWeb responde tu ticket. Una vez desbloqueado, simplemente deja el campo de IP vacío.

---

## 🆘 Soporte

Si ninguna solución funciona:

1. Ejecuta el diagnóstico completo: `test-diagnostico-culqi.php`
2. Toma captura de pantalla
3. Abre un issue en: https://github.com/iBlack14/plugin-qulqui-qr/issues
4. Adjunta el diagnóstico

---

## 📝 Notas técnicas

El workaround usa `CURLOPT_RESOLVE`:
```php
curl_setopt($ch, CURLOPT_RESOLVE, array(
    "api-sandbox.culqi.com:443:52.222.136.9"
));
```

Esto es equivalente a agregar una entrada en `/etc/hosts` pero solo para esta petición cURL específica.

---

**¿Funcionó alguna solución?** Comparte tu experiencia en un issue para ayudar a otros usuarios.
