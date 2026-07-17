-- =====================================================
-- URJI BERI SCHOOL - DATABASE SCHEMA
-- MySQL Database Structure
-- =====================================================

-- Create database
CREATE DATABASE IF NOT EXISTS urji_beri_school CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE urji_beri_school;

-- =====================================================
-- TABLE: roles
-- Stores user roles for access control
-- =====================================================
CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_role_name (name)
) ENGINE=InnoDB;

-- Insert default roles
INSERT INTO roles (name, description) VALUES 
('admin', 'Full system administrator with all privileges'),
('editor', 'Can manage content but not system settings');

-- =====================================================
-- TABLE: users
-- Stores admin and editor user accounts
-- =====================================================
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id INT UNSIGNED NOT NULL DEFAULT 1,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT,
    INDEX idx_user_email (email),
    INDEX idx_user_username (username),
    INDEX idx_user_active (is_active)
) ENGINE=InnoDB;

-- Insert default admin user (password: Admin@123)
INSERT INTO users (role_id, username, email, password, full_name) VALUES 
(1, 'admin', 'admin@urjiberischool.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator');

-- =====================================================
-- TABLE: blog_categories
-- Stores blog post categories
-- =====================================================
CREATE TABLE blog_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category_slug (slug),
    INDEX idx_category_active (is_active)
) ENGINE=InnoDB;

-- Insert default blog categories
INSERT INTO blog_categories (name, slug, description, sort_order) VALUES 
('School News', 'school-news', 'Latest news and updates from Urji Beri School', 1),
('Announcements', 'announcements', 'Important announcements for students and parents', 2),
('Events', 'events', 'Upcoming and past school events', 3),
('Education Tips', 'education-tips', 'Helpful tips for students and parents', 4);

-- =====================================================
-- TABLE: blog_posts
-- Stores all blog articles and news
-- =====================================================
CREATE TABLE blog_posts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    author_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    featured_image VARCHAR(255),
    is_featured TINYINT(1) DEFAULT 0,
    is_published TINYINT(1) DEFAULT 0,
    views INT UNSIGNED DEFAULT 0,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_post_slug (slug),
    INDEX idx_post_published (is_published),
    INDEX idx_post_featured (is_featured),
    INDEX idx_post_date (published_at),
    FULLTEXT INDEX idx_post_search (title, content)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: gallery_categories
-- Stores gallery categories for organizing images
-- =====================================================
CREATE TABLE gallery_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    cover_image VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_gallery_cat_slug (slug),
    INDEX idx_gallery_cat_active (is_active)
) ENGINE=InnoDB;

-- Insert default gallery categories
INSERT INTO gallery_categories (name, slug, description, sort_order) VALUES 
('Events', 'events', 'School events and celebrations', 1),
('Class Activities', 'class-activities', 'Daily classroom activities and learning moments', 2),
('Celebrations', 'celebrations', 'Holiday celebrations and special occasions', 3),
('Sports', 'sports', 'Sports day and physical activities', 4),
('Facilities', 'facilities', 'Our school buildings and facilities', 5);

-- =====================================================
-- TABLE: gallery_images
-- Stores all gallery images with metadata
-- =====================================================
CREATE TABLE gallery_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    uploaded_by INT UNSIGNED NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    caption VARCHAR(255),
    alt_text VARCHAR(255),
    file_size INT UNSIGNED,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES gallery_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_image_category (category_id),
    INDEX idx_image_active (is_active)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: contact_messages
-- Stores messages from the contact form
-- =====================================================
CREATE TABLE contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    is_replied TINYINT(1) DEFAULT 0,
    replied_at TIMESTAMP NULL,
    replied_by INT UNSIGNED NULL,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (replied_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_message_read (is_read),
    INDEX idx_message_date (created_at)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: site_settings
-- Stores configurable site settings
-- =====================================================
CREATE TABLE site_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value LONGTEXT,
    setting_type ENUM('text', 'textarea', 'image', 'json', 'boolean') DEFAULT 'text',
    setting_group VARCHAR(50) DEFAULT 'general',
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_setting_key (setting_key),
    INDEX idx_setting_group (setting_group)
) ENGINE=InnoDB;

