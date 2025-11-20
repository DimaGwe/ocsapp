# OCS Marketplace - Application Context

**Generated**: 2025-11-20
**Server**: 3.145.4.146 (AWS EC2)
**Environment**: Production
**URL**: https://ocsapp.ca

---

## 1. APPLICATION OVERVIEW

### Description
Zero-emission grocery delivery marketplace platform for the Canadian market with bilingual support (English/French).

### Status
- **Completion**: 95%
- **Stage**: Production-Ready
- **Market**: Canada (EN/FR)
- **Version**: 0.95

### Core Features
- Multi-vendor marketplace system
- Stripe payment integration (CAD/USD)
- Order management with status validation
- Auto-delivery assignment (zone-based)
- Stock management (Model B with auto-allocation)
- Admin panel with comprehensive stock visibility
- Bilingual support (English/French)
- Analytics dashboard with visitor tracking
- Delivery tracking system
- SEO optimization (sitemap, robots.txt, meta tags, structured data)

---

## 2. TECHNOLOGY STACK

### Backend
- **Language**: PHP 8.2.29
- **Framework**: Custom MVC Framework
- **Web Server**: Apache 2.4.52 (Ubuntu)
- **Session Storage**: File-based sessions
- **OPcache**: Enabled (Zend OPcache v8.2.29)

### Database
- **System**: MySQL 8.0
- **Host**: AWS RDS (ocs-marketplace-db.c98w8ccoghgv.us-east-2.rds.amazonaws.com)
- **Database**: marketplace_db
- **User**: ocs_admin
- **Tables**: 47 tables

### Infrastructure (AWS)
- **Compute**: EC2 Instance (Ubuntu)
- **Database**: RDS MySQL 8.0
- **Storage**: S3 (file uploads, backups)
- **Monitoring**: CloudWatch + UptimeRobot

### Dependencies (Composer)
```json
{
  "php": ">=8.0",
  "phpmailer/phpmailer": "^6.9",
  "stripe/stripe-php": "^18.2"
}
```

### Frontend
- **CSS Framework**: Tailwind CSS (CDN)
- **JavaScript**: Vanilla JS
- **Assets**: Located in `/public/assets/`

---

## 3. PROJECT STRUCTURE

