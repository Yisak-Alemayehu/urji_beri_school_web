<?php
/**
 * Helper Functions
 * Urji Beri School Website
 */

// Prevent direct access
if (!defined('BASE_PATH')) {
    exit('Direct access not allowed');
}

/**
 * Sanitize output for HTML display
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token field
 */
function csrf_field() {
    return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $_SESSION[CSRF_TOKEN_NAME] . '">';
}

/**
 * Verify CSRF token
 */
function verify_csrf($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Set flash message
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Display flash message HTML
 */
function display_flash() {
    $flash = get_flash();
    if ($flash) {
        $type = $flash['type'];
        $message = e($flash['message']);
        echo "<div class='alert alert-{$type}'>{$message}</div>";
    }
}

/**
 * Internal settings cache reference (shared across helpers)
 */
function &settings_cache() {
    static $settings = null;
    return $settings;
}

/**
 * Get site setting from database
 */
function get_setting($key, $default = '') {
    $settings = &settings_cache();
    
    if ($settings === null) {
        $db = Database::getInstance();
        $results = $db->fetchAll("SELECT setting_key, setting_value FROM site_settings");
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $settings[$key] ?? $default;
}

/**
 * Clear cached settings so subsequent get_setting() calls reload from DB
 */
function clear_settings_cache() {
    $settings = &settings_cache();
    $settings = null;
}

/**
 * Update site setting
 */
function update_setting($key, $value) {
    $db = Database::getInstance();
    $result = $db->query(
        "UPDATE site_settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?",
        [$value, $key]
    );
    if ($result->rowCount() === 0) {
        $db->query(
            "INSERT INTO site_settings (setting_key, setting_value, setting_type, setting_group, updated_at) VALUES (?, ?, 'text', 'general', NOW())",
            [$key, $value]
        );
    }

    $cache = &settings_cache();
    if ($cache !== null) {
        $cache[$key] = $value;
    }
}

/**
 * Generate URL-friendly slug
 */
function create_slug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Generate unique slug for blog posts
 */
function unique_slug($title, $table = 'blog_posts', $excludeId = null) {
    $db = Database::getInstance();
    $slug = create_slug($title);
    $originalSlug = $slug;
    $counter = 1;
    
    while (true) {
        $sql = "SELECT id FROM {$table} WHERE slug = ?";
        $params = [$slug];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $exists = $db->fetch($sql, $params);
        
        if (!$exists) {
            return $slug;
        }
        
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
}

/**
 * Format date for display
 */
function format_date($date, $format = 'M j, Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Format date with time
 */
function format_datetime($date, $format = 'M j, Y g:i A') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Time ago format
 */
function time_ago($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    if ($diff < 2592000) return floor($diff / 604800) . ' weeks ago';
    
    return format_date($datetime);
}

/**
 * Truncate text to specified length
 */
function truncate($text, $length = 150, $suffix = '...') {
    $text = strip_tags($text);
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . $suffix;
}

/**
 * Handle file upload
 */
function upload_file($file, $directory, $allowedTypes = null) {
    if ($allowedTypes === null) {
        $allowedTypes = ALLOWED_IMAGE_TYPES;
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload failed'];
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'File too large (max 5MB)'];
    }
    
    // Check file type using multiple methods
    $mimeType = '';
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
    } elseif (function_exists('mime_content_type')) {
        $mimeType = mime_content_type($file['tmp_name']);
    } else {
        // Fallback: check by extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png', 'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
    }
    
    if (!in_array($mimeType, $allowedTypes)) {
        // ICO files are often reported as octet-stream
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $icoAllowed = in_array('image/x-icon', $allowedTypes, true)
            || in_array('image/vnd.microsoft.icon', $allowedTypes, true);
        if (!($icoAllowed && $ext === 'ico' && in_array($mimeType, ['application/octet-stream', 'image/x-icon', 'image/vnd.microsoft.icon'], true))) {
            return ['success' => false, 'error' => 'Invalid file type'];
        }
    }
    
    // Get extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ALLOWED_IMAGE_EXTENSIONS;
    if (in_array('image/x-icon', $allowedTypes, true) || in_array('image/vnd.microsoft.icon', $allowedTypes, true)) {
        $allowedExtensions = array_merge($allowedExtensions, ['ico']);
    }
    if (!in_array($extension, $allowedExtensions, true)) {
        return ['success' => false, 'error' => 'Invalid file extension'];
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $uploadPath = UPLOADS_PATH . '/' . $directory;
    
    // Create directory if not exists
    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
    }
    
    $fullPath = $uploadPath . '/' . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $fullPath)) {
        return [
            'success' => true,
            'filename' => $filename,
            'original_name' => $file['name'],
            'size' => $file['size']
        ];
    }
    
    return ['success' => false, 'error' => 'Failed to save file'];
}

