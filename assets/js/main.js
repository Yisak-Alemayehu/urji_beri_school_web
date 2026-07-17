/**
 * Main JavaScript for Urji Beri School Website
 * Public Site Functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    initPageLoad();
    // Initialize all components
    initMobileNav();
    initSmoothScroll();
    initScrollAnimations();
    initScrollReveal();
    initGalleryFilter();
    initLightbox();
    initContactForm();
    initHeaderScroll();
    initHeroSlider();
    initPremiumInteractions();
    initHeroParallax();
    initAnimGrids();
    initTiltCards();
    initButtonRipple();
    initSmoothMotion();
    initMagneticButtons();
    initSitePopups();
});

// Safety net — run popups after full page load if DOMContentLoaded ran too early
window.addEventListener('load', () => {
    initSitePopups();
});

/**
 * Hero Image Slider
 */
function initHeroSlider() {
    const slides = document.querySelectorAll('.hero-slide');
    
    if (slides.length === 0) return;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion || slides.length < 2) return;
    
    let currentSlide = 0;
    const slideCount = slides.length;
    
    function goToSlide(index) {
        const prev = slides[currentSlide];
        prev.classList.remove('active');

        currentSlide = (index + slideCount) % slideCount;
        const next = slides[currentSlide];

        // Restart Ken Burns on each slide change
        next.classList.remove('active');
        void next.offsetWidth;
        next.classList.add('active');
    }
    
    function nextSlide() {
        goToSlide(currentSlide + 1);
    }
    
    // Keep movement calm so the full-bleed photography stays premium.
    setInterval(nextSlide, 6000);
}
 

/**
 * Mobile Navigation Toggle
 */
function initMobileNav() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (!navToggle || !navMenu) return;
    
    navToggle.addEventListener('click', function() {
        const isOpen = navMenu.classList.toggle('active');
        navToggle.classList.toggle('active');
        navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        
        // Toggle body scroll
        document.body.style.overflow = isOpen ? 'hidden' : '';
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
            navMenu.classList.remove('active');
            navToggle.classList.remove('active');
            navToggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }
    });
    
    // Close menu when clicking a link
    navMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function() {
            navMenu.classList.remove('active');
            navToggle.classList.remove('active');
            navToggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        });
    });
}

/**
 * Smooth Scroll for Anchor Links
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/**
 * Scroll Animations (Fade In)
 */
function initScrollAnimations() {
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    if (!animatedElements.length) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    animatedElements.forEach(el => observer.observe(el));
}

/**
 * Mark element and related animated children as visible
 */
function markVisible(element) {
    if (!element) return;

    element.classList.add('is-visible');

    element.querySelectorAll('.section-kicker, .section-title, .section-subtitle').forEach(child => {
        child.classList.add('is-visible');
    });

    if (element.classList.contains('learning-pathways') || element.classList.contains('pathways-copy')) {
        element.querySelectorAll('.section-kicker, .section-title, .section-subtitle').forEach(child => {
            child.classList.add('is-visible');
        });
        element.querySelectorAll('.pathway-card').forEach((card, index) => {
            card.style.setProperty('--pathway-index', index);
            card.classList.add('is-animated');
        });
    }

    if (element.classList.contains('school-proof-strip') ||
        element.classList.contains('section-header') ||
        element.classList.contains('stats-section') ||
        element.classList.contains('cta-section') ||
        element.classList.contains('feature-section') ||
        element.classList.contains('about-preview-section')) {
        element.querySelectorAll('.anim-grid').forEach(grid => grid.classList.add('is-visible'));
    }

    if (element.classList.contains('anim-grid')) {
        element.classList.add('is-visible');
    }
}

function isInViewport(element, margin = 80) {
    const rect = element.getBoundingClientRect();
    return rect.top < window.innerHeight - margin && rect.bottom > margin;
}

function revealAllAnimated() {
    const selectors = [
        '.reveal',
        '.reveal-left',
        '.reveal-right',
        '.reveal-scale',
        '.reveal-fade',
        '.reveal-blur',
        '.anim-grid',
        '.school-proof-strip',
        '.section-header',
        '.section-title',
        '.about-image',
        '.about-text',
        '.cta-content',
        '.page-header-content',
        '.stat-item',
        '.gallery-item',
        '.blog-card',
        '.learning-pathways',
        '.pathways-copy',
        '.feature-section',
        '.about-preview-section'
    ];

    document.querySelectorAll(selectors.join(',')).forEach(markVisible);
    document.querySelectorAll('.section-kicker').forEach(el => el.classList.add('is-visible'));
}

