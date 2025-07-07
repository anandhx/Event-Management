<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Effects Demo - EventPro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="assets/css/custom-style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <style>
        .demo-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .demo-card {
            background: var(--dark-tertiary);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            border: 1px solid rgba(99, 102, 241, 0.1);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            transform-style: preserve-3d;
            perspective: 1000px;
        }
        
        .demo-card:hover {
            transform: translateY(-20px) rotateX(10deg) rotateY(10deg);
            box-shadow: var(--shadow-xl), var(--shadow-glow-strong);
            border-color: rgba(99, 102, 241, 0.3);
        }
        
        .floating-demo {
            position: absolute;
            width: 100px;
            height: 100px;
            background: var(--gradient-primary);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
            box-shadow: var(--shadow-glow);
        }
        
        .floating-demo:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .floating-demo:nth-child(2) {
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }
        
        .floating-demo:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }
        
        .parallax-demo {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
        
        .glass-demo {
            background: rgba(26, 26, 26, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(99, 102, 241, 0.2);
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <!-- Hero Demo Section -->
    <section class="demo-section" style="background: var(--gradient-dark);">
        <div class="floating-demo"></div>
        <div class="floating-demo"></div>
        <div class="floating-demo"></div>
        
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="demo-card">
                        <h1 class="text-gradient mb-4 neon-glow">3D Effects Demo</h1>
                        <p class="text-secondary mb-4">Experience the power of modern web animations and 3D effects</p>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <button class="btn-custom btn-primary-custom shake-on-scroll">
                                <i class="fas fa-magic me-2"></i>Try Effects
                            </button>
                            <button class="btn-custom glass-effect" style="background: rgba(255,255,255,0.1); color: white; border: 2px solid rgba(255,255,255,0.3);">
                                <i class="fas fa-eye me-2"></i>View Demo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Parallax Demo Section -->
    <section class="demo-section parallax-demo" style="background-image: url('assets/img/hero-bg.jpg');">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="demo-card glass-demo">
                        <h2 class="text-gradient mb-4">Parallax Scrolling</h2>
                        <p class="text-secondary mb-4">Scroll to see the parallax effect in action</p>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="stat-card-3d">
                                    <div class="stat-number-3d">3D</div>
                                    <div class="stat-label-3d">Effects</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card-3d">
                                    <div class="stat-number-3d">360°</div>
                                    <div class="stat-label-3d">Rotation</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card-3d">
                                    <div class="stat-number-3d">∞</div>
                                    <div class="stat-label-3d">Possibilities</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3D Cards Demo Section -->
    <section class="demo-section" style="background: var(--dark-secondary);">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center">
                    <h2 class="section-title">3D Interactive Cards</h2>
                    <p class="section-subtitle">Hover over the cards to see 3D transformations</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="custom-card text-center h-100 scroll-animate">
                        <div class="card-icon">
                            <i class="fas fa-cube"></i>
                        </div>
                        <h4 class="text-primary mb-3">3D Transform</h4>
                        <p class="text-secondary mb-4">Advanced 3D transformations with smooth animations</p>
                        <button class="btn-custom btn-primary-custom shake-on-scroll">Explore</button>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="custom-card text-center h-100 scroll-animate">
                        <div class="card-icon">
                            <i class="fas fa-magic"></i>
                        </div>
                        <h4 class="text-primary mb-3">Magic Effects</h4>
                        <p class="text-secondary mb-4">Enchanting visual effects and animations</p>
                        <button class="btn-custom btn-secondary-custom shake-on-scroll">Discover</button>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="custom-card text-center h-100 scroll-animate">
                        <div class="card-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h4 class="text-primary mb-3">Performance</h4>
                        <p class="text-secondary mb-4">Optimized for smooth performance and speed</p>
                        <button class="btn-custom btn-accent-custom shake-on-scroll">Launch</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Animation Demo Section -->
    <section class="demo-section" style="background: var(--gradient-dark);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="demo-card">
                        <h2 class="text-gradient mb-4">Scroll Animations</h2>
                        <p class="text-secondary mb-4">Elements animate as you scroll through the page</p>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="scroll-animate-left p-4 glass-demo rounded">
                                    <h5 class="text-primary">Slide In Left</h5>
                                    <p class="text-secondary">Elements slide in from the left with 3D rotation</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="scroll-animate-right p-4 glass-demo rounded">
                                    <h5 class="text-primary">Slide In Right</h5>
                                    <p class="text-secondary">Elements slide in from the right with 3D rotation</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="scroll-animate p-4 glass-demo rounded">
                                <h5 class="text-primary">Fade In Up</h5>
                                <p class="text-secondary">Elements fade in and scale up smoothly</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Demo Section -->
    <section class="demo-section" style="background: var(--dark-secondary);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="demo-card">
                        <h2 class="text-gradient mb-4">Interactive Elements</h2>
                        <p class="text-secondary mb-4">Click and hover to see interactive effects</p>
                        
                        <div class="row g-4">
                            <div class="col-md-4">
                                <button class="btn-custom btn-primary-custom w-100 shake-on-scroll">
                                    <i class="fas fa-shake me-2"></i>Shake Effect
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn-custom btn-secondary-custom w-100 neon-glow">
                                    <i class="fas fa-glow me-2"></i>Neon Glow
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button class="btn-custom btn-accent-custom w-100 glass-effect">
                                    <i class="fas fa-glass me-2"></i>Glass Effect
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <div class="p-4 glass-demo rounded transform-3d">
                                <h5 class="text-primary">3D Transform Card</h5>
                                <p class="text-secondary">This card has 3D transform effects on hover</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Back to Main -->
    <section class="demo-section" style="background: var(--gradient-primary);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="text-white mb-4">Ready to Experience the Magic?</h2>
                    <p class="text-white mb-4">Go back to the main site to see all effects in action</p>
                    <a href="index.php" class="btn-custom btn-primary-custom shake-on-scroll">
                        <i class="fas fa-home me-2"></i>Back to Main Site
                    </a>
                </div>
            </div>
        </div>
    </section>

    <script src="assets/js/advanced-effects.js"></script>
    <script>
        // Additional demo-specific effects
        document.addEventListener('DOMContentLoaded', function() {
            // Add shake effect to all buttons
            document.querySelectorAll('.shake-on-scroll').forEach(button => {
                button.addEventListener('click', function() {
                    this.style.animation = 'shake 0.5s ease-in-out';
                    setTimeout(() => {
                        this.style.animation = '';
                    }, 500);
                });
            });
            
            // Add glow effect on hover
            document.querySelectorAll('.neon-glow').forEach(element => {
                element.addEventListener('mouseenter', function() {
                    this.style.boxShadow = '0 0 30px rgba(99, 102, 241, 0.8)';
                });
                
                element.addEventListener('mouseleave', function() {
                    this.style.boxShadow = '';
                });
            });
        });
    </script>
</body>
</html> 