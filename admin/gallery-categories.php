<?php
/**
 * Admin Gallery Categories Management
 * Urji Beri School Website
 */

require_once dirname(__DIR__) . '/config/config.php';

$adminPageTitle = 'Gallery Categories';
$db = Database::getInstance();
$errors = [];

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (!isset($_GET['token']) || !verify_csrf($_GET['token'])) {
        set_flash('error', 'Invalid security token.');
    } else {
        $categoryId = (int)$_GET['delete'];
        
        // Check if category has images
        $imageCount = $db->fetch("SELECT COUNT(*) as count FROM gallery_images WHERE category_id = ?", [$categoryId])['count'];
        
        if ($imageCount > 0) {
            set_flash('error', 'Cannot delete category with existing images. Please delete the images first.');
        } else {
            $db->query("DELETE FROM gallery_categories WHERE id = ?", [$categoryId]);
            set_flash('success', 'Category deleted successfully.');
        }
    }
    redirect(ADMIN_URL . '/gallery-categories.php');
}

// Handle form submission (add/edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token.';
    } else {
        $name = clean_input($_POST['name'] ?? '');
        $description = clean_input($_POST['description'] ?? '');
        $editId = isset($_POST['edit_id']) && is_numeric($_POST['edit_id']) ? (int)$_POST['edit_id'] : null;
        
        if (empty($name)) {
            $errors[] = 'Category name is required.';
        }
        
        if (empty($errors)) {
            $slug = $editId ? unique_slug($name, 'gallery_categories', $editId) : unique_slug($name, 'gallery_categories');
            
            try {
                if ($editId) {
                    $db->query("UPDATE gallery_categories SET name = ?, slug = ?, description = ? WHERE id = ?", 
                               [$name, $slug, $description, $editId]);
                    set_flash('success', 'Category updated successfully.');
                } else {
                    $db->query("INSERT INTO gallery_categories (name, slug, description) VALUES (?, ?, ?)", 
                               [$name, $slug, $description]);
                    set_flash('success', 'Category created successfully.');
                }
            } catch (Exception $e) {
                $errors[] = 'Failed to save category. Please try again.';
            }
        }
        
        if (empty($errors)) {
            redirect(ADMIN_URL . '/gallery-categories.php');
        }
    }
}

// Get categories with image count
$categories = $db->fetchAll("
    SELECT gc.*, COUNT(gi.id) as image_count
    FROM gallery_categories gc
    LEFT JOIN gallery_images gi ON gc.id = gi.category_id
    GROUP BY gc.id
    ORDER BY gc.name ASC
");

// Get category for editing
$editCategory = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editCategory = $db->fetch("SELECT * FROM gallery_categories WHERE id = ?", [$_GET['edit']]);
}

include ADMIN_PATH . '/includes/admin_header.php';
?>

<div class="admin-grid">
    <!-- Add/Edit Form -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">
                <?php echo $editCategory ? 'Edit Category' : 'Add New Category'; ?>
            </h2>
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
            
            <form method="POST" class="admin-form">
                <?php echo csrf_field(); ?>
                <?php if ($editCategory): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $editCategory['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="name">Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="<?php echo e($editCategory['name'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="3"><?php echo e($editCategory['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        <?php echo $editCategory ? 'Update' : 'Add'; ?> Category
                    </button>
                    <?php if ($editCategory): ?>
                        <a href="<?php echo ADMIN_URL; ?>/gallery-categories.php" class="btn btn-outline">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Categories List -->
    <div class="admin-card" style="flex: 2;">
        <div class="admin-card-header">
            <h2 class="admin-card-title">All Categories</h2>
            <a href="<?php echo ADMIN_URL; ?>/gallery.php" class="btn btn-outline">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to Gallery
            </a>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <?php if (!empty($categories)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Images</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($category['name']); ?></strong>
                                    <?php if ($category['description']): ?>
                                        <br><small class="text-muted"><?php echo e(truncate($category['description'], 50)); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><code><?php echo e($category['slug']); ?></code></td>
                                <td>
                                    <span class="badge badge-info"><?php echo $category['image_count']; ?> images</span>
                                </td>
                                <td>
                                    <div class="admin-table-actions">
                                        <a href="<?php echo ADMIN_URL; ?>/gallery-categories.php?edit=<?php echo $category['id']; ?>" 
                                           class="btn btn-sm btn-primary" title="Edit">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </a>
                                        <?php if ($category['image_count'] == 0): ?>
                                            <a href="<?php echo ADMIN_URL; ?>/gallery-categories.php?delete=<?php echo $category['id']; ?>&token=<?php echo $_SESSION[CSRF_TOKEN_NAME]; ?>" 
                                               class="btn btn-sm btn-outline" style="color: var(--error); border-color: var(--error);"
                                               onclick="return confirm('Are you sure you want to delete this category?')" title="Delete">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline" disabled title="Cannot delete - has images" 
                                                    style="opacity: 0.5; cursor: not-allowed;">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                </svg>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="1.5">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <h3 class="empty-state-title">No Categories Yet</h3>
                    <p class="empty-state-text">Create your first category using the form.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include ADMIN_PATH . '/includes/admin_footer.php'; ?>