/**
 * Scroll reveal animations — multiple entrance styles
 */
function initScrollReveal() {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const heroContent = document.querySelector('.hero-content');

    if (heroContent) {
        heroContent.classList.add('is-visible');
    }

    document.querySelectorAll('.anim-grid').forEach(grid => {
        grid.querySelectorAll(':scope > *').forEach((child, index) => {
            child.style.setProperty('--child-index', index);
        });
    });

    const revealConfig = [
        { selector: '.section-header', className: 'reveal-fade' },
        { selector: '.feature-card', className: 'reveal-scale' },
        { selector: '.proof-strip-item', className: 'reveal' },
        { selector: '.blog-card', className: 'reveal-scale' },
        { selector: '.gallery-item', className: 'reveal-scale' },
        { selector: '.about-image', className: 'reveal-left' },
        { selector: '.about-text', className: 'reveal-right' },
        { selector: '.contact-grid', className: 'reveal' },
        { selector: '.cta-content', className: 'reveal-scale' },
        { selector: '.stat-item', className: 'reveal-scale' },
        { selector: '.director-content', className: 'reveal' },
        { selector: '.blog-detail-header', className: 'reveal-fade' },
        { selector: '.blog-detail-image', className: 'reveal-scale' },
        { selector: '.blog-detail-content', className: 'reveal' },
        { selector: '.glass-card-solid', className: 'reveal' },
        { selector: '.page-header-content', className: 'reveal-fade' }
    ];

    const revealElements = [];

    revealConfig.forEach(({ selector, className }) => {
        document.querySelectorAll(selector).forEach((element, index) => {
            if (element.closest('.anim-grid')) return;

            if (!element.classList.contains(className)) {
                element.classList.add(className);
            }
            element.style.setProperty('--stagger-index', index % 8);
            revealElements.push(element);
        });
    });

    document.querySelectorAll('.reveal-stagger > .reveal, .reveal-stagger > .reveal-scale, .reveal-stagger > .reveal-left').forEach((element, index) => {
        element.style.setProperty('--stagger-index', index);
        if (!revealElements.includes(element)) {
            revealElements.push(element);
        }
    });

    const sectionObservers = [
        '.school-proof-strip',
        '.section-header',
        '.section-title',
        '.learning-pathways',
        '.pathways-copy',
        '.cta-section',
        '.stats-section',
        '.feature-section',
        '.about-preview-section'
    ];

    sectionObservers.forEach(selector => {
        document.querySelectorAll(selector).forEach(section => {
            if (!revealElements.includes(section)) {
                if (selector === '.section-title' && !section.classList.contains('reveal-blur')) {
                    section.classList.add('reveal-blur');
                }
                revealElements.push(section);
            }
        });
    });

    document.querySelectorAll('.anim-grid').forEach(grid => {
        if (!revealElements.includes(grid)) {
            revealElements.push(grid);
        }
    });

    if (prefersReducedMotion) {
        revealAllAnimated();
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            markVisible(entry.target);
            observer.unobserve(entry.target);
        });
    }, {
        threshold: 0.05,
        rootMargin: '0px 0px -5% 0px'
    });

    revealElements.forEach(element => observer.observe(element));

    // Reveal anything already on screen immediately (run twice to catch layout)
    const revealInView = () => {
        revealElements.forEach(element => {
            if (isInViewport(element, 60)) {
                markVisible(element);
            }
        });
    };

    window.requestAnimationFrame(revealInView);
    window.addEventListener('load', revealInView);

    // Safety net — never leave content hidden
    setTimeout(revealAllAnimated, 2000);
}

/**
 * Page load — enable animation styles without hiding content
 */
function initPageLoad() {
    document.documentElement.classList.add('js-ready');
    document.body.classList.add('page-loaded');
}

/**
 * Staggered grid children animation (child index setup only — reveal handled above)
 */
function initAnimGrids() {
    document.querySelectorAll('.anim-grid').forEach(grid => {
        grid.querySelectorAll(':scope > *').forEach((child, index) => {
            child.style.setProperty('--child-index', index);
        });
    });
}

/**
 * Subtle hero parallax on scroll
 */
