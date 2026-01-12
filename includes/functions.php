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
 * Get site setting from database
 */
function get_setting($key, $default = '') {
    static $settings = null;
    
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
 * Update site setting
 */
function update_setting($key, $value) {
    $db = Database::getInstance();
    // Try to update, if no rows affected, insert
    $result = $db->query(
        "UPDATE site_settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?",
        [$value, $key]
    );
    if ($result->rowCount() === 0) {
        $db->query(
            "INSERT INTO site_settings (setting_key, setting_value, updated_at) VALUES (?, ?, NOW())",
            [$key, $value]
        );
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
        return ['success' => false, 'error' => 'Invalid file type'];
    }
    
    // Get extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_IMAGE_EXTENSIONS)) {
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
        'image' => asset_url('images/og-image.jpg'),
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
        'logo' => asset_url('images/logo.png'),
        'image' => asset_url('images/og-image.jpg'),
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
            'latitude' => '8.9806',
            'longitude' => '38.6218'
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
                'urlTemplate' => $siteUrl . '/blog.php?search={search_term_string}'
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
        'image' => $post['featured_image'] ? upload_url('blog/' . $post['featured_image']) : asset_url('images/og-image.jpg'),
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
                'url' => asset_url('images/logo.png')
            ]
        ],
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => $siteUrl . '/blog-detail.php?slug=' . $post['slug']
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
        'url' => $siteUrl . '/gallery.php',
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
        'logo' => asset_url('images/logo.png'),
        'image' => asset_url('images/school-building.jpg'),
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
        'foundingDate' => get_setting('founding_year', '2015'),
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
        'numberOfStudents' => get_setting('total_students', '500'),
        'priceRange' => '$$'
    ];
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
}