/**
 * Normalize a single or multi-file $_FILES entry into an array of file arrays
 */
function normalize_uploaded_files($fieldName) {
    $files = [];

    if (!isset($_FILES[$fieldName])) {
        return $files;
    }

    $file = $_FILES[$fieldName];

    if (!is_array($file['name'])) {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $files[] = $file;
        }
        return $files;
    }

    $count = count($file['name']);
    for ($i = 0; $i < $count; $i++) {
        if (($file['error'][$i] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $files[] = [
            'name' => $file['name'][$i],
            'type' => $file['type'][$i],
            'tmp_name' => $file['tmp_name'][$i],
            'error' => $file['error'][$i],
            'size' => $file['size'][$i],
        ];
    }

    return $files;
}

/**
 * Delete uploaded file
 */
function delete_file($filename, $directory) {
    $path = UPLOADS_PATH . '/' . $directory . '/' . $filename;
    if (file_exists($path)) {
        return unlink($path);
    }
    return false;
}

/**
 * Build a clean site URL
 */
function url($path = '', $params = []) {
    $path = trim((string) $path, '/');

    $url = $path === '' ? rtrim(SITE_URL, '/') . '/' : rtrim(SITE_URL, '/') . '/' . $path;

    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }

    return $url;
}

/**
 * Named route helper for clean URLs
 */
function route_url(string $name, array $params = []): string {
    switch ($name) {
        case 'home':
        case 'index':
            return url('');
        case 'about':
            return url('about');
        case 'contact':
            return url('contact');
        case 'director':
            return url('director');
        case 'gallery':
            if (!empty($params['category'])) {
                return url('gallery/' . rawurlencode($params['category']));
            }
            return url('gallery');
        case 'blog':
            if (!empty($params['category'])) {
                return url('blog/category/' . rawurlencode($params['category']));
            }
            return url('blog');
        case 'blog-detail':
            return url('blog/' . rawurlencode($params['slug'] ?? ''));
        case 'sitemap':
            return url('sitemap.xml');
        default:
            return url($name, $params);
    }
}

/**
 * Fetch paginated gallery images (newest first)
 */
function fetch_gallery_images(?string $categorySlug = null, int $page = 1, ?int $perPage = null): array {
    $db = Database::getInstance();
    $perPage = $perPage ?? GALLERY_PER_PAGE;
    $page = max(1, $page);

    $where = 'gi.is_active = 1 AND gc.is_active = 1';
    $params = [];

    if ($categorySlug) {
        $where .= ' AND gc.slug = ?';
        $params[] = $categorySlug;
    }

    $total = (int) $db->fetch(
        "SELECT COUNT(*) as total
         FROM gallery_images gi
         JOIN gallery_categories gc ON gi.category_id = gc.id
         WHERE {$where}",
        $params
    )['total'];

    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $perPage;

    $images = $db->fetchAll(
        "SELECT gi.*, gc.name as category_name, gc.slug as category_slug
         FROM gallery_images gi
         JOIN gallery_categories gc ON gi.category_id = gc.id
         WHERE {$where}
         ORDER BY gi.created_at DESC, gi.id DESC
         LIMIT {$perPage} OFFSET {$offset}",
        $params
    );

    return [
        'images' => $images,
        'pagination' => [
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages,
        ],
    ];
}

/**
 * Render gallery grid HTML
 */
function render_gallery_grid(array $images): string {
    if (empty($images)) {
        return '';
    }

    ob_start();
    foreach ($images as $image) {
        echo render_gallery_item($image);
    }
    return ob_get_clean();
}

/**
 * Render a single gallery item
 */
function render_gallery_item(array $image): string {
    $src = e(upload_url($image['filename'], 'gallery'));
    $alt = e($image['alt_text'] ?: $image['caption'] ?: $image['category_name']);
    $caption = e($image['caption'] ?: $image['category_name']);
    $category = e($image['category_name']);
    $captionText = $image['caption'] ? '<p>' . e($image['caption']) . '</p>' : '';

    return <<<HTML
<div class="gallery-item"
     data-src="{$src}"
     data-caption="{$caption}"
     data-category="{$category}"
     role="button"
     tabindex="0"
     aria-label="{$caption}">
    <img src="{$src}" alt="{$alt}" loading="lazy">
    <div class="gallery-item-overlay">
        <div class="gallery-item-caption">
            {$captionText}
            <small>{$category}</small>
        </div>
    </div>
</div>
HTML;
}

/**
 * Render AJAX gallery pagination controls
 */