function initHeroParallax() {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const hero = document.querySelector('.hero');
    const slider = document.querySelector('.hero-slider');

    if (prefersReducedMotion || !hero) return;

    let ticking = false;

    const update = () => {
        const scrollY = window.pageYOffset;
        const heroHeight = hero.offsetHeight;

        if (slider && scrollY < heroHeight) {
            const offset = scrollY * 0.22;
            slider.style.transform = `translate3d(0, ${offset}px, 0)`;
        }

        hero.style.setProperty('--hero-scroll', Math.min(scrollY / heroHeight, 1).toString());

        ticking = false;
    };

    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(update);
            ticking = true;
        }
    }, { passive: true });
}

/**
 * Gentle 3D tilt on premium cards
 */
function initTiltCards() {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    const cards = document.querySelectorAll('.feature-card, .cta-content, .pathway-card, .proof-strip-item');

    cards.forEach(card => {
        card.addEventListener('pointermove', (event) => {
            const rect = card.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - 0.5;
            const y = (event.clientY - rect.top) / rect.height - 0.5;
            const lift = card.classList.contains('cta-content') ? -6 : -8;

            card.style.transform = `perspective(1000px) rotateX(${y * -5}deg) rotateY(${x * 5}deg) translateY(${lift}px) scale(1.02)`;
        });

        card.addEventListener('pointerleave', () => {
            card.style.transform = '';
        });
    });
}

/**
 * Button ripple feedback
 */
