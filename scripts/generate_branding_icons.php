<?php
/**
 * Generate favicon / PWA / OG assets from the primary logo.
 * Usage: php scripts/generate_branding_icons.php
 */

$base = dirname(__DIR__);
$src = $base . '/assets/images/logo.png';
$dir = $base . '/assets/images';

if (!is_file($src)) {
    fwrite(STDERR, "Missing logo: {$src}\n");
    exit(1);
}

$img = @imagecreatefrompng($src);
if (!$img) {
    fwrite(STDERR, "Failed to load logo PNG\n");
    exit(1);
}

$w = imagesx($img);
$h = imagesy($img);

$pngSizes = [
    'favicon-16x16.png' => 16,
    'favicon-32x32.png' => 32,
    'apple-touch-icon.png' => 180,
    'icon-72.png' => 72,
    'icon-96.png' => 96,
    'icon-128.png' => 128,
    'icon-144.png' => 144,
    'icon-152.png' => 152,
    'icon-192.png' => 192,
    'icon-384.png' => 384,
    'icon-512.png' => 512,
];

foreach ($pngSizes as $name => $size) {
    $out = imagecreatetruecolor($size, $size);
    imagealphablending($out, false);
    imagesavealpha($out, true);
    $transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
    imagefilledrectangle($out, 0, 0, $size, $size, $transparent);
    imagealphablending($out, true);
    imagecopyresampled($out, $img, 0, 0, 0, 0, $size, $size, $w, $h);
    imagepng($out, $dir . '/' . $name);
    imagedestroy($out);
    echo "OK {$name}\n";
}

// OG image (logo centered on brand blue)
$tw = 1200;
$th = 630;
$out = imagecreatetruecolor($tw, $th);
$bg = imagecolorallocate($out, 30, 58, 138);
imagefill($out, 0, 0, $bg);
$scale = min(($tw * 0.45) / $w, ($th * 0.65) / $h);
$nw = (int) ($w * $scale);
$nh = (int) ($h * $scale);
$dx = (int) (($tw - $nw) / 2);
$dy = (int) (($th - $nh) / 2);
imagecopyresampled($out, $img, $dx, $dy, 0, 0, $nw, $nh, $w, $h);
imagejpeg($out, $dir . '/og-image.jpg', 90);
imagedestroy($out);
echo "OK og-image.jpg\n";

// Favicon: copy 32px PNG (modern browsers accept PNG favicons)
copy($dir . '/favicon-32x32.png', $dir . '/favicon.ico');
echo "OK favicon.ico\n";

// Director placeholder
$ph = imagecreatetruecolor(400, 400);
$bg = imagecolorallocate($ph, 226, 232, 240);
imagefill($ph, 0, 0, $bg);
$scale = min(200 / $w, 200 / $h);
$nw = (int) ($w * $scale);
$nh = (int) ($h * $scale);
imagecopyresampled($ph, $img, (int) ((400 - $nw) / 2), (int) ((400 - $nh) / 2), 0, 0, $nw, $nh, $w, $h);
imagejpeg($ph, $dir . '/director-placeholder.jpg', 85);
imagedestroy($ph);
echo "OK director-placeholder.jpg\n";

// Branding uploads dir
$brandingDir = $base . '/uploads/branding';
if (!is_dir($brandingDir)) {
    mkdir($brandingDir, 0755, true);
}
file_put_contents($brandingDir . '/.gitkeep', '');
echo "OK uploads/branding/\n";

imagedestroy($img);
echo "DONE\n";
