<?php
/**
 * One-time upgrade: branding settings + insecure default password fix
 */
require_once dirname(__DIR__) . '/config/config.php';

$db = Database::getInstance();

$keys = [
    ['site_logo', '', 'image', 'branding'],
    ['site_logo_white', '', 'image', 'branding'],
    ['site_favicon', '', 'image', 'branding'],
    ['site_favicon_16', '', 'image', 'branding'],
    ['site_favicon_32', '', 'image', 'branding'],
    ['site_apple_touch_icon', '', 'image', 'branding'],
    ['site_og_image', '', 'image', 'branding'],
    ['site_icon_72', '', 'image', 'branding'],
    ['site_icon_96', '', 'image', 'branding'],
    ['site_icon_128', '', 'image', 'branding'],
    ['site_icon_144', '', 'image', 'branding'],
    ['site_icon_152', '', 'image', 'branding'],
    ['site_icon_192', '', 'image', 'branding'],
    ['site_icon_384', '', 'image', 'branding'],
    ['site_icon_512', '', 'image', 'branding'],
    ['founding_year', '2022', 'text', 'general'],
    ['contact_phone_2', '', 'text', 'contact'],
    ['office_hours_weekday', 'Monday - Friday: 8:00 AM - 5:00 PM', 'text', 'hours'],
    ['office_hours_saturday', 'Saturday: 8:00 AM - 12:00 PM', 'text', 'hours'],
    ['office_hours_note', 'Sunday: Closed', 'text', 'hours'],
    ['social_twitter', '', 'text', 'social'],
    ['social_linkedin', '', 'text', 'social'],
    ['online_result_url', 'https://sms.urjiberischool.com/portal/login?', 'text', 'external'],
    ['teacher_login_url', 'https://sms.urjiberischool.com/teacher-portal/login?', 'text', 'external'],
];

foreach ($keys as [$k, $v, $t, $g]) {
    $exists = $db->fetch('SELECT id FROM site_settings WHERE setting_key = ?', [$k]);
    if (!$exists) {
        $db->query(
            'INSERT INTO site_settings (setting_key, setting_value, setting_type, setting_group) VALUES (?, ?, ?, ?)',
            [$k, $v, $t, $g]
        );
        echo "INSERT {$k}\n";
    } else {
        echo "KEEP {$k}\n";
    }
}

foreach (['site_logo', 'site_favicon'] as $k) {
    $row = $db->fetch('SELECT setting_value FROM site_settings WHERE setting_key = ?', [$k]);
    if ($row && in_array($row['setting_value'], ['logo.png', 'favicon.ico'], true)) {
        if (!is_file(UPLOADS_PATH . '/branding/' . $row['setting_value'])) {
            $db->query('UPDATE site_settings SET setting_value = ? WHERE setting_key = ?', ['', $k]);
            echo "CLEARED legacy {$k}\n";
        }
    }
}

$admin = $db->fetch('SELECT id, password FROM users WHERE username = ?', ['admin']);
if ($admin && password_verify('password', $admin['password'])) {
    $hash = password_hash('Admin@123', PASSWORD_DEFAULT);
    $db->query('UPDATE users SET password = ? WHERE id = ?', [$hash, $admin['id']]);
    echo "UPDATED insecure admin password to Admin@123\n";
} else {
    echo "Admin password left unchanged\n";
}

clear_settings_cache();
echo "DONE\n";
