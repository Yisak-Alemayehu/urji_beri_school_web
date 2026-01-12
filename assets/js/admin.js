/**
 * Admin JavaScript for Urji Beri School Website
 * Admin Dashboard Functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    initSidebarToggle();
    initFileUpload();
    initFormValidation();
    initConfirmDialogs();
    initDataTables();
    initFlashMessages();
    initPWAInstall();
});

/**
 * Sidebar Toggle for Mobile
 */
function initSidebarToggle() {
    const menuToggle = document.getElementById('adminMenuToggle');
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (!menuToggle || !sidebar) return;
    
    function openSidebar() {
        sidebar.classList.add('active');
        overlay?.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay?.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    menuToggle.addEventListener('click', function(e) {
        e.preventDefault();
        if (sidebar.classList.contains('active')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });
    
    // Close sidebar when clicking overlay
    overlay?.addEventListener('click', closeSidebar);
    
    // Close sidebar when clicking a nav link (on mobile)
    sidebar.querySelectorAll('.admin-nav-link').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 992) {
                closeSidebar();
            }
        });
    });
    
    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('active')) {
            closeSidebar();
        }
    });
    
    // Close sidebar when window is resized to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            closeSidebar();
        }
    });
}

/**
 * PWA Install Prompt
 */
function initPWAInstall() {
    let deferredPrompt;
    
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        
        // Optionally show install button
        const installBtn = document.getElementById('installAppBtn');
        if (installBtn) {
            installBtn.style.display = 'flex';
            installBtn.addEventListener('click', async () => {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log(`User ${outcome} the install prompt`);
                deferredPrompt = null;
                installBtn.style.display = 'none';
            });
        }
    });
    
    window.addEventListener('appinstalled', () => {
        console.log('PWA was installed');
        deferredPrompt = null;
    });
}

/**
 * File Upload Preview
 */
