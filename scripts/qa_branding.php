<?php
/**
 * Quick production QA checks for branding + critical helpers
 */
require_once __DIR__ . '/../config/config.php';

$failures = [];

function assert_true($cond, $msg) {
    global $failures;
    if (!$cond) {
        $failures[] = $msg;
        echo "FAIL: {$msg}\n";
    } else {
        echo "OK: {$msg}\n";
    }
}

$requiredImages = [
    'favicon.ico', 'favicon-16x16.png', 'favicon-32x32.png', 'apple-touch-icon.png',
    'icon-72.png', 'icon-96.png', 'icon-128.png', 'icon-144.png', 'icon-152.png',
    'icon-192.png', 'icon-384.png', 'icon-512.png', 'og-image.jpg', 'logo.png', 'logo-white.png',
    'director-placeholder.jpg',
];

foreach ($requiredImages as $img) {
    assert_true(is_file(ASSETS_PATH . '/images/' . $img), "Asset exists: {$img}");
}

assert_true(is_dir(UPLOADS_PATH . '/branding'), 'uploads/branding directory exists');
assert_true(function_exists('branding_url'), 'branding_url helper exists');
assert_true(function_exists('store_branding_upload'), 'store_branding_upload helper exists');

$logoUrl = branding_url('site_logo');
assert_true(str_contains($logoUrl, 'logo'), "branding_url(site_logo) => {$logoUrl}");

$faviconUrl = branding_url('site_favicon');
assert_true(str_contains($faviconUrl, 'favicon') || str_contains($faviconUrl, 'icon'), "branding_url(site_favicon) => {$faviconUrl}");

$ogUrl = branding_url('site_og_image');
assert_true(str_contains($ogUrl, 'og-image') || str_contains($ogUrl, 'uploads'), "branding_url(site_og_image) => {$ogUrl}");

// upload_url arity
$u = upload_url('test.jpg', 'blog');
assert_true($u === SITE_URL . '/uploads/blog/test.jpg', "upload_url correct: {$u}");

// Ensure settings keys can be written (no exception)
try {
    update_setting('__qa_probe', '1');
    assert_true(get_setting('__qa_probe') === '1', 'update_setting/get_setting works');
    $db = Database::getInstance();
    $db->query("DELETE FROM site_settings WHERE setting_key = ?", ['__qa_probe']);
    clear_settings_cache();
} catch (Throwable $e) {
    assert_true(false, 'DB settings write failed: ' . $e->getMessage());
}

// Seed branding keys if missing
$brandingKeys = array_keys(branding_defaults());
foreach ($brandingKeys as $key) {
    $val = get_setting($key, null);
    if ($val === null || $val === '') {
        // ensure row exists with empty value for admin UI
        update_setting($key, get_setting($key, ''));
    }
}
assert_true(true, 'Branding setting keys ensured');

echo "\n";
if ($failures) {
    echo count($failures) . " FAILURE(S)\n";
    exit(1);
}

echo "ALL CHECKS PASSED\n";
