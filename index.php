<?php
/**
 * Home Page
 * Urji Beri School Website
 * With Full SEO Implementation
 */

require_once __DIR__ . '/config/config.php';

$db = Database::getInstance();

// Homepage SEO Configuration
$pageSeo = [
    'title' => get_setting('site_name', 'Urji Beri School') . ' - Quality Preschool & Elementary Education',
    'description' => get_setting('site_description', 'Urji Beri School offers education for children aged 3 to 13. Its curriculum is based on the programs of the Ministry of Education and taught in Amharic and English language.'),
    'keywords' => 'urji beri school, urji beri online result, urji beri result, urji beri report card, urji beri, urji beri furi, best school around furi, elementary school in furi',
    'type' => 'website',
    'image' => asset_url('images/og-image.jpg')
];

// Get latest blog posts
$latestPosts = $db->fetchAll(
    "SELECT bp.*, bc.name as category_name, bc.slug as category_slug, u.full_name as author_name
     FROM blog_posts bp
     JOIN blog_categories bc ON bp.category_id = bc.id
     JOIN users u ON bp.author_id = u.id
     WHERE bp.is_published = 1 AND bc.is_active = 1
     ORDER BY bp.published_at DESC
     LIMIT 3"
);

// Get featured gallery images
$galleryImages = $db->fetchAll(
    "SELECT gi.*, gc.name as category_name
     FROM gallery_images gi
     JOIN gallery_categories gc ON gi.category_id = gc.id
     WHERE gi.is_active = 1 AND gc.is_active = 1
     ORDER BY gi.created_at DESC
     LIMIT 8"
);