function render_gallery_pagination(array $pagination, string $categorySlug = ''): string {
    if ($pagination['total_pages'] <= 1) {
        return '';
    }

    $html = '<div class="gallery-pagination pagination" data-category="' . e($categorySlug) . '" data-current-page="' . (int) $pagination['page'] . '" data-total-pages="' . (int) $pagination['total_pages'] . '">';
    $html .= '<button type="button" class="pagination-btn gallery-page-btn" data-page="prev" ' . ($pagination['has_prev'] ? '' : 'disabled') . '>&laquo; Previous</button>';
    $html .= '<div class="pagination-numbers">';

    for ($i = 1; $i <= $pagination['total_pages']; $i++) {
        $active = $i === (int) $pagination['page'] ? 'active' : '';
        $html .= '<button type="button" class="pagination-num gallery-page-btn ' . $active . '" data-page="' . $i . '">' . $i . '</button>';
    }

    $html .= '</div>';
    $html .= '<button type="button" class="pagination-btn gallery-page-btn" data-page="next" ' . ($pagination['has_next'] ? '' : 'disabled') . '>Next &raquo;</button>';
    $html .= '<p class="gallery-pagination-status">Showing page ' . (int) $pagination['page'] . ' of ' . (int) $pagination['total_pages'] . ' · ' . (int) $pagination['total'] . ' photos</p>';
    $html .= '</div>';

    return $html;
}

/**
 * Get upload URL
 */
function upload_url($filename, $directory) {
    return SITE_URL . '/uploads/' . $directory . '/' . $filename;
}

/**
 * Get asset URL
 */
function asset_url($path) {
    return SITE_URL . '/assets/' . $path;
}

/**
 * Cache-busting version for a local asset/upload path
 */
function asset_version($absolutePath) {
    return is_file($absolutePath) ? (string) filemtime($absolutePath) : (string) time();
}

/**
 * Branding defaults mapped to assets/images files
 */
function branding_defaults() {
    return [
        'site_logo' => 'images/logo.png',
        'site_logo_white' => 'images/logo-white.png',
        'site_favicon' => 'images/favicon.ico',
        'site_favicon_32' => 'images/favicon-32x32.png',
        'site_favicon_16' => 'images/favicon-16x16.png',
        'site_apple_touch_icon' => 'images/apple-touch-icon.png',
        'site_og_image' => 'images/og-image.jpg',
        'site_icon_72' => 'images/icon-72.png',
        'site_icon_96' => 'images/icon-96.png',
        'site_icon_128' => 'images/icon-128.png',
        'site_icon_144' => 'images/icon-144.png',
        'site_icon_152' => 'images/icon-152.png',
        'site_icon_192' => 'images/icon-192.png',
        'site_icon_384' => 'images/icon-384.png',
        'site_icon_512' => 'images/icon-512.png',
    ];
}

/**
 * Resolve absolute filesystem path for a branding setting
 */
function branding_path($key) {
    $defaults = branding_defaults();
    $defaultRel = $defaults[$key] ?? null;
    $value = trim((string) get_setting($key, ''));

    if ($value !== '') {
        // Uploaded branding file
        $uploadPath = UPLOADS_PATH . '/branding/' . basename($value);
        if (is_file($uploadPath)) {
            return $uploadPath;
        }
        // Legacy: bare filename under assets/images
        $legacyPath = ASSETS_PATH . '/images/' . basename($value);
        if (is_file($legacyPath)) {
            return $legacyPath;
        }
    }

    if ($defaultRel) {
        $path = ASSETS_PATH . '/' . $defaultRel;
        if (is_file($path)) {
            return $path;
        }
    }

    return null;
}

/**
 * Public URL for a branding asset (logo, favicon, OG, PWA icons)
 */
function branding_url($key, $fallbackAsset = null) {
    $path = branding_path($key);
    if ($path) {
        $normalized = str_replace('\\', '/', $path);
        $uploadsRoot = str_replace('\\', '/', UPLOADS_PATH);
        if (str_starts_with($normalized, $uploadsRoot . '/')) {
            $url = upload_url(basename($path), 'branding');
        } else {
            $assetsRoot = str_replace('\\', '/', ASSETS_PATH);
            $rel = ltrim(substr($normalized, strlen($assetsRoot)), '/');
            $url = asset_url($rel);
        }
        return $url . '?v=' . asset_version($path);
    }

    $defaults = branding_defaults();
    $asset = $fallbackAsset ?: ($defaults[$key] ?? 'images/logo.png');
    return asset_url($asset);
}

/**
 * Allowed MIME types for branding uploads (includes ICO)
 */