-- Insert default site settings
INSERT INTO site_settings (setting_key, setting_value, setting_type, setting_group, description) VALUES 
-- General Settings
('site_name', 'Urji Beri School', 'text', 'general', 'School name'),
('site_tagline', 'Nurturing Young Minds for a Brighter Future', 'text', 'general', 'School tagline'),
('site_description', 'Urji Beri School offers education for children aged 3 to 13. Its curriculum is based on the programs of the Ministry of Education and taught in Amharic and English language.', 'textarea', 'seo', 'SEO meta description'),
('site_keywords', 'urji beri school, urji beri online result, urji beri result, urji beri report card, urji beri, urji beri furi, best school around furi, elementary school in furi', 'textarea', 'seo', 'SEO keywords'),
('google_analytics_id', '', 'text', 'seo', 'Google Analytics tracking ID'),
('google_site_verification', '', 'text', 'seo', 'Google Search Console verification code'),
('bing_site_verification', '', 'text', 'seo', 'Bing Webmaster verification code'),
('founding_year', '2022', 'text', 'general', 'School founding year'),
('total_students', '500', 'text', 'general', 'Total number of students'),
('social_facebook', 'https://www.facebook.com/UrjiBeriPrimarySchool', 'text', 'social', 'Facebook page URL'),
('social_instagram', '', 'text', 'social', 'Instagram profile URL'),
('social_youtube', '', 'text', 'social', 'YouTube channel URL'),
('social_telegram', '', 'text', 'social', 'Telegram channel URL'),
('site_logo', 'logo.png', 'image', 'general', 'Site logo'),
('site_favicon', 'favicon.ico', 'image', 'general', 'Site favicon'),

-- Contact Information
('contact_email', 'office@urjiberischool.com', 'text', 'contact', 'Contact email'),
('contact_phone', '+251-912-097-003', 'text', 'contact', 'Contact phone'),
('contact_address', '300m from WAS Gas Station, Alemgena, Oromia.', 'text', 'contact', 'Physical address'),
('facebook_url', 'https://www.facebook.com/UrjiBeriPrimarySchool', 'text', 'contact', 'Facebook page URL'),

-- Map Settings
('map_latitude', '8.9806', 'text', 'map', 'Map latitude coordinate'),
('map_longitude', '38.7578', 'text', 'map', 'Map longitude coordinate'),
('map_zoom', '15', 'text', 'map', 'Default map zoom level'),

-- About Content
('about_overview', 'Urji Beri School offers education for children aged 3 to 13. Its curriculum is based on the programs of the Ministry of Education and taught in Amharic and English language. Our school is accredited by the Oromia Education Bureau for Preschool and Elementary Education assures that our members Strive to achieve rigorous and common standards in education. And also Demonstrate substantive institutional commitment to continued improvement. Commit to balancing the creative tensions that exist between local autonomy and public authority and Nurture individual creative accomplishment.', 'textarea', 'about', 'School overview'),
('about_mission', 'We support a safe, caring, respectful environment that values creativity, diversity, and inclusivity. Develop self-aware learners with the tools for fulfillment in their world and beyond. Provide best practice learning that empowers individuals to set and reach high standards. Encourage students to think globally and act locally.', 'textarea', 'about', 'School mission'),
('about_vision', 'To empower students to acquire, demonstrate, and value knowledge and skills as lifelong learners who contribute to the global world.', 'textarea', 'about', 'School vision'),
('about_values', 'We believe in learner agency and the power of inquiry. There is strength in diversity and inclusivity. That we all should listen thoughtfully to others and consider their points of view. We learn best when we feel safe, happy, valued, and challenged. It is important to strive to be the best you can be. We should look beyond ourselves and seek to make genuine, positive, sustainable changes in the world around us.', 'textarea', 'about', 'School values'),
('about_benefits', 'Our school is an outstanding school with a warm and welcoming learning environment. We place special emphasis on being supportive of the diverse needs of a varied and dynamic school community and on offering all students opportunities for growth and success. Our purpose-built school, nationally inspected and accredited, caring teachers, true Ethiopian learning experience, and individualized approach are designed to enable your child to succeed at school and beyond.', 'textarea', 'about', 'Why choose us benefits'),
('about_accreditation', 'Urji Beri School is fully accredited by the Oromia Education Bureau and follows the Ministry of Education curriculum, enhanced with modern teaching methodologies and global best practices.', 'textarea', 'about', 'Accreditation details'),

-- Learner Profile Attributes
('learner_inquirer', 'Acquires skills for purposeful, constructive research.', 'text', 'learner_profile', 'Inquirer attribute'),
('learner_thinker', 'Applies thinking skills critically and creatively to solve complex problems.', 'text', 'learner_profile', 'Thinker attribute'),
('learner_communicator', 'Receives & expresses ideas in more than one language including the language of mathematical symbols.', 'text', 'learner_profile', 'Communicator attribute'),
('learner_risk_taker', 'Approaches unfamiliar situations with confidence.', 'text', 'learner_profile', 'Risk-taker attribute'),
('learner_principled', 'Displays integrity, honesty and a sense of fairness and justice.', 'text', 'learner_profile', 'Principled attribute'),
('learner_caring', 'Develops a sense of personal commitment to action and service.', 'text', 'learner_profile', 'Caring attribute'),
('learner_open_minded', 'Respects the views, values and traditions of other individuals and cultures and is accustomed to seeking and considering a range of points of view.', 'text', 'learner_profile', 'Open-minded attribute'),
('learner_balanced', 'Understands physical, mental and personal well-being.', 'text', 'learner_profile', 'Balanced attribute'),
('learner_reflective', 'Analyses own strength and weaknesses.', 'text', 'learner_profile', 'Reflective attribute'),

