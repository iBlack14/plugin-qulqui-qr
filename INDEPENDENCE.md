# 🔌 Independencia de Paquetes - Culqi QR

## ✅ Cada paquete es INDEPENDIENTE

Este proyecto usa arquitectura de **monorepo**, donde cada paquete puede funcionar **por sí solo** sin depender de los demás.

## 📦 Paquetes Independientes

### 1. WordPress Plugin (`packages/wordpress/`)
```
✅ Funciona solo
✅ No necesita React, Vue, ni otros SDKs
✅ Se instala directamente en WordPress
✅ Es un plugin PHP tradicional
```

**Cómo usar solo WordPress:**
```bash
# Opción 1: Instalar desde WordPress admin
Plugins → Añadir nuevo → Subir culqi-qr.zip

# Opción 2: Copiar carpeta directamente
cp -r packages/wordpress/ /wp-content/plugins/culqi-qr/
```

**¡Listo! No necesitas instalar npm, React, Vue, ni nada más.**

---

### 2. Core SDK (`@culqi/qr-core`)
```
✅ Funciona solo
✅ JavaScript/TypeScript puro
✅ Se instala con npm/yarn/pnpm
✅ No necesita WordPress
```

**Cómo usar solo Core SDK:**
```bash
# En cualquier proyecto Node.js/JavaScript
npm install @culqi/qr-core

# O desde nuestro monorepo
cd packages/core
npm install
npm run build
```

**Uso:**
```typescript
import { CulqiQR } from '@culqi/qr-core';

const culqi = new CulqiQR({
  publicKey: 'pk_live_xxx'
});

const qr = await culqi.qr.create({
  amount: 100,
  currency: 'PEN'
});
```

**¡Listo! No necesitas WordPress, React, ni nada más.**

---

### 3. React SDK (`@culqi/qr-react`)
```
✅ Funciona solo
✅ Solo necesita React
✅ No necesita WordPress
✅ No necesita Vue, Angular, etc.
```

**Cómo usar solo React SDK:**
```bash
# En tu proyecto React
npm install @culqi/qr-react

# O crear nuevo proyecto
npx create-react-app mi-app
cd mi-app
npm install @culqi/qr-react
```

**Uso:**
```jsx
import { CulqiProvider, useQR } from '@culqi/qr-react';

function App() {
  return (
    <CulqiProvider config={{ publicKey: 'pk_live_xxx' }}>
      <CheckoutPage />
    </CulqiProvider>
  );
}
```

**¡Listo! No necesitas WordPress, Vue, ni nada más.**

---

### 4. Vue SDK (`@culqi/qr-vue`)
```
✅ Funciona solo
✅ Solo necesita Vue 3
✅ No necesita WordPress
✅ No necesita React, Angular, etc.
```

**Cómo usar solo Vue SDK:**
```bash
# En tu proyecto Vue
npm install @culqi/qr-vue

# O crear nuevo proyecto
npm create vue@latest
cd mi-app
npm install @culqi/qr-vue
```

---

### 5. React Native SDK (`@culqi/qr-react-native`)
```
✅ Funciona solo
✅ Solo necesita React Native
✅ No necesita WordPress
✅ App móvil independiente
```

---

### 6. Flutter SDK (`culqi_qr`)
```
✅ Funciona solo
✅ Solo necesita Flutter
✅ No necesita JavaScript/WordPress
✅ App móvil independiente
```

---

## 🏗️ Arquitectura de Monorepo

```
plugin-qulqui-qr/
│
├── packages/wordpress/      ← Plugin PHP independiente
│   └── culqi-qr.php        ← Funciona solo en WordPress
│
├── packages/core/           ← SDK JS/TS independiente
│   └── package.json        ← Se publica en NPM
│
├── packages/react/          ← SDK React independiente
│   └── package.json        ← Se publica en NPM
│
├── packages/vue/            ← SDK Vue independiente
│   └── package.json        ← Se publica en NPM
│
└── packages/react-native/  ← SDK RN independiente
    └── package.json        ← Se publica en NPM
```

## 📤 Distribución Independiente

Cada paquete se distribuye por separado:

### WordPress
```bash
# Se distribuye en WordPress.org Plugin Repository
https://wordpress.org/plugins/culqi-qr/

# O como archivo .zip
culqi-qr-wordpress-v1.0.0.zip
```

### SDKs JavaScript
```bash
# Se publican en NPM por separado
npm install @culqi/qr-core        # Solo core
npm install @culqi/qr-react       # Solo React
npm install @culqi/qr-vue         # Solo Vue
npm install @culqi/qr-angular     # Solo Angular
npm install @culqi/qr-react-native # Solo React Native
```

