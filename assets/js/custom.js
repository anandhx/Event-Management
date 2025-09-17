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
    
    // Initialize form submissions
    initializeFormSubmissions();
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
    
    // Relaxed password validation for dummy showcase
    if (password.length < 3) {
        showNotification('Password must be at least 3 characters long', 'error');
        return false;
    }
    
    return true;
}

// Client signup form validation
function validateClientSignupForm() {
    const fullName = document.getElementById('clientFullName').value.trim();
    const username = document.getElementById('clientUsername').value.trim();
    const email = document.getElementById('clientEmail').value.trim();
    const phone = document.getElementById('clientPhone').value.trim();
    const address = document.getElementById('clientAddress').value.trim();
    const password = document.getElementById('clientPassword').value.trim();
    const confirmPassword = document.getElementById('clientConfirmPassword').value.trim();
    
    // Full name validation
    if (fullName.length < 2 || fullName.length > 100) {
        showNotification('Full name must be between 2 and 100 characters', 'error');
        return false;
    }
    
    // Username validation
    if (username.length < 3 || username.length > 20) {
        showNotification('Username must be between 3 and 20 characters', 'error');
        return false;
    }
    
    if (!/^[a-zA-Z0-9_]+$/.test(username)) {
        showNotification('Username can only contain letters, numbers, and underscores', 'error');
        return false;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showNotification('Please enter a valid email address', 'error');
        return false;
    }
    
    // Phone validation
    if (phone.length < 10 || phone.length > 15) {
        showNotification('Phone number must be between 10 and 15 digits', 'error');
        return false;
    }
    
    // Address validation
    if (address.length < 10 || address.length > 500) {
        showNotification('Address must be between 10 and 500 characters', 'error');
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

// Planner signup form validation
function validatePlannerSignupForm() {
    const fullName = document.getElementById('plannerFullName').value.trim();
    const username = document.getElementById('plannerUsername').value.trim();
    const email = document.getElementById('plannerEmail').value.trim();
    const phone = document.getElementById('plannerPhone').value.trim();
    const companyName = document.getElementById('plannerCompany').value.trim();
    const specialization = document.getElementById('plannerSpecialization').value.trim();
    const experienceYears = document.getElementById('plannerExperience').value;
    const location = document.getElementById('plannerLocation').value.trim();
    const bio = document.getElementById('plannerBio').value.trim();
    const password = document.getElementById('plannerPassword').value.trim();
    const confirmPassword = document.getElementById('plannerConfirmPassword').value.trim();
    
    // Full name validation
    if (fullName.length < 2 || fullName.length > 100) {
        showNotification('Full name must be between 2 and 100 characters', 'error');
        return false;
    }
    
    // Username validation
    if (username.length < 3 || username.length > 20) {
        showNotification('Username must be between 3 and 20 characters', 'error');
        return false;
    }
    
    if (!/^[a-zA-Z0-9_]+$/.test(username)) {
        showNotification('Username can only contain letters, numbers, and underscores', 'error');
        return false;
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showNotification('Please enter a valid email address', 'error');
        return false;
    }
    
    // Phone validation
    if (phone.length < 10 || phone.length > 15) {
        showNotification('Phone number must be between 10 and 15 digits', 'error');
        return false;
    }
    
    // Company name validation
    if (companyName.length < 2 || companyName.length > 100) {
        showNotification('Company name must be between 2 and 100 characters', 'error');
        return false;
    }
    
    // Specialization validation
    if (specialization.length < 10 || specialization.length > 500) {
        showNotification('Specialization must be between 10 and 500 characters', 'error');
        return false;
    }
    
    // Experience validation
    if (!experienceYears || experienceYears === '') {
        showNotification('Please select your years of experience', 'error');
        return false;
    }
    
    // Location validation
    if (location.length < 2 || location.length > 100) {
        showNotification('Location must be between 2 and 100 characters', 'error');
        return false;
    }
    
    // Bio validation
    if (bio.length < 20 || bio.length > 1000) {
        showNotification('Bio must be between 20 and 1000 characters', 'error');
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
    const userTypeSelection = document.getElementById('userTypeSelection');
    const clientSignupForm = document.getElementById('clientSignupForm');
    const plannerSignupForm = document.getElementById('plannerSignupForm');
    
    if (tab === 'login') {
        // Hide all signup forms
        userTypeSelection.style.display = 'none';
        clientSignupForm.style.display = 'none';
        plannerSignupForm.style.display = 'none';
        
        // Show login form with animation
        loginForm.style.opacity = '0';
        loginForm.style.transform = 'translateX(-20px)';
        loginForm.style.display = 'block';
        
        setTimeout(() => {
            loginForm.style.opacity = '1';
            loginForm.style.transform = 'translateX(0)';
        }, 50);
        
    } else {
        // Hide login form
        loginForm.style.display = 'none';
        
        // Show user type selection
        userTypeSelection.style.display = 'block';
        clientSignupForm.style.display = 'none';
        plannerSignupForm.style.display = 'none';
        
        // Animate in
        userTypeSelection.style.opacity = '0';
        userTypeSelection.style.transform = 'translateX(20px)';
        
        setTimeout(() => {
            userTypeSelection.style.opacity = '1';
            userTypeSelection.style.transform = 'translateX(0)';
        }, 50);
    }
}

// Function to select user type for registration
function selectUserType(userType) {
    const userTypeSelection = document.getElementById('userTypeSelection');
    const clientSignupForm = document.getElementById('clientSignupForm');
    const plannerSignupForm = document.getElementById('plannerSignupForm');
    
    // Hide user type selection
    userTypeSelection.style.opacity = '0';
    userTypeSelection.style.transform = 'translateX(-20px)';
    
    setTimeout(() => {
        userTypeSelection.style.display = 'none';
        
        if (userType === 'client') {
            // Show client signup form
            clientSignupForm.style.display = 'block';
            clientSignupForm.style.opacity = '0';
            clientSignupForm.style.transform = 'translateX(20px)';
            
            setTimeout(() => {
                clientSignupForm.style.opacity = '1';
                clientSignupForm.style.transform = 'translateX(0)';
            }, 50);
            
        } else if (userType === 'planner') {
            // Show planner signup form
            plannerSignupForm.style.display = 'block';
            plannerSignupForm.style.opacity = '0';
            plannerSignupForm.style.transform = 'translateX(20px)';
            
            setTimeout(() => {
                plannerSignupForm.style.opacity = '1';
                plannerSignupForm.style.transform = 'translateX(0)';
            }, 50);
        }
    }, 200);
}

// Function to go back to user type selection
function goBackToUserType() {
    const userTypeSelection = document.getElementById('userTypeSelection');
    const clientSignupForm = document.getElementById('clientSignupForm');
    const plannerSignupForm = document.getElementById('plannerSignupForm');
    
    // Hide current form with animation
    if (clientSignupForm.style.display !== 'none') {
        clientSignupForm.style.opacity = '0';
        clientSignupForm.style.transform = 'translateX(-20px)';
    } else if (plannerSignupForm.style.display !== 'none') {
        plannerSignupForm.style.opacity = '0';
        plannerSignupForm.style.transform = 'translateX(-20px)';
    }
    
    setTimeout(() => {
        clientSignupForm.style.display = 'none';
        plannerSignupForm.style.display = 'none';
        
        // Show user type selection
        userTypeSelection.style.display = 'block';
        userTypeSelection.style.opacity = '0';
        userTypeSelection.style.transform = 'translateX(-20px)';
        
        setTimeout(() => {
            userTypeSelection.style.opacity = '1';
            userTypeSelection.style.transform = 'translateX(0)';
        }, 50);
    }, 200);
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
function initializeFormSubmissions() {
    const loginForm = document.getElementById('loginForm');
    const clientSignupForm = document.getElementById('clientSignupForm');
    const plannerSignupForm = document.getElementById('plannerSignupForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (validateLoginForm()) {
                submitFormWithLoading(this, 'login');
            } else {
                e.preventDefault();
            }
        });
    }
    
    if (clientSignupForm) {
        clientSignupForm.addEventListener('submit', function(e) {
            if (validateClientSignupForm()) {
                submitFormWithLoading(this, 'client_signup');
            } else {
                e.preventDefault();
            }
        });
    }
    
    if (plannerSignupForm) {
        plannerSignupForm.addEventListener('submit', function(e) {
            if (validatePlannerSignupForm()) {
                submitFormWithLoading(this, 'planner_signup');
            } else {
                e.preventDefault();
            }
        });
    }
}

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
document.addEventListener('DOMContentLoaded', function() {
    initializeParallax();
});

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
function initializeCounterAnimation() {
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
}

// Initialize counter animation on load
document.addEventListener('DOMContentLoaded', initializeCounterAnimation); 