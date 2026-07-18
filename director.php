<?php
/**
 * Director's Welcome Page
 * Urji Beri School Website
 * With Full SEO Implementation
 */

require_once __DIR__ . '/config/config.php';

$pageTitle = "Director's Welcome";
$directorName = get_setting('director_name', 'School Director');

// SEO Configuration
$pageSeo = [
    'title' => "Director's Welcome - " . get_setting('site_name', 'Urji Beri School'),
    'description' => 'A warm welcome message from ' . $directorName . ', Director of Urji Beri School. Learn about our educational philosophy and commitment to student success.',
    'keywords' => 'Urji Beri School director, school leadership, welcome message, educational philosophy, Alemgena school principal',
    'image' => (function () {
        $img = get_setting('director_image');
        if ($img && is_file(UPLOADS_PATH . '/director/' . $img)) {
            return upload_url($img, 'director');
        }
        return branding_url('site_og_image');
    })(),
    'type' => 'profile'
];

// Breadcrumb Schema
$breadcrumbSchema = generate_breadcrumb_schema([
    'Home' => SITE_URL,
    "Director's Welcome" => route_url('director')
]);

include INCLUDES_PATH . '/header.php';
?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="page-header-shapes">
            <div class="page-header-shape page-header-shape-1"></div>
            <div class="page-header-shape page-header-shape-2"></div>
        </div>
        <div class="container">
            <div class="page-header-content">
                <h1 class="page-title">Director's Welcome</h1>
                <nav class="breadcrumb">
                    <a href="<?php echo SITE_URL; ?>">Home</a>
                    <span class="breadcrumb-separator">/</span>
                    <span>Director's Welcome</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Director's Message Section -->
    <section class="section">
        <div class="container">
            <div class="glass-card-solid director-panel">
                <div class="director-content">
                    <div class="director-image">
                        <?php
                        $directorImage = get_setting('director_image');
                        $directorImageUrl = ($directorImage && is_file(UPLOADS_PATH . '/director/' . $directorImage))
                            ? upload_url($directorImage, 'director')
                            : asset_url('images/director-placeholder.jpg');
                        ?>
                        <img src="<?php echo e($directorImageUrl); ?>" alt="<?php echo e(get_setting('director_name', 'School Director')); ?>">
                        <h3 class="director-name"><?php echo e(get_setting('director_name', 'School Director')); ?></h3>
                        <p class="director-title"><?php echo e(get_setting('director_title', 'School Director')); ?></p>
                    </div>

                    <div class="director-message">
                        <?php
                        $message = trim((string) get_setting(
                            'director_message',
                            "I would like to take this opportunity to welcome you to our website and thank you for considering Urji Beri School as an educational home for your children.\n\nAt UBS we are committed to developing and empowering future innovators and leaders. We invite you to become close partners in this important task."
                        ));
                        $paragraphs = preg_split("/\n\s*\n/", $message) ?: [$message];
                        foreach ($paragraphs as $paragraph) {
                            $paragraph = trim($paragraph);
                            if ($paragraph === '') {
                                continue;
                            }
                            echo '<p>' . nl2br(e($paragraph)) . '</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quote Section -->
    <section class="section" style="background-color: var(--primary);">
        <div class="container">
            <div class="text-center" style="color: white; max-width: 800px; margin: 0 auto;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="white" style="opacity: 0.3; margin-bottom: var(--spacing-4);">
                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
                </svg>
                <p style="font-size: var(--font-size-xl); font-style: italic; opacity: 0.95; margin-bottom: var(--spacing-6);">
                    <?php echo e(get_setting('director_quote', '"The more that you read, the more things you will know. The more that you learn, the more places you\'ll go." – Dr. Seuss')); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Want to Learn More?</h2>
                <p class="cta-text">Schedule a visit to our campus and meet our dedicated team of educators.</p>
                <div class="btn-group justify-center">
                    <a href="<?php echo route_url('contact'); ?>" class="btn btn-primary btn-lg">Schedule a Visit</a>
                    <a href="<?php echo route_url('about'); ?>" class="btn btn-outline btn-lg">About Our School</a>
                </div>
            </div>
        </div>
    </section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
