    <!-- Footer -->
    <footer class="footer" itemscope itemtype="https://schema.org/WPFooter">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <div class="footer-logo">
                        <img src="<?php echo asset_url('images/logo-white.png'); ?>" alt="<?php echo e(get_setting('site_name')); ?> Logo" width="50" height="50">
                        <span class="footer-logo-text"><?php echo e(get_setting('site_name', 'Urji Beri School')); ?></span>
                    </div>
                    <p>Providing quality preschool and elementary education for children ages 3-13. We nurture young minds in a safe, caring, and stimulating environment.</p>
                    <div class="footer-social" aria-label="Social Media Links">
                        <?php if (get_setting('social_facebook')): ?>
                        <a href="<?php echo e(get_setting('social_facebook')); ?>" target="_blank" rel="noopener noreferrer" aria-label="Follow us on Facebook" title="Facebook">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <?php endif; ?>
                        <?php if (get_setting('social_instagram')): ?>
                        <a href="<?php echo e(get_setting('social_instagram')); ?>" target="_blank" rel="noopener noreferrer" aria-label="Follow us on Instagram" title="Instagram">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                        <?php endif; ?>
                        <?php if (get_setting('social_telegram')): ?>
                        <a href="<?php echo e(get_setting('social_telegram')); ?>" target="_blank" rel="noopener noreferrer" aria-label="Join us on Telegram" title="Telegram">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                            </svg>
                        </a>
                        <?php endif; ?>
                        <?php if (get_setting('social_youtube')): ?>
                        <a href="<?php echo e(get_setting('social_youtube')); ?>" target="_blank" rel="noopener noreferrer" aria-label="Subscribe on YouTube" title="YouTube">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="footer-links-col">
                    <h4 class="footer-title">Quick Links</h4>
                    <nav aria-label="Footer Navigation">
                        <ul class="footer-links">
                            <li><a href="<?php echo SITE_URL; ?>">Home</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/about.php">About Us</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/gallery.php">Gallery</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/blog.php">News & Events</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/contact.php">Contact</a></li>
                        </ul>
                    </nav>
                </div>
                
                <div class="footer-links-col">
                    <h4 class="footer-title">Resources</h4>
                    <nav aria-label="Resources Navigation">
                        <ul class="footer-links">
                            <li><a href="<?php echo e(get_setting('online_result_url', 'https://result.urjiberischool.com/view_score.php')); ?>" target="_blank" rel="noopener">Online Results</a></li>
                            <li><a href="<?php echo e(get_setting('teacher_login_url', 'https://result.urjiberischool.com/login.php')); ?>" target="_blank" rel="noopener">Teachers Portal</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/director.php">Director's Message</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/sitemap.php" rel="nofollow">Sitemap</a></li>
                        </ul>
                    </nav>
                </div>
                
                <div class="footer-links-col" itemscope itemtype="https://schema.org/School">
                    <h4 class="footer-title">Contact Info</h4>
                    <address class="footer-contact">
                        <ul class="footer-links">
                            <li itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                                <span itemprop="streetAddress"><?php echo e(get_setting('contact_address')); ?></span>
                            </li>
                            <li>
                                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', get_setting('contact_phone')); ?>" itemprop="telephone">
                                    <?php echo e(get_setting('contact_phone')); ?>
                                </a>
                            </li>
                            <li>
                                <a href="mailto:<?php echo e(get_setting('contact_email')); ?>" itemprop="email">
                                    <?php echo e(get_setting('contact_email')); ?>
                                </a>
                            </li>
                        </ul>
                    </address>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <span itemprop="name"><?php echo e(get_setting('site_name', 'Urji Beri School')); ?></span>. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- Google Analytics -->
    <?php if (get_setting('google_analytics_id')): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e(get_setting('google_analytics_id')); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo e(get_setting('google_analytics_id')); ?>');
    </script>
    <?php endif; ?>
    
    <!-- Lightbox -->
    <div class="lightbox" id="lightbox">
        <div class="lightbox-close" id="lightboxClose">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </div>
        <div class="lightbox-content">
            <img src="" alt="" id="lightboxImage">
        </div>
    </div>
    
    <!-- Main JavaScript -->
    <script src="<?php echo asset_url('js/main.js'); ?>?v=<?php echo time(); ?>"></script>
    <?php if (!empty($_SESSION['settings_updated'])): ?>
    <script>
        window.__settingsUpdated = true;
    </script>
    <?php unset($_SESSION['settings_updated']); endif; ?>
    
    <!-- PWA Install Prompt -->
    <div class="pwa-install-prompt hidden" id="pwaInstallPrompt">
        <img src="<?php echo asset_url('images/logo.png'); ?>" alt="Urji Beri School">
        <div class="pwa-install-content">
            <h4>Install Urji Beri App</h4>
            <p>Add to home screen for quick access</p>
        </div>
        <div class="pwa-install-buttons">
            <button class="pwa-install-btn secondary" id="pwaInstallLater">Later</button>
            <button class="pwa-install-btn primary" id="pwaInstallNow">Install</button>
        </div>
    </div>
    
    <!-- Service Worker Registration & PWA -->
    <script>
        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((registration) => {
                        console.log('SW registered:', registration.scope);

                        // Force update check on every page load
                        registration.update();

                        // If settings were updated in admin, instruct SW to clear cache
                        if (window.__settingsUpdated) {
                            try {
                                if (registration.active) {
                                    registration.active.postMessage({ type: 'CLEAR_CACHE' });
                                } else if (navigator.serviceWorker.controller) {
                                    navigator.serviceWorker.controller.postMessage({ type: 'CLEAR_CACHE' });
                                } else {
                                    navigator.serviceWorker.addEventListener('controllerchange', () => {
                                        if (navigator.serviceWorker.controller) {
                                            navigator.serviceWorker.controller.postMessage({ type: 'CLEAR_CACHE' });
                                        }
                                    });
                                }
                            } catch (err) {
                                console.warn('Failed to message SW to clear cache', err);
                            }
                        }

                        // Listen for new service worker
                        registration.addEventListener('updatefound', () => {
                            const newWorker = registration.installing;
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    // New service worker available, activate it immediately
                                    newWorker.postMessage({ type: 'SKIP_WAITING' });
                                }
                            });
                        });

                        // Refresh page when new service worker takes control
                        navigator.serviceWorker.addEventListener('controllerchange', () => {
                            console.log('New SW active, refreshing for updates...');
                        });
                    })
                    .catch((error) => {
                        console.log('SW registration failed:', error);
                    });
            });
        }
        
        // PWA Install Prompt
        let deferredPrompt;
        const installPrompt = document.getElementById('pwaInstallPrompt');
        const installBtn = document.getElementById('pwaInstallNow');
        const laterBtn = document.getElementById('pwaInstallLater');
        
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            // Check if user has dismissed before
            const dismissed = localStorage.getItem('pwaInstallDismissed');
            if (!dismissed) {
                setTimeout(() => {
                    installPrompt.classList.remove('hidden');
                }, 3000);
            }
        });
        
        if (installBtn) {
            installBtn.addEventListener('click', async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    console.log('User response:', outcome);
                    deferredPrompt = null;
                }
                installPrompt.classList.add('hidden');
            });
        }
        
        if (laterBtn) {
            laterBtn.addEventListener('click', () => {
                installPrompt.classList.add('hidden');
                localStorage.setItem('pwaInstallDismissed', Date.now());
            });
        }
        
        // Detect if app is already installed
        window.addEventListener('appinstalled', () => {
            installPrompt.classList.add('hidden');
            deferredPrompt = null;
            console.log('PWA installed');
        });
        
        // Check if running as PWA
        if (window.matchMedia('(display-mode: standalone)').matches) {
            console.log('Running as PWA');
        }
    </script>
    
    <?php if (isset($extraJs)): ?>
        <?php echo $extraJs; ?>
    <?php endif; ?>
</body>
</html>
