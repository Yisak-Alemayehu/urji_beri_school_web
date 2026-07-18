<?php
/**
 * Admin Site Settings Management
 * Urji Beri School Website
 */

require_once dirname(__DIR__) . '/config/config.php';

require_admin();

$adminPageTitle = 'Site Settings';
$db = Database::getInstance();
$errors = [];

$brandingFields = [
    'site_logo' => [
        'label' => 'Primary Logo',
        'hint' => 'Used in the header, admin panel, and login. PNG or SVG-ready PNG with transparent background recommended. Uploading also regenerates favicon and PWA icons.',
        'accept' => 'image/png,image/jpeg,image/webp,image/gif',
    ],
    'site_logo_white' => [
        'label' => 'White / Footer Logo',
        'hint' => 'Shown on the dark footer. Use a light or white version of your logo.',
        'accept' => 'image/png,image/jpeg,image/webp,image/gif',
    ],
    'site_favicon' => [
        'label' => 'Favicon',
        'hint' => 'Browser tab icon. ICO, PNG, or WEBP. If empty, derived from the primary logo.',
        'accept' => 'image/x-icon,image/vnd.microsoft.icon,image/png,image/jpeg,image/webp,.ico',
    ],
    'site_apple_touch_icon' => [
        'label' => 'Apple Touch Icon',
        'hint' => '180×180 recommended for iOS home screen. Auto-generated from the primary logo if not set.',
        'accept' => 'image/png,image/jpeg,image/webp',
    ],
    'site_og_image' => [
        'label' => 'Social Share Image (OG)',
        'hint' => '1200×630 recommended. Used when pages are shared on Facebook, Telegram, etc.',
        'accept' => 'image/png,image/jpeg,image/webp',
    ],
];

