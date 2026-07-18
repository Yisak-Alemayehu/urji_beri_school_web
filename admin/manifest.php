<?php
/**
 * Dynamic Admin Web App Manifest
 * Urji Beri School Website
 */

require_once dirname(__DIR__) . '/config/config.php';

header('Content-Type: application/manifest+json; charset=utf-8');
header('Cache-Control: public, max-age=3600');

$siteName = get_setting('site_name', 'Urji Beri School');

$manifest = [
    'name' => $siteName . ' Admin',
    'short_name' => 'UBS Admin',
    'description' => 'Admin panel for ' . $siteName,
    'start_url' => '/admin/',
    'display' => 'standalone',
    'background_color' => '#0f172a',
    'theme_color' => '#1E3A8A',
    'orientation' => 'any',
    'scope' => '/admin/',
    'icons' => [
        [
            'src' => branding_url('site_icon_192'),
            'sizes' => '192x192',
            'type' => 'image/png',
            'purpose' => 'any maskable',
        ],
        [
            'src' => branding_url('site_icon_512'),
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'any maskable',
        ],
    ],
    'lang' => 'en',
];

echo json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