function branding_allowed_mime_types() {
    return array_merge(ALLOWED_IMAGE_TYPES, [
        'image/x-icon',
        'image/vnd.microsoft.icon',
        'image/ico',
    ]);
}

/**
 * Load an image resource from a path for GD processing
 */
function branding_load_image($path) {
    if (!is_file($path) || !function_exists('imagecreatetruecolor')) {
        return null;
    }

    $info = @getimagesize($path);
    if (!$info) {
        return null;
    }

    return match ($info[2]) {
        IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
        IMAGETYPE_PNG => @imagecreatefrompng($path),
        IMAGETYPE_GIF => @imagecreatefromgif($path),
        IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
        default => null,
    };
}

/**
 * Resize source image to a square PNG and save under uploads/branding
 */
function branding_save_resized_png($sourcePath, $size, $filename) {
    $src = branding_load_image($sourcePath);
    if (!$src) {
        return null;
    }

    $w = imagesx($src);
    $h = imagesy($src);
    $out = imagecreatetruecolor($size, $size);
    imagealphablending($out, false);
    imagesavealpha($out, true);
    $transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
    imagefilledrectangle($out, 0, 0, $size, $size, $transparent);
    imagealphablending($out, true);
    imagecopyresampled($out, $src, 0, 0, 0, 0, $size, $size, $w, $h);

    $dir = UPLOADS_PATH . '/branding';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $full = $dir . '/' . $filename;
    imagepng($out, $full);
    imagedestroy($out);
    imagedestroy($src);

    return is_file($full) ? $filename : null;
}

/**
 * Generate derived favicon / PWA icons from a primary logo upload
 */
function generate_branding_derivatives($sourceFilename) {
    $sourcePath = UPLOADS_PATH . '/branding/' . $sourceFilename;
    if (!is_file($sourcePath)) {
        return [];
    }

    $prefix = pathinfo($sourceFilename, PATHINFO_FILENAME);
    $map = [
        'site_favicon_16' => 16,
        'site_favicon_32' => 32,
        'site_apple_touch_icon' => 180,
        'site_icon_72' => 72,
        'site_icon_96' => 96,
        'site_icon_128' => 128,
        'site_icon_144' => 144,
        'site_icon_152' => 152,
        'site_icon_192' => 192,
        'site_icon_384' => 384,
        'site_icon_512' => 512,
    ];

    $generated = [];
    foreach ($map as $key => $size) {
        $filename = $prefix . '_' . $size . '.png';
        $saved = branding_save_resized_png($sourcePath, $size, $filename);
        if ($saved) {
            $old = get_setting($key, '');
            if ($old && $old !== $saved && is_file(UPLOADS_PATH . '/branding/' . basename($old))) {
                @unlink(UPLOADS_PATH . '/branding/' . basename($old));
            }
            update_setting($key, $saved);
            $generated[$key] = $saved;
        }
    }

    // Prefer 32px PNG as favicon when no dedicated ICO uploaded
    if (!empty($generated['site_favicon_32'])) {
        $currentFavicon = get_setting('site_favicon', '');
        $faviconPath = $currentFavicon ? UPLOADS_PATH . '/branding/' . basename($currentFavicon) : '';
        if ($currentFavicon === '' || !is_file($faviconPath)) {
            update_setting('site_favicon', $generated['site_favicon_32']);
            $generated['site_favicon'] = $generated['site_favicon_32'];
        }
    }

    // OG image if missing
    $og = get_setting('site_og_image', '');
    if ($og === '' || !is_file(UPLOADS_PATH . '/branding/' . basename($og))) {
        $src = branding_load_image($sourcePath);
        if ($src) {
            $tw = 1200;
            $th = 630;
            $w = imagesx($src);
            $h = imagesy($src);
            $out = imagecreatetruecolor($tw, $th);
            $bg = imagecolorallocate($out, 30, 58, 138);
            imagefill($out, 0, 0, $bg);
            $scale = min(($tw * 0.45) / $w, ($th * 0.65) / $h);
            $nw = (int) ($w * $scale);
            $nh = (int) ($h * $scale);
            imagecopyresampled($out, $src, (int) (($tw - $nw) / 2), (int) (($th - $nh) / 2), 0, 0, $nw, $nh, $w, $h);
            $ogName = $prefix . '_og.jpg';
            imagejpeg($out, UPLOADS_PATH . '/branding/' . $ogName, 90);
            imagedestroy($out);
            imagedestroy($src);
            update_setting('site_og_image', $ogName);
            $generated['site_og_image'] = $ogName;
        }
    }

    return $generated;
}

/**
 * Store an uploaded branding file and return the stored filename
 */
