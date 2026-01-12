<?php
/**
 * Public Header Template
 * Urji Beri School Website
 * With Full SEO Implementation
 */

// Get current page for active nav highlighting
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Default SEO settings (can be overridden by individual pages)
$seoDefaults = [
    'title' => isset($pageTitle) ? $pageTitle . ' - ' . get_setting('site_name', 'Urji Beri School') : get_setting('site_name', 'Urji Beri School'),
    'description' => get_setting('site_description', 'Urji Beri School offers education for children aged 3 to 13. Its curriculum is based on the programs of the Ministry of Education and taught in Amharic and English language.'),
    'keywords' => get_setting('site_keywords', 'urji beri school, urji beri online result, urji beri result, urji beri report card, urji beri, urji beri furi, best school around furi, elementary school in furi'),
    'image' => asset_url('images/og-image.jpg'),
    'type' => 'website'
];

// Merge with page-specific SEO if set
$seo = isset($pageSeo) ? array_merge($seoDefaults, $pageSeo) : $seoDefaults;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Primary Meta Tags -->
    <title><?php echo e($seo['title']); ?></title>
    <?php echo generate_seo_meta($seo); ?>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#3679ff">
    <meta name="msapplication-TileColor" content="#3679ff">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Urji Beri">
    <meta name="application-name" content="Urji Beri School">
    <meta name="msapplication-TileImage" content="<?php echo asset_url('images/icon-144.png'); ?>">
    
    <!-- Additional SEO Meta Tags -->
    <meta name="geo.region" content="ET-OR">
    <meta name="geo.placename" content="Alemgena, Oromia">
    <meta name="geo.position" content="8.9806;38.6218">
    <meta name="ICBM" content="8.9806, 38.6218">
    
    <!-- Language Alternates -->
    <link rel="alternate" hreflang="en" href="<?php echo SITE_URL; ?>">
    <link rel="alternate" hreflang="x-default" href="<?php echo SITE_URL; ?>">
    
    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/x-icon" href="<?php echo asset_url('images/favicon.ico'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo asset_url('images/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo asset_url('images/favicon-16x16.png'); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo asset_url('images/apple-touch-icon.png'); ?>">
    <link rel="manifest" href="<?php echo SITE_URL; ?>/manifest.json">
    
    <!-- Preconnect for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>?v=<?php echo time(); ?>">
    
    <!-- JSON-LD Structured Data -->
    <?php echo generate_organization_schema(); ?>
    <?php echo generate_website_schema(); ?>
    <?php if ($currentPage === 'index'): ?>
    <?php echo generate_school_schema(); ?>
    <?php endif; ?>
    <?php if (isset($articleSchema)): ?>
    <?php echo $articleSchema; ?>
    <?php endif; ?>
    <?php if (isset($gallerySchema)): ?>
    <?php echo $gallerySchema; ?>
    <?php endif; ?>
    <?php if (isset($breadcrumbSchema)): ?>
    <?php echo $breadcrumbSchema; ?>
    <?php endif; ?>
    
    <?php if (isset($extraCss)): ?>
        <?php echo $extraCss; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Header -->
    <header class="header" id="header">
        <div class="container">
            <div class="header-inner">
                <a href="<?php echo SITE_URL; ?>" class="logo" title="<?php echo e(get_setting('site_name')); ?> - Home">
                    <img src="<?php echo asset_url('images/logo.png'); ?>" alt="<?php echo e(get_setting('site_name')); ?> Logo" width="50" height="50">
                    <span class="logo-text"><?php echo e(get_setting('site_name', 'Urji Beri School')); ?></span>
                </a>
                
                <nav class="nav-menu" id="navMenu" role="navigation" aria-label="Main Navigation">
                    <a href="<?php echo SITE_URL; ?>" class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>" title="Home">Home</a>
                    <a href="<?php echo SITE_URL; ?>/about.php" class="nav-link <?php echo $currentPage === 'about' ? 'active' : ''; ?>" title="About Urji Beri School">About Us</a>
                    <a href="<?php echo SITE_URL; ?>/director.php" class="nav-link <?php echo $currentPage === 'director' ? 'active' : ''; ?>" title="Message from the Director">Director's Welcome</a>
                    <a href="<?php echo SITE_URL; ?>/gallery.php" class="nav-link <?php echo $currentPage === 'gallery' ? 'active' : ''; ?>" title="Photo Gallery">Gallery</a>
                    <a href="<?php echo SITE_URL; ?>/blog.php" class="nav-link <?php echo $currentPage === 'blog' ? 'active' : ''; ?>" title="Latest News & Events">News</a>
                    <a href="https://result.urjiberischool.com/view_score.php" class="nav-link" target="_blank" rel="noopener" title="Check Student Results Online">Online Result</a>
                    <a href="https://result.urjiberischool.com/login.php" class="nav-link" target="_blank" rel="noopener" title="Teachers Portal Login">Teachers Login</a>
                    <a href="<?php echo SITE_URL; ?>/contact.php" class="nav-link <?php echo $currentPage === 'contact' ? 'active' : ''; ?>" title="Contact Us">Contact</a>
                </nav>
                
                <div class="nav-toggle" id="navToggle" aria-label="Toggle Navigation Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>
