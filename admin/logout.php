<?php
/**
 * Admin Logout
 * Urji Beri School Website
 */

require_once dirname(__DIR__) . '/config/config.php';

logout();
set_flash('success', 'You have been logged out successfully.');
redirect(ADMIN_URL . '/login.php');
