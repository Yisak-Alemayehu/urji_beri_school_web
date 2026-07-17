<?php
/**
 * 404 Not Found Page
 * Urji Beri School Website
 */

require_once __DIR__ . '/config/config.php';

http_response_code(404);

$pageTitle = 'Page Not Found';
$pageSeo = [
    'title' => '404 - Page Not Found | ' . get_setting('site_name', 'Urji Beri School'),
    'description' => 'The page you are looking for could not be found. Return to Urji Beri School homepage or contact us for help.',
    'robots' => 'noindex, follow',
    'canonical' => SITE_URL . '/404.php'
];

include INCLUDES_PATH . '/header.php';
?>

    <section class="page-header">
        <div class="container">
            <div class="page-header-content">
                <h1 class="page-title">Page Not Found</h1>
                <nav class="breadcrumb" aria-label="Breadcrumb">
                    <a href="<?php echo SITE_URL; ?>">Home</a>
                    <span class="breadcrumb-separator">/</span>
                    <span>404</span>
                </nav>
            </div>
        </div>
    </section>

    <section class="section" style="background-color: var(--bg-section);">
        <div class="container">
            <div class="glass-card-solid" style="max-width: 640px; margin: 0 auto; text-align: center; padding: clamp(2rem, 5vw, 3rem);">
                <p class="section-kicker">404</p>
                <h2 class="section-title">We couldn't find that page</h2>
                <p class="section-subtitle" style="margin: 0 auto var(--spacing-6);">
                    The link may be outdated or the page may have moved. Try the links below or return home.
                </p>
                <div class="btn-group justify-center">
                    <a href="<?php echo SITE_URL; ?>" class="btn btn-primary">Back to Home</a>
                    <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-outline">Contact Us</a>
                </div>
            </div>
        </div>
    </section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
