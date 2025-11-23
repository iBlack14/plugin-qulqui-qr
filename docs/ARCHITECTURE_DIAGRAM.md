# 🏗️ Diagrama de Arquitectura - Independencia de Paquetes

## 📊 Vista General: Paquetes Independientes

```
                    ┌─────────────────────────────┐
                    │     API Culqi (Cloud)       │
                    │   api.culqi.com/v2/         │
                    └──────────────┬──────────────┘
                                   │
                    ┌──────────────┴──────────────┐
                    │  Todos se conectan aquí     │
                    │  independientemente         │
                    └──────────────┬──────────────┘
                                   │
        ┌──────────────────────────┼──────────────────────────┐
        │                          │                          │
        ▼                          ▼                          ▼
┌───────────────┐          ┌───────────────┐        ┌───────────────┐
│   WordPress   │          │   React SDK   │        │   Vue SDK     │
│   Plugin      │          │   @culqi/qr   │        │   @culqi/qr   │
│               │          │   -react      │        │   -vue        │
│  PHP + MySQL  │          │  JS + Hooks   │        │ JS + Compos.  │
└───────────────┘          └───────────────┘        └───────────────┘
        │                          │                          │
        ▼                          ▼                          ▼
  WooCommerce             React App (SPA)              Vue App (SPA)
  WordPress Site          Next.js, CRA, Vite           Nuxt, Vite


        ┌──────────────────────────┼──────────────────────────┐
        │                          │                          │
        ▼                          ▼                          ▼
┌───────────────┐          ┌───────────────┐        ┌───────────────┐
│ React Native  │          │   Flutter     │        │   Node.js     │
│   SDK         │          │   SDK         │        │   SDK         │
│  @culqi/qr-rn │          │  culqi_qr     │        │  @culqi/qr-   │
│  iOS/Android  │          │  Dart/Native  │        │   node        │
└───────────────┘          └───────────────┘        └───────────────┘
        │                          │                          │
        ▼                          ▼                          ▼
  Mobile App (RN)          Mobile App (FL)          API REST/Backend
  iOS + Android            iOS + Android            Express, Fastify
```

## ✅ Punto Clave: TODOS SON INDEPENDIENTES

```
WordPress Plugin    ─┐
                     │
React SDK           ─┤
                     │
Vue SDK             ─┤   TODOS se conectan
                     ├── directamente a
Angular SDK         ─┤   API Culqi
                     │
React Native SDK    ─┤   NO dependen
                     │   entre sí
Flutter SDK         ─┤
                     │
Node.js SDK         ─┘
```

## 🔄 Flujo de Uso - WordPress (Independiente)

```
Usuario final
    │
    │ Instala plugin .zip
    ▼
WordPress Admin
    │
    │ Activa plugin
    ▼
WooCommerce
    │
    │ Configura método de pago
    ▼
Cliente compra
    │
    │ Genera QR
    ▼
API Culqi
    │
    │ Retorna QR
    ▼
Cliente ve QR
    │
    │ Escanea y paga
    ▼
Webhook → WordPress
    │
    ▼
Orden completada

❌ NO necesita: npm, React, Vue, Node.js, nada
✅ Solo necesita: PHP + WordPress + WooCommerce
```

## 🔄 Flujo de Uso - React (Independiente)

```
Desarrollador
    │
    │ npm install @culqi/qr-react
    ▼
React App
    │
    │ Importa componentes
    ▼
<CulqiProvider>
    │
    │ Usuario hace click
    ▼
generateQR()
    │
    │ Llama API
    ▼
API Culqi
    │
    │ Retorna QR
    ▼
<QRDisplay>
    │
    │ Muestra QR
    ▼
Cliente escanea
    │
    ▼
Webhook → Tu backend

❌ NO necesita: WordPress, PHP, servidor
✅ Solo necesita: React + npm
```

## 🔄 Flujo de Uso - Flutter (Independiente)

```
Desarrollador
    │
    │ flutter pub add culqi_qr
    ▼
Flutter App
    │
    │ import 'package:culqi_qr/culqi_qr.dart'
    ▼
CulqiQR(publicKey: 'xxx')
    │
    │ Usuario toca botón
    ▼
culqi.createQR()
    │
    │ Llama API
    ▼
API Culqi
    │
    │ Retorna QR
    ▼
QRDisplayWidget()
    │
    │ Muestra QR
    ▼
Cliente escanea
    │
    ▼
Webhook → Tu backend

❌ NO necesita: WordPress, JavaScript, npm, Node.js
✅ Solo necesita: Flutter + Dart
```

## 🎯 Arquitectura de Distribución

