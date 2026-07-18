<?php
/**
 * Public Header Template
 * Urji Beri School Website
 * With Full SEO Implementation
 */

// Get current page for active nav highlighting
$currentPage = defined('CURRENT_PAGE') ? CURRENT_PAGE : basename($_SERVER['PHP_SELF'], '.php');

// Default SEO settings (can be overridden by individual pages)
$seoDefaults = [
    'title' => isset($pageTitle) ? $pageTitle . ' - ' . get_setting('site_name', 'Urji Beri School') : get_setting('site_name', 'Urji Beri School'),
    'description' => get_setting('site_description', 'Urji Beri School offers education for children aged 3 to 13. Its curriculum is based on the programs of the Ministry of Education and taught in Amharic and English language.'),
    'keywords' => get_setting('site_keywords', 'urji beri school, urji beri online result, urji beri result, urji beri report card, urji beri, urji beri furi, best school around furi, elementary school in furi'),
    'image' => branding_url('site_og_image'),
    'type' => 'website'
];

// Merge with page-specific SEO if set
$seo = isset($pageSeo) ? array_merge($seoDefaults, $pageSeo) : $seoDefaults;
$siteNameShort = get_setting('site_name', 'Urji Beri School');
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
    <?php echo generate_seo_verification_meta(); ?>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#1E3A8A">
    <meta name="msapplication-TileColor" content="#1E3A8A">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="<?php echo e($siteNameShort); ?>">
    <meta name="application-name" content="<?php echo e($siteNameShort); ?>">
    <meta name="site-url" content="<?php echo SITE_URL; ?>">
    <meta name="msapplication-TileImage" content="<?php echo e(branding_url('site_icon_144')); ?>">
    
    <!-- Additional SEO Meta Tags -->
    <?php
    $mapLat = get_setting('map_latitude', '8.9806');
    $mapLng = get_setting('map_longitude', '38.7578');
    $canonicalPage = get_canonical_url();
    ?>
    <meta name="geo.region" content="ET-OR">
    <meta name="geo.placename" content="Alemgena, Oromia">
    <meta name="geo.position" content="<?php echo e($mapLat); ?>;<?php echo e($mapLng); ?>">
    <meta name="ICBM" content="<?php echo e($mapLat); ?>, <?php echo e($mapLng); ?>">
    
    <!-- Language Alternates -->
    <link rel="alternate" hreflang="en" href="<?php echo e($canonicalPage); ?>">
    <link rel="alternate" hreflang="x-default" href="<?php echo e($canonicalPage); ?>">
    
    <!-- Favicon & App Icons -->
    <link rel="icon" href="<?php echo e(branding_url('site_favicon')); ?>" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo e(branding_url('site_favicon_32')); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo e(branding_url('site_favicon_16')); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e(branding_url('site_apple_touch_icon')); ?>">
    <link rel="manifest" href="<?php echo SITE_URL; ?>/manifest.php">
    
    <!-- Preconnect for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,650;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>?v=<?php echo asset_version(ASSETS_PATH . '/css/style.css'); ?>">
    
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
    <?php if (isset($contactPageSchema)): ?>
    <?php echo $contactPageSchema; ?>
    <?php endif; ?>
    
    <?php if (isset($extraCss)): ?>
        <?php echo $extraCss; ?>
    <?php endif; ?>
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>
    <!-- Header -->
    <header class="header" id="header">
        <div class="container">
            <div class="header-inner">
                <a href="<?php echo route_url('home'); ?>" class="logo" title="<?php echo e(get_setting('site_name')); ?> - Home">
                    <img src="<?php echo e(branding_url('site_logo')); ?>" alt="<?php echo e(get_setting('site_name')); ?> Logo" width="50" height="50">
                    <span class="logo-text"><?php echo e(get_setting('site_name', 'Urji Beri School')); ?></span>
                </a>
                
                <nav class="nav-menu" id="navMenu" role="navigation" aria-label="Main Navigation">
                    <a href="<?php echo route_url('home'); ?>" class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>" title="Home">Home</a>
                    <a href="<?php echo route_url('about'); ?>" class="nav-link <?php echo $currentPage === 'about' ? 'active' : ''; ?>" title="About Urji Beri School">About Us</a>
                    <a href="<?php echo route_url('director'); ?>" class="nav-link <?php echo $currentPage === 'director' ? 'active' : ''; ?>" title="Message from the Director">Director's Welcome</a>
                    <a href="<?php echo route_url('gallery'); ?>" class="nav-link <?php echo $currentPage === 'gallery' ? 'active' : ''; ?>" title="Photo Gallery">Gallery</a>
                    <a href="<?php echo route_url('blog'); ?>" class="nav-link <?php echo in_array($currentPage, ['blog', 'blog-detail'], true) ? 'active' : ''; ?>" title="Latest News & Events">News</a>
                    <a href="<?php echo e(get_setting('online_result_url', 'https://sms.urjiberischool.com/portal/login?')); ?>" class="nav-link nav-link-app" target="_blank" rel="noopener" title="Students & Parents App — results, attendance, fees">Students/Parents App</a>
                    <a href="<?php echo e(get_setting('teacher_login_url', 'https://sms.urjiberischool.com/teacher-portal/login?')); ?>" class="nav-link nav-link-app" target="_blank" rel="noopener" title="Teachers App — sign in to UBS Teacher">Teachers App</a>
                    <a href="<?php echo route_url('contact'); ?>" class="nav-link <?php echo $currentPage === 'contact' ? 'active' : ''; ?>" title="Contact Us">Contact</a>
                </nav>
                
                <button type="button" class="nav-toggle" id="navToggle" aria-label="Toggle Navigation Menu" aria-expanded="false" aria-controls="navMenu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>
    
    <main id="main-content">
