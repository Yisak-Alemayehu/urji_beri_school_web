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
                    <img src="<?php echo asset_url('images/about-main.jpg'); ?>" alt="About Urji Beri School" loading="lazy">
                </div>
                <div class="about-text">
                    <p class="section-kicker">Our Story</p>
                    <h2><?php echo e(get_setting('site_name', 'Urji Beri School')); ?></h2>
                    <p><?php echo nl2br(e(get_setting('about_overview', 'Urji Beri School offers education for children aged 3 to 13. Its curriculum is based on the programs of the Ministry of Education and taught in Amharic and English language.'))); ?></p>
                    <div class="about-meta mt-6">
                        <p><strong>Education Level:</strong> Preschool &amp; Elementary (Ages 3–13)</p>
                        <p><strong>Languages:</strong> Amharic &amp; English</p>
                        <p><strong>Accreditation:</strong> Oromia Education Bureau</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="section mission-vision-section" style="background-color: var(--bg-section);">
        <div class="container">
            <div class="section-header">
                <p class="section-kicker">Purpose</p>
                <h2 class="section-title">Mission &amp; Vision</h2>
                <p class="section-subtitle">What guides every classroom, every teacher, and every child at Urji Beri School.</p>
            </div>
            <div class="grid grid-2 mission-vision-grid">
                <article class="glass-card-solid mission-card">
                    <span class="mission-card-label">Mission</span>
                    <h3>Our Mission</h3>
                    <p><?php echo nl2br(e(get_setting('about_mission', 'We support a safe, caring, respectful environment that values creativity, diversity, and inclusivity. Develop self-aware learners with the tools for fulfillment in their world and beyond. Provide best practice learning that empowers individuals to set and reach high standards. Encourage students to think globally and act locally.'))); ?></p>
                </article>
                <article class="glass-card-solid mission-card">
                    <span class="mission-card-label">Vision</span>
                    <h3>Our Vision</h3>
                    <p><?php echo nl2br(e(get_setting('about_vision', 'To empower students to acquire, demonstrate, and value knowledge and skills as lifelong learners who contribute to the global world.'))); ?></p>
                </article>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="section">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <p class="section-kicker">Beliefs</p>
                    <h2>Our Values</h2>
                    <p><?php echo nl2br(e(get_setting('about_values', 'We believe in learner agency and the power of inquiry. There is strength in diversity and inclusivity. That we all should listen thoughtfully to others and consider their points of view. We learn best when we feel safe, happy, valued, and challenged. It is important to strive to be the best you can be. We should look beyond ourselves and seek to make genuine, positive, sustainable changes in the world around us.'))); ?></p>
                </div>
                <div class="about-image">
                    <img src="<?php echo asset_url('images/values.jpg'); ?>" alt="Our Values" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- Learner Profile -->
    <section class="section" style="background-color: var(--bg-section);">
        <div class="container">
            <div class="section-header">
                <p class="section-kicker">Learner Profile</p>
                <h2 class="section-title">The qualities we nurture</h2>
                <p class="section-subtitle">Nine attributes that shape confident, caring, and capable students.</p>
            </div>
            <div class="grid grid-3 anim-grid">
                <div class="glass-card feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                    <h3 class="feature-title">Inquirer</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_inquirer', 'Acquires skills for purposeful, constructive research.')); ?></p>
                </div>
                <div class="glass-card feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a7 7 0 0 1 7 7c0 2.5-1.3 4.1-3 5.5V17H8v-2.5C6.3 13.1 5 11.5 5 9a7 7 0 0 1 7-7z"></path><line x1="9" y1="21" x2="15" y2="21"></line></svg>
                    </div>
                    <h3 class="feature-title">Thinker</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_thinker', 'Applies thinking skills critically and creatively to solve complex problems.')); ?></p>
                </div>
                <div class="glass-card feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    </div>
                    <h3 class="feature-title">Communicator</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_communicator', 'Receives & expresses ideas in more than one language including the language of mathematical symbols.')); ?></p>
                </div>
                <div class="glass-card feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="12" r="6"></circle><circle cx="12" cy="12" r="2"></circle></svg>
                    </div>
                    <h3 class="feature-title">Risk-taker</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_risk_taker', 'Approaches unfamiliar situations with confidence.')); ?></p>
                </div>
                <div class="glass-card feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </div>
                    <h3 class="feature-title">Principled</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_principled', 'Displays integrity, honesty and a sense of fairness and justice.')); ?></p>
                </div>
                <div class="glass-card feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    </div>
                    <h3 class="feature-title">Caring</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_caring', 'Develops a sense of personal commitment to action and service.')); ?></p>
                </div>
                <div class="glass-card feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M2 12h20"></path><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                    </div>
                    <h3 class="feature-title">Open-minded</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_open_minded', 'Respects the views, values and traditions of other individuals and cultures.')); ?></p>
                </div>
                <div class="glass-card feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"></path></svg>
                    </div>
                    <h3 class="feature-title">Balanced</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_balanced', 'Understands physical, mental and personal well-being.')); ?></p>
                </div>
                <div class="glass-card feature-card">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
                    </div>
                    <h3 class="feature-title">Reflective</h3>
                    <p class="feature-text"><?php echo e(get_setting('learner_reflective', 'Analyses own strength and weaknesses.')); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="section">
        <div class="container">
            <div class="about-content">
                <div class="about-image">
                    <img src="<?php echo asset_url('images/why-choose-us.jpg'); ?>" alt="Why Choose Urji Beri School" loading="lazy">
                </div>
                <div class="about-text">
                    <p class="section-kicker">Why Families Choose Us</p>
                    <h2>A school built for growth</h2>
                    <p><?php echo nl2br(e(get_setting('about_benefits', 'Our school is an outstanding school with a warm and welcoming learning environment. We place special emphasis on being supportive of the diverse needs of a varied and dynamic school community and on offering all students opportunities for growth and success.'))); ?></p>
                    <a href="<?php echo route_url('director'); ?>" class="btn btn-outline mt-4">Read Director's Welcome</a>
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