function store_branding_upload(array $file, $settingKey, $oldFilename = '') {
    $result = upload_file($file, 'branding', branding_allowed_mime_types());
    if (!$result['success']) {
        return $result;
    }

    if ($oldFilename && is_file(UPLOADS_PATH . '/branding/' . basename($oldFilename))) {
        // Don't delete if another setting still points at the same file
        delete_file(basename($oldFilename), 'branding');
    }

    update_setting($settingKey, $result['filename']);

    if ($settingKey === 'site_logo') {
        generate_branding_derivatives($result['filename']);
    }

    return $result;
}

/**
 * Pagination helper
 */
function paginate($totalItems, $currentPage, $perPage, $baseUrl) {
    $totalPages = ceil($totalItems / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    
    return [
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'per_page' => $perPage,
        'offset' => ($currentPage - 1) * $perPage,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages,
        'prev_url' => $currentPage > 1 ? $baseUrl . '?page=' . ($currentPage - 1) : null,
        'next_url' => $currentPage < $totalPages ? $baseUrl . '?page=' . ($currentPage + 1) : null,
        'base_url' => $baseUrl
    ];
}

/**
 * Render pagination HTML
 */
function render_pagination($pagination) {
    if ($pagination['total_pages'] <= 1) return '';
    
    $html = '<div class="pagination">';
    
    // Previous button
    if ($pagination['has_prev']) {
        $html .= '<a href="' . $pagination['prev_url'] . '" class="pagination-btn">&laquo; Previous</a>';
    }
    
    // Page numbers
    $html .= '<div class="pagination-numbers">';
    for ($i = 1; $i <= $pagination['total_pages']; $i++) {
        $active = $i === $pagination['current_page'] ? 'active' : '';
        $url = $pagination['base_url'] . '?page=' . $i;
        $html .= '<a href="' . $url . '" class="pagination-num ' . $active . '">' . $i . '</a>';
    }
    $html .= '</div>';
    
    // Next button
    if ($pagination['has_next']) {
        $html .= '<a href="' . $pagination['next_url'] . '" class="pagination-btn">Next &raquo;</a>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Get client IP address
 */
function get_client_ip() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }
    return trim($ip);
}

/**
 * Validate email
 */
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone (basic)
 */
function is_valid_phone($phone) {
    return preg_match('/^[\+]?[0-9\-\s]{9,20}$/', $phone);
}

/**
 * Clean input
 */
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    return $data;
}

/**
 * Get values array (pipe-separated string)
 */
function get_values_array($key) {
    $value = get_setting($key);
    if (empty($value)) return [];
    return array_filter(array_map('trim', explode('|', $value)));
}

// =====================================================
// SEO HELPER FUNCTIONS
// =====================================================

/**
 * Generate SEO meta tags
 */