-- Features
('feature_1_title', 'Experienced Teachers', 'text', 'features', 'Feature 1 title'),
('feature_1_desc', 'We have experienced teachers that are effective in raising our students achievement.', 'text', 'features', 'Feature 1 description'),
('feature_2_title', 'High Quality Education', 'text', 'features', 'Feature 2 title'),
('feature_2_desc', 'Our quality education puts our students in the centre and helps them to reach their full potential.', 'text', 'features', 'Feature 2 description'),
('feature_3_title', 'Comfortable Classrooms', 'text', 'features', 'Feature 3 title'),
('feature_3_desc', 'With our classrooms that help them relax and feel comfortable, students can focus better on their education.', 'text', 'features', 'Feature 3 description'),
('feature_4_title', 'Disciplined Students', 'text', 'features', 'Feature 4 title'),
('feature_4_desc', 'Our students are well known for their good behavior and respect for others.', 'text', 'features', 'Feature 4 description'),

-- Director's Message
('director_name', 'Mr. Alemayehu Aga', 'text', 'director', 'Director full name'),
('director_title', 'General Manager', 'text', 'director', 'Director title'),
('director_image', 'director.jpg', 'image', 'director', 'Director photo'),
('director_message', 'I would like to take this opportunity to welcome you to our website and thank you for considering Urji Beri School as an educational home for your children. Whether you are considering a move to Alemgena, have just newly arrived or call Alemgena home, UBS is here to help you. We are the best primary school in Ethiopia and we offer good quality facilities, best teaching and a student centred approach to learning. The challenges and opportunities of a small, caring, exciting and rigorous education await you. More than that, UBS is a diverse, exciting community where students and families from all over the world come together to share ideas, discuss perspectives and learn from each other.\n\nThe school has strong practices in place to support students as they transition to, through and beyond the school and specialist staff that support all the learning needs of its students. At UBS we are committed to our Vision of being a school that develops and empowers future innovators and leaders. We work to give our very best in all our endeavors, and we invite you to become close partners in this important task. I personally extend a very warm welcome to UBS – a great place to grow and learn!', 'textarea', 'director', 'Director welcome message'),
('director_quote', '"The more that you read, the more things you will know. The more that you learn, the more places you\'ll go." – Dr. Seuss', 'text', 'director', 'Director quote'),

-- External Links
('online_result_url', 'https://sms.urjiberischool.com/portal/login?', 'text', 'external', 'Students/parents app URL'),
('teacher_login_url', 'https://sms.urjiberischool.com/teacher-portal/login?', 'text', 'external', 'Teachers app URL'),

-- Homepage Content
('hero_title', 'Welcome to the official website of', 'text', 'homepage', 'Hero section title'),
('hero_subtitle', 'Urji Beri School', 'text', 'homepage', 'Hero section subtitle'),
('hero_description', 'Providing quality education for preschool and elementary students in a safe, caring, and stimulating environment.', 'textarea', 'homepage', 'Hero description'),
('cta_primary_text', 'About Us', 'text', 'homepage', 'Primary CTA button text'),
('cta_primary_link', '/about.php', 'text', 'homepage', 'Primary CTA button link'),
('cta_secondary_text', 'Contact Us', 'text', 'homepage', 'Secondary CTA button text'),
('cta_secondary_link', '/contact.php', 'text', 'homepage', 'Secondary CTA button link'),

-- Homepage Statistics
('stat_students', '550', 'text', 'stats', 'Total students shown on homepage'),
('stat_teachers', '30', 'text', 'stats', 'Qualified teachers shown on homepage'),
('stat_experience', '18', 'text', 'stats', 'Years of excellence shown on homepage'),
('stat_programs', '98', 'text', 'stats', 'Parent satisfaction percentage on homepage'),

-- Popups & Announcements
('popup_registration_enabled', '1', 'boolean', 'announcements', 'Show registration popup'),
('popup_registration_title', '2025/26 Registration Has Started!', 'text', 'announcements', 'Registration popup title'),
('popup_registration_text', 'We have started registration for the new academic year. Families are welcome to visit our campus, ask questions, and register their children for preschool and elementary programs.', 'textarea', 'announcements', 'Registration popup message'),
('popup_registration_cta_text', 'Register Your Child', 'text', 'announcements', 'Registration popup button'),
('popup_registration_cta_link', '/contact.php', 'text', 'announcements', 'Registration popup link'),
('popup_promo_enabled', '1', 'boolean', 'announcements', 'Show promotional popup'),
('popup_promo_title', 'A School Families Trust', 'text', 'announcements', 'Promo popup title'),
('popup_promo_text', 'Experienced teachers, comfortable classrooms, and a caring community for children ages 3 to 13. Come see why parents choose Urji Beri School.', 'textarea', 'announcements', 'Promo popup message'),
('popup_promo_cta_text', 'Explore Our School', 'text', 'announcements', 'Promo popup button'),
('popup_promo_cta_link', '/about.php', 'text', 'announcements', 'Promo popup link'),

