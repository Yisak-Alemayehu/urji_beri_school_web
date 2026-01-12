<?php
/**
 * Blog / News Page
 * Urji Beri School Website
 * With Full SEO Implementation
 */

require_once __DIR__ . '/config/config.php';

$db = Database::getInstance();

// Get current page
$currentPageNum = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Get selected category
$categorySlug = isset($_GET['category']) ? clean_input($_GET['category']) : '';

// Get search query
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';

// Get all blog categories
$categories = $db->fetchAll(
    "SELECT bc.*, COUNT(bp.id) as post_count 
     FROM blog_categories bc
     LEFT JOIN blog_posts bp ON bc.id = bp.category_id AND bp.is_published = 1
     WHERE bc.is_active = 1
     GROUP BY bc.id
     ORDER BY bc.sort_order ASC, bc.name ASC"
);

// Get current category for SEO
$currentCategory = null;
if ($categorySlug) {
    foreach ($categories as $cat) {
        if ($cat['slug'] === $categorySlug) {
            $currentCategory = $cat;
            break;
        }
    }
}

// Set page title for SEO
$pageTitle = 'News & Events';
if ($currentCategory) {
    $pageTitle = $currentCategory['name'] . ' - News';
} elseif ($search) {
    $pageTitle = 'Search: ' . $search . ' - News';
}

// SEO Configuration
$newsDescription = $currentCategory 
    ? 'Read the latest ' . $currentCategory['name'] . ' from Urji Beri School. ' . ($currentCategory['description'] ?? 'Stay updated with school news.')
    : 'Stay updated with the latest news, events, and announcements from Urji Beri School in Alemgena, Oromia.';

$pageSeo = [
    'title' => $pageTitle . ' - ' . get_setting('site_name', 'Urji Beri School'),
    'description' => truncate($newsDescription, 160),
    'keywords' => 'Urji Beri School news, school events, ' . ($currentCategory ? $currentCategory['name'] . ', ' : '') . 'Alemgena education news, school announcements Ethiopia',
    'type' => 'website'
];

// Breadcrumb Schema
$breadcrumbs = ['Home' => SITE_URL, 'News & Events' => SITE_URL . '/blog.php'];
if ($currentCategory) {
    $breadcrumbs[$currentCategory['name']] = SITE_URL . '/blog.php?category=' . $currentCategory['slug'];
}
$breadcrumbSchema = generate_breadcrumb_schema($breadcrumbs);

// Build count query
$countSql = "SELECT COUNT(*) as total FROM blog_posts bp
             JOIN blog_categories bc ON bp.category_id = bc.id
             WHERE bp.is_published = 1 AND bc.is_active = 1";
$params = [];

if ($categorySlug) {
    $countSql .= " AND bc.slug = ?";
    $params[] = $categorySlug;
}

if ($search) {
    $countSql .= " AND (bp.title LIKE ? OR bp.content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$totalCount = $db->fetch($countSql, $params)['total'];

// Calculate pagination
$baseUrl = SITE_URL . '/blog.php';
if ($categorySlug) $baseUrl .= '?category=' . $categorySlug;
elseif ($search) $baseUrl .= '?search=' . urlencode($search);

$pagination = paginate($totalCount, $currentPageNum, POSTS_PER_PAGE, $baseUrl);

// Build posts query
$sql = "SELECT bp.*, bc.name as category_name, bc.slug as category_slug, u.full_name as author_name
        FROM blog_posts bp
        JOIN blog_categories bc ON bp.category_id = bc.id
        JOIN users u ON bp.author_id = u.id
        WHERE bp.is_published = 1 AND bc.is_active = 1";
$params = [];

if ($categorySlug) {
    $sql .= " AND bc.slug = ?";
    $params[] = $categorySlug;
}

if ($search) {
    $sql .= " AND (bp.title LIKE ? OR bp.content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY bp.published_at DESC LIMIT ? OFFSET ?";
$params[] = POSTS_PER_PAGE;
$params[] = $pagination['offset'];

$posts = $db->fetchAll($sql, $params);

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
                <h1 class="page-title">News & Events</h1>
                <nav class="breadcrumb">
                    <a href="<?php echo SITE_URL; ?>">Home</a>
                    <span class="breadcrumb-separator">/</span>
                    <span>News</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="section">
        <div class="container">
            <!-- Search and Filter -->
            <div class="d-flex justify-between align-center gap-4 mb-8" style="flex-wrap: wrap;">
                <!-- Search Form -->
                <form action="<?php echo SITE_URL; ?>/blog.php" method="GET" class="d-flex gap-4" style="flex: 1; max-width: 400px;">
                    <input type="text" name="search" class="form-control" placeholder="Search articles..." value="<?php echo e($search); ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
                
                <!-- Category Filter -->
                <div class="gallery-filter" style="margin-bottom: 0;">
                    <a href="<?php echo SITE_URL; ?>/blog.php" class="filter-btn <?php echo !$categorySlug && !$search ? 'active' : ''; ?>">All</a>
                    <?php foreach ($categories as $category): ?>
                        <a href="<?php echo SITE_URL; ?>/blog.php?category=<?php echo e($category['slug']); ?>" 
                           class="filter-btn <?php echo $categorySlug === $category['slug'] ? 'active' : ''; ?>">
                            <?php echo e($category['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <?php if ($search): ?>
                <div class="mb-6">
                    <p class="text-muted">
                        Search results for "<?php echo e($search); ?>" 
                        <a href="<?php echo SITE_URL; ?>/blog.php" style="margin-left: var(--spacing-2);">Clear search</a>
                    </p>
                </div>
            <?php endif; ?>
            
            <!-- Blog Grid -->
            <?php if (!empty($posts)): ?>
                <div class="grid grid-3">
                    <?php foreach ($posts as $post): ?>
                        <article class="glass-card blog-card">
                            <div class="blog-card-image">
                                <?php if ($post['featured_image']): ?>
                                    <img src="<?php echo upload_url($post['featured_image'], 'blog'); ?>" alt="<?php echo e($post['title']); ?>">
                                <?php else: ?>
                                    <img src="<?php echo asset_url('images/blog-placeholder.jpg'); ?>" alt="<?php echo e($post['title']); ?>">
                                <?php endif; ?>
                                <span class="blog-card-category"><?php echo e($post['category_name']); ?></span>
                            </div>
                            <div class="blog-card-content">
                                <div class="blog-card-meta">
                                    <span><?php echo format_date($post['published_at']); ?></span>
                                    <span>By <?php echo e($post['author_name']); ?></span>
                                </div>
                                <h3 class="blog-card-title">
                                    <a href="<?php echo SITE_URL; ?>/blog-detail.php?slug=<?php echo e($post['slug']); ?>"><?php echo e($post['title']); ?></a>
                                </h3>
                                <p class="blog-card-excerpt"><?php echo e(truncate($post['excerpt'] ?: strip_tags($post['content']))); ?></p>
                                <a href="<?php echo SITE_URL; ?>/blog-detail.php?slug=<?php echo e($post['slug']); ?>" class="blog-card-link">
                                    Read More
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                        <polyline points="12 5 19 12 12 19"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php echo render_pagination($pagination); ?>
            <?php else: ?>
                <div class="text-center py-12">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="1.5" style="margin: 0 auto var(--spacing-4);">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <h3 style="color: var(--gray-500);">No Articles Found</h3>
                    <p style="color: var(--gray-400);">
                        <?php if ($search): ?>
                            No articles match your search query.
                        <?php elseif ($categorySlug): ?>
                            There are no articles in this category yet.
                        <?php else: ?>
                            Check back soon for new articles!
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