```
/var/www/html/marketplace/
├── app/
│   ├── Controllers/         # 27+ controllers
│   │   ├── AccountController.php
│   │   ├── AdminController.php
│   │   ├── AdminAdsController.php
│   │   ├── AdminDeliveryController.php
│   │   ├── AdminOrdersController.php
│   │   ├── AdminSellerController.php
│   │   ├── AdminShopController.php
│   │   ├── AnalyticsController.php
│   │   ├── AuthController.php
│   │   ├── BrandController.php
│   │   ├── CartController.php
│   │   ├── CategoryController.php
│   │   ├── CheckoutController.php
│   │   ├── DashboardController.php
│   │   ├── DealsController.php
│   │   ├── DeliveryController.php
│   │   ├── HomeController.php
│   │   ├── InventoryController.php
│   │   ├── OrderController.php
│   │   ├── PageController.php
│   │   ├── PaymentController.php
│   │   ├── ProductController.php
│   │   ├── PublicCategoryController.php
│   │   ├── ReportsController.php
│   │   ├── SearchController.php
│   │   ├── SeoController.php
│   │   ├── SettingsController.php
│   │   ├── ShopController.php
│   │   └── VisitorAnalyticsController.php
│   │
│   ├── Helpers/
│   │   ├── EmailHelper.php
│   │   ├── ImageUploadHelper.php
│   │   ├── SeoHelper.php
│   │   ├── StockHelper.php
│   │   ├── StockValidator.php
│   │   ├── VisitorTracker.php
│   │   ├── functions.php           # Global helper functions
│   │   └── translation_helper.php
│   │
│   ├── Middlewares/
│   │   └── AuthMiddleware.php
│   │
│   └── Views/                # 77+ view files
│       ├── admin/            # Admin panel views
│       ├── advertiser/       # Advertiser views
│       ├── affiliate/        # Affiliate views
│       ├── auth/             # Login/Register views
│       ├── buyer/            # Customer interface views
│       ├── components/       # Reusable components
│       ├── delivery/         # Driver interface views
│       ├── emails/           # Email templates
│       ├── pages/            # Static pages
│       ├── seller/           # Vendor interface views
│       └── user/             # User account views
│
├── config/
│   ├── app.php              # App configuration
│   ├── database.php         # Database configuration
│   ├── mail.php             # Mail configuration
│   └── translations.php     # Bilingual translations
│
├── database/
│   └── migrations/          # Database migrations
│       ├── 2025_11_14_enhance_visitor_tracking.sql
│       └── 2025_11_14_fix_product_type_enum.sql
│
├── public/                  # Web root (document root)
│   ├── assets/
│   │   ├── css/             # Stylesheets
│   │   ├── images/          # Static images
│   │   └── js/              # JavaScript files
│   ├── uploads/             # User-uploaded files
│   │   ├── categories/
│   │   ├── products/
│   │   └── shops/
│   ├── .htaccess            # Apache rewrite rules
│   └── index.php            # Front controller
│
├── routes/
│   └── web.php              # Route definitions
│
├── storage/
│   ├── cache/               # Application cache
│   ├── logs/                # Application logs
│   └── uploads/             # Additional uploads
│       ├── avatars/
│       ├── categories/
│       └── products/
│
├── vendor/                  # Composer dependencies
│
├── .env                     # Environment variables
├── .gitignore
├── composer.json
├── composer.lock
└── README.md
```

---

## 4. DATABASE SCHEMA

### Key Tables (47 total)

#### Users & Authentication
- **users**: User accounts (id, email, password, first_name, last_name, phone, avatar, status, email_verified_at, last_login_at)
- **user_roles**: User role assignments
- **roles**: System roles (admin, seller, buyer, delivery, affiliate, advertiser)
- **sessions**: User sessions
- **password_resets**: Password reset tokens
- **login_attempts**: Login attempt tracking

#### Shops & Products
- **shops**: Vendor stores (47 columns including seller_id, name, slug, shop_type, description, logo, cover_image, location data, delivery settings, approval status, ratings, SEO fields)
  - Shop Types: `grocery_store`, `food_court`, `store`, `products`
- **products**: Product catalog (53 columns including brand_id, name, slug, sku, description, pricing, inventory, status, product_type, seller_id, ratings, SEO fields)
  - Product Types: `global`, `seller`
  - Status: `draft`, `active`, `inactive`, `out_of_stock`
- **brands**: Product brands
- **categories**: Product categories
- **product_categories**: Product-category relationships
- **product_images**: Product images
- **product_variants**: Product variations
- **product_variant_options**: Variant options
- **product_options**: Product options
- **product_option_values**: Option values
- **product_tags**: Product tags
- **tags**: Tag definitions
- **product_views**: Product view tracking

#### Inventory Management
- **inventories**: Global inventory
- **shop_inventory**: Shop-specific inventory (id, shop_id, product_id, price, compare_at_price, stock_quantity, allocated_quantity, sold_quantity, returned_quantity, current_stock, track_inventory, status)
- **inventory_stock_history**: Stock movement history
- **stock_movements**: Stock movement tracking
- **stock_allocation_requests**: Stock allocation requests
- **v_product_stock_summary**: Product stock summary view
- **v_shop_inventory_summary**: Shop inventory summary view

