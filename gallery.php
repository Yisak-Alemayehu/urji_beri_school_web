<?php
/**
 * Gallery Page
 * Urji Beri School Website
 * With Full SEO Implementation
 */

require_once __DIR__ . '/config/config.php';

$db = Database::getInstance();

// Get selected category
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
    'image' => $currentCategory && $currentCategory['cover_image'] 
        ? upload_url('gallery/' . $currentCategory['cover_image']) 
        : asset_url('images/og-image.jpg'),
    'type' => 'website'
];

// Build query based on category filter
$sql = "SELECT gi.*, gc.name as category_name, gc.slug as category_slug
        FROM gallery_images gi
        JOIN gallery_categories gc ON gi.category_id = gc.id
        WHERE gi.is_active = 1 AND gc.is_active = 1";
$params = [];

if ($categorySlug) {
    $sql .= " AND gc.slug = ?";
    $params[] = $categorySlug;
}

$sql .= " ORDER BY gi.created_at DESC";

$images = $db->fetchAll($sql, $params);

// Generate Gallery Schema for SEO
$gallerySchema = generate_gallery_schema($images, $currentCategory ? $currentCategory['name'] : 'Photo Gallery');

// Generate Breadcrumb Schema
$breadcrumbs = ['Home' => SITE_URL, 'Gallery' => SITE_URL . '/gallery.php'];
if ($currentCategory) {
    $breadcrumbs[$currentCategory['name']] = SITE_URL . '/gallery.php?category=' . $currentCategory['slug'];
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
                    <a href="<?php echo SITE_URL; ?>">Home</a>
                    <span class="breadcrumb-separator">/</span>
                    <span>Gallery</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Our Moments</h2>
                <p class="section-subtitle">Explore the vibrant life at Urji Beri School through our photo gallery</p>
            </div>
            
            <!-- Category Filter -->
            <div class="gallery-filter">
                <a href="<?php echo SITE_URL; ?>/gallery.php" class="filter-btn <?php echo !$categorySlug ? 'active' : ''; ?>">All</a>
                <?php foreach ($categories as $category): ?>
                    <a href="<?php echo SITE_URL; ?>/gallery.php?category=<?php echo e($category['slug']); ?>" 
                       class="filter-btn <?php echo $categorySlug === $category['slug'] ? 'active' : ''; ?>">
                        <?php echo e($category['name']); ?>
                        <small>(<?php echo $category['image_count']; ?>)</small>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <!-- Gallery Grid -->
            <?php if (!empty($images)): ?>
                <div class="gallery-grid">
                    <?php foreach ($images as $image): ?>
                        <div class="gallery-item" data-src="<?php echo upload_url($image['filename'], 'gallery'); ?>">
                            <img src="<?php echo upload_url($image['filename'], 'gallery'); ?>" 
                                 alt="<?php echo e($image['alt_text'] ?: $image['caption'] ?: $image['category_name']); ?>"
                                 loading="lazy">
                            <div class="gallery-item-overlay">
                                <div class="gallery-item-caption">
                                    <?php if ($image['caption']): ?>
                                        <p><?php echo e($image['caption']); ?></p>
                                    <?php endif; ?>
                                    <small><?php echo e($image['category_name']); ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
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
