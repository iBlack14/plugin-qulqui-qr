# Changelog

Todos los cambios notables de este proyecto serán documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere a [Semantic Versioning](https://semver.org/lang/es/).

## [1.0.0] - 2024-11-24

### Añadido
- ✨ Módulo inicial de gateway de pago para WHMCS
- ✨ Generación automática de códigos QR para facturas
- ✨ Verificación de pago en tiempo real con AJAX
- ✨ Soporte para webhooks de Culqi
- ✨ Procesamiento de reembolsos
- ✨ Soporte para múltiples monedas (PEN, USD)
- ✨ Modo sandbox y producción
- ✨ Configuración de tiempo de expiración de QR
- ✨ Auto-redirección después del pago
- ✨ Modo de prueba con logs detallados
- ✨ Almacenamiento de transacciones en base de datos
- ✨ Interfaz responsive para móviles
- ✨ Documentación completa en español
- ✨ Guía de instalación rápida
- ✨ Instrucciones de configuración de webhooks

### Características
- 🔒 Comunicación segura vía HTTPS con la API de Culqi
- 📊 Seguimiento completo de transacciones
- 🎨 Interfaz de usuario moderna y limpia
- 🔄 Actualización automática del estado de pago
- 📧 Notificaciones por email de confirmación de pago
- 🧪 Soporte completo para ambiente de pruebas
- 📝 Sistema de logging para debugging
- ⚡ Respuesta rápida con validación de pagos

### Seguridad
- 🔐 Validación de webhooks
- 🔒 Almacenamiento seguro de API Keys
- ✅ Protección contra duplicación de pagos
- ✅ Validación de firmas de webhook (opcional)

### Documentación
- 📖 README completo con ejemplos
- 📝 Guía de instalación paso a paso
- 🔧 Sección de solución de problemas
- 💡 Ejemplos de uso
- 🎯 Diagrama de flujo de pagos

## [Unreleased]

### Por añadir
- [ ] Soporte para más monedas
- [ ] Panel de administración para transacciones
- [ ] Reportes de pagos
- [ ] Plantillas personalizables de QR
- [ ] Notificaciones push
- [ ] Integración con sistema de tickets
- [ ] Soporte multi-idioma
- [ ] API REST para integraciones
- [ ] Dashboard de analytics

---

## Tipos de cambios

- **Añadido** para nuevas características
- **Cambiado** para cambios en funcionalidad existente
- **Obsoleto** para características que serán removidas
- **Removido** para características removidas
- **Corregido** para corrección de bugs
- **Seguridad** para vulnerabilidades

---

[1.0.0]: https://github.com/iBlack14/plugin-qulqui-qr/releases/tag/whmcs-v1.0.0
