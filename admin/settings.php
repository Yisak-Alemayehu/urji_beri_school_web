<?php
/**
 * Admin Site Settings Management
 * Urji Beri School Website
 */

require_once dirname(__DIR__) . '/config/config.php';

$adminPageTitle = 'Site Settings';
$db = Database::getInstance();
$errors = [];

// Define setting groups
$settingGroups = [
    'general' => [
        'title' => 'General Information',
        'settings' => [
            'site_name' => ['label' => 'School Name', 'type' => 'text', 'required' => true],
            'site_tagline' => ['label' => 'Tagline', 'type' => 'text'],
            'site_description' => ['label' => 'Description', 'type' => 'textarea'],
            'school_established' => ['label' => 'Year Established', 'type' => 'text'],
        ]
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
    'social' => [
        'title' => 'Social Media',
        'settings' => [
            'social_facebook' => ['label' => 'Facebook URL', 'type' => 'url'],
            'social_twitter' => ['label' => 'Twitter/X URL', 'type' => 'url'],
            'social_instagram' => ['label' => 'Instagram URL', 'type' => 'url'],
            'social_youtube' => ['label' => 'YouTube URL', 'type' => 'url'],
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
            'map_latitude' => ['label' => 'Latitude', 'type' => 'text', 'placeholder' => 'e.g. 8.9467'],
            'map_longitude' => ['label' => 'Longitude', 'type' => 'text', 'placeholder' => 'e.g. 38.7214'],
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token.';
    } else {
        $tab = clean_input($_POST['active_tab'] ?? 'general');
        
        // Validate required fields
        if (isset($settingGroups[$tab])) {
            foreach ($settingGroups[$tab]['settings'] as $key => $config) {
                $value = clean_input($_POST[$key] ?? '');
                
                if (!empty($config['required']) && empty($value)) {
                    $errors[] = $config['label'] . ' is required.';
                }
                
                // Store value for update
                $currentSettings[$key] = $value;
            }
        }
        
        if (empty($errors)) {
            try {
                // Update all settings from the submitted tab
                foreach ($settingGroups[$tab]['settings'] as $key => $config) {
                    $value = clean_input($_POST[$key] ?? '');
                    update_setting($key, $value);
                }
                
                // Mark that settings were updated so client can notify service worker
                $_SESSION['settings_updated'] = true;
                set_flash('success', 'Settings updated successfully.');
                redirect(ADMIN_URL . '/settings.php?tab=' . $tab);
            } catch (Exception $e) {
                $errors[] = 'Failed to save settings. Please try again.';
            }
        }
    }
}

// Get active tab
$activeTab = clean_input($_GET['tab'] ?? 'general');
if (!isset($settingGroups[$activeTab])) {
    $activeTab = 'general';
}

// DEBUG: Show all current settings for troubleshooting
if (isset($_GET['debug']) && $_GET['debug'] === '1') {
    echo '<pre style="background:#222;color:#fff;padding:1em;">';
    echo "<b>DEBUG: currentSettings</b>\n";
    print_r($currentSettings);
    echo '</pre>';
}

include ADMIN_PATH . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Site Settings</h2>
    </div>
    <div class="admin-card-body" style="padding: 0;">
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
                
                <form method="POST" class="admin-form">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="active_tab" value="<?php echo $activeTab; ?>">
                    
                    <h3 class="settings-section-title"><?php echo $settingGroups[$activeTab]['title']; ?></h3>
                    
                    <?php foreach ($settingGroups[$activeTab]['settings'] as $key => $config): ?>
                        <div class="form-group">
                            <label for="<?php echo $key; ?>">
                                <?php echo $config['label']; ?>
                                <?php if (!empty($config['required'])): ?>
                                    <span class="required">*</span>
                                <?php endif; ?>
                            </label>
                            
                            <?php if ($config['type'] === 'textarea'): ?>
                                <textarea id="<?php echo $key; ?>" name="<?php echo $key; ?>" 
                                          class="form-control" rows="4"
                                          <?php echo isset($config['placeholder']) ? 'placeholder="' . e($config['placeholder']) . '"' : ''; ?>
                                          <?php echo !empty($config['required']) ? 'required' : ''; ?>><?php echo e($currentSettings[$key] ?? ''); ?></textarea>
                            <?php else: ?>
                                    <input type="<?php echo $config['type']; ?>" 
                                        id="<?php echo $key; ?>" 
                                        name="<?php echo $key; ?>" 
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
            </div>
        </div>
    </div>
</div>

<style>
.settings-layout {
    display: flex;
    min-height: 500px;
}

.settings-tabs {
    width: 220px;
    background: var(--gray-50);
    border-right: 1px solid var(--gray-200);
    padding: 1rem 0;
    flex-shrink: 0;
}

.settings-tab {
    display: block;
    padding: 0.875rem 1.5rem;
    color: var(--gray-600);
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: var(--transition);
}

.settings-tab:hover {
    background: var(--white);
    color: var(--primary);
}

.settings-tab.active {
    background: var(--white);
    color: var(--primary);
    border-left-color: var(--primary);
    font-weight: 600;
}

.settings-content {
    flex: 1;
    padding: 2rem;
}

.settings-section-title {
    font-size: 1.25rem;
    color: var(--gray-800);
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid var(--gray-200);
}

@media (max-width: 768px) {
    .settings-layout {
        flex-direction: column;
    }
    
    .settings-tabs {
        width: 100%;
        display: flex;
        overflow-x: auto;
        border-right: none;
        border-bottom: 1px solid var(--gray-200);
        padding: 0;
    }
    
    .settings-tab {
        white-space: nowrap;
        border-left: none;
        border-bottom: 3px solid transparent;
    }
    
    .settings-tab.active {
        border-left-color: transparent;
        border-bottom-color: var(--primary);
    }
    
    .settings-content {
        padding: 1.5rem;
    }
}
</style>

<?php include ADMIN_PATH . '/includes/admin_footer.php'; ?>