include INCLUDES_PATH . '/header.php';
?>

    <!-- Hero Section -->
    <section class="hero pro-hero" aria-label="Welcome to Urji Beri School">
        <div class="hero-slider" aria-hidden="true">
            <div class="hero-slide active" style="background-image: url('<?php echo asset_url('images/home-header.jpg'); ?>')"></div>
            <div class="hero-slide" style="background-image: url('<?php echo asset_url('images/home-header1.jpg'); ?>')"></div>
        </div>
        <div class="hero-overlay" aria-hidden="true"></div>
        <div class="hero-decor" aria-hidden="true">
            <span class="hero-decor-ring"></span>
            <span class="hero-decor-dot hero-decor-dot-1"></span>
            <span class="hero-decor-dot hero-decor-dot-2"></span>
            <span class="hero-decor-dot hero-decor-dot-3"></span>
        </div>
        <div class="container">
            <div class="hero-layout">
                <div class="hero-content">
                    <h1 class="hero-brand"><?php echo e(get_setting('site_name', 'Urji Beri School')); ?></h1>
                    <p class="hero-title"><?php echo e(get_setting('hero_title', 'Future-ready learning starts here')); ?></p>
                    <p class="hero-subtitle"><?php echo e(get_setting('site_description', 'A welcoming preschool and elementary community helping children ages 3 to 13 grow with confidence, curiosity, and strong foundations.')); ?></p>
                    <div class="hero-buttons">
                        <a href="<?php echo e(get_setting('cta_primary_link', SITE_URL . '/about.php')); ?>" class="btn btn-white btn-lg">
                            <?php echo e(get_setting('cta_primary_text', 'Discover Our School')); ?>
                        </a>
                        <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-outline btn-lg">
                            Contact Admissions
                        </a>
                    </div>
                </div>

                <a href="#trust-strip" class="hero-scroll-cue" aria-label="Scroll to explore more">
                    <span>Scroll</span>
                    <i aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- School Trust Strip -->
    <section class="school-proof-strip" id="trust-strip" aria-label="Why families choose Urji Beri">
        <div class="container">
            <div class="proof-strip-grid anim-grid anim-grid-3d">
                <div class="proof-strip-item">
                    <span>01</span>
                    <strong>Bilingual Foundations</strong>
                    <p>Students build confidence in Amharic and English from the earliest years.</p>
                </div>
                <div class="proof-strip-item">
                    <span>02</span>
                    <strong>Whole-Child Care</strong>
                    <p>Safe classrooms, thoughtful discipline, and age-aware support for ages 3 to 13.</p>
                </div>
                <div class="proof-strip-item">
                    <span>03</span>
                    <strong>Parent Partnership</strong>
                    <p>News, campus moments, contacts, and online results stay easy to reach.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section feature-section">
        <div class="container">
            <div class="section-header">
                <p class="section-kicker">The Urji Beri Difference</p>
                <h2 class="section-title">A campus built around attention, care, and progress.</h2>
                <p class="section-subtitle">The people, values, and learning culture families look for when choosing a school.</p>
            </div>
            
            <div class="grid grid-4 anim-grid anim-grid-3d">
                <div class="glass-card feature-card">
                    <span class="feature-index" aria-hidden="true">01</span>
                    <div class="feature-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title"><?php echo e(get_setting('feature_1_title', 'Experienced Teachers')); ?></h3>
                    <p class="feature-text"><?php echo e(get_setting('feature_1_desc', 'We have experienced teachers that are effective in raising our students achievement.')); ?></p>
                </div>
                
                <div class="glass-card feature-card">
                    <span class="feature-index" aria-hidden="true">02</span>
                    <div class="feature-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                        </svg>
                    </div>
                    <h3 class="feature-title"><?php echo e(get_setting('feature_2_title', 'High Quality Education')); ?></h3>
                    <p class="feature-text"><?php echo e(get_setting('feature_2_desc', 'Our quality education puts our students in the centre and helps them to reach their full potential.')); ?></p>
                </div>
                
                <div class="glass-card feature-card">
                    <span class="feature-index" aria-hidden="true">03</span>
                    <div class="feature-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <h3 class="feature-title"><?php echo e(get_setting('feature_3_title', 'Comfortable Classrooms')); ?></h3>
                    <p class="feature-text"><?php echo e(get_setting('feature_3_desc', 'With our classrooms that help them relax and feel comfortable, students can focus better on their education.')); ?></p>
                </div>
                
                <div class="glass-card feature-card">
                    <span class="feature-index" aria-hidden="true">04</span>
                    <div class="feature-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                    </div>
                    <h3 class="feature-title"><?php echo e(get_setting('feature_4_title', 'Disciplined Students')); ?></h3>
                    <p class="feature-text"><?php echo e(get_setting('feature_4_desc', 'Our students are well known for their good behavior and respect for others.')); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Learning Pathways Section -->
    <section class="section learning-pathways" id="learning-pathways">
        <div class="container">
            <div class="pathways-layout">
                <div class="pathways-copy">
                    <p class="section-kicker">Learning Pathways</p>
                    <h2 class="section-title">From first letters to confident problem solvers.</h2>
                    <p class="section-subtitle">Clear programs for every stage — early years through elementary — grounded in Ministry of Education standards.</p>
                </div>
                <div class="pathways-board">
                    <article class="glass-card-solid pathway-card">
                        <span class="pathway-number">Ages 3–6</span>
                        <h3>Early Years</h3>
                        <p>Playful routines, language foundations, social confidence, and safe discovery.</p>
                    </article>
                    <article class="glass-card-solid pathway-card">
                        <span class="pathway-number">Grades 1–8</span>
                        <h3>Elementary Pathway</h3>
                        <p>Core academics, disciplined habits, bilingual expression, and steady progress.</p>
                    </article>
                    <article class="glass-card-solid pathway-card">
                        <span class="pathway-number">Every Week</span>
                        <h3>Campus Life</h3>
                        <p>Events, celebrations, and classroom moments families can follow and feel proud of.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <?php
    $statStudents = (int) get_setting('stat_students', 550);
    $statTeachers = (int) get_setting('stat_teachers', 30);
    $statExperience = (int) get_setting('stat_experience', 18);
    $statSatisfaction = (int) get_setting('stat_programs', 98);
    ?>
    <section class="stats-section" aria-label="School at a glance">
        <div class="container">
            <div class="stats-grid anim-grid anim-grid-3d">
                <div class="stat-item">
                    <div class="stat-number"><span class="counter" data-key="stat_students" data-target="<?php echo $statStudents; ?>"><?php echo $statStudents; ?></span>+</div>
                    <div class="stat-label">Happy Students</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><span class="counter" data-key="stat_teachers" data-target="<?php echo $statTeachers; ?>"><?php echo $statTeachers; ?></span>+</div>
                    <div class="stat-label">Expert Teachers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><span class="counter" data-key="stat_experience" data-target="<?php echo $statExperience; ?>"><?php echo $statExperience; ?></span>+</div>
                    <div class="stat-label">Years of Excellence</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><span class="counter" data-key="stat_programs" data-target="<?php echo $statSatisfaction; ?>"><?php echo $statSatisfaction; ?></span>%</div>
                    <div class="stat-label">Parent Satisfaction</div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Preview Section -->
    <section class="section about-preview-section" style="background-color: var(--bg-section);">
        <div class="container">
            <div class="about-content">
                <div class="about-image">
                    <img src="<?php echo asset_url('images/about-preview.jpg'); ?>" alt="Students and campus life at Urji Beri School">
                </div>
                <div class="about-text">
                    <p class="section-kicker">About Us</p>
                    <h2 class="section-title" style="text-align: left;">A school families trust.</h2>
                    <p>Urji Beri School offers education for children aged 3 to 13. Its curriculum is based on the programs of the Ministry of Education and taught in Amharic and English.</p>
                    <p>Accredited by the Oromia Education Bureau for Preschool and Elementary Education, we strive for rigorous standards, continuous improvement, and creative individual accomplishment.</p>
                    <div class="about-mini-grid">
                        <span>Preschool</span>
                        <span>Elementary</span>
                        <span>Amharic</span>
                        <span>English</span>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/about.php" class="btn btn-primary">Read Our Story</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Latest News Section -->
    <?php if (!empty($latestPosts)): ?>
    <section class="section">
        <div class="container">
            <div class="section-header">
                <p class="section-kicker">School Journal</p>
                <h2 class="section-title">Latest News</h2>
                <p class="section-subtitle">Announcements and stories that keep the school community connected.</p>
            </div>
            
            <div class="grid grid-3 anim-grid anim-grid-3d">
                <?php foreach ($latestPosts as $post): ?>
                <article class="glass-card blog-card">
                    <div class="blog-card-image">
                        <?php if ($post['featured_image']): ?>
                            <img src="<?php echo upload_url($post['featured_image'], 'blog'); ?>" alt="<?php echo e($post['title']); ?>">
                        <?php else: ?>
                            <img src="<?php echo asset_url('images/blog-placeholder.jpg'); ?>" alt="<?php echo e($post['title']); ?>">
                        <?php endif; ?>
                        <span class="blog-card-category"><?php echo e($post['category_name']); ?></span>
                    </div>
                    <div class="blog-card-content">
                        <div class="blog-card-meta">
                            <span><?php echo format_date($post['published_at']); ?></span>
                            <span>By <?php echo e($post['author_name']); ?></span>
                        </div>
                        <h3 class="blog-card-title">
                            <a href="<?php echo SITE_URL; ?>/blog-detail.php?slug=<?php echo e($post['slug']); ?>"><?php echo e($post['title']); ?></a>
                        </h3>
                        <p class="blog-card-excerpt"><?php echo e(truncate($post['excerpt'] ?: strip_tags($post['content']))); ?></p>
                        <a href="<?php echo SITE_URL; ?>/blog-detail.php?slug=<?php echo e($post['slug']); ?>" class="blog-card-link">
                            Read More
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-8">
                <a href="<?php echo SITE_URL; ?>/blog.php" class="btn btn-outline">View All News</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Gallery Preview Section -->
    <?php if (!empty($galleryImages)): ?>
    <section class="section" style="background-color: var(--bg-section);">
        <div class="container">
            <div class="section-header">
                <div>
                    <p class="section-kicker">Campus Life</p>
                    <h2 class="section-title">Moments families can feel.</h2>
                </div>
                <a href="<?php echo SITE_URL; ?>/gallery.php" class="section-link">View Gallery</a>
            </div>
            
            <div class="gallery-grid anim-grid anim-grid-3d">
                <?php foreach ($galleryImages as $image): ?>
                <div class="gallery-item" data-src="<?php echo upload_url($image['filename'], 'gallery'); ?>" role="button" tabindex="0" aria-label="<?php echo e($image['caption'] ?: $image['category_name'] ?: 'View gallery image'); ?>">
                    <img src="<?php echo upload_url($image['filename'], 'gallery'); ?>" alt="<?php echo e($image['alt_text'] ?: $image['caption']); ?>" loading="lazy">
                    <div class="gallery-item-overlay">
                        <div class="gallery-item-caption">
                            <?php echo e($image['caption'] ?: $image['category_name']); ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-8">
                <a href="<?php echo SITE_URL; ?>/gallery.php" class="btn btn-outline">View Full Gallery</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section class="cta-section section">
        <div class="container">
            <div class="cta-content">
                <h2 class="cta-title"><?php echo e(get_setting('registration_title', 'Join Urji Beri School')); ?></h2>
                <p class="cta-subtitle"><?php echo e(get_setting('registration_subtitle', '2025/26 registration is now open.')); ?></p>
                <p class="cta-text"><?php echo e(get_setting('registration_text', 'We invite you to explore our website and reach out with any questions. We would be happy to help as you prepare to join our school.')); ?></p>
                <div class="admissions-steps" aria-label="Admissions steps">
                    <span>Ask</span>
                    <span>Visit</span>
                    <span>Register</span>
                </div>
                <div class="btn-group justify-center">
                    <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary btn-lg">Contact Admissions</a>
                </div>
            </div>
        </div>
    </section>

<?php include INCLUDES_PATH . '/footer.php'; ?>
