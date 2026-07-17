<?php
/**
 * About Us Page
 * Urji Beri School Website
 * With Full SEO Implementation
 */

require_once __DIR__ . '/config/config.php';

$pageTitle = 'About Us';

// SEO Configuration
$pageSeo = [
    'title' => 'About Us - ' . get_setting('site_name', 'Urji Beri School'),
    'description' => 'Learn about Urji Beri School - a leading preschool and elementary institution in Alemgena, Oromia. Discover our mission, vision, values, and commitment to quality education.',
    'keywords' => 'about Urji Beri School, Alemgena school history, school mission vision, Ethiopian elementary school, preschool Oromia, quality education Ethiopia',
    'type' => 'website'
];

// Breadcrumb Schema
$breadcrumbSchema = generate_breadcrumb_schema([
    'Home' => SITE_URL,
    'About Us' => route_url('about')
]);

// Get values array from settings
$values = get_values_array('about_values');

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
                <h1 class="page-title">About Us</h1>
                <nav class="breadcrumb">
                    <a href="<?php echo SITE_URL; ?>">Home</a>
                    <span class="breadcrumb-separator">/</span>
                    <span>About Us</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- About Overview Section -->
    <section class="section">
        <div class="container">
            <div class="about-content">
                <div class="about-image">
                    <img src="<?php echo asset_url('images/about-main.jpg'); ?>" alt="About Urji Beri School">
                </div>
                <div class="about-text">
                    <h3 class="text-primary">Our Story</h3>
                    <h2>Urji Beri School</h2>
                    <p><?php echo nl2br(e(get_setting('about_overview'))); ?></p>
                    <div class="mt-6">
                        <p><strong>Education Level:</strong> Preschool & Elementary (Ages 3-13)</p>
                        <p><strong>Languages:</strong> Amharic & English</p>
                        <p><strong>Accreditation:</strong> Oromia Education Bureau</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="section" style="background-color: var(--gray-100);">
        <div class="container">
            <div class="grid grid-2">
                <div class="glass-card-solid">
                    <h3 class="text-primary mb-4">Our Mission</h3>
                    <p class="mb-0"><?php echo nl2br(e(get_setting('about_mission'))); ?></p>
                </div>
                <div class="glass-card-solid">
                    <h3 class="text-primary mb-4">Our Vision</h3>
                    <p class="mb-0"><?php echo nl2br(e(get_setting('about_vision'))); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section (Learner Profile Attributes) -->
    <section class="section">
        <div class="container">
            <div class="grid grid-3">
                <div class="glass-card feature-card">
                    <div class="feature-icon">🔍</div>
                    <h3 class="feature-title">Inquirer</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_inquirer', 'Acquires skills for purposeful, constructive research.')); ?></p>
                </div>
                
                <div class="glass-card feature-card">
                    <div class="feature-icon">🧠</div>
                    <h3 class="feature-title">Thinker</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_thinker', 'Applies thinking skills critically and creatively to solve complex problems.')); ?></p>
                </div>
                
                <div class="glass-card feature-card">
                    <div class="feature-icon">💬</div>
                    <h3 class="feature-title">Communicator</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_communicator', 'Receives & expresses ideas in more than one language including the language of mathematical symbols.')); ?></p>
                </div>
                
                <div class="glass-card feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3 class="feature-title">Risk-taker</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_risk_taker', 'Approaches unfamiliar situations with confidence.')); ?></p>
                </div>
                
                <div class="glass-card feature-card">
                    <div class="feature-icon">⚖️</div>
                    <h3 class="feature-title">Principled</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_principled', 'Displays integrity, honesty and a sense of fairness and justice.')); ?></p>
                </div>
                
                <div class="glass-card feature-card">
                    <div class="feature-icon">💚</div>
                    <h3 class="feature-title">Caring</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_caring', 'Develops a sense of personal commitment to action and service.')); ?></p>
                </div>
                
                <div class="glass-card feature-card">
                    <div class="feature-icon">🌍</div>
                    <h3 class="feature-title">Open-minded</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_open_minded', 'Respects the views, values and traditions of other individuals and cultures and is accustomed to seeking and considering a range of points of view.')); ?></p>
                </div>
                
                <div class="glass-card feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3 class="feature-title">Balanced</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_balanced', 'Understands physical, mental and personal well-being.')); ?></p>
                </div>
                
                <div class="glass-card feature-card">
                    <div class="feature-icon">🔄</div>
                    <h3 class="feature-title">Reflective</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_reflective', 'Analyses own strength and weaknesses.')); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="section" style="background-color: var(--gray-100);">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <div class="feature-icon" style="font-size: 48px; margin-bottom: 1rem;">🎓</div>
                    <h2>Why choose us?</h2>
                    <p><?php echo e(get_setting('about_benefits', 'Our school is an outstanding school with a warm and welcoming learning environment. We place special emphasis on being supportive of the diverse needs of a varied and dynamic school community and on offering all students opportunities for growth and success. Our purpose-built school, nationally inspected and accredited, caring teachers, true Ethiopian learning experience, and individualized approach are designed to enable your child to succeed at school and beyond.')); ?></p>
                </div>
                <div class="about-image">
                    <img src="<?php echo asset_url('images/why-choose-us.jpg'); ?>" alt="Why Choose Urji Beri School">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="section">
        <div class="container">
            <div class="about-content" style="flex-direction: row-reverse;">
                <div class="about-text">
                    <div class="feature-icon" style="font-size: 48px; margin-bottom: 1rem;">🎯</div>
                    <h2>Our Mission</h2>
                    <p><?php echo e(get_setting('about_mission', 'We support a safe, caring, respectful environment that values creativity, diversity, and inclusivity. Develop self-aware learners with the tools for fulfillment in their world and beyond. Provide best practice learning that empowers individuals to set and reach high standards. Encourage students to think globally and act locally.')); ?></p>
                </div>
                <div class="about-image">
                    <img src="<?php echo asset_url('images/mission.jpg'); ?>" alt="Our Mission">
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="section" style="background-color: var(--gray-100);">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <div class="feature-icon" style="font-size: 48px; margin-bottom: 1rem;">💎</div>
                    <h2>Our Value</h2>
                    <p><?php echo e(get_setting('about_values', 'We believe in learner agency and the power of inquiry. There is strength in diversity and inclusivity. That we all should listen thoughtfully to others and consider their points of view. We learn best when we feel safe, happy, valued, and challenged. It is important to strive to be the best you can be. We should look beyond ourselves and seek to make genuine, positive, sustainable changes in the world around us.')); ?></p>
                </div>
                <div class="about-image">
                    <img src="<?php echo asset_url('images/values.jpg'); ?>" alt="Our Values">
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title">Ready to Join Our Community?</h2>
                <p class="cta-text">Contact us today to learn more about enrollment opportunities at Urji Beri School.</p>
                <a href="<?php echo route_url('contact'); ?>" class="btn btn-primary btn-lg">Get in Touch</a>
            </div>
        </div>
    </section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
