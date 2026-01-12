/**
 * Main JavaScript for Urji Beri School Website
 * Public Site Functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initMobileNav();
    initSmoothScroll();
    initScrollAnimations();
    initGalleryFilter();
    initLightbox();
    initContactForm();
    initHeaderScroll();
    initHeroSlider();
});

/**
 * Hero Image Slider
 */
function initHeroSlider() {
    const slides = document.querySelectorAll('.hero-slide');
    
    if (slides.length === 0) return;
    
    console.log('Hero slider initialized with', slides.length, 'slides');
    
    let currentSlide = 0;
    const slideCount = slides.length;
    
    function goToSlide(index) {
        slides[currentSlide].classList.remove('active');
        currentSlide = (index + slideCount) % slideCount;
        slides[currentSlide].classList.add('active');
        console.log('Showing slide', currentSlide + 1);
    }
    
    function nextSlide() {
        goToSlide(currentSlide + 1);
    }
    
    // Start auto sliding every 3 seconds
    setInterval(nextSlide, 3000);
}
 

/**
 * Mobile Navigation Toggle
 */
function initMobileNav() {
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (!navToggle || !navMenu) return;
    
    navToggle.addEventListener('click', function() {
        navMenu.classList.toggle('active');
        navToggle.classList.toggle('active');
        
        // Toggle body scroll
        document.body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : '';
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(e) {
        if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
            navMenu.classList.remove('active');
            navToggle.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
    
    // Close menu when clicking a link
    navMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function() {
            navMenu.classList.remove('active');
            navToggle.classList.remove('active');
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
    const contactForm = document.querySelector('#contact-form');
    
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
    
    let lastScroll = 0;
    
    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;
        
        // Add scrolled class
        if (currentScroll > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        // Hide/show header on scroll (optional)
        // if (currentScroll > lastScroll && currentScroll > 200) {
        //     header.style.transform = 'translateY(-100%)';
        // } else {
        //     header.style.transform = 'translateY(0)';
        // }
        
        lastScroll = currentScroll;
    });
}

/**
 * Counter Animation (for stats)
 */
function animateCounters() {
    const counters = document.querySelectorAll('.counter');

    // Fetch live stats JSON first, then animate
    fetch('/settings_json.php')
        .then(res => res.json())
        .then(data => {
            counters.forEach(counter => {
                const key = counter.dataset.key;
                const target = key && data[key] !== undefined ? parseInt(data[key]) : parseInt(counter.dataset.target || 0);
                const duration = 2000;
                const step = target / (duration / 16 || 1);
                let current = 0;

                const updateCounter = () => {
                    current += step;
                    if (current < target) {
                        counter.textContent = Math.round(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target;
                    }
                };

                // Update data-target attribute so other logic can see it
                counter.dataset.target = target;

                updateCounter();
            });
        })
        .catch(() => {
            // Fallback to existing behavior if fetch fails
            counters.forEach(counter => {
                const target = parseInt(counter.dataset.target);
                const duration = 2000;
                const step = target / (duration / 16);
                let current = 0;

                const updateCounter = () => {
                    current += step;
                    if (current < target) {
                        counter.textContent = Math.round(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target;
                    }
                };

                updateCounter();
            });
        });
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