function initFileUpload() {
    const fileInputs = document.querySelectorAll('.file-input');
    
    fileInputs.forEach(input => {
        const wrapper = input.closest('.file-upload-area');
        const label = wrapper?.querySelector('.file-label');
        
        if (!label) return;
        
        // Drag and drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            wrapper.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            wrapper.addEventListener(eventName, () => wrapper.classList.add('dragover'), false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            wrapper.addEventListener(eventName, () => wrapper.classList.remove('dragover'), false);
        });
        
        wrapper.addEventListener('drop', function(e) {
            const files = e.dataTransfer.files;
            if (files.length) {
                input.files = files;
                handleFileSelect(input, files[0]);
            }
        });
        
        // File input change
        input.addEventListener('change', function() {
            if (this.files.length) {
                handleFileSelect(this, this.files[0]);
            }
        });
    });
    
    function handleFileSelect(input, file) {
        const wrapper = input.closest('.file-upload-area');
        const label = wrapper?.querySelector('.file-label');
        
        // Validate file type
        const allowedTypes = input.accept.split(',').map(t => t.trim());
        const fileType = file.type;
        
        if (!allowedTypes.some(type => fileType.match(type.replace('*', '.*')))) {
            alert('Invalid file type. Please select a valid image file.');
            input.value = '';
            return;
        }
        
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File size exceeds 5MB limit.');
            input.value = '';
            return;
        }
        
        // Show preview
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Remove existing preview
                wrapper.querySelector('.file-preview')?.remove();
                
                const preview = document.createElement('div');
                preview.className = 'file-preview';
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <div class="file-info">
                        <span class="file-name">${file.name}</span>
                        <span class="file-size">${formatFileSize(file.size)}</span>
                    </div>
                    <button type="button" class="file-remove">&times;</button>
                `;
                
                wrapper.insertBefore(preview, label);
                label.style.display = 'none';
                
                // Remove preview button
                preview.querySelector('.file-remove').addEventListener('click', function() {
                    input.value = '';
                    preview.remove();
                    label.style.display = '';
                });
            };
            reader.readAsDataURL(file);
        }
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}

/**
 * Form Validation
 */
function initFormValidation() {
    const forms = document.querySelectorAll('.admin-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Clear previous errors
            form.querySelectorAll('.form-error').forEach(el => el.remove());
            form.querySelectorAll('.form-group').forEach(el => el.classList.remove('has-error'));
            
            // Validate required fields
            form.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    showFieldError(field, 'This field is required');
                    isValid = false;
                }
            });
            
            // Validate email fields
            form.querySelectorAll('[type="email"]').forEach(field => {
                if (field.value && !isValidEmail(field.value)) {
                    showFieldError(field, 'Please enter a valid email address');
                    isValid = false;
                }
            });
            
            // Validate URL fields
            form.querySelectorAll('[type="url"]').forEach(field => {
                if (field.value && !isValidUrl(field.value)) {
                    showFieldError(field, 'Please enter a valid URL');
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = form.querySelector('.has-error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });
    
    function showFieldError(field, message) {
        const formGroup = field.closest('.form-group');
        if (!formGroup) return;
        
        formGroup.classList.add('has-error');
        
        const error = document.createElement('span');
        error.className = 'form-error';
        error.textContent = message;
        formGroup.appendChild(error);
    }
    
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }
    
    function isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    }
}

/**
 * Confirm Dialogs
 */
function initConfirmDialogs() {
    // Already handled via onclick="return confirm(...)" in PHP
    // This is for any additional JS-based confirmations
}

/**
 * Data Tables Enhancement
 */
function initDataTables() {
    const tables = document.querySelectorAll('.admin-table');
    
    tables.forEach(table => {
        // Add hover effect
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = 'rgba(54, 121, 255, 0.05)';
            });
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });
    });
}

/**
 * Flash Messages Auto-hide
 */
function initFlashMessages() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        // Auto-hide after 5 seconds
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
        
        // Click to dismiss
        alert.style.cursor = 'pointer';
        alert.addEventListener('click', function() {
            this.style.opacity = '0';
            this.style.transform = 'translateY(-10px)';
            setTimeout(() => this.remove(), 300);
        });
    });
}

/**
 * Character Counter for Textareas
 */
document.querySelectorAll('textarea[maxlength]').forEach(textarea => {
    const maxLength = textarea.getAttribute('maxlength');
    const counter = document.createElement('div');
    counter.className = 'char-counter';
    counter.textContent = `0 / ${maxLength}`;
    textarea.parentNode.appendChild(counter);
    
    textarea.addEventListener('input', function() {
        counter.textContent = `${this.value.length} / ${maxLength}`;
        if (this.value.length > maxLength * 0.9) {
            counter.classList.add('warning');
        } else {
            counter.classList.remove('warning');
        }
    });
});

/**
 * Search/Filter for Tables
 */
function filterTable(tableId, searchTerm) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const rows = table.querySelectorAll('tbody tr');
    const term = searchTerm.toLowerCase();
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
}

/**
 * Toggle Switch Handler
 */
document.querySelectorAll('.toggle-switch input').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const url = this.dataset.url;
        if (url) {
            window.location.href = url + '&value=' + (this.checked ? 1 : 0);
        }
    });
});

/**
 * Select All Checkbox
 */
document.querySelectorAll('.select-all').forEach(selectAll => {
    selectAll.addEventListener('change', function() {
        const table = this.closest('table');
        table.querySelectorAll('tbody input[type="checkbox"]').forEach(cb => {
            cb.checked = this.checked;
        });
    });
});

/**
 * Bulk Actions
 */
document.querySelectorAll('.bulk-action-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const action = form.querySelector('select[name="action"]')?.value;
        const checked = form.querySelectorAll('tbody input[type="checkbox"]:checked');
        
        if (!action) {
            e.preventDefault();
            alert('Please select an action');
            return;
        }
        
        if (checked.length === 0) {
            e.preventDefault();
            alert('Please select at least one item');
            return;
        }
        
        if (action === 'delete' && !confirm('Are you sure you want to delete the selected items?')) {
            e.preventDefault();
        }
    });
});
