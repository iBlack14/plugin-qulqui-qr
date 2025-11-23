# 🎯 Escenarios de Uso - Culqi QR

## Ejemplos reales de cómo usar cada paquete INDEPENDIENTEMENTE

---

## 📱 Escenario 1: E-commerce con WordPress + WooCommerce

**Necesita:** Solo WordPress Plugin

### Pasos:
```bash
1. Descargar: culqi-qr.zip
2. Subir a WordPress: Plugins → Añadir nuevo
3. Activar plugin
4. Configurar: WooCommerce → Ajustes → Pagos → Culqi QR
5. Poner API keys
```

### Código usado:
```php
// ¡NINGÚN CÓDIGO! Todo desde el admin de WordPress
// Los clientes verán "Pagar con QR" automáticamente en checkout
```

### Dependencias:
- ✅ WordPress 6.0+
- ✅ WooCommerce 7.0+
- ❌ NO necesita: npm, Node.js, React, Vue, nada de JavaScript moderno

### Resultado:
- Cliente compra producto
- Ve opción "Culqi QR" en checkout
- Se genera QR automáticamente
- Cliente escanea y paga
- Orden se completa automáticamente

**✅ 100% INDEPENDIENTE - Solo PHP + WordPress**

---

## ⚛️ Escenario 2: Landing Page con React (Sin WordPress)

**Necesita:** Solo React SDK

### Pasos:
```bash
# Crear proyecto React
npx create-react-app mi-landing
cd mi-landing

# Instalar SDK
npm install @culqi/qr-react

# ¡Listo!
```

### Código usado:
```jsx
// src/App.jsx
import { CulqiProvider, useQR, QRDisplay } from '@culqi/qr-react';

function App() {
  return (
    <CulqiProvider config={{ publicKey: 'pk_live_xxx' }}>
      <ProductPage />
    </CulqiProvider>
  );
}

function ProductPage() {
  const { generateQR, qrCode, status } = useQR();

  const handleBuy = async () => {
    await generateQR({
      amount: 99.90,
      currency: 'PEN',
      description: 'Curso de programación'
    });
  };

  return (
    <div>
      <h1>Curso de Programación - S/ 99.90</h1>
      <button onClick={handleBuy}>Comprar con QR</button>

      {qrCode && (
        <QRDisplay
          qrCode={qrCode}
          status={status}
        />
      )}
    </div>
  );
}

export default App;
```

### Dependencias:
- ✅ React 18+
- ✅ @culqi/qr-react
- ❌ NO necesita: WordPress, PHP, servidor backend

### Resultado:
- Landing page estática o SPA
- Botón de compra
- Genera QR en frontend
- Cliente paga
- Webhook notifica tu servidor (opcional)

**✅ 100% INDEPENDIENTE - Solo React**

---

## 💚 Escenario 3: Dashboard Vue.js (Sin WordPress)

**Necesita:** Solo Vue SDK

### Pasos:
```bash
# Crear proyecto Vue
npm create vue@latest
cd mi-dashboard

# Instalar SDK
npm install @culqi/qr-vue

# ¡Listo!
```

### Código usado:
```vue
<!-- src/views/CheckoutView.vue -->
<template>
  <div class="checkout">
    <h1>Pagar Suscripción</h1>

    <button @click="handlePay">
      Generar QR - S/ {{ amount }}
    </button>

    <QRDisplay
      v-if="qrCode"
      :qr-code="qrCode"
      :status="status"
    />

    <p v-if="error">Error: {{ error.message }}</p>
  </div>
</template>

<script setup>
import { useQR, QRDisplay } from '@culqi/qr-vue';

const amount = 49.90;

const { generateQR, qrCode, status, error } = useQR();

const handlePay = async () => {
  await generateQR({
    amount: amount,
    currency: 'PEN',
    description: 'Suscripción Premium'
  });
};
</script>
```

### Dependencias:
- ✅ Vue 3+
- ✅ @culqi/qr-vue
- ❌ NO necesita: WordPress, React, Angular

### Resultado:
- Dashboard Vue puro
- Sistema de pagos integrado
- QR generado en frontend
- Sin backend WordPress

