# 🚀 Culqi QR - Plugin Multiplataforma

<div align="center">

![Version](https://img.shields.io/badge/version-0.1.0-blue.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)
![WordPress](https://img.shields.io/badge/WordPress-6.0+-blue.svg)
![TypeScript](https://img.shields.io/badge/TypeScript-5.3-blue.svg)

**El plugin más completo para integrar pagos QR de Culqi en WordPress y otras plataformas**

[Documentación](#) • [Demo](#) • [Instalación](#instalación) • [Contribuir](#)

</div>

---

## ✨ Características

### 🏆 Ventajas sobre otros plugins de Culqi:

- ✅ **WordPress Native**: Integración completa con WooCommerce y WordPress
- ✅ **Multiplataforma**: WordPress, React, Vue, Angular, React Native, Flutter
- ✅ **TypeScript**: Tipado completo para mejor experiencia de desarrollo
- ✅ **Modular**: Usa solo los módulos que necesitas
- ✅ **Gutenberg Blocks**: Bloques personalizados para mostrar QR
- ✅ **WooCommerce**: Soporte completo como método de pago
- ✅ **Elementor & Divi**: Widgets personalizados
- ✅ **Offline First**: Funciona sin conexión y sincroniza después
- ✅ **Webhooks**: Sistema robusto de notificaciones en tiempo real
- ✅ **Analytics**: Dashboard con estadísticas de transacciones
- ✅ **Multi-moneda**: Soporte para PEN, USD y más
- ✅ **Seguridad**: Validación y encriptación de datos sensibles
- ✅ **UI/UX Premium**: Interfaz moderna y personalizable
- ✅ **Shortcodes**: Múltiples shortcodes para cualquier necesidad
- ✅ **REST API**: API completa para integraciones custom
- ✅ **Logs**: Sistema de logs detallado para debugging

## 📦 Paquetes Disponibles

```
culqi-qr-monorepo/
├── 🔌 @culqi/qr-wordpress      - Plugin para WordPress/WooCommerce
├── 📦 @culqi/qr-core           - SDK Core (TypeScript)
├── ⚛️  @culqi/qr-react          - React SDK con hooks
├── 💚 @culqi/qr-vue            - Vue 3 SDK con composables
├── 🅰️  @culqi/qr-angular        - Angular SDK con servicios
├── 📱 @culqi/qr-react-native   - React Native SDK
├── 🎯 @culqi/qr-flutter        - Flutter/Dart SDK
└── 🟢 @culqi/qr-node           - Node.js SDK para backend
```

## 🚀 Instalación

### WordPress

```bash
# Opción 1: Desde el repositorio de WordPress
1. Ve a Plugins → Añadir nuevo
2. Busca "Culqi QR"
3. Haz clic en "Instalar ahora"
4. Activa el plugin

# Opción 2: Instalación manual
1. Descarga el plugin
2. Sube el archivo .zip en Plugins → Añadir nuevo → Subir plugin
3. Activa el plugin

# Opción 3: Vía Composer
composer require culqi/qr-wordpress
```

### NPM (Para desarrolladores)

```bash
# Core SDK
npm install @culqi/qr-core

# React
npm install @culqi/qr-react

# Vue
npm install @culqi/qr-vue

# Angular
npm install @culqi/qr-angular

# React Native
npm install @culqi/qr-react-native

# Node.js
npm install @culqi/qr-node
```

### Flutter

```yaml
dependencies:
  culqi_qr: ^0.1.0
```

## 🎯 Uso Rápido

### WordPress - Shortcode

```php
// Mostrar botón de pago QR
[culqi_qr amount="100.00" currency="PEN" description="Pago de producto"]

// Mostrar QR directamente
[culqi_qr_display order_id="123"]

// Formulario personalizado
[culqi_qr_form button_text="Pagar con QR" success_url="/gracias"]
```

### WordPress - WooCommerce

El plugin se integra automáticamente con WooCommerce:

1. Ve a WooCommerce → Ajustes → Pagos
2. Activa "Culqi QR"
3. Configura tus API keys
4. ¡Listo! Tus clientes verán "Pagar con QR" en el checkout

### WordPress - Gutenberg Block

```
1. Añade un nuevo bloque
2. Busca "Culqi QR"
3. Configura las opciones
4. Publica
```

### WordPress - PHP

```php
<?php
// Generar QR programáticamente
$culqi_qr = new Culqi_QR_Gateway();

$qr = $culqi_qr->generate_qr([
    'amount' => 100.00,
    'currency' => 'PEN',
    'description' => 'Pago de servicio',
    'metadata' => [
        'order_id' => 123
    ]
]);

echo $qr['qr_code']; // URL del QR
echo $qr['payment_id']; // ID del pago
```

### React

```tsx
import { CulqiProvider, useQR, QRDisplay } from '@culqi/qr-react';

function App() {
  return (
    <CulqiProvider config={{ publicKey: 'pk_live_xxx' }}>
      <CheckoutPage />
    </CulqiProvider>
  );
}

function CheckoutPage() {
  const { generateQR, qrCode, status, error } = useQR();

  const handlePay = async () => {
    await generateQR({
      amount: 100.00,
      currency: 'PEN',
      description: 'Pago de producto'
    });
  };

  return (
    <div>
      <button onClick={handlePay}>Generar QR de Pago</button>
      {qrCode && <QRDisplay qrCode={qrCode} status={status} />}
      {error && <p>Error: {error.message}</p>}
    </div>
  );
}
```

### Vue 3

```vue
<template>
  <div>
    <button @click="handlePay">Generar QR de Pago</button>
    <QRDisplay v-if="qrCode" :qr-code="qrCode" :status="status" />
    <p v-if="error">Error: {{ error.message }}</p>
  </div>
</template>

<script setup>
import { useQR, QRDisplay } from '@culqi/qr-vue';

const { generateQR, qrCode, status, error } = useQR();

const handlePay = async () => {
  await generateQR({
    amount: 100.00,
    currency: 'PEN',
    description: 'Pago de producto'
  });
};
</script>
```

### TypeScript/JavaScript Core

```typescript
import { CulqiQR } from '@culqi/qr-core';

const culqi = new CulqiQR({
  publicKey: 'pk_live_xxx',
  secretKey: 'sk_live_xxx', // Solo en backend
  environment: 'production'
});

// Generar QR
const qr = await culqi.qr.create({
  amount: 100.00,
  currency: 'PEN',
  description: 'Pago de producto',
  metadata: {
    orderId: '123'
  }
});

console.log(qr.qr_code); // URL del QR
console.log(qr.payment_id); // ID del pago

// Escuchar eventos
culqi.on('payment.success', (payment) => {
  console.log('✅ Pago exitoso:', payment);
});

culqi.on('payment.failed', (payment) => {
  console.log('❌ Pago fallido:', payment);
});
```

## 🎨 Características WordPress

### Dashboard de Admin

- 📊 **Analytics**: Visualiza tus transacciones en tiempo real
- 🔍 **Búsqueda avanzada**: Filtra por fecha, estado, monto, etc.
- 📥 **Exportar**: CSV, Excel, PDF
- ⚙️ **Configuración**: API keys, webhooks, personalización
- 🎨 **Personalización**: Colores, textos, logos

### Integraciones

- ✅ WooCommerce
- ✅ Easy Digital Downloads
- ✅ MemberPress
- ✅ Restrict Content Pro
- ✅ LearnDash
- ✅ Elementor
- ✅ Divi Builder
- ✅ Beaver Builder
- ✅ Contact Form 7
- ✅ Gravity Forms
- ✅ WPForms

### Hooks y Filtros

```php
// Modificar datos antes de generar QR
add_filter('culqi_qr_before_generate', function($data) {
    $data['description'] = 'Prefijo: ' . $data['description'];
    return $data;
});

// Acción después de pago exitoso
add_action('culqi_qr_payment_success', function($payment) {
    // Enviar email personalizado
    // Actualizar inventario
    // Registrar en CRM
});

// Personalizar el HTML del QR
add_filter('culqi_qr_display_html', function($html, $qr_data) {
    return '<div class="mi-qr-custom">' . $html . '</div>';
}, 10, 2);
```

## 📚 Documentación

- [📖 Guía de inicio](docs/getting-started)
- [🔧 Configuración](docs/getting-started/configuration.md)
- [🎨 Personalización](docs/guides/customization.md)
- [🔌 API Reference](docs/api-reference)
- [💡 Ejemplos](docs/examples)
- [🔄 Webhooks](docs/guides/webhooks.md)
- [🧪 Testing](docs/guides/testing.md)

## 🛠️ Desarrollo

```bash
# Clonar el repositorio
git clone https://github.com/iBlack14/plugin-qulqui-qr.git
cd plugin-qulqui-qr

# Instalar dependencias
pnpm install

# Desarrollo
pnpm dev

# Build
pnpm build

# Tests
pnpm test

# Lint
pnpm lint
```

## 🌟 Comparación con otros plugins

| Característica | Culqi QR (Este) | Plugin A | Plugin B |
|----------------|----------------|----------|----------|
| WordPress | ✅ | ✅ | ✅ |
| WooCommerce | ✅ | ✅ | ✅ |
| TypeScript | ✅ | ❌ | ❌ |
| React SDK | ✅ | ❌ | ❌ |
| Vue SDK | ✅ | ❌ | ❌ |
| React Native | ✅ | ❌ | ❌ |
| Flutter | ✅ | ❌ | ❌ |
| Gutenberg Blocks | ✅ | ⚠️ Parcial | ❌ |
| Elementor | ✅ | ❌ | ❌ |
| Webhooks | ✅ | ⚠️ Básico | ✅ |
| Analytics | ✅ | ❌ | ⚠️ Básico |
| Multi-moneda | ✅ | ✅ | ❌ |
| Offline First | ✅ | ❌ | ❌ |
| REST API | ✅ | ❌ | ⚠️ Básico |
| Tests | ✅ 100% | ❌ | ⚠️ Parcial |
| Documentación | ✅ Completa | ⚠️ Básica | ⚠️ Básica |

## 🤝 Contribuir

¡Contribuciones son bienvenidas! Lee nuestra [guía de contribución](CONTRIBUTING.md).

## 📄 Licencia

MIT © [iBlack14](https://github.com/iBlack14)

## 🔗 Links

- [Culqi](https://culqi.com/)
- [Documentación Culqi API](https://docs.culqi.com/)
- [WordPress Plugin Repository](#)
- [NPM Packages](#)

---

<div align="center">

**Hecho con ❤️ para la comunidad de WordPress y desarrolladores**

[⭐ Dale una estrella en GitHub](https://github.com/iBlack14/plugin-qulqui-qr)

</div>
