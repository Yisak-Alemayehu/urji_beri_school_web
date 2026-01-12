<?php
/**
 * Contact Page
 * Urji Beri School Website
 * With Full SEO Implementation
 */

require_once __DIR__ . '/config/config.php';

$pageTitle = 'Contact Us';
$db = Database::getInstance();

// SEO Configuration
$pageSeo = [
    'title' => 'Contact Us - ' . get_setting('site_name', 'Urji Beri School'),
    'description' => 'Contact Urji Beri School in Alemgena, Oromia. Reach us by phone, email, or visit our campus. We welcome inquiries about admissions, programs, and more.',
    'keywords' => 'contact Urji Beri School, school phone number, Alemgena school address, school email, admissions inquiry, Ethiopian school contact',
    'type' => 'website'
];

// Breadcrumb Schema
$breadcrumbSchema = generate_breadcrumb_schema([
    'Home' => SITE_URL,
    'Contact Us' => SITE_URL . '/contact.php'
]);

$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST[CSRF_TOKEN_NAME]) || !verify_csrf($_POST[CSRF_TOKEN_NAME])) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        // Sanitize inputs
        $name = clean_input($_POST['name'] ?? '');
        $email = clean_input($_POST['email'] ?? '');
        $phone = clean_input($_POST['phone'] ?? '');
        $subject = clean_input($_POST['subject'] ?? '');
        $message = clean_input($_POST['message'] ?? '');
        
        // Validate inputs
        if (empty($name)) {
            $errors[] = 'Please enter your name.';
        }
        
        if (empty($email)) {
            $errors[] = 'Please enter your email address.';
        } elseif (!is_valid_email($email)) {
            $errors[] = 'Please enter a valid email address.';
        }
        
        if (!empty($phone) && !is_valid_phone($phone)) {
            $errors[] = 'Please enter a valid phone number.';
        }
        
        if (empty($subject)) {
            $errors[] = 'Please enter a subject.';
        }
        
        if (empty($message)) {
            $errors[] = 'Please enter your message.';
        } elseif (strlen($message) < 10) {
            $errors[] = 'Message must be at least 10 characters.';
        }
        
        // If no errors, save to database
        if (empty($errors)) {
            try {
                $db->query(
                    "INSERT INTO contact_messages (name, email, phone, subject, message, ip_address, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, NOW())",
                    [$name, $email, $phone, $subject, $message, get_client_ip()]
                );
                
                $success = true;
                
                // Clear form data on success
                $name = $email = $phone = $subject = $message = '';
                
            } catch (Exception $e) {
                error_log("Contact form error: " . $e->getMessage());
                $errors[] = 'An error occurred. Please try again later.';
            }
        }
    }
}

// Get map coordinates
$mapLat = get_setting('map_latitude', '8.9806');
$mapLng = get_setting('map_longitude', '38.7578');
$mapZoom = get_setting('map_zoom', '15');

include INCLUDES_PATH . '/header.php';
?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="page-header-shapes">
            <div class="page-header-shape page-header-shape-1"></div>
            <div class="page-header-shape page-header-shape-2"></div>
        </div>
        <div class="container">
            <div class="page-header-content">
                <h1 class="page-title">Contact Us</h1>
                <nav class="breadcrumb">
                    <a href="<?php echo SITE_URL; ?>">Home</a>
                    <span class="breadcrumb-separator">/</span>
                    <span>Contact</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Get in Touch</h2>
                <p class="section-subtitle">We'd love to hear from you. Send us a message or visit our campus.</p>
            </div>
            
            <div class="contact-grid">
                <!-- Contact Form -->
                <div class="glass-card-solid">
                    <h3 class="mb-6">Send Us a Message</h3>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <strong>Thank you!</strong> Your message has been sent successfully. We'll get back to you soon.
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-error">
                            <ul style="margin: 0; padding-left: 20px;">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" id="contactForm">
                        <?php echo csrf_field(); ?>
                        
                        <div class="admin-form-row">
                            <div class="form-group">
                                <label class="form-label" for="name">Your Name *</label>
                                <input type="text" id="name" name="name" class="form-control" 
                                       value="<?php echo e($name ?? ''); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="email">Email Address *</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       value="<?php echo e($email ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="admin-form-row">
                            <div class="form-group">
                                <label class="form-label" for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-control" 
                                       value="<?php echo e($phone ?? ''); ?>" placeholder="+251-xxx-xxx-xxx">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="subject">Subject *</label>
                                <select id="subject" name="subject" class="form-control" required>
                                    <option value="">Select a subject</option>
                                    <option value="Admission Inquiry" <?php echo (($subject ?? '') === 'Admission Inquiry') ? 'selected' : ''; ?>>Admission Inquiry</option>
                                    <option value="General Question" <?php echo (($subject ?? '') === 'General Question') ? 'selected' : ''; ?>>General Question</option>
                                    <option value="Schedule Visit" <?php echo (($subject ?? '') === 'Schedule Visit') ? 'selected' : ''; ?>>Schedule a Visit</option>
                                    <option value="Feedback" <?php echo (($subject ?? '') === 'Feedback') ? 'selected' : ''; ?>>Feedback</option>
                                    <option value="Other" <?php echo (($subject ?? '') === 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="message">Your Message *</label>
                            <textarea id="message" name="message" class="form-control" rows="5" 
                                      required minlength="10"><?php echo e($message ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg">
                            Send Message
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left: var(--spacing-2);">
                                <line x1="22" y1="2" x2="11" y2="13"></line>
                                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                            </svg>
                        </button>
                    </form>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <div class="contact-info">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </div>
                            <div class="contact-details">
                                <h4>Our Location</h4>
                                <p><?php echo e(get_setting('contact_address')); ?></p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </div>
                            <div class="contact-details">
                                <h4>Phone Number</h4>
                                <p><a href="tel:<?php echo e(get_setting('contact_phone')); ?>"><?php echo e(get_setting('contact_phone')); ?></a></p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </div>
                            <div class="contact-details">
                                <h4>Email Address</h4>
                                <p><a href="mailto:<?php echo e(get_setting('contact_email')); ?>"><?php echo e(get_setting('contact_email')); ?></a></p>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </div>
                            <div class="contact-details">
                                <h4>Follow Us</h4>
                                <p><a href="<?php echo e(get_setting('facebook_url')); ?>" target="_blank">Urji Beri Primary School</a></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Map -->
                    <div class="contact-map mt-6">
                        <iframe 
                            src="https://www.openstreetmap.org/export/embed.html?bbox=<?php echo ($mapLng - 0.01); ?>%2C<?php echo ($mapLat - 0.01); ?>%2C<?php echo ($mapLng + 0.01); ?>%2C<?php echo ($mapLat + 0.01); ?>&layer=mapnik&marker=<?php echo $mapLat; ?>%2C<?php echo $mapLng; ?>"
                            style="border:0; width: 100%; height: 100%;" 
                            allowfullscreen="" 
                            loading="lazy"
                            title="School Location Map">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Visit Our Campus</h2>
                <p class="cta-text">We invite you to visit Urji Beri School and see our facilities firsthand. Schedule a campus tour today!</p>
                <a href="tel:<?php echo e(get_setting('contact_phone')); ?>" class="btn btn-primary btn-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: var(--spacing-2);">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                    Call to Schedule
                </a>
            </div>
        </div>
    </section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
