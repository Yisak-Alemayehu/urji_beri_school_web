<?php
/**
 * Front Controller
 * Urji Beri School Website
 *
 * Handles clean URLs for Apache (.htaccess) and Nginx (try_files → index.php).
 */

require_once __DIR__ . '/config/config.php';
require_once INCLUDES_PATH . '/router.php';

(new Router())->dispatch($_SERVER['REQUEST_URI'] ?? '/');
