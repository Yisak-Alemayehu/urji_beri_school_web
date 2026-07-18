<?php
/**
 * Gallery Page
 * Urji Beri School Website
 * With Full SEO Implementation
 */

require_once __DIR__ . '/config/config.php';

if (!defined('CURRENT_PAGE')) {
    define('CURRENT_PAGE', 'gallery');
}

$db = Database::getInstance();

// Get selected category (clean URL or legacy query string)
$categorySlug = isset($_GET['category']) ? clean_input($_GET['category']) : '';

// Get all gallery categories
$categories = $db->fetchAll(
    "SELECT gc.*, COUNT(gi.id) as image_count 
     FROM gallery_categories gc
     LEFT JOIN gallery_images gi ON gc.id = gi.category_id AND gi.is_active = 1
     WHERE gc.is_active = 1
     GROUP BY gc.id
     ORDER BY gc.sort_order ASC, gc.name ASC"
);

// Get current category info for SEO
$currentCategory = null;
if ($categorySlug) {
    foreach ($categories as $cat) {
        if ($cat['slug'] === $categorySlug) {
            $currentCategory = $cat;
            break;
        }
    }
}

// Set page title based on category
$pageTitle = $currentCategory
    ? $currentCategory['name'] . ' Photos - Gallery'
    : 'Photo Gallery';

// SEO Configuration
$galleryDescription = $currentCategory
    ? 'Browse ' . $currentCategory['name'] . ' photos from Urji Beri School. ' . ($currentCategory['description'] ?? 'See our vibrant school life.')
    : 'Explore the vibrant life at Urji Beri School through our photo gallery. See events, classroom activities, celebrations, and more.';

$pageSeo = [
    'title' => $pageTitle . ' - ' . get_setting('site_name', 'Urji Beri School'),
    'description' => truncate($galleryDescription, 160),
    'keywords' => 'Urji Beri School gallery, school photos, ' . ($currentCategory ? $currentCategory['name'] . ', ' : '') . 'Alemgena school, student activities, school events Ethiopia',
    'image' => branding_url('site_og_image'),
    'type' => 'website',
    'canonical' => route_url('gallery', $categorySlug ? ['category' => $categorySlug] : []),
];

// First page of images (15 per page, newest first)
$galleryData = fetch_gallery_images($categorySlug ?: null, 1, GALLERY_PER_PAGE);
$images = $galleryData['images'];
$pagination = $galleryData['pagination'];

// Generate Gallery Schema for SEO (first page only)
$gallerySchema = generate_gallery_schema($images, $currentCategory ? $currentCategory['name'] : 'Photo Gallery');

// Generate Breadcrumb Schema
$breadcrumbs = ['Home' => route_url('home'), 'Gallery' => route_url('gallery')];
if ($currentCategory) {
    $breadcrumbs[$currentCategory['name']] = route_url('gallery', ['category' => $currentCategory['slug']]);
}
$breadcrumbSchema = generate_breadcrumb_schema($breadcrumbs);

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
                <h1 class="page-title">Photo Gallery</h1>
                <nav class="breadcrumb">
                    <a href="<?php echo route_url('home'); ?>">Home</a>
                    <span class="breadcrumb-separator">/</span>
                    <span>Gallery</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="section" id="gallery-section"
             data-gallery-category="<?php echo e($categorySlug); ?>"
             data-api-url="<?php echo e(url('api/gallery')); ?>">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Moments</h2>
                <p class="section-subtitle">Explore the vibrant life at Urji Beri School through our photo gallery</p>
            </div>
            
            <!-- Category Filter -->
            <div class="gallery-filter">
                <a href="<?php echo route_url('gallery'); ?>" class="filter-btn <?php echo !$categorySlug ? 'active' : ''; ?>">All</a>
                <?php foreach ($categories as $category): ?>
                    <a href="<?php echo route_url('gallery', ['category' => $category['slug']]); ?>"
                       class="filter-btn <?php echo $categorySlug === $category['slug'] ? 'active' : ''; ?>">
                        <?php echo e($category['name']); ?>
                        <small>(<?php echo $category['image_count']; ?>)</small>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Gallery Grid -->
            <div id="galleryGridWrapper">
                <?php if (!empty($images)): ?>
                    <div class="gallery-grid anim-grid anim-grid-3d" id="galleryGrid">
                        <?php echo render_gallery_grid($images); ?>
                    </div>
                    <div id="galleryPagination">
                        <?php echo render_gallery_pagination($pagination, $categorySlug); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12" id="galleryEmptyState">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="1.5" style="margin: 0 auto var(--spacing-4);">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <h3 style="color: var(--gray-500);">No Images Found</h3>
                        <p style="color: var(--gray-400);">There are no images in this category yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Want to See More?</h2>
                <p class="cta-text">Follow us on Facebook for more photos and updates from Urji Beri School.</p>
                <a href="<?php echo e(get_setting('facebook_url', '#')); ?>" target="_blank" class="btn btn-primary btn-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="margin-right: var(--spacing-2);">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    Follow Us on Facebook
                </a>
            </div>
        </div>
    </section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
