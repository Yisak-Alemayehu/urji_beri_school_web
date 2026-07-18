<?php
/**
 * Admin Login Page
 * Urji Beri School Website
 */

require_once dirname(__DIR__) . '/config/config.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect(ADMIN_URL . '/index.php');
}

$error = '';

// Handle login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!isset($_POST[CSRF_TOKEN_NAME]) || !verify_csrf($_POST[CSRF_TOKEN_NAME])) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $username = clean_input($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password.';
        } else {
            $result = attempt_login($username, $password);
            
            if ($result['success']) {
                redirect(ADMIN_URL . '/index.php');
            } else {
                $error = $result['error'];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1E3A8A">
    <title>Admin Login | <?php echo e(get_setting('site_name')); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="<?php echo e(branding_url('site_favicon')); ?>" sizes="any">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="<?php echo asset_url('css/style.css'); ?>?v=<?php echo asset_version(ASSETS_PATH . '/css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/admin.css'); ?>?v=<?php echo asset_version(ASSETS_PATH . '/css/admin.css'); ?>">
</head>
<body class="admin-app">
    <div class="login-page">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <img src="<?php echo e(branding_url('site_logo')); ?>" alt="<?php echo e(get_setting('site_name')); ?>" class="login-logo">
                    <h1 class="login-title">Admin Sign In</h1>
                    <p class="login-subtitle">Enter your credentials to manage <?php echo e(get_setting('site_name')); ?>.</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo e($error); ?></div>
                <?php endif; ?>
                
                <?php display_flash(); ?>
                
                <form method="POST" action="" class="login-form">
                    <?php echo csrf_field(); ?>
                    
                    <div class="form-group">
                        <label class="form-label" for="username">Username or Email</label>
                        <input type="text" id="username" name="username" class="form-control" 
                               value="<?php echo e($_POST['username'] ?? ''); ?>" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Sign In</button>
                </form>
                
                <div class="login-footer">
                    <a href="<?php echo SITE_URL; ?>">← Back to website</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