#### Orders & Payments
- **orders**: Customer orders (id, user_id, shop_id, order_number, status, subtotal, tax, delivery_fee, discount, total, payment_method, payment_status, delivery_date, delivery_time, notes)
  - Status: `pending`, `confirmed`, `processing`, `ready`, `out_for_delivery`, `delivered`, `cancelled`, `refunded`
  - Payment Method: `cash`, `card`, `transfer`, `paypal`, `stripe`
  - Payment Status: `pending`, `paid`, `failed`, `refunded`
- **order_items**: Order line items (id, order_id, product_id, shop_inventory_id, product_name, sku, quantity, price, subtotal)
- **addresses**: Customer addresses

#### Delivery System
- **delivery_assignments**: Driver-order assignments
- **delivery_status_history**: Delivery status tracking
- **delivery_zones**: Delivery zone definitions
- **shop_delivery_zones**: Shop-specific delivery zones
- **driver_availability**: Driver availability tracking
- **delivery_earnings**: Driver earnings

#### Reviews & Ratings
- **reviews**: Product reviews
- **review_images**: Review images
- **shop_reviews**: Shop reviews

#### Shop Management
- **shop_banners**: Shop promotional banners
- **shop_hours**: Shop operating hours
- **shop_policies**: Shop policies (returns, shipping, etc.)
- **shop_social_links**: Shop social media links

#### Analytics & Tracking
- **visitor_analytics**: Page visit tracking
- **visitor_sessions**: Visitor session tracking
- **popular_pages**: Popular page tracking
- **audit_logs**: System audit logs

#### System
- **settings**: Application settings

### Database Views
- **v_product_stock_summary**: Aggregate product stock data
- **v_shop_inventory_summary**: Aggregate shop inventory data

---

## 5. ROUTING SYSTEM

### Router Architecture
- **Type**: Custom router with pattern matching
- **Front Controller**: `/public/index.php`
- **Routes File**: `/routes/web.php`
- **URL Rewriting**: Apache `.htaccess` mod_rewrite

### Route Format
```php
'METHOD /path' => ['ControllerName', 'methodName']
'GET /product/{slug}' => ['ProductController', 'show']
```

### Key Route Groups

#### Public Routes
- Home: `/`
- Products: `/product/{slug}`
- Search: `/search`
- Categories: `/categories`, `/category/{slug}`
- Shops: `/shops`, `/shops/{slug}`
- Cart: `/cart`, `/cart/add`, `/cart/update`, `/cart/remove`
- Checkout: `/checkout`, `/checkout/process`, `/checkout/success`
- Static Pages: `/terms`, `/privacy`, `/about`, `/contact`

#### Authentication
- `/login`, `/register`, `/logout`
- `/forgot-password`, `/reset-password`

#### User Account
- `/account/orders`
- `/account/settings`
- `/account/addresses`
- `/account/wishlist`

#### Seller Dashboard
- `/seller` - Dashboard
- `/seller/shop` - Shop management
- `/seller/products` - Product management
- `/seller/inventory` - Inventory management
- `/seller/orders` - Order management
- `/seller/reports` - Reports

#### Admin Panel
- `/admin` - Dashboard
- `/admin/users` - User management
- `/admin/shops` - Shop management
- `/admin/products` - Product management
- `/admin/inventory` - Inventory management
- `/admin/orders` - Order management
- `/admin/delivery` - Delivery management
- `/admin/reports` - Reports
- `/admin/settings` - Settings

#### Delivery Dashboard
- `/delivery` - Dashboard
- `/delivery/orders` - Assigned orders
- `/delivery/earnings` - Earnings report

#### Payment (Stripe)
- `/payment/create-session` - Create checkout session
- `/payment/success` - Payment success
- `/payment/cancel` - Payment cancelled
- `/payment/webhook` - Stripe webhook

#### SEO
- `/sitemap.xml` - XML sitemap
- `/robots.txt` - Robots file

---

## 6. MVC ARCHITECTURE

### Controllers
Controllers are located in `/app/Controllers/` and handle HTTP requests.

