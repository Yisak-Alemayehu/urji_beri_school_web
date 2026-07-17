<?php
/**
 * Gallery API — paginated images (AJAX)
 */

require_once dirname(__DIR__) . '/config/config.php';

header('Content-Type: application/json; charset=utf-8');

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$categorySlug = isset($_GET['category']) ? clean_input($_GET['category']) : '';

$result = fetch_gallery_images($categorySlug ?: null, $page, GALLERY_PER_PAGE);

$payload = [
    'success' => true,
    'images' => array_map(static function (array $image) {
        return [
            'id' => (int) $image['id'],
            'src' => upload_url($image['filename'], 'gallery'),
            'alt' => $image['alt_text'] ?: $image['caption'] ?: $image['category_name'],
            'caption' => $image['caption'] ?: '',
            'category' => $image['category_name'],
            'category_slug' => $image['category_slug'],
        ];
    }, $result['images']),
    'pagination' => $result['pagination'],
    'html' => render_gallery_grid($result['images']),
    'pagination_html' => render_gallery_pagination($result['pagination'], $categorySlug),
];

echo json_encode($payload);
exit;
