<?php
/**
 * Admin Director's Message Management
 * Urji Beri School Website
 */

require_once dirname(__DIR__) . '/config/config.php';

$adminPageTitle = "Director's Message";
$db = Database::getInstance();
$errors = [];

// Get current director content
$director = [
    'name' => get_setting('director_name') ?: 'Director Name',
    'title' => get_setting('director_title') ?: 'School Director',
    'message' => get_setting('director_message') ?: '',
    'image' => get_setting('director_image') ?: ''
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token.';
    } else {
        $name = clean_input($_POST['name'] ?? '');
        $title = clean_input($_POST['title'] ?? '');
        $message = $_POST['message'] ?? ''; // Allow HTML
        
        if (empty($name)) {
            $errors[] = 'Director name is required.';
        }
        if (empty($message)) {
            $errors[] = 'Director message is required.';
        }
        
        // Handle image upload
        $newImage = null;
        if (!empty($_FILES['director_image']['name'])) {
            $uploadResult = upload_file($_FILES['director_image'], 'director');
            if ($uploadResult['success']) {
                $newImage = $uploadResult['filename'];
            } else {
                $errors[] = $uploadResult['error'];
            }
        }
        
        if (empty($errors)) {
            try {
                update_setting('director_name', $name);
                update_setting('director_title', $title);
                update_setting('director_message', $message);
                
                if ($newImage) {
                    // Delete old image
                    if ($director['image']) {
                        delete_file($director['image'], 'director');
                    }
                    update_setting('director_image', $newImage);
                }
                
                set_flash('success', "Director's message updated successfully.");
                redirect(ADMIN_URL . '/director.php');
            } catch (Exception $e) {
                $errors[] = 'Failed to save changes. Please try again.';
                if ($newImage) {
                    delete_file($newImage, 'director');
                }
            }
        }
    }
    
    // Keep form values on error
    $director['name'] = $name ?? $director['name'];
    $director['title'] = $title ?? $director['title'];
    $director['message'] = $message ?? $director['message'];
}

include ADMIN_PATH . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Director's Message</h2>
        <a href="<?php echo SITE_URL; ?>/director.php" class="btn btn-outline" target="_blank">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
            View Page
        </a>
    </div>
    <div class="admin-card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <?php echo csrf_field(); ?>
            
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label for="name">Director's Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="<?php echo e($director['name']); ?>" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="title">Title/Position</label>
                    <input type="text" id="title" name="title" class="form-control" 
                           value="<?php echo e($director['title']); ?>" placeholder="e.g. School Director">
                </div>
            </div>
            
            <div class="form-group">
                <label for="message">Welcome Message <span class="required">*</span></label>
                <textarea id="message" name="message" class="form-control" rows="15" required><?php echo e($director['message']); ?></textarea>
                <small class="form-text">You can use HTML tags for formatting (paragraphs, bold, italic, lists, etc.)</small>
            </div>
            
            <div class="form-group">
                <label>Director's Photo</label>
                <div class="file-upload-wrapper">
                    <?php if ($director['image']): ?>
                        <div class="current-image">
                            <img src="<?php echo upload_url($director['image'], 'director'); ?>" 
                                 alt="Director's photo" class="preview-image" style="max-width: 200px;">
                            <p>Current photo. Upload a new image to replace it.</p>
                        </div>
                    <?php endif; ?>
                    <div class="file-upload-area">
                        <input type="file" name="director_image" id="director_image" 
                               accept="image/jpeg,image/png,image/gif,image/webp" class="file-input">
                        <label for="director_image" class="file-label">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <span>Click to upload photo</span>
                            <small>JPEG, PNG, GIF, WebP (Max 5MB)</small>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<?php include ADMIN_PATH . '/includes/admin_footer.php'; ?>
