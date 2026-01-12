<?php
/**
 * Admin Header Template
 * Urji Beri School Website
 */

// Prevent direct access
if (!defined('BASE_PATH')) {
    exit('Direct access not allowed');
}

// Require authentication
require_auth();

$currentUser = current_user();
$currentAdminPage = basename($_SERVER['PHP_SELF'], '.php');

// Get unread message count
$db = Database::getInstance();
$unreadCount = $db->fetch("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Urji Beri Admin">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#3679ff">
    <title><?php echo isset($adminPageTitle) ? e($adminPageTitle) . ' - ' : ''; ?>Admin Dashboard | <?php echo e(get_setting('site_name')); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo asset_url('images/favicon.ico'); ?>">
    <link rel="apple-touch-icon" href="<?php echo asset_url('images/icon-192.png'); ?>">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="<?php echo ADMIN_URL; ?>/manifest.json">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/admin.css'); ?>">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar Overlay (Mobile) -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-header">
                <a href="<?php echo ADMIN_URL; ?>" class="admin-logo">
                    <img src="<?php echo asset_url('images/logo.png'); ?>" alt="<?php echo e(get_setting('site_name')); ?>">
                    <span class="admin-logo-text">Admin</span>
                </a>
            </div>
            
            <nav class="admin-nav">
                <div class="admin-nav-section">
                    <span class="admin-nav-title">Main</span>
                    <a href="<?php echo ADMIN_URL; ?>/index.php" class="admin-nav-link <?php echo $currentAdminPage === 'index' ? 'active' : ''; ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="9"></rect>
                            <rect x="14" y="3" width="7" height="5"></rect>
                            <rect x="14" y="12" width="7" height="9"></rect>
                            <rect x="3" y="16" width="7" height="5"></rect>
                        </svg>
                        Dashboard
                    </a>
                </div>
                
                <div class="admin-nav-section">
                    <span class="admin-nav-title">Content</span>
                    <a href="<?php echo ADMIN_URL; ?>/blogs.php" class="admin-nav-link <?php echo $currentAdminPage === 'blogs' || $currentAdminPage === 'blog-edit' ? 'active' : ''; ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                        Blog Posts
                    </a>
                    <a href="<?php echo ADMIN_URL; ?>/blog-categories.php" class="admin-nav-link <?php echo $currentAdminPage === 'blog-categories' ? 'active' : ''; ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                        Blog Categories
                    </a>
                    <a href="<?php echo ADMIN_URL; ?>/gallery.php" class="admin-nav-link <?php echo $currentAdminPage === 'gallery' ? 'active' : ''; ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        Gallery
                    </a>
                    <a href="<?php echo ADMIN_URL; ?>/gallery-categories.php" class="admin-nav-link <?php echo $currentAdminPage === 'gallery-categories' ? 'active' : ''; ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        Gallery Categories
                    </a>
                </div>
                
                <div class="admin-nav-section">
                    <span class="admin-nav-title">Pages</span>
                    <a href="<?php echo ADMIN_URL; ?>/director.php" class="admin-nav-link <?php echo $currentAdminPage === 'director' ? 'active' : ''; ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Director's Message
                    </a>
                </div>
                
                <div class="admin-nav-section">
                    <span class="admin-nav-title">Communication</span>
                    <a href="<?php echo ADMIN_URL; ?>/messages.php" class="admin-nav-link <?php echo $currentAdminPage === 'messages' ? 'active' : ''; ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                        Messages
                        <?php if ($unreadCount > 0): ?>
                            <span class="badge badge-error" style="margin-left: auto;"><?php echo $unreadCount; ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                
                <div class="admin-nav-section">
                    <span class="admin-nav-title">Settings</span>
                    <a href="<?php echo ADMIN_URL; ?>/settings.php" class="admin-nav-link <?php echo $currentAdminPage === 'settings' ? 'active' : ''; ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                        Site Settings
                    </a>
                </div>
            </nav>
            
            <div class="admin-sidebar-footer">
                <a href="<?php echo SITE_URL; ?>" class="admin-nav-link" target="_blank">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <line x1="10" y1="14" x2="21" y2="3"></line>
                    </svg>
                    View Website
                </a>
                <a href="<?php echo ADMIN_URL; ?>/logout.php" class="admin-nav-link" style="color: var(--error);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    Logout
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div class="admin-header-left">
                    <button class="admin-menu-toggle" id="adminMenuToggle">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>
                    <h1 class="admin-page-title"><?php echo isset($adminPageTitle) ? e($adminPageTitle) : 'Dashboard'; ?></h1>
                </div>
                
                <div class="admin-header-right">
                    <div class="admin-user">
                        <div class="admin-user-info">
                            <div class="admin-user-name"><?php echo e($currentUser['full_name']); ?></div>
                            <div class="admin-user-role"><?php echo ucfirst(e($currentUser['role_name'])); ?></div>
                        </div>
                        <div class="admin-user-avatar">
                            <?php echo strtoupper(substr($currentUser['full_name'], 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </header>
            
            <div class="admin-content">
                <?php display_flash(); ?>