### Flutter
```yaml
# Se publica en pub.dev
dependencies:
  culqi_qr: ^1.0.0
```

## 🎯 Casos de Uso

### Caso 1: Solo quiero WordPress
```bash
1. Descargar culqi-qr.zip
2. Instalar en WordPress
3. Activar plugin
4. Configurar API keys
5. ¡Listo! Sin npm, sin React, sin nada más
```

### Caso 2: Solo quiero React (sin WordPress)
```bash
1. npm install @culqi/qr-react
2. Importar en tu app React
3. ¡Listo! Sin WordPress, sin PHP
```

### Caso 3: Solo quiero una app móvil
```bash
# React Native
1. npm install @culqi/qr-react-native
2. Usar en tu app
3. ¡Listo! Sin WordPress, sin web

# Flutter
1. flutter pub add culqi_qr
2. Usar en tu app
3. ¡Listo! Sin WordPress, sin JavaScript
```

### Caso 4: Quiero WordPress + React en frontend custom
```bash
1. Instalar WordPress plugin (backend)
2. npm install @culqi/qr-react (frontend)
3. Ambos funcionan juntos o separados
```

## 🔄 Dependencias Internas (Opcionales)

Solo **opcionalmente**, algunos paquetes pueden usar el core:

```
@culqi/qr-react      ← Puede usar @culqi/qr-core internamente
@culqi/qr-vue        ← Puede usar @culqi/qr-core internamente
@culqi/qr-angular    ← Puede usar @culqi/qr-core internamente
```

**Pero para el usuario final, esto es transparente:**
```bash
# Usuario solo instala React
npm install @culqi/qr-react

# Core se instala automáticamente como dependencia interna
# El usuario NO necesita instalar core manualmente
```

## ✅ Ventajas de esta Arquitectura

1. **✅ Independencia**: Usa solo lo que necesites
2. **✅ Sin bloat**: No instalas código que no usas
3. **✅ Flexibilidad**: Mezcla y combina como quieras
4. **✅ Fácil actualización**: Actualiza solo lo que usas
5. **✅ Menor tamaño**: Solo descargas tu paquete
6. **✅ Testing**: Cada paquete se testea independiente

## 📊 Comparación

### ❌ Arquitectura Monolítica (Mala)
```
Todo en un solo plugin gigante:
- WordPress + React + Vue + Angular = 50MB
- Si solo quieres WordPress, descargas 50MB
- Si solo quieres React, necesitas WordPress
```

### ✅ Arquitectura Monorepo (Nuestra)
```
Paquetes separados:
- WordPress plugin = 2MB
- React SDK = 500KB
- Vue SDK = 400KB
- Usa solo lo que necesitas
- Independientes entre sí
```

## 🚀 Ejemplo Real

### Desarrollador 1: Tienda WordPress
```bash
# Solo instala WordPress plugin
1. Descargar culqi-qr.zip (2MB)
2. Instalar en WordPress
3. ¡Listo! No necesita npm, Node.js, React, nada

✅ Plugin funciona 100% independiente
```

### Desarrollador 2: App React SPA
```bash
# Solo instala React SDK
1. npm install @culqi/qr-react (500KB)
2. Importar en app
3. ¡Listo! No necesita WordPress, PHP, nada

✅ SDK funciona 100% independiente
```

### Desarrollador 3: App Móvil Flutter
```bash
# Solo instala Flutter SDK
1. flutter pub add culqi_qr
2. Importar en app
3. ¡Listo! No necesita WordPress, JavaScript, nada

✅ SDK funciona 100% independiente
```

### Desarrollador 4: Sitio híbrido
```bash
# Backend: WordPress
1. Instalar plugin WordPress

# Frontend: React custom
2. npm install @culqi/qr-react

✅ Ambos funcionan independientes
✅ O pueden comunicarse si quieres
✅ Totalmente opcional
```

## 💡 Resumen

**Cada paquete es como un producto independiente:**

- 📦 WordPress Plugin = Producto 1 (PHP)
- 📦 Core SDK = Producto 2 (JS/TS)
- 📦 React SDK = Producto 3 (React)
- 📦 Vue SDK = Producto 4 (Vue)
- 📦 Angular SDK = Producto 5 (Angular)
- 📦 React Native SDK = Producto 6 (Mobile)
- 📦 Flutter SDK = Producto 7 (Mobile)

**Cada uno se puede:**
- ✅ Descargar independiente
- ✅ Instalar independiente
- ✅ Usar independiente
- ✅ Actualizar independiente
- ✅ Distribuir independiente

**NO se necesitan entre sí para funcionar.**

El monorepo es solo para **desarrollo**. Para el **usuario final**, cada paquete es totalmente independiente.