**✅ 100% INDEPENDIENTE - Solo Vue**

---

## 📱 Escenario 4: App Móvil React Native (Sin WordPress)

**Necesita:** Solo React Native SDK

### Pasos:
```bash
# Crear proyecto React Native
npx react-native init MiApp
cd MiApp

# Instalar SDK
npm install @culqi/qr-react-native

# iOS
cd ios && pod install && cd ..

# ¡Listo!
```

### Código usado:
```jsx
// src/screens/CheckoutScreen.js
import React from 'react';
import { View, Button, Text } from 'react-native';
import { CulqiProvider, useQR, QRDisplay } from '@culqi/qr-react-native';

function CheckoutScreen() {
  const { generateQR, qrCode, status } = useQR();

  const handlePay = async () => {
    await generateQR({
      amount: 150.00,
      currency: 'PEN',
      description: 'Compra en app móvil'
    });
  };

  return (
    <View style={{ padding: 20 }}>
      <Text style={{ fontSize: 24 }}>Total: S/ 150.00</Text>

      <Button
        title="Pagar con Culqi QR"
        onPress={handlePay}
      />

      {qrCode && (
        <QRDisplay
          qrCode={qrCode}
          status={status}
          size={300}
        />
      )}
    </View>
  );
}

export default function App() {
  return (
    <CulqiProvider config={{ publicKey: 'pk_live_xxx' }}>
      <CheckoutScreen />
    </CulqiProvider>
  );
}
```

### Dependencias:
- ✅ React Native
- ✅ @culqi/qr-react-native
- ❌ NO necesita: WordPress, web, navegador

### Resultado:
- App móvil nativa (iOS + Android)
- Pagos con QR
- Totalmente offline-first
- Sin necesidad de web

**✅ 100% INDEPENDIENTE - Solo React Native**

---

## 🎯 Escenario 5: App Flutter (Sin JavaScript)

**Necesita:** Solo Flutter SDK

### Pasos:
```bash
# Crear proyecto Flutter
flutter create mi_app
cd mi_app

# Instalar SDK
flutter pub add culqi_qr

# ¡Listo!
```

### Código usado:
```dart
// lib/screens/checkout_screen.dart
import 'package:flutter/material.dart';
import 'package:culqi_qr/culqi_qr.dart';

class CheckoutScreen extends StatefulWidget {
  @override
  _CheckoutScreenState createState() => _CheckoutScreenState();
}

class _CheckoutScreenState extends State<CheckoutScreen> {
  final culqi = CulqiQR(publicKey: 'pk_live_xxx');
  QRData? qrCode;
  String? error;

  Future<void> _generateQR() async {
    try {
      final qr = await culqi.createQR(
        amount: 200.00,
        currency: 'PEN',
        description: 'Compra en app Flutter',
      );

      setState(() {
        qrCode = qr;
      });
    } catch (e) {
      setState(() {
        error = e.toString();
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Checkout')),
      body: Padding(
        padding: EdgeInsets.all(20),
        child: Column(
          children: [
            Text('Total: S/ 200.00',
              style: TextStyle(fontSize: 24)
            ),

            ElevatedButton(
              onPressed: _generateQR,
              child: Text('Pagar con Culqi QR'),
            ),

            if (qrCode != null)
              QRDisplayWidget(qrData: qrCode!),

            if (error != null)
              Text('Error: $error',
                style: TextStyle(color: Colors.red)
              ),
          ],
        ),
      ),
    );
  }
}
```

### Dependencias:
- ✅ Flutter 3.0+
- ✅ culqi_qr
- ❌ NO necesita: WordPress, JavaScript, Node.js, npm

### Resultado:
- App móvil nativa (iOS + Android)
- Código 100% Dart
- Sin dependencias de JavaScript
- Alto rendimiento

**✅ 100% INDEPENDIENTE - Solo Flutter/Dart**

---

## 🔧 Escenario 6: Backend Node.js (API REST)

**Necesita:** Solo Node.js SDK