// Define setting groups
$settingGroups = [
    'general' => [
        'title' => 'General Information',
        'settings' => [
            'site_name' => ['label' => 'School Name', 'type' => 'text', 'required' => true],
            'site_tagline' => ['label' => 'Tagline', 'type' => 'text'],
            'site_description' => ['label' => 'Meta Description (SEO)', 'type' => 'textarea'],
            'site_keywords' => ['label' => 'Meta Keywords (SEO)', 'type' => 'textarea'],
            'founding_year' => ['label' => 'Year Established', 'type' => 'text', 'placeholder' => 'e.g. 2022'],
        ]
    ],
    'branding' => [
        'title' => 'Logo & Branding',
        'settings' => []
    ],
    'contact' => [
        'title' => 'Contact Information',
        'settings' => [
            'contact_email' => ['label' => 'Email Address', 'type' => 'email', 'required' => true],
            'contact_phone' => ['label' => 'Phone Number', 'type' => 'text'],
            'contact_phone_2' => ['label' => 'Phone Number 2', 'type' => 'text'],
            'contact_address' => ['label' => 'Address', 'type' => 'textarea'],
        ]
    ],
    'external' => [
        'title' => 'External Links',
        'settings' => [
            'online_result_url' => ['label' => 'Students/Parents App URL', 'type' => 'url', 'placeholder' => 'https://sms.urjiberischool.com/portal/login?'],
            'teacher_login_url' => ['label' => 'Teachers App URL', 'type' => 'url', 'placeholder' => 'https://sms.urjiberischool.com/teacher-portal/login?'],
        ]
    ],
    'social' => [
        'title' => 'Social Media',
        'settings' => [
            'social_facebook' => ['label' => 'Facebook URL', 'type' => 'url'],
            'social_twitter' => ['label' => 'Twitter/X URL', 'type' => 'url'],
            'social_instagram' => ['label' => 'Instagram URL', 'type' => 'url'],
            'social_youtube' => ['label' => 'YouTube URL', 'type' => 'url'],
            'social_telegram' => ['label' => 'Telegram Channel URL', 'type' => 'url', 'placeholder' => 'https://t.me/yourchannel'],
            'social_linkedin' => ['label' => 'LinkedIn URL', 'type' => 'url'],
        ]
    ],
    'hours' => [
        'title' => 'Operating Hours',
        'settings' => [
            'office_hours_weekday' => ['label' => 'Weekday Hours', 'type' => 'text', 'placeholder' => 'e.g. Monday - Friday: 8:00 AM - 5:00 PM'],
            'office_hours_saturday' => ['label' => 'Saturday Hours', 'type' => 'text', 'placeholder' => 'e.g. Saturday: 8:00 AM - 12:00 PM'],
            'office_hours_note' => ['label' => 'Additional Note', 'type' => 'text', 'placeholder' => 'e.g. Sunday: Closed'],
        ]
    ],
    'map' => [
        'title' => 'Location Map',
        'settings' => [
            'map_latitude' => ['label' => 'Latitude', 'type' => 'text', 'placeholder' => 'e.g. 8.9806'],
            'map_longitude' => ['label' => 'Longitude', 'type' => 'text', 'placeholder' => 'e.g. 38.7578'],
            'map_zoom' => ['label' => 'Google Maps Zoom (1–20)', 'type' => 'number', 'min' => 1, 'max' => 20, 'placeholder' => '15'],
        ]
    ],
    'seo' => [
        'title' => 'SEO & Analytics',
        'settings' => [
            'google_analytics_id' => ['label' => 'Google Analytics ID (G-XXXXXXXX)', 'type' => 'text', 'placeholder' => 'G-XXXXXXXXXX'],
            'google_site_verification' => ['label' => 'Google Search Console Verification', 'type' => 'text', 'placeholder' => 'meta content value'],
            'bing_site_verification' => ['label' => 'Bing Webmaster Verification', 'type' => 'text', 'placeholder' => 'meta content value'],
        ]
    ],
    'announcements' => [
        'title' => 'Popups & Announcements',
        'settings' => [
            'popup_registration_enabled' => ['label' => 'Show Registration Popup', 'type' => 'boolean'],
            'popup_registration_title' => ['label' => 'Registration Popup Title', 'type' => 'text', 'placeholder' => '2025/26 Registration Is Open!'],
            'popup_registration_text' => ['label' => 'Registration Popup Message', 'type' => 'textarea', 'placeholder' => 'We have started registration...'],
            'popup_registration_cta_text' => ['label' => 'Registration Button Text', 'type' => 'text', 'placeholder' => 'Register Your Child'],
            'popup_registration_cta_link' => ['label' => 'Registration Button Link', 'type' => 'text', 'placeholder' => '/contact.php'],
            'popup_promo_enabled' => ['label' => 'Show Promotional Popup', 'type' => 'boolean'],
            'popup_promo_title' => ['label' => 'Promo Popup Title', 'type' => 'text', 'placeholder' => 'Why Families Choose Urji Beri'],
            'popup_promo_text' => ['label' => 'Promo Popup Message', 'type' => 'textarea'],
            'popup_promo_cta_text' => ['label' => 'Promo Button Text', 'type' => 'text', 'placeholder' => 'Learn More'],
            'popup_promo_cta_link' => ['label' => 'Promo Button Link', 'type' => 'text', 'placeholder' => '/about.php'],
        ]
    ],
    'stats' => [
        'title' => 'Statistics (Homepage)',
        'settings' => [
            'stat_students' => ['label' => 'Total Students', 'type' => 'number'],
            'stat_teachers' => ['label' => 'Qualified Teachers', 'type' => 'number'],
            'stat_experience' => ['label' => 'Years of Experience', 'type' => 'number'],
            'stat_programs' => [
                'label' => 'Parent Satisfaction',
                'type' => 'number',
                'min' => 0,
                'max' => 100,
                'step' => 1,
                'placeholder' => 'e.g. 90'
            ],
        ]
    ],
];

// Get current settings
$currentSettings = [];
$allSettings = $db->fetchAll("SELECT setting_key, setting_value FROM site_settings");
foreach ($allSettings as $setting) {
    $currentSettings[$setting['setting_key']] = $setting['setting_value'];
}