**Base Pattern**:
```php
namespace App\Controllers;

class ExampleController {
    public function index() {
        // Logic here
        view('example/index', ['data' => $data]);
    }
}
```

### Views
Views are located in `/app/Views/` and use plain PHP templates with helper functions.

**Rendering**:
```php
view('path/to/view', ['variable' => $value]);
```

### Helpers
Global helper functions are autoloaded from `/app/Helpers/functions.php`.

**Key Functions**:
- `view($view, $data)` - Render view
- `url($path)` - Generate URL
- `redirect($path)` - HTTP redirect
- `env($key, $default)` - Get environment variable
- `db()` - Get database connection
- `auth()` - Get authenticated user
- `t($key, $default)` - Translate text (bilingual)

---

## 7. INVENTORY MANAGEMENT (MODEL B)

### Architecture
The application uses a sophisticated "Model B" inventory system with auto-allocation.

### Stock Flow
```
Total Stock (Warehouse)
├─ OCS Store (shop_id=1) - Auto-allocated
└─ Available Stock (For Sellers) - Manual allocation
```

### Example
```
Product: "Organic Bananas"
├─ total_stock: 2,000 units (warehouse)
├─ allocated_stock: 1,500 units (OCS Store auto-allocated)
└─ available_stock: 500 units (for sellers to request)
```

### Key Features
- **Auto-Allocation**: OCS Store (shop_id=1) automatically receives stock allocation
- **Manual Allocation**: Sellers request allocation from available_stock pool
- **Stock Tracking**: Complete history via `inventory_stock_history` and `stock_movements`
- **Admin Visibility**: Color-coded stock levels across all admin pages
  - Green: OCS Store
  - Blue: Warehouse/Available
  - Orange: Low stock
  - Red: Out of stock

### Related Tables
- `products.total_stock` - Total warehouse stock
- `products.allocated_stock` - Total allocated to shops
- `products.available_stock` - Available for allocation (computed: total_stock - allocated_stock)
- `shop_inventory.stock_quantity` - Shop-specific stock
- `shop_inventory.allocated_quantity` - Allocated to shop
- `shop_inventory.sold_quantity` - Sold by shop
- `shop_inventory.returned_quantity` - Returned to shop
- `shop_inventory.current_stock` - Current available (computed: stock_quantity - sold_quantity + returned_quantity)

---

## 8. PAYMENT INTEGRATION

### Stripe
- **Library**: stripe/stripe-php ^18.2
- **Mode**: Production
- **Currency**: CAD (Canadian Dollar) primary, USD secondary
- **Features**: Checkout Sessions, Webhooks
- **Controllers**: PaymentController.php

### Checkout Flow
1. User completes cart
2. Checkout page collects delivery details
3. Stripe Checkout Session created
4. User redirects to Stripe hosted checkout
5. Payment processed
6. Webhook receives payment confirmation
7. Order status updated to "paid"
8. Email notifications sent

---

## 9. BILINGUAL SUPPORT (EN/FR)

### Implementation
- **Helper**: `translation_helper.php`
- **Config**: `config/translations.php`
- **Function**: `t($key, $default = null)`
- **Storage**: Session-based language preference

### Usage Example
```php
echo t('welcome_message', 'Welcome'); // Returns "Bienvenue" in French mode
```

### Supported Languages
- English (EN) - Default
- French (FR) - Quebec market

---

## 10. USER ROLES & PERMISSIONS

### Roles
1. **Admin** - Full system access
2. **Seller** - Vendor/shop owner
3. **Buyer** - Customer
4. **Delivery** - Delivery driver
5. **Affiliate** - Affiliate marketer
6. **Advertiser** - Ad manager

### Role Assignment
- Table: `user_roles`
- Middleware: `AuthMiddleware.php`
- Users can have multiple roles

---

## 11. SEO FEATURES

