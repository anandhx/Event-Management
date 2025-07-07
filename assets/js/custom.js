// Event Management System - Custom JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize animations
    initializeAnimations();
    
    // Initialize smooth scrolling
    initializeSmoothScrolling();
    
    // Initialize back to top button
    initializeBackToTop();
    
    // Initialize notification system
    initializeNotifications();
});

// Animation initialization
function initializeAnimations() {
    // Add fade-in animation to elements when they come into view
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);
    
    // Observe all cards and sections
    document.querySelectorAll('.custom-card, .section').forEach(el => {
        observer.observe(el);
    });
}

// Smooth scrolling for anchor links
function initializeSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Back to top functionality
function initializeBackToTop() {
    const backToTopBtn = document.querySelector('.btn-custom[href="#"]');
    if (backToTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopBtn.style.display = 'block';
                backToTopBtn.style.opacity = '1';
            } else {
                backToTopBtn.style.opacity = '0';
                setTimeout(() => {
                    backToTopBtn.style.display = 'none';
                }, 300);
            }
        });
        
        backToTopBtn.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

// Notification system
function initializeNotifications() {
    // Auto-hide notifications after 5 seconds
    const notifications = document.querySelectorAll('.notification');
    notifications.forEach(notification => {
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    });
}

// Enhanced form validation
function validateLoginForm() {
    const username = document.getElementById('loginUsername').value.trim();
    const password = document.getElementById('loginPassword').value.trim();
    
    // Username validation
    if (username.length < 3 || username.length > 20) {
        showNotification('Username must be between 3 and 20 characters', 'error');
        return false;
    }
    
    // Password validation
    if (password.length < 8) {
        showNotification('Password must be at least 8 characters long', 'error');
        return false;
    }
    
    return true;
}

function validateSignupForm() {
    const username = document.getElementById('signupUsername').value.trim();
    const password = document.getElementById('signupPassword').value.trim();
    const confirmPassword = document.getElementById('signupConfirmPassword').value.trim();
    const email = document.getElementById('signupEmail').value.trim();
    
    // Username validation
    if (username.length < 3 || username.length > 20) {
        showNotification('Username must be between 3 and 20 characters', 'error');
        return false;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showNotification('Please enter a valid email address', 'error');
        return false;
    }
    
    // Password validation
    if (password.length < 8) {
        showNotification('Password must be at least 8 characters long', 'error');
        return false;
    }
    
    if (password !== confirmPassword) {
        showNotification('Passwords do not match', 'error');
        return false;
    }
    
    return true;
}

// Show notification function
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);
}

// Enhanced tab switching with animations
function toggleTab(tab) {
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const loginTab = document.getElementById('loginTab');
    const signupTab = document.getElementById('signupTab');
    
    if (tab === 'login') {
        // Update tab states
        loginTab.classList.add('active');
        signupTab.classList.remove('active');
        
        // Animate form switch
        signupForm.style.opacity = '0';
        signupForm.style.transform = 'translateX(20px)';
        
        setTimeout(() => {
            signupForm.style.display = 'none';
            loginForm.style.display = 'block';
            loginForm.style.opacity = '0';
            loginForm.style.transform = 'translateX(-20px)';
            
            setTimeout(() => {
                loginForm.style.opacity = '1';
                loginForm.style.transform = 'translateX(0)';
            }, 50);
        }, 200);
        
    } else {
        // Update tab states
        signupTab.classList.add('active');
        loginTab.classList.remove('active');
        
        // Animate form switch
        loginForm.style.opacity = '0';
        loginForm.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
            loginForm.style.display = 'none';
            signupForm.style.display = 'block';
            signupForm.style.opacity = '0';
            signupForm.style.transform = 'translateX(20px)';
            
            setTimeout(() => {
                signupForm.style.opacity = '1';
                signupForm.style.transform = 'translateX(0)';
            }, 50);
        }, 200);
    }
}

// Loading spinner functionality
function showLoading(element) {
    const spinner = document.createElement('div');
    spinner.className = 'loading-spinner';
    element.appendChild(spinner);
    element.disabled = true;
}

function hideLoading(element) {
    const spinner = element.querySelector('.loading-spinner');
    if (spinner) {
        spinner.remove();
    }
    element.disabled = false;
}

// Form submission with loading state
function submitFormWithLoading(form, action) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    showLoading(submitBtn);
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    
    // Simulate form submission (replace with actual AJAX call)
    setTimeout(() => {
        hideLoading(submitBtn);
        submitBtn.innerHTML = originalText;
        showNotification('Form submitted successfully!', 'success');
    }, 2000);
}

// Initialize form submissions
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (validateLoginForm()) {
                submitFormWithLoading(this, 'login');
            } else {
                e.preventDefault();
            }
        });
    }
    
    if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
            if (validateSignupForm()) {
                submitFormWithLoading(this, 'signup');
            } else {
                e.preventDefault();
            }
        });
    }
});

// Parallax effect for hero section
function initializeParallax() {
    const heroSection = document.querySelector('.hero-section');
    if (heroSection) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            heroSection.style.transform = `translateY(${rate}px)`;
        });
    }
}

// Initialize parallax on load
document.addEventListener('DOMContentLoaded', initializeParallax);

// Counter animation for statistics
function animateCounters() {
    const counters = document.querySelectorAll('.stat-number');
    const speed = 200;
    
    counters.forEach(counter => {
        const updateCount = () => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            const inc = target / speed;
            
            if (count < target) {
                counter.innerText = Math.ceil(count + inc);
                setTimeout(updateCount, 1);
            } else {
                counter.innerText = target;
            }
        };
        
        updateCount();
    });
}

// Initialize counter animation when elements come into view
const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateCounters();
            counterObserver.unobserve(entry.target);
        }
    });
});

document.querySelectorAll('.stat-card').forEach(card => {
    counterObserver.observe(card);
}); 