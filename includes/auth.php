<?php
/**
 * Authentication Helper Functions
 * Urji Beri School Website
 */

// Prevent direct access
if (!defined('BASE_PATH')) {
    exit('Direct access not allowed');
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current logged in user
 */
function current_user() {
    if (!is_logged_in()) return null;
    
    static $user = null;
    
    if ($user === null) {
        $db = Database::getInstance();
        $user = $db->fetch(
            "SELECT u.*, r.name as role_name 
             FROM users u 
             JOIN roles r ON u.role_id = r.id 
             WHERE u.id = ? AND u.is_active = 1",
            [$_SESSION['user_id']]
        );
    }
    
    return $user;
}

/**
 * Check if current user has admin role
 */
function is_admin() {
    $user = current_user();
    return $user && $user['role_name'] === 'admin';
}

/**
 * Attempt to login user
 */
function attempt_login($username, $password) {
    $db = Database::getInstance();
    
    // Find user by username or email
    $user = $db->fetch(
        "SELECT u.*, r.name as role_name 
         FROM users u 
         JOIN roles r ON u.role_id = r.id 
         WHERE (u.username = ? OR u.email = ?) AND u.is_active = 1",
        [$username, $username]
    );
    
    if (!$user) {
        return ['success' => false, 'error' => 'Invalid username or password'];
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'error' => 'Invalid username or password'];
    }
    
    // Update last login
    $db->query(
        "UPDATE users SET last_login = NOW() WHERE id = ?",
        [$user['id']]
    );
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_role'] = $user['role_name'];
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    return ['success' => true, 'user' => $user];
}

/**
 * Logout user
 */
function logout() {
    // Unset all session variables
    $_SESSION = [];
    
    // Destroy session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    
    // Destroy session
    session_destroy();
}

/**
 * Require authentication - redirect if not logged in
 */
function require_auth() {
    if (!is_logged_in()) {
        set_flash('error', 'Please login to access this page');
        redirect(ADMIN_URL . '/login.php');
    }
}

/**
 * Require admin role
 */
function require_admin() {
    require_auth();
    if (!is_admin()) {
        set_flash('error', 'You do not have permission to access this page');
        redirect(ADMIN_URL . '/index.php');
    }
}

/**
 * Update user password
 */
function update_password($userId, $newPassword) {
    $db = Database::getInstance();
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    return $db->query(
        "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?",
        [$hashedPassword, $userId]
    );
}

/**
 * Verify current password
 */
function verify_password($userId, $password) {
    $db = Database::getInstance();
    $user = $db->fetch("SELECT password FROM users WHERE id = ?", [$userId]);
    
    if (!$user) return false;
    
    return password_verify($password, $user['password']);
}

/**
 * Create new user
 */
function create_user($data) {
    $db = Database::getInstance();
    
    // Check if username exists
    $exists = $db->fetch(
        "SELECT id FROM users WHERE username = ? OR email = ?",
        [$data['username'], $data['email']]
    );
    
    if ($exists) {
        return ['success' => false, 'error' => 'Username or email already exists'];
    }
    
    // Hash password
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    
    // Insert user
    $db->query(
        "INSERT INTO users (role_id, username, email, password, full_name) VALUES (?, ?, ?, ?, ?)",
        [
            $data['role_id'] ?? 2,
            $data['username'],
            $data['email'],
            $hashedPassword,
            $data['full_name']
        ]
    );
    
    return ['success' => true, 'user_id' => $db->lastInsertId()];
}