### Pasos:
```bash
# Crear proyecto Node.js
mkdir mi-api && cd mi-api
npm init -y

# Instalar SDK
npm install @culqi/qr-node express

# ¡Listo!
```

### Código usado:
```javascript
// server.js
const express = require('express');
const { CulqiQR } = require('@culqi/qr-node');

const app = express();
app.use(express.json());

const culqi = new CulqiQR({
  publicKey: 'pk_live_xxx',
  secretKey: 'sk_live_xxx',
  environment: 'production'
});

// Endpoint para generar QR
app.post('/api/generate-qr', async (req, res) => {
  try {
    const { amount, currency, description } = req.body;

    const qr = await culqi.qr.create({
      amount,
      currency,
      description
    });

    res.json({
      success: true,
      qr: qr
    });
  } catch (error) {
    res.status(400).json({
      success: false,
      error: error.message
    });
  }
});

// Webhook para recibir notificaciones
app.post('/api/webhook', (req, res) => {
  const signature = req.headers['x-culqi-signature'];
  const payload = JSON.stringify(req.body);

  if (culqi.verifyWebhook(payload, signature, 'webhook_secret')) {
    console.log('Pago recibido:', req.body);
    // Actualizar base de datos, enviar email, etc.

    res.json({ received: true });
  } else {
    res.status(401).json({ error: 'Invalid signature' });
  }
});

app.listen(3000, () => {
  console.log('API corriendo en puerto 3000');
});
```

### Dependencias:
- ✅ Node.js 18+
- ✅ @culqi/qr-node
- ✅ Express (o cualquier framework)
- ❌ NO necesita: WordPress, frontend, React, Vue

### Resultado:
- API REST pura
- Backend para cualquier frontend
- Webhooks integrados
- Puede servir a React, Vue, móvil, etc.

**✅ 100% INDEPENDIENTE - Solo Node.js**

---

## 🔄 Escenario 7: Híbrido (WordPress + React Frontend Custom)

**Necesita:** WordPress Plugin + React SDK

### Estructura:
```
mi-proyecto/
├── backend/           ← WordPress con plugin
│   └── wp-content/
│       └── plugins/
│           └── culqi-qr/
│
└── frontend/          ← React app
    └── src/
        └── App.jsx
```

### Backend (WordPress):
```bash
# Instalar plugin WordPress normal
wp plugin install culqi-qr.zip --activate
```

### Frontend (React):
```bash
cd frontend
npm install @culqi/qr-react
```

### Ventajas:
- WordPress maneja: Base de datos, admin, webhooks
- React maneja: UI moderna, UX fluida
- Ambos son independientes
- Comunicación vía REST API (opcional)

**✅ AMBOS INDEPENDIENTES - Funcionan juntos o separados**

---

## 📊 Tabla de Comparación

| Escenario | Paquete | Necesita WordPress? | Necesita Node.js? | Necesita otro paquete? |
|-----------|---------|-------------------|------------------|----------------------|
| E-commerce WooCommerce | WordPress | ✅ Sí | ❌ No | ❌ No |
| Landing React | React SDK | ❌ No | ✅ Sí | ❌ No |
| Dashboard Vue | Vue SDK | ❌ No | ✅ Sí | ❌ No |
| App React Native | RN SDK | ❌ No | ✅ Sí | ❌ No |
| App Flutter | Flutter SDK | ❌ No | ❌ No | ❌ No |
| API Backend | Node SDK | ❌ No | ✅ Sí | ❌ No |
| Híbrido | WP + React | ✅ Sí | ✅ Sí | ❌ No* |

*Ambos funcionan independientes, la comunicación es opcional

---

## 💡 Conclusión

**Cada paquete es un producto completo e independiente:**

1. **WordPress** = Plugin PHP tradicional
2. **React** = Librería npm para React
3. **Vue** = Librería npm para Vue
4. **Angular** = Librería npm para Angular
5. **React Native** = Librería npm para móvil
6. **Flutter** = Package pub.dev para móvil
7. **Node.js** = SDK para backend

**NO se necesitan entre sí.**

Elige el que necesites, instálalo, úsalo. Así de simple. 🚀
