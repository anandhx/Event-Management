// Optimized Advanced Effects for EventPro
class OptimizedEffects {
    constructor() {
        this.init();
        this.setupThemeToggle();
    }

    init() {
        this.setupScrollAnimations();
        this.setupHeaderScroll();
        this.setupSmoothScrolling();
        this.setupPerformanceOptimizations();
    }

    setupScrollAnimations() {
        // Use Intersection Observer for better performance
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate');
                }
            });
        }, observerOptions);

        // Observe elements with scroll animations
        document.querySelectorAll('.scroll-animate, .scroll-animate-left, .scroll-animate-right').forEach(el => {
            observer.observe(el);
        });
    }

    setupHeaderScroll() {
        const header = document.querySelector('.custom-header');
        if (!header) return;

        let lastScroll = 0;
        let ticking = false;

        const updateHeader = () => {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }

            lastScroll = currentScroll;
            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateHeader);
                ticking = true;
            }
        });
    }

    setupSmoothScrolling() {
        // Smooth scroll for anchor links
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

    setupPerformanceOptimizations() {
        // Optimize animations for better performance
        const optimizeElements = () => {
            const elements = document.querySelectorAll('.custom-card, .stat-card-3d, .btn-custom');
            elements.forEach(el => {
                el.style.willChange = 'transform';
            });
        };

        // Debounce scroll events
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            if (scrollTimeout) {
                clearTimeout(scrollTimeout);
            }
            scrollTimeout = setTimeout(() => {
                // Clean up will-change after scroll stops
                const elements = document.querySelectorAll('.custom-card, .stat-card-3d, .btn-custom');
                elements.forEach(el => {
                    el.style.willChange = 'auto';
                });
            }, 150);
        });

        optimizeElements();
    }

    setupThemeToggle() {
        // Create theme toggle button
        const themeToggle = document.createElement('button');
        themeToggle.className = 'theme-toggle';
        themeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        themeToggle.setAttribute('aria-label', 'Toggle theme');
        document.body.appendChild(themeToggle);

        // Theme toggle functionality
        let currentTheme = localStorage.getItem('theme') || 'dark';
        this.setTheme(currentTheme);

        themeToggle.addEventListener('click', () => {
            currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
            this.setTheme(currentTheme);
            localStorage.setItem('theme', currentTheme);
        });
    }

    setTheme(theme) {
        const themeToggle = document.querySelector('.theme-toggle');
        const icon = themeToggle.querySelector('i');
        
        if (theme === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
            icon.className = 'fas fa-sun';
        } else {
            document.documentElement.setAttribute('data-theme', 'dark');
            icon.className = 'fas fa-moon';
        }
    }

    // Optimized 3D card effects
    setup3DEffects() {
        const cards = document.querySelectorAll('.custom-card');
        
        cards.forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                const rotateX = (y - centerY) / 20;
                const rotateY = (centerX - x) / 20;
                
                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
            });
        });
    }

    // Optimized parallax effect
    setupParallax() {
        let ticking = false;
        
        const updateParallax = () => {
            const scrolled = window.pageYOffset;
            const parallaxElements = document.querySelectorAll('.parallax-bg');
            
            parallaxElements.forEach((element, index) => {
                const speed = 0.3 + (index * 0.1);
                const yPos = -(scrolled * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
            
            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateParallax);
                ticking = true;
            }
        });
    }

    // Add loading animation
    setupLoading() {
        window.addEventListener('load', () => {
            const spinner = document.getElementById('spinner');
            if (spinner) {
                spinner.style.opacity = '0';
                setTimeout(() => {
                    spinner.style.display = 'none';
                }, 300);
            }
        });
    }

    // Add scroll progress indicator
    setupScrollProgress() {
        const progressBar = document.createElement('div');
        progressBar.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: var(--gradient-primary);
            z-index: 10000;
            transition: width 0.1s ease;
        `;
        document.body.appendChild(progressBar);

        let progressTicking = false;
        
        const updateProgress = () => {
            const scrolled = (window.pageYOffset / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
            progressBar.style.width = scrolled + '%';
            progressTicking = false;
        };

        window.addEventListener('scroll', () => {
            if (!progressTicking) {
                requestAnimationFrame(updateProgress);
                progressTicking = true;
            }
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const effects = new OptimizedEffects();
    
    // Setup additional effects
    effects.setup3DEffects();
    effects.setupParallax();
    effects.setupLoading();
    effects.setupScrollProgress();
});

// Performance optimizations
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(console.error);
    });
}

// Preload critical resources
const preloadResources = () => {
    const links = [
        'assets/img/hero-bg.jpg',
        'assets/img/corporate-event.jpg',
        'assets/img/wedding-event.jpg'
    ];
    
    links.forEach(href => {
        const link = document.createElement('link');
        link.rel = 'preload';
        link.as = 'image';
        link.href = href;
        document.head.appendChild(link);
    });
};

// Run preload
preloadResources(); 