<?php
/**
 * Dynamic Web App Manifest
 * Urji Beri School Website
 */

require_once __DIR__ . '/config/config.php';

header('Content-Type: application/manifest+json; charset=utf-8');
header('Cache-Control: public, max-age=3600');

$siteName = get_setting('site_name', 'Urji Beri School');
$shortName = mb_strlen($siteName) > 12 ? 'Urji Beri' : $siteName;
$description = get_setting('site_description', 'Quality preschool and elementary education in Alemgena, Oromia');

$iconKeys = [
    72 => 'site_icon_72',
    96 => 'site_icon_96',
    128 => 'site_icon_128',
    144 => 'site_icon_144',
    152 => 'site_icon_152',
    192 => 'site_icon_192',
    384 => 'site_icon_384',
    512 => 'site_icon_512',
];

$icons = [];
foreach ($iconKeys as $size => $key) {
    $icons[] = [
        'src' => branding_url($key),
        'sizes' => $size . 'x' . $size,
        'type' => 'image/png',
        'purpose' => 'any maskable',
    ];
}

$manifest = [
    'name' => $siteName,
    'short_name' => $shortName,
    'description' => $description,
    'start_url' => '/',
    'display' => 'standalone',
    'background_color' => '#ffffff',
    'theme_color' => '#1E3A8A',
    'orientation' => 'portrait-primary',
    'scope' => '/',
    'icons' => $icons,
    'categories' => ['education', 'kids'],
    'lang' => 'en',
    'dir' => 'ltr',
    'shortcuts' => [
        [
            'name' => 'News & Events',
            'short_name' => 'News',
            'url' => '/blog',
            'icons' => [['src' => branding_url('site_icon_96'), 'sizes' => '96x96']],
        ],
        [
            'name' => 'Photo Gallery',
            'short_name' => 'Gallery',
            'url' => '/gallery',
            'icons' => [['src' => branding_url('site_icon_96'), 'sizes' => '96x96']],
        ],
        [
            'name' => 'Contact Us',
            'short_name' => 'Contact',
            'url' => '/contact',
            'icons' => [['src' => branding_url('site_icon_96'), 'sizes' => '96x96']],
        ],
    ],
    'prefer_related_applications' => false,
];

echo json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
