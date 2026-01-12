<?php
/**
 * Admin Dashboard
 * Urji Beri School Website
 */

require_once dirname(__DIR__) . '/config/config.php';

$adminPageTitle = 'Dashboard';
$db = Database::getInstance();

// Get statistics
$stats = [
    'blogs' => $db->fetch("SELECT COUNT(*) as count FROM blog_posts")['count'],
    'published_blogs' => $db->fetch("SELECT COUNT(*) as count FROM blog_posts WHERE is_published = 1")['count'],
    'gallery_images' => $db->fetch("SELECT COUNT(*) as count FROM gallery_images WHERE is_active = 1")['count'],
    'messages' => $db->fetch("SELECT COUNT(*) as count FROM contact_messages")['count'],
    'unread_messages' => $db->fetch("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")['count']
];

// Get recent blog posts
$recentPosts = $db->fetchAll(
    "SELECT bp.*, bc.name as category_name 
     FROM blog_posts bp 
     JOIN blog_categories bc ON bp.category_id = bc.id 
     ORDER BY bp.created_at DESC 
     LIMIT 5"
);

// Get recent messages
$recentMessages = $db->fetchAll(
    "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5"
);

include ADMIN_PATH . '/includes/admin_header.php';
?>

<!-- Stats Row -->
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-card-icon primary">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
            </svg>
        </div>
        <div class="stat-card-content">
            <h3><?php echo $stats['blogs']; ?></h3>
            <p>Total Blog Posts</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-icon success">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                <polyline points="21 15 16 10 5 21"></polyline>
            </svg>
        </div>
        <div class="stat-card-content">
            <h3><?php echo $stats['gallery_images']; ?></h3>
            <p>Gallery Images</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-icon warning">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
        </div>
        <div class="stat-card-content">
            <h3><?php echo $stats['messages']; ?></h3>
            <p>Contact Messages</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-icon info">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
        </div>
        <div class="stat-card-content">
            <h3><?php echo $stats['unread_messages']; ?></h3>
            <p>Unread Messages</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="admin-card">
    <div class="admin-card-header">
        <h2 class="admin-card-title">Quick Actions</h2>
    </div>
    <div class="admin-card-body">
        <div class="quick-actions">
            <a href="<?php echo ADMIN_URL; ?>/blog-edit.php" class="quick-action-card">
                <div class="quick-action-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </div>
                <span class="quick-action-label">New Blog Post</span>
            </a>
            <a href="<?php echo ADMIN_URL; ?>/gallery.php?action=add" class="quick-action-card">
                <div class="quick-action-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                </div>
                <span class="quick-action-label">Upload Images</span>
            </a>
            <a href="<?php echo ADMIN_URL; ?>/messages.php" class="quick-action-card">
                <div class="quick-action-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                        <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                </div>
                <span class="quick-action-label">View Messages</span>
            </a>
            <a href="<?php echo ADMIN_URL; ?>/settings.php" class="quick-action-card">
                <div class="quick-action-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                </div>
                <span class="quick-action-label">Settings</span>
            </a>
        </div>
    </div>
</div>

<div class="grid grid-2">
    <!-- Recent Blog Posts -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Recent Blog Posts</h2>
            <a href="<?php echo ADMIN_URL; ?>/blogs.php" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <?php if (!empty($recentPosts)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPosts as $post): ?>
                            <tr>
                                <td>
                                    <a href="<?php echo ADMIN_URL; ?>/blog-edit.php?id=<?php echo $post['id']; ?>">
                                        <?php echo e(truncate($post['title'], 40)); ?>
                                    </a>
                                </td>
                                <td><?php echo e($post['category_name']); ?></td>
                                <td>
                                    <?php if ($post['is_published']): ?>
                                        <span class="badge badge-success">Published</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo format_date($post['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <p class="text-muted">No blog posts yet.</p>
                    <a href="<?php echo ADMIN_URL; ?>/blog-edit.php" class="btn btn-primary btn-sm">Create First Post</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recent Messages -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h2 class="admin-card-title">Recent Messages</h2>
            <a href="<?php echo ADMIN_URL; ?>/messages.php" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <?php if (!empty($recentMessages)): ?>
                <?php foreach ($recentMessages as $msg): ?>
                    <div class="message-item <?php echo !$msg['is_read'] ? 'unread' : ''; ?>">
                        <div class="message-avatar">
                            <?php echo strtoupper(substr($msg['name'], 0, 1)); ?>
                        </div>
                        <div class="message-content">
                            <div class="message-header">
                                <span class="message-sender"><?php echo e($msg['name']); ?></span>
                                <span class="message-date"><?php echo time_ago($msg['created_at']); ?></span>
                            </div>
                            <div class="message-subject"><?php echo e($msg['subject']); ?></div>
                            <div class="message-preview"><?php echo e(truncate($msg['message'], 60)); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p class="text-muted">No messages yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include ADMIN_PATH . '/includes/admin_footer.php'; ?>
