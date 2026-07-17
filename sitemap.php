<?php
/**
 * Dynamic XML Sitemap Generator
 * Urji Beri School Website
 * 
 * This file generates an XML sitemap for better SEO
 * Access at: yoursite.com/sitemap.php
 */

require_once __DIR__ . '/config/config.php';

$db = Database::getInstance();

// Set XML header
header('Content-Type: application/xml; charset=utf-8');

// Start XML
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
    
    <!-- Homepage -->
    <url>
        <loc><?php echo SITE_URL; ?>/</loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    
    <!-- Static Pages -->
    <url>
        <loc><?php echo route_url('about'); ?></loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.9</priority>
    </url>
    
    <url>
        <loc><?php echo route_url('director'); ?></loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    
    <url>
        <loc><?php echo route_url('contact'); ?></loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    
    <url>
        <loc><?php echo route_url('gallery'); ?></loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    
    <url>
        <loc><?php echo route_url('blog'); ?></loc>
        <lastmod><?php echo date('Y-m-d'); ?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    
    <!-- Blog Categories -->
    <?php
    $blogCategories = $db->fetchAll(
        "SELECT slug, updated_at FROM blog_categories WHERE is_active = 1 ORDER BY sort_order ASC"
    );
    foreach ($blogCategories as $category):
    ?>
    <url>
        <loc><?php echo route_url('blog', ['category' => $category['slug']]); ?></loc>
        <lastmod><?php echo date('Y-m-d', strtotime($category['updated_at'])); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>
    
    <!-- Blog Posts -->
    <?php
    $blogPosts = $db->fetchAll(
        "SELECT bp.slug, bp.title, bp.updated_at, bp.featured_image, bc.name as category_name
         FROM blog_posts bp
         JOIN blog_categories bc ON bp.category_id = bc.id
         WHERE bp.is_published = 1 AND bc.is_active = 1
         ORDER BY bp.published_at DESC"
    );
    foreach ($blogPosts as $post):
    ?>
    <url>
        <loc><?php echo route_url('blog-detail', ['slug' => $post['slug']]); ?></loc>
        <lastmod><?php echo date('Y-m-d', strtotime($post['updated_at'])); ?></lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
        <?php if ($post['featured_image']): ?>
        <image:image>
            <image:loc><?php echo SITE_URL; ?>/uploads/blog/<?php echo e($post['featured_image']); ?></image:loc>
            <image:title><?php echo e($post['title']); ?></image:title>
            <image:caption><?php echo e($post['title']); ?> - <?php echo e($post['category_name']); ?></image:caption>
        </image:image>
        <?php endif; ?>
        <news:news>
            <news:publication>
                <news:name><?php echo e(get_setting('site_name', 'Urji Beri School')); ?></news:name>
                <news:language>en</news:language>
            </news:publication>
            <news:genres>Blog, PressRelease</news:genres>
            <news:publication_date><?php echo date('Y-m-d', strtotime($post['updated_at'])); ?></news:publication_date>
            <news:title><?php echo e($post['title']); ?></news:title>
            <news:keywords><?php echo e($post['category_name']); ?>, school news</news:keywords>
        </news:news>
    </url>
    <?php endforeach; ?>
    
    <!-- Gallery Categories -->
    <?php
    $galleryCategories = $db->fetchAll(
        "SELECT slug, updated_at FROM gallery_categories WHERE is_active = 1 ORDER BY sort_order ASC"
    );
    foreach ($galleryCategories as $category):
    ?>
    <url>
        <loc><?php echo route_url('gallery', ['category' => $category['slug']]); ?></loc>
        <lastmod><?php echo date('Y-m-d', strtotime($category['updated_at'])); ?></lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <?php endforeach; ?>
    
    <!-- Gallery Images (with image sitemap extension) -->
    <?php
    $galleryImages = $db->fetchAll(
        "SELECT gi.filename, gi.caption, gi.alt_text, gi.updated_at, gc.name as category_name, gc.slug as category_slug
         FROM gallery_images gi
         JOIN gallery_categories gc ON gi.category_id = gc.id
         WHERE gi.is_active = 1 AND gc.is_active = 1
         ORDER BY gi.created_at DESC
         LIMIT 1000"
    );
    
    // Group images by category for image sitemap
    $imagesByCategory = [];
    foreach ($galleryImages as $image) {
        $catSlug = $image['category_slug'];
        if (!isset($imagesByCategory[$catSlug])) {
            $imagesByCategory[$catSlug] = [
                'name' => $image['category_name'],
                'images' => []
            ];
        }
        $imagesByCategory[$catSlug]['images'][] = $image;
    }
    
    foreach ($imagesByCategory as $catSlug => $catData):
    ?>
    <url>
        <loc><?php echo route_url('gallery', ['category' => $catSlug]); ?></loc>
        <?php foreach (array_slice($catData['images'], 0, 50) as $image): ?>
        <image:image>
            <image:loc><?php echo SITE_URL; ?>/uploads/gallery/<?php echo e($image['filename']); ?></image:loc>
            <image:title><?php echo e($image['caption'] ?: $image['category_name'] . ' Photo'); ?></image:title>
            <image:caption><?php echo e($image['alt_text'] ?: $image['caption'] ?: 'Photo from ' . $image['category_name']); ?></image:caption>
        </image:image>
        <?php endforeach; ?>
    </url>
    <?php endforeach; ?>
    
</urlset>
