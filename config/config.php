<?php
/**
 * Application Configuration
 * Urji Beri School Website
 */

// Base path definition
define('BASE_PATH', dirname(__DIR__));

// URL Configuration (update for production)
define('SITE_URL', 'https://urjiberischool.test');
define('ADMIN_URL', SITE_URL . '/admin');

// Directory paths
define('CONFIG_PATH', BASE_PATH . '/config');
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('ADMIN_PATH', BASE_PATH . '/admin');
define('ASSETS_PATH', BASE_PATH . '/assets');
define('UPLOADS_PATH', BASE_PATH . '/uploads');

// Upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Session settings
define('SESSION_NAME', 'urji_beri_session');
define('SESSION_LIFETIME', 3600); // 1 hour

// Pagination
define('POSTS_PER_PAGE', 9);
define('GALLERY_PER_PAGE', 15);
define('MESSAGES_PER_PAGE', 20);

// Security
define('CSRF_TOKEN_NAME', 'csrf_token');

// Timezone
date_default_timezone_set('Africa/Addis_Ababa');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once CONFIG_PATH . '/database.php';

// Include helper functions
require_once INCLUDES_PATH . '/functions.php';

// Include authentication helpers
require_once INCLUDES_PATH . '/auth.php';

// Include router
require_once INCLUDES_PATH . '/router.php';

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path' => '/',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}

// Generate CSRF token if not exists
if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}