```
┌─────────────────────────────────────────────────────┐
│               MONOREPO (Solo desarrollo)            │
│  plugin-qulqui-qr/ (GitHub)                        │
│                                                     │
│  ├── packages/wordpress/    ───────────────┐       │
│  ├── packages/core/         ──────────┐    │       │
│  ├── packages/react/        ─────┐    │    │       │
│  ├── packages/vue/          ──┐  │    │    │       │
│  ├── packages/angular/      ─┐│  │    │    │       │
│  ├── packages/react-native/ ──┼┼──┼────┼────┼───┐  │
│  └── packages/flutter/      ──┼┼──┼────┼────┼───┼┐ │
└────────────────────────────────┼┼──┼────┼────┼───┼┼─┘
                                 ││  │    │    │   ││
                    ┌────────────┘│  │    │    │   ││
                    │  ┌───────────┘  │    │    │   ││
                    │  │  ┌───────────┘    │    │   ││
                    │  │  │  ┌─────────────┘    │   ││
                    │  │  │  │   ┌──────────────┘   ││
                    │  │  │  │   │    ┌─────────────┘│
                    │  │  │  │   │    │    ┌─────────┘
                    ▼  ▼  ▼  ▼   ▼    ▼    ▼
┌──────────────────────────────────────────────────────┐
│         DISTRIBUCIÓN (Usuario final)                 │
├──────────────────────────────────────────────────────┤
│                                                      │
│  WordPress.org   NPM Registry    NPM    pub.dev    │
│       │              │    │        │        │       │
│       ▼              ▼    ▼        ▼        ▼       │
│  culqi-qr.zip  @culqi/   @culqi/ culqi_qr  │       │
│                qr-core   qr-react           │       │
│                @culqi/   @culqi/            │       │
│                qr-vue    qr-angular         │       │
│                         @culqi/             │       │
│                         qr-react-native     │       │
└──────────────────────────────────────────────────────┘
         │              │            │          │
         │              │            │          │
    Instalación    Instalación  Instalación  Instalación
    independiente  independiente independ.  independiente
         │              │            │          │
         ▼              ▼            ▼          ▼
    WordPress       React App    RN App    Flutter App
```

## 🔑 Principios de Independencia

### 1. Instalación Independiente
```
WordPress:        wp plugin install culqi-qr
React:           npm install @culqi/qr-react
Vue:             npm install @culqi/qr-vue
React Native:    npm install @culqi/qr-react-native
Flutter:         flutter pub add culqi_qr
Node.js:         npm install @culqi/qr-node
```

### 2. Ejecución Independiente
```
WordPress:        Funciona en servidor PHP
React:           Funciona en navegador
Vue:             Funciona en navegador
React Native:    Funciona en móvil nativo
Flutter:         Funciona en móvil nativo
Node.js:         Funciona en servidor Node
```

### 3. Sin Dependencias Cruzadas
```
WordPress        ≠ NO necesita → React, Vue, npm
React            ≠ NO necesita → WordPress, PHP
Vue              ≠ NO necesita → React, WordPress
React Native     ≠ NO necesita → WordPress, Vue
Flutter          ≠ NO necesita → JavaScript, npm
Node.js          ≠ NO necesita → WordPress, frontend
```

## 🌟 Analogía Simple

**Es como una pizzería:**

```
🍕 Pizza Margherita    = WordPress Plugin
🍕 Pizza Pepperoni     = React SDK
🍕 Pizza Hawaiana      = Vue SDK
🍕 Pizza Vegetariana   = Angular SDK
🍕 Pizza BBQ           = React Native SDK
🍕 Pizza 4 Quesos      = Flutter SDK
```

**Cada pizza (paquete) es independiente:**
- ✅ Se puede pedir sola
- ✅ No necesitas comprar las otras
- ✅ Cada una es completa por sí misma
- ✅ Todas usan los mismos ingredientes base (API Culqi)
- ✅ Pero son productos separados

**El monorepo es la cocina:**
- Es donde se preparan todas
- Pero el cliente no ve la cocina
- El cliente solo recibe su pizza
- Cada pizza se empaqueta y entrega por separado

## 📦 Resumen Visual

```
                    API Culqi (El Chef)
                           │
                           │ Todos usan el mismo chef
                           │
        ┌──────────────────┼──────────────────┐
        │                  │                  │
        ▼                  ▼                  ▼
   WordPress            React               Flutter
   (Pizza 1)         (Pizza 2)           (Pizza 3)
        │                  │                  │
        ▼                  ▼                  ▼
  Se come sola      Se come sola        Se come sola
  No necesita      No necesita         No necesita
  las otras        las otras           las otras

✅ Independientes
✅ Completas
✅ Autosuficientes
```

---

## 💡 Conclusión

**Monorepo ≠ Monolito**

- **Monorepo** = Múltiples paquetes en un repo (para desarrollo)
- **Cada paquete** = Independiente y autosuficiente (para usuarios)

**Para el usuario final:**
- Instala solo lo que necesita
- No descarga código innecesario
- Cada paquete funciona solo
- Puede combinarlos si quiere (opcional)

**¡Es lo mejor de ambos mundos!** 🚀