### Implemented Features
- Dynamic XML sitemap (`/sitemap.xml`)
- Robots.txt (`/robots.txt`)
- Meta tags (title, description, keywords)
- Open Graph tags
- Canonical URLs
- Structured data (JSON-LD)
- Friendly URLs (slug-based)
- 301 redirects

### Controllers
- `SeoController.php` - Sitemap and robots.txt
- `SeoHelper.php` - SEO helper functions

### Database Fields
Products and shops have SEO fields:
- `meta_title`
- `meta_description`
- `meta_keywords`
- `canonical_url`
- `og_image`
- `structured_data` (JSON)
- `robots_meta`

---

## 12. ANALYTICS & TRACKING

### Visitor Tracking
- **Tables**: `visitor_analytics`, `visitor_sessions`
- **Helper**: `VisitorTracker.php`
- **Controller**: `VisitorAnalyticsController.php`

### Tracked Metrics
- Page views
- Unique visitors
- Session duration
- Popular pages
- Product views
- Search queries
- Conversion funnel

### Dashboard
Admin analytics dashboard shows:
- Daily/weekly/monthly traffic
- Top products
- Top shops
- Revenue metrics
- Order statistics

---

## 13. EMAIL SYSTEM

### Provider
- **Library**: PHPMailer 6.9
- **Helper**: `EmailHelper.php`
- **Config**: `config/mail.php`
- **Templates**: `/app/Views/emails/`

### Email Types
- Order confirmation
- Order status updates
- Password reset
- Welcome email
- Seller notifications
- Admin notifications
- Delivery assignment notifications

---

## 14. FILE UPLOADS

### Upload Directories
```
/public/uploads/
├── categories/      # Category images
├── products/        # Product images
└── shops/           # Shop logos/banners

/storage/uploads/
├── avatars/         # User avatars
├── categories/      # Additional category images
└── products/        # Additional product images
```

### Upload Helper
- **File**: `ImageUploadHelper.php`
- **Features**: Image validation, resizing, compression, unique naming

---

## 15. ENVIRONMENT CONFIGURATION

### .env Variables
```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ocsapp.ca
APP_CURRENCY=CAD
APP_TIMEZONE=America/Toronto

DB_HOST=ocs-marketplace-db.c98w8ccoghgv.us-east-2.rds.amazonaws.com
DB_PORT=3306
DB_NAME=marketplace_db
DB_USER=ocs_admin
DB_PASS=********

MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=********
MAIL_FROM=noreply@ocsapp.ca
MAIL_FROM_NAME="OCS Marketplace"

STRIPE_PUBLIC_KEY=pk_live_********
STRIPE_SECRET_KEY=sk_live_********
STRIPE_WEBHOOK_SECRET=whsec_********

AWS_ACCESS_KEY=********
AWS_SECRET_KEY=********
AWS_REGION=us-east-2
AWS_BUCKET=ocs-marketplace-storage
```

---

## 16. RECENT UPDATES

### Latest Git Commits (Last 15)
```
7b55dd6 - Implement critical email notifications for customer journey
26a1267 - Fix sitemap URLs and enhance security headers
1d69499 - Fix sitemap.xml 500 error by correcting table column names
b6a8de7 - Add product rating display to related products section
916c60f - Update CLAUDE.md with homepage UX and OCS Store architecture fixes
01bd534 - Fix homepage carousel spacing and restrict to admin products only
6244f30 - Add horizontal scroll functionality to all homepage sections
e5c9c62 - Add comprehensive SEO management system
8351422 - Add product image management to seller inventory edit page
9e3af4b - Add server permissions checker for debugging uploads
8b3504a - Add upload debug script for server testing
ca213ee - Add debug logging to track file upload data in shop update
094f6fa - Add detailed logging and error messages for shop image uploads
e11f986 - Fix scroll buttons visibility on desktop
d6f6819 - Add navigation buttons to Best Sellers horizontal scroll
```

### Git Status
- Branch: `main`
- Untracked files: Some category images and production scripts
- Modified: One deleted category image

