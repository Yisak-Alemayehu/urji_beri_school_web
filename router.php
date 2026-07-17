<?php
/**
 * Application Front Controller
 */

require_once __DIR__ . '/config/config.php';
require_once INCLUDES_PATH . '/router.php';

$router = new Router();
$router->dispatch($_SERVER['REQUEST_URI'] ?? '/');
