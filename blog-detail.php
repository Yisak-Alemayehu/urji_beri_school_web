<?php
/**
 * Blog Detail Page
 * Urji Beri School Website
 * With Full SEO Implementation
 */

require_once __DIR__ . '/config/config.php';

$db = Database::getInstance();

// Get post by slug
$slug = isset($_GET['slug']) ? clean_input($_GET['slug']) : '';

if (!$slug) {
    redirect(route_url('blog'));
}

$post = $db->fetch(
    "SELECT bp.*, bc.name as category_name, bc.slug as category_slug, u.full_name as author_name
     FROM blog_posts bp
     JOIN blog_categories bc ON bp.category_id = bc.id
     JOIN users u ON bp.author_id = u.id
     WHERE bp.slug = ? AND bp.is_published = 1",
    [$slug]
);

if (!$post) {
    redirect(route_url('blog'));
}

// Update view count
$db->query("UPDATE blog_posts SET views = views + 1 WHERE id = ?", [$post['id']]);

$pageTitle = $post['title'];

// SEO Configuration for this blog post
$postDescription = $post['excerpt'] ? $post['excerpt'] : truncate(strip_tags($post['content']), 160);
$postImage = $post['featured_image'] ? upload_url('blog/' . $post['featured_image']) : asset_url('images/og-image.jpg');
$postUrl = route_url('blog-detail', ['slug' => $post['slug']]);

$pageSeo = [
    'title' => $post['title'] . ' - ' . get_setting('site_name', 'Urji Beri School'),
    'description' => $postDescription,
    'keywords' => $post['category_name'] . ', ' . get_setting('site_name') . ', school news, ' . implode(', ', array_slice(explode(' ', $post['title']), 0, 5)),
    'image' => $postImage,
    'url' => $postUrl,
    'type' => 'article',
    'published_time' => date('c', strtotime($post['published_at'])),
    'modified_time' => date('c', strtotime($post['updated_at'] ?? $post['published_at'])),
    'article_section' => $post['category_name'],
    'article_tag' => $post['category_name'],
    'author' => $post['author_name']
];

// Generate Article Schema
$articleSchema = generate_article_schema($post);

// Generate Breadcrumb Schema
$breadcrumbSchema = generate_breadcrumb_schema([
    'Home' => route_url('home'),
    'News' => route_url('blog'),
    $post['title'] => $postUrl
]);

// Get related posts
$relatedPosts = $db->fetchAll(
    "SELECT bp.*, bc.name as category_name
     FROM blog_posts bp
     JOIN blog_categories bc ON bp.category_id = bc.id
     WHERE bp.is_published = 1 AND bp.category_id = ? AND bp.id != ?
     ORDER BY bp.published_at DESC
     LIMIT 3",
    [$post['category_id'], $post['id']]
);

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
                    <a href="<?php echo route_url('home'); ?>">Home</a>
                    <span class="breadcrumb-separator">/</span>
                    <a href="<?php echo route_url('blog'); ?>">News</a>
                    <span class="breadcrumb-separator">/</span>
                    <span><?php echo e(truncate($post['title'], 30)); ?></span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Blog Detail Section -->
    <section class="section">
        <div class="container">
            <article class="glass-card-solid">
                <!-- Blog Header -->
                <div class="blog-detail-header">
                    <span class="blog-detail-category"><?php echo e($post['category_name']); ?></span>
                    <h1 class="blog-detail-title"><?php echo e($post['title']); ?></h1>
                    <div class="blog-detail-meta">
                        <span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                            <?php echo format_date($post['published_at']); ?>
                        </span>
                        <span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <?php echo e($post['author_name']); ?>
                        </span>
                        <span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <?php echo number_format($post['views']); ?> views
                        </span>
                    </div>
                </div>
                
                <!-- Featured Image -->
                <?php if ($post['featured_image']): ?>
                    <div class="blog-detail-image">
                        <img src="<?php echo upload_url($post['featured_image'], 'blog'); ?>" alt="<?php echo e($post['title']); ?>">
                    </div>
                <?php endif; ?>
                
                <!-- Blog Content -->
                <div class="blog-detail-content">
                    <?php echo $post['content']; ?>
                </div>
                
                <!-- Share Buttons -->
                <div class="text-center mt-8 pt-8" style="border-top: 1px solid var(--gray-200);">
                    <p class="text-muted mb-4">Share this article:</p>
                    <div class="btn-group justify-center">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($postUrl); ?>" 
                           target="_blank" class="btn btn-outline btn-sm">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($postUrl); ?>&text=<?php echo urlencode($post['title']); ?>" 
                           target="_blank" class="btn btn-outline btn-sm">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                            Twitter
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode($post['title'] . ' - ' . $postUrl); ?>" 
                           target="_blank" class="btn btn-outline btn-sm">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            WhatsApp
                        </a>
                    </div>
                </div>
            </article>
            
            <!-- Related Posts -->
            <?php if (!empty($relatedPosts)): ?>
                <div class="mt-12">
                    <h2 class="section-title text-center mb-8">Related Articles</h2>
                    <div class="grid grid-3">
                        <?php foreach ($relatedPosts as $relatedPost): ?>
                            <article class="glass-card blog-card">
                                <div class="blog-card-image">
                                    <?php if ($relatedPost['featured_image']): ?>
                                        <img src="<?php echo upload_url($relatedPost['featured_image'], 'blog'); ?>" alt="<?php echo e($relatedPost['title']); ?>">
                                    <?php else: ?>
                                        <img src="<?php echo asset_url('images/blog-placeholder.jpg'); ?>" alt="<?php echo e($relatedPost['title']); ?>">
                                    <?php endif; ?>
                                    <span class="blog-card-category"><?php echo e($relatedPost['category_name']); ?></span>
                                </div>
                                <div class="blog-card-content">
                                    <div class="blog-card-meta">
                                        <span><?php echo format_date($relatedPost['published_at']); ?></span>
                                    </div>
                                    <h3 class="blog-card-title">
                                        <a href="<?php echo route_url('blog-detail', ['slug' => $relatedPost['slug']]); ?>"><?php echo e($relatedPost['title']); ?></a>
                                    </h3>
                                    <a href="<?php echo route_url('blog-detail', ['slug' => $relatedPost['slug']]); ?>" class="blog-card-link">
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
                </div>
            <?php endif; ?>
            
            <!-- Back to Blog -->
            <div class="text-center mt-8">
                <a href="<?php echo route_url('blog'); ?>" class="btn btn-outline">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: var(--spacing-2);">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Back to All News
                </a>
            </div>
        </div>
    </section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