function initButtonRipple() {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const ripple = document.createElement('span');
            const size = Math.max(rect.width, rect.height);

            ripple.className = 'btn-ripple';
            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${e.clientX - rect.left - size / 2}px`;
            ripple.style.top = `${e.clientY - rect.top - size / 2}px`;

            this.appendChild(ripple);
            ripple.addEventListener('animationend', () => ripple.remove());
        });
    });
}

/**
 * Extra smooth motion polish
 */
function initSmoothMotion() {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    document.documentElement.classList.add('motion-smooth', 'motion-premium');

    const pathwaysSection = document.querySelector('.learning-pathways');
    if (pathwaysSection && isInViewport(pathwaysSection, 100)) {
        markVisible(pathwaysSection);
    }

    const parallaxTargets = document.querySelectorAll('.about-image img');
    let ticking = false;

    const updateParallax = () => {
        parallaxTargets.forEach(img => {
            const rect = img.getBoundingClientRect();
            const center = rect.top + rect.height / 2;
            const viewCenter = window.innerHeight / 2;
            const distance = (center - viewCenter) / window.innerHeight;
            const offset = Math.max(Math.min(distance * 24, 20), -20);
            if (img.closest('.about-image')?.classList.contains('is-visible')) {
                img.style.transform = `translateY(${offset}px) scale(1.03)`;
            }
        });
        ticking = false;
    };

    window.addEventListener('scroll', () => {
        if (!ticking) {
            window.requestAnimationFrame(updateParallax);
            ticking = true;
        }
    }, { passive: true });

    updateParallax();
}

/**
 * Registration & promotional announcement popups
 */
let sitePopupsInitialized = false;

function initSitePopups() {
    if (sitePopupsInitialized) return;

    const registrationPopup = document.getElementById('registrationPopup');
    const promoPopup = document.getElementById('promoPopup');
    const registrationEnabled = registrationPopup?.dataset.enabled !== '0';
    const promoEnabled = promoPopup?.dataset.enabled !== '0';

    if ((!registrationPopup || !registrationEnabled) && (!promoPopup || !promoEnabled)) {
        return;
    }

    sitePopupsInitialized = true;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const STORAGE_KEYS = {
        registration: 'ub_popup_registration_dismissed',
        promo: 'ub_popup_promo_dismissed'
    };
    const DISMISS_DAYS = 3;
    const OPEN_DELAY = prefersReducedMotion ? 0 : 1000;

    const isDismissed = (key) => {
        try {
            const raw = localStorage.getItem(key);
            if (!raw) return false;
            const data = JSON.parse(raw);
            return data.expires && Date.now() < data.expires;
        } catch {
            return false;
        }
    };

    const dismissPopup = (key) => {
        try {
            localStorage.setItem(key, JSON.stringify({
                expires: Date.now() + DISMISS_DAYS * 24 * 60 * 60 * 1000
            }));
        } catch {
            // ignore
        }
    };

    const openPopup = (popup) => {
        if (!popup || popup.classList.contains('is-open')) return;
        popup.setAttribute('aria-hidden', 'false');
        popup.classList.add('is-open');
        document.body.classList.add('popup-open');
        const focusTarget = popup.querySelector('.btn-primary, .site-popup-close');
        if (focusTarget) focusTarget.focus();
    };

    const closePopup = (popup, storageKey, showPromoAfter = false) => {
        if (!popup) return;
        popup.classList.remove('is-open');
        popup.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('popup-open');
        if (storageKey) dismissPopup(storageKey);
        if (showPromoAfter && promoPopup && promoEnabled && !isDismissed(STORAGE_KEYS.promo)) {
            setTimeout(() => openPopup(promoPopup), prefersReducedMotion ? 0 : 700);
        }
    };

    document.querySelectorAll('.site-popup').forEach(popup => {
        if (popup.dataset.enabled === '0') return;

        const type = popup.dataset.popup;
        const storageKey = STORAGE_KEYS[type];

        popup.querySelectorAll('[data-popup-close]').forEach(btn => {
            btn.addEventListener('click', () => {
                closePopup(popup, storageKey, popup === registrationPopup);
            });
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        const open = document.querySelector('.site-popup.is-open');
        if (!open) return;
        const type = open.dataset.popup;
        closePopup(open, STORAGE_KEYS[type], open === registrationPopup);
    });

    if (registrationPopup && registrationEnabled && !isDismissed(STORAGE_KEYS.registration)) {
        setTimeout(() => openPopup(registrationPopup), OPEN_DELAY);
    } else if (promoPopup && promoEnabled && !isDismissed(STORAGE_KEYS.promo)) {
        setTimeout(() => openPopup(promoPopup), OPEN_DELAY);
    }
}

/**
 * Subtle magnetic pull on primary action buttons
 */
function initMagneticButtons() {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    const buttons = document.querySelectorAll('.btn-primary, .btn-white, .hero-buttons .btn');

    buttons.forEach(btn => {
        btn.addEventListener('pointermove', (event) => {
            const rect = btn.getBoundingClientRect();
            const x = event.clientX - rect.left - rect.width / 2;
            const y = event.clientY - rect.top - rect.height / 2;

            btn.style.transform = `translate(${x * 0.18}px, ${y * 0.22}px) scale(1.04)`;
        });

        btn.addEventListener('pointerleave', () => {
            btn.style.transform = '';
        });
    });
}

/**
 * Premium interaction details for agency-style polish
 */
function initPremiumInteractions() {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    const interactiveCards = document.querySelectorAll('.feature-card, .pathway-card, .proof-strip-item, .blog-card, .glass-card-solid');

    interactiveCards.forEach(card => {
        card.addEventListener('pointermove', (event) => {
            const rect = card.getBoundingClientRect();
            const x = ((event.clientX - rect.left) / rect.width) * 100;
            const y = ((event.clientY - rect.top) / rect.height) * 100;

            card.style.setProperty('--pointer-x', `${x}%`);
            card.style.setProperty('--pointer-y', `${y}%`);
        });

        card.addEventListener('pointerleave', () => {
            card.style.removeProperty('--pointer-x');
            card.style.removeProperty('--pointer-y');
        });
    });

    const updateScrollProgress = () => {
        const scrollable = document.documentElement.scrollHeight - window.innerHeight;
        const progress = scrollable > 0 ? window.scrollY / scrollable : 0;
        document.documentElement.style.setProperty('--scroll-progress', progress.toString());
    };

    updateScrollProgress();
    window.addEventListener('scroll', updateScrollProgress, { passive: true });
}

/**
 * Gallery Category Filter
 */
function initGalleryFilter() {
    const filterBtns = document.querySelectorAll('.gallery-filter .filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    if (!filterBtns.length || !galleryItems.length) return;
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Only prevent default for buttons without href
            if (this.getAttribute('href') === '#') {
                e.preventDefault();
            }
            
            // Update active state
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
            // Filter items
            galleryItems.forEach(item => {
                if (filter === 'all' || item.dataset.category === filter) {
                    item.style.display = '';
                    setTimeout(() => item.classList.add('show'), 10);
                } else {
                    item.classList.remove('show');
                    setTimeout(() => item.style.display = 'none', 300);
                }
            });
        });
    });
}

/**
 * Image Lightbox
 */
function initLightbox() {
    // Create lightbox elements if they don't exist
    let lightbox = document.querySelector('.lightbox');
    
    if (!lightbox) {
        lightbox = document.createElement('div');
        lightbox.className = 'lightbox';
        lightbox.innerHTML = `
            <button class="lightbox-close" aria-label="Close lightbox">&times;</button>
            <button class="lightbox-prev" aria-label="Previous image">&#10094;</button>
            <button class="lightbox-next" aria-label="Next image">&#10095;</button>
            <div class="lightbox-content">
                <img src="" alt="" class="lightbox-image">
                <div class="lightbox-caption"></div>
            </div>
        `;
        document.body.appendChild(lightbox);
    }
    
    const lightboxImg = lightbox.querySelector('.lightbox-image');
    const lightboxCaption = lightbox.querySelector('.lightbox-caption');
    const closeBtn = lightbox.querySelector('.lightbox-close');
    const prevBtn = lightbox.querySelector('.lightbox-prev');
    const nextBtn = lightbox.querySelector('.lightbox-next');
    
    let galleryImages = [];
    let currentIndex = 0;
    
    // Open lightbox when clicking gallery images
    document.querySelectorAll('.gallery-item img, [data-lightbox]').forEach((img, index) => {
        img.style.cursor = 'pointer';
        img.addEventListener('click', function() {
            // Collect all gallery images
            galleryImages = Array.from(document.querySelectorAll('.gallery-item img, [data-lightbox]'));
            currentIndex = galleryImages.indexOf(this);
            
            showImage(currentIndex);
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });
    
    function showImage(index) {
        if (galleryImages[index]) {
            lightboxImg.src = galleryImages[index].src;
            lightboxCaption.textContent = galleryImages[index].alt || '';
            currentIndex = index;
            
            // Show/hide navigation
            prevBtn.style.display = galleryImages.length > 1 ? '' : 'none';
            nextBtn.style.display = galleryImages.length > 1 ? '' : 'none';
        }
    }
    
    // Navigation
    prevBtn.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
        showImage(currentIndex);
    });
    
    nextBtn.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % galleryImages.length;
        showImage(currentIndex);
    });
    
    // Close lightbox
    closeBtn.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) closeLightbox();
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (!lightbox.classList.contains('active')) return;
        
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') prevBtn.click();
        if (e.key === 'ArrowRight') nextBtn.click();
    });
    
    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }
}

/**
 * Contact Form Validation
 */
function initContactForm() {
    const contactForm = document.querySelector('#contact-form, #contactForm');
    
    if (!contactForm) return;
    
    contactForm.addEventListener('submit', function(e) {
        let isValid = true;
        const errors = [];
        
        // Get fields
        const name = this.querySelector('[name="name"]');
        const email = this.querySelector('[name="email"]');
        const subject = this.querySelector('[name="subject"]');
        const message = this.querySelector('[name="message"]');
        
        // Clear previous errors
        this.querySelectorAll('.form-error').forEach(el => el.remove());
        this.querySelectorAll('.form-group').forEach(el => el.classList.remove('has-error'));
        
        // Validate name
        if (!name.value.trim()) {
            showFieldError(name, 'Please enter your name');
            isValid = false;
        }
        
        // Validate email
        if (!email.value.trim()) {
            showFieldError(email, 'Please enter your email');
            isValid = false;
        } else if (!isValidEmail(email.value)) {
            showFieldError(email, 'Please enter a valid email address');
            isValid = false;
        }
        
        // Validate subject
        if (!subject.value.trim()) {
            showFieldError(subject, 'Please enter a subject');
            isValid = false;
        }
        
        // Validate message
        if (!message.value.trim()) {
            showFieldError(message, 'Please enter your message');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
    
    function showFieldError(field, message) {
        const formGroup = field.closest('.form-group');
        formGroup.classList.add('has-error');
        
        const error = document.createElement('span');
        error.className = 'form-error';
        error.textContent = message;
        formGroup.appendChild(error);
    }
    
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
}

/**
 * Header Background on Scroll
 */
function initHeaderScroll() {
    const header = document.querySelector('.header');
    
    if (!header) return;
    
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let lastScroll = 0;
    let ticking = false;
    
    const onScroll = () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        if (!prefersReducedMotion && currentScroll > 120) {
            if (currentScroll > lastScroll + 8) {
                header.classList.add('header-hidden');
            } else if (currentScroll < lastScroll - 8) {
                header.classList.remove('header-hidden');
            }
        } else {
            header.classList.remove('header-hidden');
        }
        
        lastScroll = currentScroll;
        ticking = false;
    };
    
    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(onScroll);
            ticking = true;
        }
    }, { passive: true });
}

/**
 * Counter Animation (for stats) — smooth ease-out
 */
function easeOutExpo(t) {
    return t >= 1 ? 1 : 1 - Math.pow(2, -10 * t);
}

function animateCounterValue(counter, target) {
    const duration = 2200;
    const start = performance.now();

    const tick = (now) => {
        const progress = Math.min((now - start) / duration, 1);
        counter.textContent = Math.round(target * easeOutExpo(progress));
        if (progress < 1) {
            requestAnimationFrame(tick);
        } else {
            counter.textContent = target;
        }
    };

    requestAnimationFrame(tick);
}

function animateCounters() {
    const counters = document.querySelectorAll('.counter');

    const runCounters = (data = {}) => {
        counters.forEach(counter => {
            const key = counter.dataset.key;
            const htmlTarget = parseInt(counter.dataset.target || 0, 10);
            const apiTarget = key && data[key] !== undefined ? parseInt(data[key], 10) : null;
            const target = apiTarget !== null && !Number.isNaN(apiTarget) && apiTarget > 0
                ? apiTarget
                : htmlTarget;

            counter.dataset.target = target;
            counter.textContent = '0';
            animateCounterValue(counter, target);
        });
    };

    const siteUrl = document.querySelector('meta[name="site-url"]')?.content || '';
    fetch(`${siteUrl}/settings_json.php`)
        .then(res => res.json())
        .then(runCounters)
        .catch(() => runCounters());
}

// Initialize counters when they come into view
const statsSection = document.querySelector('.stats-section, .about-stats');
if (statsSection) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    observer.observe(statsSection);
}

/**
 * Share buttons functionality
 */
document.querySelectorAll('.share-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const url = this.href;
        const width = 600;
        const height = 400;
        const left = (window.innerWidth - width) / 2;
        const top = (window.innerHeight - height) / 2;
        
        window.open(url, 'share', `width=${width},height=${height},left=${left},top=${top}`);
    });
});

/**
 * Mobile Touch Enhancements
 */
(function() {
    // Detect touch device
    const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    
    if (isTouchDevice) {
        document.body.classList.add('touch-device');
    }
    
    // Prevent double-tap zoom on buttons
    document.querySelectorAll('a, button, .btn').forEach(el => {
        el.addEventListener('touchend', (e) => {
            // Allow default behavior
        });
    });
    
    // Swipe support for hero slider
    const heroSection = document.querySelector('.hero');
    if (heroSection && isTouchDevice) {
        let touchStartX = 0;
        let touchEndX = 0;
        
        heroSection.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        heroSection.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, { passive: true });
        
        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;
            
            if (Math.abs(diff) > swipeThreshold) {
                const dots = document.querySelectorAll('.hero-dot');
                const activeIndex = Array.from(dots).findIndex(d => d.classList.contains('active'));
                
                if (diff > 0 && dots[activeIndex + 1]) {
                    // Swipe left - next slide
                    dots[activeIndex + 1].click();
                } else if (diff < 0 && dots[activeIndex - 1]) {
                    // Swipe right - prev slide
                    dots[activeIndex - 1].click();
                }
            }
        }
    }
    
    // Swipe support for gallery lightbox
    const lightbox = document.querySelector('.lightbox');
    if (lightbox && isTouchDevice) {
        let touchStartX = 0;
        
        lightbox.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        lightbox.addEventListener('touchend', (e) => {
            const touchEndX = e.changedTouches[0].screenX;
            const diff = touchStartX - touchEndX;
            
            if (Math.abs(diff) > 100) {
                // Close lightbox on swipe
                lightbox.classList.remove('active');
                document.body.style.overflow = '';
            }
        }, { passive: true });
    }
    
    // Pull to refresh indicator (visual only)
    let pullStartY = 0;
    let isPulling = false;
    
    if (isTouchDevice && window.scrollY === 0) {
        document.addEventListener('touchstart', (e) => {
            if (window.scrollY === 0) {
                pullStartY = e.touches[0].clientY;
                isPulling = true;
            }
        }, { passive: true });
        
        document.addEventListener('touchmove', (e) => {
            if (!isPulling) return;
            const pullDistance = e.touches[0].clientY - pullStartY;
            
            if (pullDistance > 100 && window.scrollY === 0) {
                // User is pulling down - browser handles refresh
            }
        }, { passive: true });
        
        document.addEventListener('touchend', () => {
            isPulling = false;
        }, { passive: true });
    }
})();

/**
 * Native Share API
 */
async function shareContent(title, text, url) {
    if (navigator.share) {
        try {
            await navigator.share({ title, text, url });
            return true;
        } catch (err) {
            console.log('Share cancelled or failed:', err);
            return false;
        }
    }
    return false;
}

// Expose for use in HTML
window.shareContent = shareContent;