---

## 17. REMAINING TASKS

### Medium Priority
- [ ] Review submission form (40% complete)
- [ ] Run contact_messages database migration

### Low Priority
- [ ] Remove test files (debug-*.php, test-*.php, check-*.php)
- [ ] Code cleanup and refactoring
- [ ] Remove backup files

### Optional
- [ ] PayPal integration (Stripe fully working)
- [ ] Advanced reporting features
- [ ] Mobile app API

---

## 18. SECURITY FEATURES

### Implemented
- Prepared statements for all database queries
- Password hashing (bcrypt)
- CSRF protection
- Input sanitization
- XSS prevention
- SQL injection prevention
- Stock validation before checkout
- Role-based access control
- Session management
- Login attempt tracking

### Headers
- Security headers configured in `.htaccess`
- HTTPS enforcement
- Content Security Policy (CSP)

---

## 19. PERFORMANCE OPTIMIZATIONS

### Implemented
- Zend OPcache enabled
- Output buffering
- Database query optimization
- Image optimization
- Asset minification (CSS/JS)
- CDN for Tailwind CSS
- Database indexing on key columns

---

## 20. BACKUP & RECOVERY

### Database Backups
- Location: AWS S3
- Frequency: Daily automated backups via AWS RDS
- Manual backups in `/home/ubuntu/` directory

### Application Backups
- Git repository (version control)
- AWS S3 (file uploads)

---

## 21. MONITORING & LOGGING

### Monitoring
- **AWS CloudWatch**: Server metrics
- **UptimeRobot**: Uptime monitoring
- **Application Logs**: `/storage/logs/`

### Error Handling
- PHP error logging
- Custom error handler in production
- Debug mode in development

---

## 22. DEPLOYMENT

### Production Server
- **Server**: AWS EC2 (Ubuntu)
- **IP**: 3.145.4.146
- **Domain**: ocsapp.ca
- **SSL**: HTTPS enabled
- **Web Root**: `/var/www/html/marketplace/public/`

### Deployment Process
1. SSH into server: `ssh -i "ocs-marketplace-key.pem" ubuntu@3.145.4.146`
2. Navigate to app: `cd /var/www/html/marketplace`
3. Pull latest code: `git pull origin main`
4. Run migrations if needed: `php run-migration.php`
5. Clear cache: `php public/clear-cache.php`
6. Restart Apache: `sudo systemctl restart apache2`

---

## 23. API ENDPOINTS

### Internal APIs (AJAX)
- Cart operations: `/cart/add`, `/cart/update`, `/cart/remove`
- Language switching: `/set-language`
- Location settings: `/set-location`
- Stock availability checks
- Real-time search

---

## 24. DOCUMENTATION FILES

Located in project root:

- **ADMIN-STOCK-UI-UPDATE.md** - Stock visibility feature documentation
- **AUTO-OCS-STORE-ALLOCATION.md** - Auto-allocation system docs
- **AWS-DEPLOYMENT-GUIDE.md** - AWS deployment guide
- **BILINGUAL-UPDATE.md** - EN/FR implementation guide
- **CLAUDE.md** - Project overview and context
- **CRITICAL-FIXES-APPLIED.md** - Critical fixes log
- **NEXT-STEPS.md** - Planned features
- **PAYMENT-SETUP.md** - Stripe payment setup
- **README.md** - Main project README
- **SEO_IMPLEMENTATION_COMPLETE.md** - SEO feature documentation
- **SESSION-NOTES.md** - Development session notes
- **SESSION_NOTE_2025_11_14_VISITOR_ANALYTICS.md** - Analytics implementation
- **STATIC-PAGES-COMPLETE.md** - Static pages documentation
- **STRIPE-QUICKSTART.md** - Stripe quick start guide
- **VISITOR_ANALYTICS_MIGRATION.md** - Analytics migration guide
- **WORKFLOW-AUDIT.md** - Order workflow audit

