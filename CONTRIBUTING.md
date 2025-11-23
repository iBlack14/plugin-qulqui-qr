# Contribuyendo a Culqi QR

¡Gracias por tu interés en contribuir! 🎉

## 🚀 Empezando

### Prerequisitos

- Node.js 18+
- pnpm 8+
- Git

### Configuración del entorno

```bash
# Clonar el repositorio
git clone https://github.com/iBlack14/plugin-qulqui-qr.git
cd plugin-qulqui-qr

# Instalar dependencias
pnpm install

# Ejecutar en modo desarrollo
pnpm dev
```

## 📝 Proceso de Contribución

### 1. Fork y Clone

1. Haz fork del repositorio
2. Clona tu fork localmente
3. Agrega el upstream remote

```bash
git remote add upstream https://github.com/iBlack14/plugin-qulqui-qr.git
```

### 2. Crear una rama

```bash
git checkout -b feature/mi-nueva-caracteristica
# o
git checkout -b fix/mi-bug-fix
```

Convención de nombres de ramas:
- `feature/` - Nuevas características
- `fix/` - Correcciones de bugs
- `docs/` - Cambios en documentación
- `refactor/` - Refactorización de código
- `test/` - Agregar o actualizar tests
- `chore/` - Tareas de mantenimiento

### 3. Hacer cambios

- Escribe código limpio y bien documentado
- Sigue las convenciones de estilo del proyecto
- Agrega tests para nuevas funcionalidades
- Actualiza la documentación si es necesario

### 4. Commits

Usamos [Conventional Commits](https://www.conventionalcommits.org/):

```bash
git commit -m "feat: agregar soporte para múltiples monedas"
git commit -m "fix: corregir error en generación de QR"
git commit -m "docs: actualizar README con ejemplos"
```

Tipos de commits:
- `feat`: Nueva característica
- `fix`: Corrección de bug
- `docs`: Cambios en documentación
- `style`: Formato, punto y coma faltantes, etc.
- `refactor`: Refactorización de código
- `test`: Agregar tests
- `chore`: Mantenimiento

### 5. Push y Pull Request

```bash
# Push a tu fork
git push origin feature/mi-nueva-caracteristica

# Crear Pull Request en GitHub
```

## 🧪 Testing

```bash
# Ejecutar todos los tests
pnpm test

# Tests con watch mode
pnpm test:watch

# Coverage
pnpm test:coverage
```

## 🎨 Estilo de Código

### TypeScript

- Usa tipos explícitos cuando sea posible
- Evita `any`, usa `unknown` si es necesario
- Documenta funciones públicas con JSDoc

```typescript
/**
 * Genera un código QR para un pago
 * @param params - Parámetros del pago
 * @returns Datos del QR generado
 */
public async createQR(params: QRCreateParams): Promise<QRData> {
  // ...
}
```

### PHP (WordPress)

- Sigue los [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
- Usa nombres de funciones con prefijo `culqi_qr_`
- Documenta con PHPDoc

```php
/**
 * Generate QR code
 *
 * @param array $args QR parameters
 * @return array|WP_Error QR data or error
 */
function culqi_qr_generate($args) {
    // ...
}
```

## 📦 Estructura del Proyecto

```
plugin-qulqui-qr/
├── packages/
│   ├── core/           # SDK Core TypeScript
│   ├── wordpress/      # Plugin WordPress
│   ├── react/          # React SDK
│   ├── vue/            # Vue SDK
│   └── ...
├── examples/           # Ejemplos de uso
├── docs/               # Documentación
└── tools/              # Herramientas de desarrollo
```

## 🐛 Reportar Bugs

### Antes de reportar

1. Busca en [issues existentes](https://github.com/iBlack14/plugin-qulqui-qr/issues)
2. Asegúrate de usar la última versión
3. Reproduce el bug en un entorno limpio

### Crear un issue

Incluye:
- Descripción clara del problema
- Pasos para reproducir
- Comportamiento esperado vs actual
- Versión del plugin
- Entorno (OS, Node.js version, PHP version, etc.)
- Screenshots si aplica

## 💡 Solicitar Features

1. Busca en [issues existentes](https://github.com/iBlack14/plugin-qulqui-qr/issues)
2. Crea un nuevo issue con etiqueta `enhancement`
3. Describe:
   - Qué problema resuelve
   - Cómo lo usarías
   - Ejemplos de implementación

## 📚 Documentación

- Mantén el README actualizado
- Documenta nuevas APIs en `/docs`
- Agrega ejemplos de uso
- Actualiza CHANGELOG.md

## ✅ Checklist antes de PR

- [ ] Tests pasan (`pnpm test`)
- [ ] Lint pasa (`pnpm lint`)
- [ ] Type check pasa (`pnpm type-check`)
- [ ] Build exitoso (`pnpm build`)
- [ ] Documentación actualizada
- [ ] Commits siguen Conventional Commits
- [ ] PR tiene descripción clara
- [ ] Branch está actualizado con main

## 🔄 Review Process

1. Automatic checks must pass
2. Code review por maintainers
3. Cambios solicitados si es necesario
4. Merge cuando todo esté aprobado

## 📄 Licencia

Al contribuir, aceptas que tus contribuciones serán licenciadas bajo la licencia MIT del proyecto.

## 🤝 Código de Conducta

- Sé respetuoso
- Acepta críticas constructivas
- Enfócate en lo mejor para la comunidad
- Muestra empatía hacia otros contribuidores

## 📞 Contacto

- Issues: [GitHub Issues](https://github.com/iBlack14/plugin-qulqui-qr/issues)
- Discussions: [GitHub Discussions](https://github.com/iBlack14/plugin-qulqui-qr/discussions)

## 🎉 Reconocimientos

Todos los contribuidores serán agregados al README y releases notes.

---

¡Gracias por contribuir! 🚀