function generate_seo_meta($seo = []) {
    $siteName = get_setting('site_name', 'Urji Beri School');
    $siteUrl = SITE_URL;
    
    // Default values
    $defaults = [
        'title' => $siteName,
        'description' => get_setting('site_description', 'Urji Beri School - Quality preschool and elementary education in Alemgena, Oromia. Nurturing young minds for a brighter future.'),
        'keywords' => get_setting('site_keywords', 'Urji Beri School, elementary school, preschool, Alemgena, Oromia, education, Ethiopia, nursery school, kindergarten'),
        'image' => branding_url('site_og_image'),
        'url' => $siteUrl . $_SERVER['REQUEST_URI'],
        'type' => 'website',
        'locale' => 'en_US',
        'twitter_card' => 'summary_large_image',
        'author' => $siteName,
        'robots' => 'index, follow',
        'canonical' => null,
        'published_time' => null,
        'modified_time' => null,
        'article_section' => null,
        'article_tag' => null,
    ];
    
    $seo = array_merge($defaults, $seo);
    
    // Clean canonical URL (strip tracking params, keep meaningful query strings on blog)
    if (empty($seo['canonical'])) {
        $seo['canonical'] = get_canonical_url();
    }
    $seo['url'] = $seo['canonical'];
    
    // Build meta tags
    $meta = '';
    
    // Basic meta tags
    $meta .= '<meta name="description" content="' . e($seo['description']) . '">' . "\n";
    $meta .= '    <meta name="keywords" content="' . e($seo['keywords']) . '">' . "\n";
    $meta .= '    <meta name="author" content="' . e($seo['author']) . '">' . "\n";
    $meta .= '    <meta name="robots" content="' . e($seo['robots']) . '">' . "\n";
    
    // Canonical URL
    $canonical = $seo['canonical'] ?? $seo['url'];
    $meta .= '    <link rel="canonical" href="' . e($canonical) . '">' . "\n";
    
    // Open Graph meta tags
    $meta .= '    ' . "\n";
    $meta .= '    <!-- Open Graph / Facebook -->' . "\n";
    $meta .= '    <meta property="og:type" content="' . e($seo['type']) . '">' . "\n";
    $meta .= '    <meta property="og:url" content="' . e($seo['url']) . '">' . "\n";
    $meta .= '    <meta property="og:title" content="' . e($seo['title']) . '">' . "\n";
    $meta .= '    <meta property="og:description" content="' . e($seo['description']) . '">' . "\n";
    $meta .= '    <meta property="og:image" content="' . e($seo['image']) . '">' . "\n";
    $meta .= '    <meta property="og:image:alt" content="' . e($seo['image_alt'] ?? $seo['title']) . '">' . "\n";
    $meta .= '    <meta property="og:image:width" content="1200">' . "\n";
    $meta .= '    <meta property="og:image:height" content="630">' . "\n";
    $meta .= '    <meta property="og:site_name" content="' . e($siteName) . '">' . "\n";
    $meta .= '    <meta property="og:locale" content="' . e($seo['locale']) . '">' . "\n";
    
    // Article specific Open Graph tags
    if ($seo['type'] === 'article') {
        if ($seo['published_time']) {
            $meta .= '    <meta property="article:published_time" content="' . e($seo['published_time']) . '">' . "\n";
        }
        if ($seo['modified_time']) {
            $meta .= '    <meta property="article:modified_time" content="' . e($seo['modified_time']) . '">' . "\n";
        }
        if ($seo['article_section']) {
            $meta .= '    <meta property="article:section" content="' . e($seo['article_section']) . '">' . "\n";
        }
        if ($seo['article_tag']) {
            $meta .= '    <meta property="article:tag" content="' . e($seo['article_tag']) . '">' . "\n";
        }
    }
    
    // Twitter Card meta tags
    $meta .= '    ' . "\n";
    $meta .= '    <!-- Twitter Card -->' . "\n";
    $meta .= '    <meta name="twitter:card" content="' . e($seo['twitter_card']) . '">' . "\n";
    $meta .= '    <meta name="twitter:url" content="' . e($seo['url']) . '">' . "\n";
    $meta .= '    <meta name="twitter:title" content="' . e($seo['title']) . '">' . "\n";
    $meta .= '    <meta name="twitter:description" content="' . e($seo['description']) . '">' . "\n";
    $meta .= '    <meta name="twitter:image" content="' . e($seo['image']) . '">' . "\n";
    $meta .= '    <meta name="twitter:image:alt" content="' . e($seo['image_alt'] ?? $seo['title']) . '">' . "\n";
    
    $twitterSite = get_setting('social_twitter', '');
    if ($twitterSite) {
        $handle = strpos($twitterSite, 'twitter.com/') !== false
            ? '@' . trim(parse_url($twitterSite, PHP_URL_PATH), '/')
            : $twitterSite;
        $meta .= '    <meta name="twitter:site" content="' . e($handle) . '">' . "\n";
    }
    
    return $meta;
}

/**
 * Build a clean canonical URL for the current request
 */
function get_canonical_url() {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $path = rtrim($path, '/') ?: '/';
    $query = [];
    parse_str(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY) ?? '', $query);

    // Keep only SEO-meaningful query params on non-clean legacy URLs
    $allowed = ['page', 'search'];
    $filtered = array_intersect_key($query, array_flip($allowed));

    $url = rtrim(SITE_URL, '/') . ($path === '/' ? '/' : $path);
    if (!empty($filtered)) {
        $url .= '?' . http_build_query($filtered);
    }

    return $url;
}

/**
 * Search engine verification meta tags
 */
function generate_seo_verification_meta() {
    $meta = '';
    $google = trim(get_setting('google_site_verification', ''));
    $bing = trim(get_setting('bing_site_verification', ''));

    if ($google) {
        $meta .= '    <meta name="google-site-verification" content="' . e($google) . '">' . "\n";
    }
    if ($bing) {
        $meta .= '    <meta name="msvalidate.01" content="' . e($bing) . '">' . "\n";
    }

    return $meta;
}

/**
 * Generate JSON-LD structured data for Organization
 */