---

## 25. DEVELOPMENT WORKFLOW

### Local Development
- **Server**: XAMPP (Apache + MySQL + PHP)
- **Path**: `C:\xampp\htdocs\marketplace`
- **URL**: `http://localhost/marketplace`

### Version Control
- **Repository**: Git
- **Branch Strategy**: Main branch for production
- **Remote**: GitHub/GitLab (assumed)

### Testing
- Manual testing in production
- Debug mode available via `.env` (`APP_DEBUG=true`)

---

## 26. KNOWN ISSUES & CONSIDERATIONS

### Current State
- Production server has some untracked files (category images, production scripts)
- Some backup files and test scripts remain in codebase
- Review submission form partially complete (40%)

### Best Practices
- Always backup database before major changes
- Test in staging environment before production deployment
- Monitor error logs after deployments
- Clear cache after code changes

---

## 27. CONTACT & SUPPORT

### Key Files for Support
- Error logs: `/storage/logs/`
- Apache logs: `/var/log/apache2/error.log`
- PHP logs: Check `php.ini` configuration

### Debugging
- Enable debug mode: Set `APP_DEBUG=true` in `.env`
- Check database connection: Use diagnostic scripts in `/public/`
- Test uploads: Check directory permissions (755 for storage, 755 for public/uploads)

---

## 28. QUICK REFERENCE COMMANDS

### SSH Access
```bash
ssh -i "C:\Users\dimit\Downloads\ocs-marketplace-key.pem" ubuntu@3.145.4.146
```

### Navigate to Application
```bash
cd /var/www/html/marketplace
```

### Database Access
```bash
mysql -h ocs-marketplace-db.c98w8ccoghgv.us-east-2.rds.amazonaws.com \
  -u ocs_admin -p marketplace_db
```

### Git Commands
```bash
git status
git pull origin main
git log --oneline -10
```

### Apache Commands
```bash
sudo systemctl status apache2
sudo systemctl restart apache2
sudo systemctl reload apache2
```

### File Permissions
```bash
sudo chmod -R 755 storage/
sudo chmod -R 755 public/uploads/
sudo chown -R www-data:www-data storage/
sudo chown -R www-data:www-data public/uploads/
```

### Clear Cache
```bash
php public/clear-cache.php
```

---

## 29. FRONTEND ASSETS

### CSS
- **Framework**: Tailwind CSS (via CDN)
- **Custom Styles**: `/public/assets/css/`
- **Approach**: Utility-first with custom components

### JavaScript
- **Location**: `/public/assets/js/`
- **Dependencies**: None (vanilla JS)
- **Features**:
  - Cart functionality
  - Image carousels
  - Form validation
  - AJAX requests
  - Horizontal scrolling
  - Dynamic UI updates

### Images
- **Static Assets**: `/public/assets/images/`
- **Product Images**: `/public/uploads/products/`
- **Shop Logos**: `/public/uploads/shops/`
- **Category Images**: `/public/uploads/categories/`

---

## 30. ADDITIONAL NOTES

### Code Quality
- Uses PSR-4 autoloading
- Follows MVC pattern
- Custom framework (lightweight, optimized for this specific application)
- Prepared statements for all database operations
- Input validation and sanitization throughout

### Scalability Considerations
- Database views for complex queries
- Indexed key columns for performance
- Stock management handles high-volume transactions
- Session-based cart (can be migrated to Redis if needed)

### Future Enhancements
- API for mobile apps
- Advanced analytics dashboard
- Multi-currency support expansion
- Automated testing suite
- Docker containerization
- Redis caching layer

---

**END OF CONTEXT FILE**

---

### Document Metadata
- **Generated By**: Claude Code
- **Server IP**: 3.145.4.146
- **Date**: 2025-11-20
- **Purpose**: Complete application context for development updates
- **Maintainer**: Development Team
