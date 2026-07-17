<?php
/**
 * Admin Blog Posts Management
 * Urji Beri School Website
 */

require_once dirname(__DIR__) . '/config/config.php';

$adminPageTitle = 'Blog Posts';
$db = Database::getInstance();

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (!isset($_GET['token']) || !verify_csrf($_GET['token'])) {
        set_flash('error', 'Invalid security token.');
    } else {
        $postId = (int)$_GET['delete'];
        
        // Get post to delete image
        $post = $db->fetch("SELECT featured_image FROM blog_posts WHERE id = ?", [$postId]);
        
        if ($post) {
            // Delete featured image if exists
            if ($post['featured_image']) {
                delete_file($post['featured_image'], 'blog');
            }
            
            // Delete post
            $db->query("DELETE FROM blog_posts WHERE id = ?", [$postId]);
            set_flash('success', 'Blog post deleted successfully.');
        }
    }
    redirect(ADMIN_URL . '/blogs.php');
}

// Get current page
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Get filter
$filter = isset($_GET['filter']) ? clean_input($_GET['filter']) : '';

// Count total posts
$countSql = "SELECT COUNT(*) as total FROM blog_posts";
$params = [];

if ($filter === 'published') {
    $countSql .= " WHERE is_published = 1";
} elseif ($filter === 'draft') {
    $countSql .= " WHERE is_published = 0";
}

$totalPosts = $db->fetch($countSql, $params)['total'];
$pagination = paginate($totalPosts, $currentPage, 10, ADMIN_URL . '/blogs.php');

// Get posts
$sql = "SELECT bp.*, bc.name as category_name, u.full_name as author_name
        FROM blog_posts bp
        JOIN blog_categories bc ON bp.category_id = bc.id
        JOIN users u ON bp.author_id = u.id";

if ($filter === 'published') {
    $sql .= " WHERE bp.is_published = 1";
} elseif ($filter === 'draft') {
    $sql .= " WHERE bp.is_published = 0";
}

$sql .= " ORDER BY bp.created_at DESC LIMIT ? OFFSET ?";

$posts = $db->fetchAll($sql, [10, $pagination['offset']]);

include ADMIN_PATH . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <div class="d-flex align-center gap-4">
            <h2 class="admin-card-title">All Blog Posts</h2>
            <div class="gallery-filter" style="margin: 0;">
                <a href="<?php echo ADMIN_URL; ?>/blogs.php" class="filter-btn <?php echo !$filter ? 'active' : ''; ?>">All</a>
                <a href="<?php echo ADMIN_URL; ?>/blogs.php?filter=published" class="filter-btn <?php echo $filter === 'published' ? 'active' : ''; ?>">Published</a>
                <a href="<?php echo ADMIN_URL; ?>/blogs.php?filter=draft" class="filter-btn <?php echo $filter === 'draft' ? 'active' : ''; ?>">Drafts</a>
            </div>
        </div>
        <a href="<?php echo ADMIN_URL; ?>/blog-edit.php" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            New Post
        </a>
    </div>
    <div class="admin-card-body" style="padding: 0;">
        <?php if (!empty($posts)): ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td>
                                <?php if ($post['featured_image']): ?>
                                    <img src="<?php echo upload_url($post['featured_image'], 'blog'); ?>" 
                                         alt="" class="admin-table-img">
                                <?php else: ?>
                                    <div class="admin-table-img" style="background: var(--gray-200); display: flex; align-items: center; justify-content: center;">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--gray-400)" stroke-width="2">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                            <polyline points="21 15 16 10 5 21"></polyline>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo e(truncate($post['title'], 50)); ?></strong>
                            </td>
                            <td><?php echo e($post['category_name']); ?></td>
                            <td><?php echo e($post['author_name']); ?></td>
                            <td>
                                <?php if ($post['is_published']): ?>
                                    <span class="badge badge-success">Published</span>
                                <?php else: ?>
                                    <span class="badge badge-warning">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo number_format($post['views']); ?></td>
                            <td><?php echo format_date($post['created_at']); ?></td>
                            <td>
                                <div class="admin-table-actions">
                                    <a href="<?php echo route_url('blog-detail', ['slug' => $post['slug']]); ?>" 
                                       class="btn btn-sm btn-outline" target="_blank" title="View">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                    </a>
                                    <a href="<?php echo ADMIN_URL; ?>/blog-edit.php?id=<?php echo $post['id']; ?>" 
                                       class="btn btn-sm btn-primary" title="Edit">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </a>
                                    <a href="<?php echo ADMIN_URL; ?>/blogs.php?delete=<?php echo $post['id']; ?>&token=<?php echo $_SESSION[CSRF_TOKEN_NAME]; ?>" 
                                       class="btn btn-sm btn-outline" style="color: var(--error); border-color: var(--error);"
                                       onclick="return confirm('Are you sure you want to delete this post?')" title="Delete">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php echo render_pagination($pagination); ?>
        <?php else: ?>
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="1.5" class="empty-state-icon">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
                <h3 class="empty-state-title">No Blog Posts Yet</h3>
                <p class="empty-state-text">Get started by creating your first blog post.</p>
                <a href="<?php echo ADMIN_URL; ?>/blog-edit.php" class="btn btn-primary">Create First Post</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include ADMIN_PATH . '/includes/admin_footer.php'; ?>