-- Registration CTA
('registration_title', 'Join Urji Beri School', 'text', 'homepage', 'Registration section title'),
('registration_subtitle', '2025/26 registration is now open.', 'text', 'homepage', 'Registration subtitle'),
('registration_text', 'We invite you to explore our website and reach out to us if you have any queries about our school. We would be happy to answer any questions you may have as you prepare to join our school.', 'textarea', 'homepage', 'Registration description');

-- =====================================================
-- TABLE: page_views (Optional - for analytics)
-- Tracks page views for basic analytics
-- =====================================================
CREATE TABLE page_views (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    page_url VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    referrer VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_page_url (page_url),
    INDEX idx_view_date (created_at)
) ENGINE=InnoDB;

-- =====================================================
-- Create views for common queries
-- =====================================================

-- View: Published blog posts with category and author info
CREATE VIEW v_published_posts AS
SELECT 
    bp.id,
    bp.title,
    bp.slug,
    bp.excerpt,
    bp.content,
    bp.featured_image,
    bp.is_featured,
    bp.views,
    bp.published_at,
    bp.created_at,
    bc.id AS category_id,
    bc.name AS category_name,
    bc.slug AS category_slug,
    u.full_name AS author_name
FROM blog_posts bp
JOIN blog_categories bc ON bp.category_id = bc.id
JOIN users u ON bp.author_id = u.id
WHERE bp.is_published = 1 AND bc.is_active = 1
ORDER BY bp.published_at DESC;

-- View: Active gallery images with category info
CREATE VIEW v_gallery_images AS
SELECT 
    gi.id,
    gi.filename,
    gi.original_name,
    gi.caption,
    gi.alt_text,
    gi.created_at,
    gc.id AS category_id,
    gc.name AS category_name,
    gc.slug AS category_slug
FROM gallery_images gi
JOIN gallery_categories gc ON gi.category_id = gc.id
WHERE gi.is_active = 1 AND gc.is_active = 1
ORDER BY gi.sort_order ASC, gi.created_at DESC;

-- View: Unread contact messages
CREATE VIEW v_unread_messages AS
SELECT * FROM contact_messages
WHERE is_read = 0
ORDER BY created_at DESC;

-- =====================================================
-- Upgrade: homepage statistics (run once on existing DBs)
-- =====================================================
INSERT INTO site_settings (setting_key, setting_value, setting_type, setting_group, description) VALUES
('stat_students', '550', 'text', 'stats', 'Total students shown on homepage'),
('stat_teachers', '30', 'text', 'stats', 'Qualified teachers shown on homepage'),
('stat_experience', '18', 'text', 'stats', 'Years of excellence shown on homepage'),
('stat_programs', '98', 'text', 'stats', 'Parent satisfaction percentage on homepage')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- =====================================================
-- Upgrade: SEO, popups, map zoom (run once on existing DBs)
-- =====================================================
INSERT INTO site_settings (setting_key, setting_value, setting_type, setting_group, description) VALUES
('popup_registration_enabled', '1', 'boolean', 'announcements', 'Show registration popup'),
('popup_registration_title', '2025/26 Registration Has Started!', 'text', 'announcements', 'Registration popup title'),
('popup_registration_text', 'We have started registration for the new academic year. Families are welcome to visit our campus, ask questions, and register their children for preschool and elementary programs.', 'textarea', 'announcements', 'Registration popup message'),
('popup_registration_cta_text', 'Register Your Child', 'text', 'announcements', 'Registration popup button'),
('popup_registration_cta_link', '/contact.php', 'text', 'announcements', 'Registration popup link'),
('popup_promo_enabled', '1', 'boolean', 'announcements', 'Show promotional popup'),
('popup_promo_title', 'A School Families Trust', 'text', 'announcements', 'Promo popup title'),
('popup_promo_text', 'Experienced teachers, comfortable classrooms, and a caring community for children ages 3 to 13. Come see why parents choose Urji Beri School.', 'textarea', 'announcements', 'Promo popup message'),
('popup_promo_cta_text', 'Explore Our School', 'text', 'announcements', 'Promo popup button'),
('popup_promo_cta_link', '/about.php', 'text', 'announcements', 'Promo popup link')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
