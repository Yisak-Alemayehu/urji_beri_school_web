<?php
/**
 * Admin Gallery Management
 * Urji Beri School Website
 */

require_once dirname(__DIR__) . '/config/config.php';

$adminPageTitle = 'Gallery';
$db = Database::getInstance();
$errors = [];

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (!isset($_GET['token']) || !verify_csrf($_GET['token'])) {
        set_flash('error', 'Invalid security token.');
    } else {
        $imageId = (int)$_GET['delete'];
        
        // Get image to delete file
        $image = $db->fetch("SELECT filename FROM gallery_images WHERE id = ?", [$imageId]);
        
        if ($image) {
            // Delete image file
            if ($image['filename']) {
                delete_file($image['filename'], 'gallery');
            }
            
            // Delete from database
            $db->query("DELETE FROM gallery_images WHERE id = ?", [$imageId]);
            set_flash('success', 'Image deleted successfully.');
        }
    }
    redirect(ADMIN_URL . '/gallery.php');
}

// Handle form submission (upload)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token.';
    } else {
        $title = clean_input($_POST['title'] ?? '');
        $altText = clean_input($_POST['alt_text'] ?? '');
        $description = clean_input($_POST['description'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
        
        if (empty($categoryId)) {
            $errors[] = 'Category is required.';
        }
        
        // Handle image upload
        if (empty($_FILES['image']['name'])) {
            $errors[] = 'Please select an image to upload.';
        } else {
            $uploadResult = upload_file($_FILES['image'], 'gallery');
            if (!$uploadResult['success']) {
                $errors[] = $uploadResult['error'];
            }
        }
        
        if (empty($errors)) {
            try {
                $db->query("INSERT INTO gallery_images (category_id, uploaded_by, filename, original_name, caption, alt_text, is_active, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())", 
                           [$categoryId, current_user()['id'], $uploadResult['filename'], $_FILES['image']['name'], $title, $altText, $isActive]);
                set_flash('success', 'Image uploaded successfully.');
                redirect(ADMIN_URL . '/gallery.php');
            } catch (Exception $e) {
                // Delete uploaded file if database insert fails
                delete_file($uploadResult['filename'], 'gallery');
                $errors[] = 'Failed to save image. Please try again.';
            }
        }
    }
}

// Get filter
$filter = isset($_GET['category']) && is_numeric($_GET['category']) ? (int)$_GET['category'] : null;

// Get categories
$categories = $db->fetchAll("
    SELECT gc.*, COUNT(gi.id) as image_count
    FROM gallery_categories gc
    LEFT JOIN gallery_images gi ON gc.id = gi.category_id
    GROUP BY gc.id
    ORDER BY gc.name ASC
");

// Get images
$sql = "SELECT gi.*, gc.name as category_name
        FROM gallery_images gi
        JOIN gallery_categories gc ON gi.category_id = gc.id";

$params = [];
if ($filter) {
    $sql .= " WHERE gi.category_id = ?";
    $params[] = $filter;
}
$sql .= " ORDER BY gi.created_at DESC";

$images = $db->fetchAll($sql, $params);

include ADMIN_PATH . '/includes/admin_header.php';
?>

<div class="admin-grid">
    <!-- Upload Form -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Upload Image</h2>
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
                
                <div class="form-group">
                    <label for="category_id">Category <span class="required">*</span></label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo e($category['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="title">Caption/Title</label>
                    <input type="text" id="title" name="title" class="form-control" 
                           placeholder="Image caption (shows on hover)">
                </div>
                
                <div class="form-group">
                    <label for="alt_text">Alt Text (SEO) <small class="text-muted">- Describes the image for search engines</small></label>
                    <input type="text" id="alt_text" name="alt_text" class="form-control" 
                           placeholder="e.g., Students learning in classroom at Urji Beri School">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="2" 
                              placeholder="Optional longer description"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Image <span class="required">*</span></label>
                    <div class="file-upload-area">
                        <input type="file" name="image" id="image" 
                               accept="image/jpeg,image/png,image/gif,image/webp" class="file-input" required>
                        <label for="image" class="file-label">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <span>Click to upload</span>
                            <small>JPEG, PNG, GIF, WebP (Max 5MB)</small>
                        </label>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" value="1" checked>
                            <span class="checkbox-custom"></span>
                            Active
                        </label>
                    </div>
                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_featured" value="1">
                            <span class="checkbox-custom"></span>
                            Featured
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    Upload Image
                </button>
            </form>
        </div>
    </div>
    
    <!-- Gallery -->
    <div class="admin-card" style="flex: 2;">
        <div class="admin-card-header">
            <div class="d-flex align-center gap-4">
                <h2 class="admin-card-title">Gallery Images</h2>
                <div class="gallery-filter" style="margin: 0;">
                    <a href="<?php echo ADMIN_URL; ?>/gallery.php" class="filter-btn <?php echo !$filter ? 'active' : ''; ?>">All</a>
                    <?php foreach ($categories as $category): ?>
                        <a href="<?php echo ADMIN_URL; ?>/gallery.php?category=<?php echo $category['id']; ?>" 
                           class="filter-btn <?php echo $filter === $category['id'] ? 'active' : ''; ?>">
                            <?php echo e($category['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <a href="<?php echo ADMIN_URL; ?>/gallery-categories.php" class="btn btn-outline">
                Manage Categories
            </a>
        </div>
        <div class="admin-card-body">
            <?php if (!empty($images)): ?>
                <div class="admin-gallery-grid">
                    <?php foreach ($images as $image): ?>
                        <div class="admin-gallery-item <?php echo !$image['is_active'] ? 'inactive' : ''; ?>">
                            <div class="admin-gallery-img">
                                <img src="<?php echo upload_url($image['filename'], 'gallery'); ?>" 
                                     alt="<?php echo e($image['title']); ?>">
                                <?php if ($image['is_featured']): ?>
                                    <span class="badge badge-warning" style="position: absolute; top: 0.5rem; left: 0.5rem;">Featured</span>
                                <?php endif; ?>
                                <?php if (!$image['is_active']): ?>
                                    <span class="badge badge-secondary" style="position: absolute; top: 0.5rem; right: 0.5rem;">Hidden</span>
                                <?php endif; ?>
                            </div>
                            <div class="admin-gallery-info">
                                <strong><?php echo e($image['title'] ?: 'Untitled'); ?></strong>
                                <small><?php echo e($image['category_name']); ?></small>
                            </div>
                            <div class="admin-gallery-actions">
                                <a href="<?php echo upload_url($image['filename'], 'gallery'); ?>" 
                                   target="_blank" class="btn btn-sm btn-outline" title="View Full Size">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                                <a href="<?php echo ADMIN_URL; ?>/gallery.php?delete=<?php echo $image['id']; ?>&token=<?php echo $_SESSION[CSRF_TOKEN_NAME]; ?>" 
                                   class="btn btn-sm btn-outline" style="color: var(--error); border-color: var(--error);"
                                   onclick="return confirm('Are you sure you want to delete this image?')" title="Delete">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    <h3 class="empty-state-title">No Images Yet</h3>
                    <p class="empty-state-text">Upload your first image using the form.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.admin-gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 1rem;
}

.admin-gallery-item {
    background: var(--white);
    border-radius: var(--radius-lg);
    overflow: hidden;
    border: 1px solid var(--gray-200);
    transition: var(--transition);
}

.admin-gallery-item:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow);
}

.admin-gallery-item.inactive {
    opacity: 0.6;
}

.admin-gallery-img {
    position: relative;
    aspect-ratio: 1;
    overflow: hidden;
}

.admin-gallery-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.admin-gallery-info {
    padding: 0.75rem;
    border-top: 1px solid var(--gray-100);
}

.admin-gallery-info strong {
    display: block;
    font-size: 0.875rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.admin-gallery-info small {
    color: var(--gray-500);
}

.admin-gallery-actions {
    display: flex;
    gap: 0.5rem;
    padding: 0.75rem;
    border-top: 1px solid var(--gray-100);
    justify-content: center;
}
</style>

<?php include ADMIN_PATH . '/includes/admin_footer.php'; ?>