function generate_organization_schema() {
    $siteName = get_setting('site_name', 'Urji Beri School');
    $siteUrl = SITE_URL;
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'EducationalOrganization',
        '@id' => $siteUrl . '/#organization',
        'name' => $siteName,
        'alternateName' => 'Urji Beri',
        'url' => $siteUrl,
        'logo' => branding_url('site_logo'),
        'image' => branding_url('site_og_image'),
        'description' => get_setting('site_description', 'Quality preschool and elementary education in Alemgena, Oromia.'),
        'telephone' => get_setting('contact_phone', '+251-912-097-003'),
        'email' => get_setting('contact_email', 'office@urjiberischool.com'),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => get_setting('contact_address', 'Alemgena'),
            'addressLocality' => 'Alemgena',
            'addressRegion' => 'Oromia',
            'addressCountry' => 'ET'
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => get_setting('map_latitude', '8.9806'),
            'longitude' => get_setting('map_longitude', '38.7578')
        ],
        'sameAs' => array_filter([
            get_setting('social_facebook'),
            get_setting('social_instagram'),
            get_setting('social_youtube'),
            get_setting('social_telegram')
        ]),
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'telephone' => get_setting('contact_phone', '+251-912-097-003'),
            'contactType' => 'customer service',
            'availableLanguage' => ['English', 'Amharic']
        ]
    ];
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
}

/**
 * Generate JSON-LD for WebSite (enables sitelinks search)
 */
function generate_website_schema() {
    $siteName = get_setting('site_name', 'Urji Beri School');
    $siteUrl = SITE_URL;
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => $siteUrl . '/#website',
        'name' => $siteName,
        'url' => $siteUrl,
        'publisher' => [
            '@id' => $siteUrl . '/#organization'
        ],
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => route_url('blog') . '?search={search_term_string}'
            ],
            'query-input' => 'required name=search_term_string'
        ]
    ];
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
}

/**
 * Generate JSON-LD for Article/Blog Post
 */
function generate_article_schema($post) {
    $siteName = get_setting('site_name', 'Urji Beri School');
    $siteUrl = SITE_URL;
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $post['title'],
        'description' => $post['excerpt'] ?? truncate(strip_tags($post['content']), 160),
        'image' => $post['featured_image'] ? upload_url($post['featured_image'], 'blog') : branding_url('site_og_image'),
        'datePublished' => date('c', strtotime($post['published_at'])),
        'dateModified' => date('c', strtotime($post['updated_at'] ?? $post['published_at'])),
        'author' => [
            '@type' => 'Person',
            'name' => $post['author_name'] ?? 'Urji Beri School'
        ],
        'publisher' => [
            '@type' => 'Organization',
            '@id' => $siteUrl . '/#organization',
            'name' => $siteName,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => branding_url('site_logo')
            ]
        ],
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => route_url('blog-detail', ['slug' => $post['slug']])
        ],
        'articleSection' => $post['category_name'] ?? 'News',
        'wordCount' => str_word_count(strip_tags($post['content']))
    ];
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
}

/**
 * Generate JSON-LD for Image Gallery
 */
function generate_gallery_schema($images, $categoryName = 'Gallery') {
    $siteName = get_setting('site_name', 'Urji Beri School');
    $siteUrl = SITE_URL;
    
    $imageObjects = [];
    foreach ($images as $image) {
        $imageObjects[] = [
            '@type' => 'ImageObject',
            'contentUrl' => upload_url($image['filename'], 'gallery'),
            'name' => $image['caption'] ?? $image['original_name'],
            'description' => $image['alt_text'] ?? $image['caption'] ?? 'Photo from ' . $siteName,
            'uploadDate' => date('c', strtotime($image['created_at']))
        ];
    }
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'ImageGallery',
        'name' => $categoryName . ' - ' . $siteName,
        'description' => 'Photo gallery of ' . $categoryName . ' at ' . $siteName,
        'url' => route_url('gallery'),
        'image' => $imageObjects,
        'publisher' => [
            '@type' => 'Organization',
            '@id' => $siteUrl . '/#organization'
        ]
    ];
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
}

/**
 * Generate JSON-LD for BreadcrumbList
 */
function generate_breadcrumb_schema($breadcrumbs) {
    $items = [];
    $position = 1;
    
    foreach ($breadcrumbs as $name => $url) {
        $items[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $name,
            'item' => $url
        ];
        $position++;
    }
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $items
    ];
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
}

/**
 * Generate JSON-LD for School (more specific than EducationalOrganization)
 */
