<?php
/**
 * Admin Messages Management
 * Urji Beri School Website
 */

require_once dirname(__DIR__) . '/config/config.php';

$adminPageTitle = 'Messages';
$db = Database::getInstance();

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    if (!isset($_GET['token']) || !verify_csrf($_GET['token'])) {
        set_flash('error', 'Invalid security token.');
    } else {
        $messageId = (int)$_GET['delete'];
        $db->query("DELETE FROM contact_messages WHERE id = ?", [$messageId]);
        set_flash('success', 'Message deleted successfully.');
    }
    redirect(ADMIN_URL . '/messages.php');
}

// Handle mark as read/unread
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    if (!isset($_GET['token']) || !verify_csrf($_GET['token'])) {
        set_flash('error', 'Invalid security token.');
    } else {
        $messageId = (int)$_GET['toggle'];
        $message = $db->fetch("SELECT is_read FROM contact_messages WHERE id = ?", [$messageId]);
        if ($message) {
            $newStatus = $message['is_read'] ? 0 : 1;
            $db->query("UPDATE contact_messages SET is_read = ? WHERE id = ?", [$newStatus, $messageId]);
            set_flash('success', 'Message status updated.');
        }
    }
    redirect(ADMIN_URL . '/messages.php');
}

// Handle bulk mark as read
if (isset($_GET['mark_all_read'])) {
    if (!isset($_GET['token']) || !verify_csrf($_GET['token'])) {
        set_flash('error', 'Invalid security token.');
    } else {
        $db->query("UPDATE contact_messages SET is_read = 1 WHERE is_read = 0");
        set_flash('success', 'All messages marked as read.');
    }
    redirect(ADMIN_URL . '/messages.php');
}

// Get current page
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

// Get filter
$filter = isset($_GET['filter']) ? clean_input($_GET['filter']) : '';

// Count total messages
$countSql = "SELECT COUNT(*) as total FROM contact_messages";
$params = [];

if ($filter === 'unread') {
    $countSql .= " WHERE is_read = 0";
} elseif ($filter === 'read') {
    $countSql .= " WHERE is_read = 1";
}

$totalMessages = $db->fetch($countSql, $params)['total'];
$pagination = paginate($totalMessages, $currentPage, 15, ADMIN_URL . '/messages.php');

// Get messages
$sql = "SELECT * FROM contact_messages";

if ($filter === 'unread') {
    $sql .= " WHERE is_read = 0";
} elseif ($filter === 'read') {
    $sql .= " WHERE is_read = 1";
}

$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";

$messages = $db->fetchAll($sql, [15, $pagination['offset']]);

// Count unread
$unreadCount = $db->fetch("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")['count'];

include ADMIN_PATH . '/includes/admin_header.php';
?>

<div class="admin-card">
    <div class="admin-card-header">
        <div class="d-flex align-center gap-4">
            <h2 class="admin-card-title">Contact Messages</h2>
            <div class="gallery-filter" style="margin: 0;">
                <a href="<?php echo ADMIN_URL; ?>/messages.php" class="filter-btn <?php echo !$filter ? 'active' : ''; ?>">
                    All (<?php echo $totalMessages; ?>)
                </a>
                <a href="<?php echo ADMIN_URL; ?>/messages.php?filter=unread" class="filter-btn <?php echo $filter === 'unread' ? 'active' : ''; ?>">
                    Unread (<?php echo $unreadCount; ?>)
                </a>
                <a href="<?php echo ADMIN_URL; ?>/messages.php?filter=read" class="filter-btn <?php echo $filter === 'read' ? 'active' : ''; ?>">
                    Read
                </a>
            </div>
        </div>
        <?php if ($unreadCount > 0): ?>
            <a href="<?php echo ADMIN_URL; ?>/messages.php?mark_all_read=1&token=<?php echo $_SESSION[CSRF_TOKEN_NAME]; ?>" 
               class="btn btn-outline"
               onclick="return confirm('Mark all messages as read?')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                Mark All Read
            </a>
        <?php endif; ?>
    </div>
    <div class="admin-card-body" style="padding: 0;">
        <?php if (!empty($messages)): ?>
            <div class="message-list">
                <?php foreach ($messages as $message): ?>
                    <div class="message-item <?php echo !$message['is_read'] ? 'unread' : ''; ?>">
                        <div class="message-header">
                            <div class="message-sender">
                                <span class="sender-avatar">
                                    <?php echo strtoupper(substr($message['name'], 0, 1)); ?>
                                </span>
                                <div class="sender-info">
                                    <strong><?php echo e($message['name']); ?></strong>
                                    <span class="sender-email"><?php echo e($message['email']); ?></span>
                                    <?php if ($message['phone']): ?>
                                        <span class="sender-phone"><?php echo e($message['phone']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="message-meta">
                                <span class="message-date">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <polyline points="12 6 12 12 16 14"></polyline>
                                    </svg>
                                    <?php echo format_date($message['created_at'], true); ?>
                                </span>
                                <?php if (!$message['is_read']): ?>
                                    <span class="badge badge-primary">New</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="message-subject">
                            <strong><?php echo e($message['subject']); ?></strong>
                        </div>
                        <div class="message-body">
                            <?php echo nl2br(e($message['message'])); ?>
                        </div>
                        <div class="message-actions">
                            <a href="mailto:<?php echo e($message['email']); ?>?subject=Re: <?php echo e($message['subject']); ?>" 
                               class="btn btn-sm btn-primary">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                Reply
                            </a>
                            <a href="<?php echo ADMIN_URL; ?>/messages.php?toggle=<?php echo $message['id']; ?>&token=<?php echo $_SESSION[CSRF_TOKEN_NAME]; ?>" 
                               class="btn btn-sm btn-outline">
                                <?php if ($message['is_read']): ?>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                    </svg>
                                    Mark Unread
                                <?php else: ?>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="20 6 9 17 4 12"></polyline>
                                    </svg>
                                    Mark Read
                                <?php endif; ?>
                            </a>
                            <a href="<?php echo ADMIN_URL; ?>/messages.php?delete=<?php echo $message['id']; ?>&token=<?php echo $_SESSION[CSRF_TOKEN_NAME]; ?>" 
                               class="btn btn-sm btn-outline" style="color: var(--error); border-color: var(--error);"
                               onclick="return confirm('Are you sure you want to delete this message?')">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                                Delete
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php echo render_pagination($pagination); ?>
        <?php else: ?>
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="var(--gray-300)" stroke-width="1.5">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                    <polyline points="22,6 12,13 2,6"></polyline>
                </svg>
                <h3 class="empty-state-title">No Messages</h3>
                <p class="empty-state-text">
                    <?php echo $filter === 'unread' ? 'No unread messages.' : 'No messages yet.'; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include ADMIN_PATH . '/includes/admin_footer.php'; ?>
