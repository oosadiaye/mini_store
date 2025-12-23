# Mini Store - Laravel Multi-Tenant E-Commerce Platform

A modern, mobile-optimized multi-tenant e-commerce platform built with Laravel 12, featuring a comprehensive admin panel, POS terminal, and inventory management system.

## 🚀 Features

### Core Features
- **Multi-Tenant Architecture** - Isolated databases for each tenant
- **Mobile-Responsive Admin Panel** - Fully optimized for mobile devices
- **POS Terminal** - Complete point-of-sale system with barcode scanning
- **Inventory Management** - Products, categories, and stock transfers
- **Order Processing** - Complete order management workflow
- **User Management** - Roles and permissions with Spatie
- **PWA Support** - Progressive Web App capabilities

### Mobile Optimization
- ✅ Products List - Mobile card view with drag-and-drop
- ✅ Orders List - Mobile cards with status badges
- ✅ Product Forms - Responsive stacked layout
- ✅ Dashboard - Responsive charts and analytics
- ✅ Categories - Mobile cards with images
- ✅ Order Details - Stacked layout with item cards
- ✅ Stock Transfers - Mobile cards with approve/reject actions
- ✅ Admin Layout - Mobile topbar and bottom navigation

### POS Features
- Barcode scanning (USB and camera)
- Thermal printer support (Bluetooth)
- Offline mode with IndexedDB
- Touch-friendly interface
- Quick product search

## 📋 Requirements

- **PHP:** 8.2 or higher
- **MySQL:** 8.0+ or MariaDB 10.6+
- **Composer:** 2.x
- **Node.js:** 18.x or higher
- **NPM:** 9.x or higher

### Required PHP Extensions
- php-mysql
- php-mbstring
- php-xml
- php-bcmath
- php-curl
- php-zip
- php-gd
- php-intl

## 🛠️ Installation

### Local Development

1. **Clone Repository**
   ```bash
   git clone https://github.com/oosadiaye/quot_mini_laravel.git
   cd quot_mini_laravel
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Setup**
   ```bash
   # Create database
   mysql -u root -p
   CREATE DATABASE mini_store;
   EXIT;
   
   # Run migrations
   php artisan migrate
   php artisan db:seed
   ```

5. **Build Assets**
   ```bash
   npm run dev
   ```

6. **Start Development Server**
   ```bash
   php artisan serve
   ```

Visit: `http://localhost:8000`

## 🚀 Production Deployment

### AlmaLinux 8.10 + Webuzo

See detailed deployment guide: [ALMALINUX_WEBUZO_DEPLOYMENT.md](ALMALINUX_WEBUZO_DEPLOYMENT.md)

**Quick Steps:**
1. Create subdomain in Webuzo
2. Clone repository
3. Install dependencies
4. Configure environment
5. Setup database
6. Install SSL certificate
7. Configure queue worker and cron jobs

**Live Demo:** https://mini.tryquot.com

## 📦 Tech Stack

- **Framework:** Laravel 12
- **Frontend:** Blade, Alpine.js, Tailwind CSS
- **Database:** MySQL
- **Multi-Tenancy:** stancl/tenancy
- **Permissions:** spatie/laravel-permission
- **PDF Generation:** barryvdh/laravel-dompdf
- **PWA:** ladumor/laravel-pwa

## 📱 Mobile Optimization

The admin panel has been fully optimized for mobile devices with:
- Responsive card layouts for all list pages
- Touch-friendly buttons (44px+ height)
- Stacked form layouts
- Mobile bottom navigation
- Responsive charts and analytics
- Dual-view pattern (desktop tables, mobile cards)

## 🔒 Security

- CSRF protection
- XSS prevention
- SQL injection protection
- Role-based access control
- Secure password hashing
- SSL/TLS encryption

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👥 Credits

Developed by the Mini Store Team

## 📞 Support

For support, email support@tryquot.com or visit https://mini.tryquot.com
