# Urji Beri School Website

A fully dynamic, database-driven website for **Urji Beri School** - an Ethiopian elementary school located in Alemgena, Oromia Region, Ethiopia.

## 🏫 About the School

- **Type:** Preschool & Elementary School
- **Ages:** 3-13 years (Nursery through Grade 8)
- **Location:** Alemgena, Oromia Region, Ethiopia
- **Established:** [Year to be configured in admin]

## ✨ Features

### Public Website
- **Homepage** - Hero section, features, statistics, latest news, gallery preview
- **About Us** - School overview, mission, vision, values, accreditation
- **Director's Welcome** - Message from the school director with photo
- **Photo Gallery** - Dynamic gallery with category filtering and lightbox
- **Blog/News** - News articles with categories, search, and pagination
- **Contact** - Contact form, school information, OpenStreetMap integration

### Admin Dashboard
- **Dashboard Overview** - Statistics, quick actions, recent content
- **Blog Management** - Create, edit, delete posts with featured images
- **Blog Categories** - Manage post categories
- **Gallery Management** - Upload and manage gallery images
- **Gallery Categories** - Organize gallery by categories
- **Messages** - View and manage contact form submissions
- **Director's Message** - Update director's welcome page content
- **Site Settings** - Configure school info, contact details, social media, etc.

## 🛠️ Technical Stack

- **Backend:** PHP 7.4+ (vanilla, no frameworks)
- **Database:** MySQL 5.7+ / MariaDB 10.3+
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Design:** Light futuristic UI with teal/cyan accents and purposeful motion
- **Maps:** OpenStreetMap (Leaflet.js ready)

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache/Nginx web server
- PDO PHP extension
- GD PHP extension (for image handling)
- mod_rewrite enabled (for Apache)

## 🚀 Installation

### 1. Clone/Upload Files

Upload all files to your web server's document root (e.g., `/var/www/html/` or `public_html/`).

### 2. Create Database

```sql
CREATE DATABASE urji_beri_school CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3. Import Database Schema

```bash
mysql -u username -p urji_beri_school < database.sql
```

Or import `database.sql` through phpMyAdmin.

### 4. Configure Database Connection

Edit `config/database.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'urji_beri_school');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 5. Configure Site URL

Edit `config/config.php` and update the site URL:

```php
define('SITE_URL', 'https://your-domain.com');
```

### 6. Set File Permissions

```bash
chmod 755 uploads/
chmod 755 uploads/blog/
chmod 755 uploads/gallery/
chmod 755 uploads/director/
```

### 7. Access the Site

- **Public Site:** `https://your-domain.com`
- **Admin Panel:** `https://your-domain.com/admin`

## 🔐 Default Admin Credentials

After installation, log in to the admin panel with:

- **Username:** `admin`
- **Email:** `admin@urjiberischool.com`
- **Password:** `Admin@123`

⚠️ **IMPORTANT:** Change the default password immediately after first login!

## 📁 Directory Structure

```
urji_beri_school_website/
├── admin/                      # Admin dashboard
│   ├── includes/
│   │   ├── admin_header.php
│   │   └── admin_footer.php
│   ├── index.php              # Dashboard
│   ├── login.php
│   ├── logout.php
│   ├── blogs.php              # Blog posts list
│   ├── blog-edit.php          # Create/edit posts
│   ├── blog-categories.php    # Blog categories
│   ├── gallery.php            # Gallery management
│   ├── gallery-categories.php # Gallery categories
│   ├── messages.php           # Contact messages
│   ├── director.php           # Director's message
│   └── settings.php           # Site settings
├── assets/
│   ├── css/
│   │   ├── style.css          # Public styles
│   │   └── admin.css          # Admin styles
│   └── js/
│       ├── main.js            # Public JavaScript
│       └── admin.js           # Admin JavaScript
├── config/
│   ├── config.php             # Site configuration
│   └── database.php           # Database connection
├── includes/
│   ├── header.php             # Public header
│   ├── footer.php             # Public footer
│   ├── functions.php          # Helper functions
│   └── auth.php               # Authentication
├── uploads/                    # User uploads
│   ├── blog/
│   ├── gallery/
│   └── director/
├── index.php                   # Homepage
├── about.php                   # About page
├── director.php                # Director's welcome
├── gallery.php                 # Photo gallery
├── blog.php                    # Blog listing
├── blog-detail.php            # Single blog post
├── contact.php                 # Contact page
├── database.sql               # Database schema
└── README.md                  # This file
```

## 🎨 Design System

### Colors
- **Primary:** `#0f766e` (Deep teal)
- **White:** `#ffffff`
- **Gray shades:** `#f8f9fa`, `#e9ecef`, `#6c757d`, `#212529`
- **Success:** `#28a745`
- **Warning:** `#ffc107`
- **Error:** `#dc3545`

### Glassmorphism Style
- Background: `rgba(255, 255, 255, 0.8)`
- Backdrop filter: `blur(10px)`
- Border: `1px solid rgba(255, 255, 255, 0.2)`
- Border radius: `16px` (cards), `8px` (buttons/inputs)

## 🔧 Configuration Options

### Site Settings (Admin Panel)

| Setting | Description |
|---------|-------------|
| `site_name` | School name |
| `site_tagline` | School tagline |
| `contact_email` | Contact email address |
| `contact_phone` | Primary phone number |
| `contact_address` | Physical address |
| `social_facebook` | Facebook page URL |
| `social_twitter` | Twitter/X profile URL |
| `social_instagram` | Instagram profile URL |
| `map_latitude` | School location latitude |
| `map_longitude` | School location longitude |
| `stat_students` | Total students count |
| `stat_teachers` | Number of teachers |

## 🔒 Security Features

- CSRF protection on all forms
- Password hashing with `password_hash()` (bcrypt)
- Prepared statements for all database queries
- Input sanitization and validation
- Session-based authentication
- File upload validation (type, size)
- XSS prevention with output escaping

## 📝 License

This project is proprietary software developed for Urji Beri School.

## 👨‍💻 Developer

Built with care for Urji Beri School, Alemgena, Ethiopia.

---

**Need help?** Contact the administrator at the email configured in site settings.