// Migrate legacy school_established → founding_year for display
if (empty($currentSettings['founding_year']) && !empty($currentSettings['school_established'])) {
    $currentSettings['founding_year'] = $currentSettings['school_established'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token.';
    } else {
        $tab = clean_input($_POST['active_tab'] ?? 'general');

        if ($tab === 'branding') {
            foreach ($brandingFields as $key => $config) {
                if (empty($_FILES[$key]['name']) || ($_FILES[$key]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                $old = $currentSettings[$key] ?? '';
                $result = store_branding_upload($_FILES[$key], $key, $old);
                if (!$result['success']) {
                    $errors[] = $config['label'] . ': ' . ($result['error'] ?? 'Upload failed');
                } else {
                    $currentSettings[$key] = $result['filename'];
                }
            }

            // Optional: reset individual assets to defaults
            foreach ($brandingFields as $key => $config) {
                if (!empty($_POST['reset_' . $key])) {
                    $old = $currentSettings[$key] ?? '';
                    if ($old && is_file(UPLOADS_PATH . '/branding/' . basename($old))) {
                        delete_file(basename($old), 'branding');
                    }
                    update_setting($key, '');
                    $currentSettings[$key] = '';
                }
            }

            if (empty($errors)) {
                $_SESSION['settings_updated'] = true;
                set_flash('success', 'Branding updated successfully.');
                redirect(ADMIN_URL . '/settings.php?tab=branding');
            }
        } elseif (isset($settingGroups[$tab])) {
            foreach ($settingGroups[$tab]['settings'] as $key => $config) {
                $value = clean_input($_POST[$key] ?? '');

                if (!empty($config['required']) && empty($value)) {
                    $errors[] = $config['label'] . ' is required.';
                }

                $currentSettings[$key] = $value;
            }

            if (empty($errors)) {
                try {
                    foreach ($settingGroups[$tab]['settings'] as $key => $config) {
                        if ($config['type'] === 'boolean') {
                            $value = isset($_POST[$key]) ? '1' : '0';
                        } else {
                            $value = clean_input($_POST[$key] ?? '');
                        }
                        update_setting($key, $value);

                        // Keep legacy key in sync
                        if ($key === 'founding_year') {
                            update_setting('school_established', $value);
                        }
                    }

                    $_SESSION['settings_updated'] = true;
                    set_flash('success', 'Settings updated successfully.');
                    redirect(ADMIN_URL . '/settings.php?tab=' . $tab);
                } catch (Exception $e) {
                    $errors[] = 'Failed to save settings. Please try again.';
                }
            }
        }
    }
}

// Get active tab
$activeTab = clean_input($_GET['tab'] ?? 'general');
if (!isset($settingGroups[$activeTab])) {
    $activeTab = 'general';
}

include ADMIN_PATH . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Site Settings</h2>
    </div>
    <div class="admin-card-body admin-card-body--flush">
        <div class="settings-layout">
            <!-- Tabs -->
            <div class="settings-tabs">
                <?php foreach ($settingGroups as $key => $group): ?>
                    <a href="<?php echo ADMIN_URL; ?>/settings.php?tab=<?php echo $key; ?>"
                       class="settings-tab <?php echo $activeTab === $key ? 'active' : ''; ?>">
                        <?php echo $group['title']; ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Content -->
            <div class="settings-content">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <ul style="margin: 0; padding-left: 1.5rem;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($activeTab === 'branding'): ?>
                    <form method="POST" class="admin-form" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="active_tab" value="branding">

                        <h3 class="settings-section-title">Logo & Branding</h3>
                        <p class="settings-section-intro">Upload your school logo and icons. Changes appear across the public site, admin panel, browser tab, and when pages are shared on social media.</p>

                        <div class="branding-grid">
                            <?php foreach ($brandingFields as $key => $config): ?>
                                <?php
                                $previewUrl = branding_url($key);
                                $isDarkPreview = ($key === 'site_logo_white');
                                ?>
                                <div class="branding-card">
                                    <div class="branding-preview <?php echo $isDarkPreview ? 'branding-preview--dark' : ''; ?>">
                                        <img src="<?php echo e($previewUrl); ?>" alt="<?php echo e($config['label']); ?> preview" id="preview-<?php echo e($key); ?>">
                                    </div>
                                    <div class="branding-meta">
                                        <label for="<?php echo e($key); ?>" class="branding-label"><?php echo e($config['label']); ?></label>
                                        <p class="branding-hint"><?php echo e($config['hint']); ?></p>
                                        <input type="file"
                                               id="<?php echo e($key); ?>"
                                               name="<?php echo e($key); ?>"
                                               class="form-control branding-file"
                                               accept="<?php echo e($config['accept']); ?>"
                                               data-preview="preview-<?php echo e($key); ?>">
                                        <label class="branding-reset">
                                            <input type="checkbox" name="reset_<?php echo e($key); ?>" value="1">
                                            Reset to default
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="branding-pwa-note">
                            <strong>PWA icons</strong> (72–512px) are generated automatically when you upload a primary logo. Current icons:
                            <div class="branding-icon-row">
                                <?php foreach (['site_icon_192', 'site_icon_512'] as $iconKey): ?>
                                    <img src="<?php echo e(branding_url($iconKey)); ?>" alt="<?php echo e($iconKey); ?>" width="48" height="48">
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                Save Branding
                            </button>
                        </div>
                    </form>
                    <script>
                        document.querySelectorAll('.branding-file').forEach(function (input) {
                            input.addEventListener('change', function () {
                                var preview = document.getElementById(input.getAttribute('data-preview'));
                                if (!preview || !input.files || !input.files[0]) return;
                                var url = URL.createObjectURL(input.files[0]);
                                preview.src = url;
                            });
                        });
                    </script>
                <?php else: ?>
                    <form method="POST" class="admin-form">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="active_tab" value="<?php echo e($activeTab); ?>">

                        <h3 class="settings-section-title"><?php echo e($settingGroups[$activeTab]['title']); ?></h3>

                        <?php foreach ($settingGroups[$activeTab]['settings'] as $key => $config): ?>
                            <div class="form-group">
                                <label for="<?php echo e($key); ?>">
                                    <?php echo e($config['label']); ?>
                                    <?php if (!empty($config['required'])): ?>
                                        <span class="required">*</span>
                                    <?php endif; ?>
                                </label>

                                <?php if ($config['type'] === 'textarea'): ?>
                                    <textarea id="<?php echo e($key); ?>" name="<?php echo e($key); ?>"
                                              class="form-control" rows="4"
                                              <?php echo isset($config['placeholder']) ? 'placeholder="' . e($config['placeholder']) . '"' : ''; ?>
                                              <?php echo !empty($config['required']) ? 'required' : ''; ?>><?php echo e($currentSettings[$key] ?? ''); ?></textarea>
                                <?php elseif ($config['type'] === 'boolean'): ?>
                                    <label class="settings-toggle">
                                        <input type="checkbox" id="<?php echo e($key); ?>" name="<?php echo e($key); ?>" value="1"
                                            <?php echo ($currentSettings[$key] ?? '0') === '1' ? 'checked' : ''; ?>>
                                        <span>Enabled</span>
                                    </label>
                                <?php else: ?>
                                    <input type="<?php echo e($config['type']); ?>"
                                        id="<?php echo e($key); ?>"
                                        name="<?php echo e($key); ?>"
                                        class="form-control"
                                        value="<?php echo e($currentSettings[$key] ?? ''); ?>"
                                        <?php echo isset($config['placeholder']) ? 'placeholder="' . e($config['placeholder']) . '"' : ''; ?>
                                        <?php echo isset($config['min']) ? 'min="' . e($config['min']) . '"' : ''; ?>
                                        <?php echo isset($config['max']) ? 'max="' . e($config['max']) . '"' : ''; ?>
                                        <?php echo isset($config['step']) ? 'step="' . e($config['step']) . '"' : ''; ?>
                                        <?php echo !empty($config['required']) ? 'required' : ''; ?>>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                                    <polyline points="7 3 7 8 15 8"></polyline>
                                </svg>
                                Save Settings
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include ADMIN_PATH . '/includes/admin_footer.php'; ?>
