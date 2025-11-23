# Changelog - Culqi QR Plugin

## [0.1.1] - 2024-11-23

### ✅ Added
- JavaScript assets for shortcodes functionality (public.js)
- CSS styles for QR display and modals (public.css)
- Assets enqueue handler (class-culqi-qr-assets.php)
- AJAX endpoint for QR generation
- Modal popup for QR display
- Auto status checking every 5 seconds
- Manual payment verification button
- Loading animations
- Responsive mobile design

### 🐛 Fixed
- Shortcodes now display QR codes instead of just buttons
- WooCommerce checkout page now shows QR properly
- Modal functionality working correctly
- JavaScript errors resolved

### 🔧 Changed
- Updated plugin version from 0.1.0 to 0.1.1
- Improved user interface with better styling
- Enhanced error handling and user feedback

---

## [0.1.0] - 2024-11-23

### ✅ Initial Release
- WordPress plugin structure
- WooCommerce integration
- API client for Culqi
- QR code generation
- Webhook system
- Transaction logging
- Admin dashboard
- Settings page
- Shortcodes: [culqi_qr], [culqi_qr_display], [culqi_qr_form]
- REST API endpoints
- Database tables for transactions and logs
