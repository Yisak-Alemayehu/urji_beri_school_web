<?php
/**
 * Admin Blog Post Edit/Create
 * Urji Beri School Website
 */

require_once dirname(__DIR__) . '/config/config.php';

$db = Database::getInstance();
$errors = [];
$post = [
    'id' => null,
    'title' => '',
    'slug' => '',
    'excerpt' => '',
    'content' => '',
    'featured_image' => '',
    'category_id' => '',
    'is_published' => 0,
    'is_featured' => 0
];

// Check if editing existing post
$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);

if ($isEdit) {
    $existingPost = $db->fetch("SELECT * FROM blog_posts WHERE id = ?", [$_GET['id']]);
    if (!$existingPost) {
        set_flash('error', 'Blog post not found.');
        redirect(ADMIN_URL . '/blogs.php');
    }
    $post = array_merge($post, $existingPost);
    $adminPageTitle = 'Edit Post';
} else {
    $adminPageTitle = 'New Post';
}

// Get categories
$categories = $db->fetchAll("SELECT * FROM blog_categories ORDER BY name ASC");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        // Get form data
        $post['title'] = clean_input($_POST['title'] ?? '');
        $post['excerpt'] = clean_input($_POST['excerpt'] ?? '');
        $post['content'] = $_POST['content'] ?? ''; // Don't clean HTML content
        $post['category_id'] = (int)($_POST['category_id'] ?? 0);
        $post['is_published'] = isset($_POST['is_published']) ? 1 : 0;
        $post['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
        
        // Validate
        if (empty($post['title'])) {
            $errors[] = 'Title is required.';
        }
        if (empty($post['content'])) {
            $errors[] = 'Content is required.';
        }
        if (empty($post['category_id'])) {
            $errors[] = 'Category is required.';
        }
        
        // Generate slug
        if (empty($errors)) {
            if ($isEdit) {
                $post['slug'] = unique_slug($post['title'], 'blog_posts', $post['id']);
            } else {
                $post['slug'] = unique_slug($post['title'], 'blog_posts');
            }
        }
        
        // Handle image upload
        $newImage = null;
        if (!empty($_FILES['featured_image']['name'])) {
            $uploadResult = upload_file($_FILES['featured_image'], 'blog');
            if ($uploadResult['success']) {
                $newImage = $uploadResult['filename'];
            } else {
                $errors[] = $uploadResult['error'];
            }
        }
        
        // Save if no errors
        if (empty($errors)) {
            try {
                if ($isEdit) {
                    // Update existing post
                    $sql = "UPDATE blog_posts SET 
                            title = ?, slug = ?, excerpt = ?, content = ?, 
                            category_id = ?, is_published = ?, is_featured = ?, 
                            updated_at = NOW()";
                    $params = [
                        $post['title'], $post['slug'], $post['excerpt'], $post['content'],
                        $post['category_id'], $post['is_published'], $post['is_featured']
                    ];
                    
                    if ($newImage) {
                        // Delete old image
                        if ($post['featured_image']) {
                            delete_file($post['featured_image'], 'blog');
                        }
                        $sql .= ", featured_image = ?";
                        $params[] = $newImage;
                    }
                    
                    $sql .= " WHERE id = ?";
                    $params[] = $post['id'];
                    
                    $db->query($sql, $params);
                    set_flash('success', 'Blog post updated successfully.');
                } else {
                    // Create new post
                    $featuredImage = $newImage ?? '';
                    
                    $sql = "INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, 
                            category_id, author_id, is_published, is_featured, created_at, updated_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
                    
                    $db->query($sql, [
                        $post['title'], $post['slug'], $post['excerpt'], $post['content'],
                        $featuredImage, $post['category_id'], current_user()['id'],
                        $post['is_published'], $post['is_featured']
                    ]);
                    
                    set_flash('success', 'Blog post created successfully.');
                }
                
                redirect(ADMIN_URL . '/blogs.php');
            } catch (Exception $e) {
                $errors[] = 'Failed to save blog post. Please try again.';
                // Delete uploaded image if save failed
                if ($newImage) {
                    delete_file($newImage, 'blog');
                }
            }
        }
    }
}

include ADMIN_PATH . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title"><?php echo $isEdit ? 'Edit Post' : 'Create New Post'; ?></h2>
        <a href="<?php echo ADMIN_URL; ?>/blogs.php" class="btn btn-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Posts
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
                <div class="form-group" style="flex: 2;">
                    <label for="title">Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" class="form-control" 
                           value="<?php echo e($post['title']); ?>" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="category_id">Category <span class="required">*</span></label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                    <?php echo $post['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo e($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="excerpt">Excerpt / SEO Description</label>
                <textarea id="excerpt" name="excerpt" class="form-control" rows="2" 
                          placeholder="Brief summary for search engines (150-160 characters recommended)"><?php echo e($post['excerpt']); ?></textarea>
                <small class="form-text text-muted">This appears in search results and social media shares. Keep it under 160 characters for best results.</small>
            </div>
            
            <div class="form-group">
                <label for="content">Content <span class="required">*</span></label>
                <textarea id="content" name="content" class="form-control" rows="15" required><?php echo e($post['content']); ?></textarea>
                <small class="form-text">You can use HTML tags for formatting.</small>
            </div>
            
            <div class="form-group">
                <label>Featured Image</label>
                <div class="file-upload-wrapper">
                    <?php if ($post['featured_image']): ?>
                        <div class="current-image">
                            <img src="<?php echo upload_url($post['featured_image'], 'blog'); ?>" 
                                 alt="Current featured image" class="preview-image">
                            <p>Current image. Upload a new image to replace it.</p>
                        </div>
                    <?php endif; ?>
                    <div class="file-upload-area">
                        <input type="file" name="featured_image" id="featured_image" 
                               accept="image/jpeg,image/png,image/gif,image/webp" class="file-input">
                        <label for="featured_image" class="file-label">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="17 8 12 3 7 8"></polyline>
                                <line x1="12" y1="3" x2="12" y2="15"></line>
                            </svg>
                            <span>Click to upload or drag and drop</span>
                            <small>JPEG, PNG, GIF, WebP (Max 5MB)</small>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_published" value="1" 
                               <?php echo $post['is_published'] ? 'checked' : ''; ?>>
                        <span class="checkbox-custom"></span>
                        Publish this post
                    </label>
                </div>
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" value="1" 
                               <?php echo $post['is_featured'] ? 'checked' : ''; ?>>
                        <span class="checkbox-custom"></span>
                        Feature this post
                    </label>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-lg">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <?php echo $isEdit ? 'Update Post' : 'Create Post'; ?>
                </button>
                <a href="<?php echo ADMIN_URL; ?>/blogs.php" class="btn btn-outline btn-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include ADMIN_PATH . '/includes/admin_footer.php'; ?>