function generate_school_schema() {
    $siteName = get_setting('site_name', 'Urji Beri School');
    $siteUrl = SITE_URL;
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'School',
        'name' => $siteName,
        'description' => get_setting('about_overview', 'Leading preschool and elementary institution serving children ages 3-13.'),
        'url' => $siteUrl,
        'logo' => branding_url('site_logo'),
        'image' => branding_url('site_og_image'),
        'telephone' => get_setting('contact_phone', '+251-912-097-003'),
        'email' => get_setting('contact_email', 'office@urjiberischool.com'),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => get_setting('contact_address', 'Alemgena'),
            'addressLocality' => 'Alemgena',
            'addressRegion' => 'Oromia',
            'postalCode' => '',
            'addressCountry' => 'Ethiopia'
        ],
        'areaServed' => [
            '@type' => 'Place',
            'name' => 'Alemgena, Oromia, Ethiopia'
        ],
        'foundingDate' => get_setting('founding_year', get_setting('school_established', '2022')),
        'educationalCredentialAwarded' => 'Elementary School Certificate',
        'hasCredential' => [
            '@type' => 'EducationalOccupationalCredential',
            'credentialCategory' => 'Accreditation',
            'recognizedBy' => [
                '@type' => 'Organization',
                'name' => 'Oromia Education Bureau'
            ]
        ],
        'teaches' => [
            'Preschool Education',
            'Elementary Education',
            'English Language',
            'Amharic Language',
            'Mathematics',
            'Science',
            'Social Studies'
        ],
        'numberOfStudents' => get_setting('stat_students', get_setting('total_students', '550')),
        'priceRange' => '$$'
    ];
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
}

/**
 * Generate JSON-LD for ContactPage / LocalBusiness map
 */
function generate_contact_page_schema() {
    $siteName = get_setting('site_name', 'Urji Beri School');
    $siteUrl = SITE_URL;
    $lat = get_setting('map_latitude', '8.9806');
    $lng = get_setting('map_longitude', '38.7578');

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'ContactPage',
        'name' => 'Contact ' . $siteName,
        'url' => route_url('contact'),
        'description' => 'Contact ' . $siteName . ' for admissions, campus visits, and general inquiries.',
        'mainEntity' => [
            '@type' => 'School',
            'name' => $siteName,
            'telephone' => get_setting('contact_phone', '+251-912-097-003'),
            'email' => get_setting('contact_email', 'office@urjiberischool.com'),
            'url' => $siteUrl,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => get_setting('contact_address', 'Alemgena'),
                'addressLocality' => 'Alemgena',
                'addressRegion' => 'Oromia',
                'addressCountry' => 'ET'
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => $lat,
                'longitude' => $lng
            ],
            'hasMap' => 'https://www.google.com/maps?q=' . $lat . ',' . $lng
        ]
    ];

    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
}

/**
 * Build Google Maps embed URL from settings
 */
function get_google_maps_embed_url() {
    $lat = get_setting('map_latitude', '8.9806');
    $lng = get_setting('map_longitude', '38.7578');
    $zoom = (int) get_setting('map_zoom', 15);
    $place = urlencode(get_setting('site_name', 'Urji Beri School') . ', ' . get_setting('contact_address', 'Alemgena'));

    return 'https://maps.google.com/maps?q=' . $place . '&ll=' . $lat . ',' . $lng . '&z=' . $zoom . '&output=embed&hl=en';
}

/**
 * Build Google Maps directions URL
 */
function get_google_maps_directions_url() {
    $lat = get_setting('map_latitude', '8.9806');
    $lng = get_setting('map_longitude', '38.7578');
    return 'https://www.google.com/maps/dir/?api=1&destination=' . $lat . ',' . $lng;
}

/**
 * Check if a boolean site setting is enabled
 */
function setting_is_enabled($key, $default = false) {
    $value = get_setting($key, '');
    if ($value === '' || $value === null) {
        return $default;
    }
    return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
}

/**
 * Resolve relative site paths to full URLs
 */
function site_url_for($path) {
    if (preg_match('/^https?:\/\//i', $path)) {
        return $path;
    }

    $path = ltrim((string) $path, '/');
    $query = '';

    if (str_contains($path, '?')) {
        [$path, $query] = explode('?', $path, 2);
    }

    $legacyMap = [
        '' => 'home',
        'index.php' => 'home',
        'about.php' => 'about',
        'contact.php' => 'contact',
        'director.php' => 'director',
        'gallery.php' => 'gallery',
        'blog.php' => 'blog',
        'blog-detail.php' => 'blog-detail',
        'sitemap.php' => 'sitemap',
    ];

    if (isset($legacyMap[$path])) {
        $params = [];
        if ($query !== '') {
            parse_str($query, $params);
        }

        $route = $legacyMap[$path];

        if ($route === 'blog-detail' && !empty($params['slug'])) {
            return route_url('blog-detail', ['slug' => $params['slug']]);
        }

        if ($route === 'gallery' && !empty($params['category'])) {
            return route_url('gallery', ['category' => $params['category']]);
        }

        if ($route === 'blog' && !empty($params['category'])) {
            return route_url('blog', ['category' => $params['category']]);
        }

        return route_url($route);
    }

    $url = rtrim(SITE_URL, '/') . '/' . $path;
    if ($query !== '') {
        $url .= '?' . $query;
    }

    return $url;
}
