<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventPro - Modern Event Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-light: #818cf8;
            --primary-dark: #4338ca;
            --secondary: #06b6d4;
            --accent: #f59e0b;
            --success: #10b981;
            --danger: #ef4444;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --text: #f1f5f9;
            --text-muted: #94a3b8;
            --bg-glass: rgba(15, 23, 42, 0.8);
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-accent: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --shadow-lg: 0 35px 60px -12px rgba(0, 0, 0, 0.35);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0c0c0c 0%, #1a0b2e 50%, #16213e 100%);
            color: var(--text);
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Cursor Effects */
        .cursor {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(99, 102, 241, 0.8);
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            transition: transform 0.1s ease;
        }

        .cursor-follower {
            width: 40px;
            height: 40px;
            border: 2px solid rgba(99, 102, 241, 0.3);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 9998;
            transition: all 0.3s ease;
        }

        /* Animated Background */
        .bg-animated {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .bg-animated::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(99, 102, 241, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(6, 182, 212, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(245, 158, 11, 0.1) 0%, transparent 50%);
            animation: morphBg 20s ease-in-out infinite alternate;
        }

        @keyframes morphBg {
            0% { transform: scale(1) rotate(0deg); }
            100% { transform: scale(1.1) rotate(5deg); }
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 1rem 2rem;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .logo {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            0% { filter: brightness(1); }
            100% { filter: brightness(1.2); }
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }

        .nav-link {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            position: relative;
            transition: all 0.3s ease;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gradient-primary);
            transition: width 0.3s ease;
        }

        .nav-link:hover {
            color: var(--text);
            transform: translateY(-2px);
        }

        .nav-link:hover::before {
            width: 100%;
        }

        /* Hero Section */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, 
                rgba(99, 102, 241, 0.1) 0%, 
                rgba(6, 182, 212, 0.1) 50%, 
                rgba(245, 158, 11, 0.1) 100%);
            animation: heroGlow 8s ease-in-out infinite alternate;
        }

        @keyframes heroGlow {
            0% { transform: scale(1) rotate(0deg); opacity: 0.3; }
            100% { transform: scale(1.05) rotate(2deg); opacity: 0.6; }
        }

        .hero-content {
            text-align: center;
            max-width: 800px;
            z-index: 2;
            position: relative;
            padding: 2rem;
        }

        .hero-subtitle {
            font-size: 1rem;
            color: var(--primary-light);
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 600;
            margin-bottom: 1rem;
            animation: slideUp 0.8s ease 0.2s both;
        }

        .hero-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(2.5rem, 6vw, 6rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #a78bfa 50%, #06b6d4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: slideUp 0.8s ease 0.4s both;
        }

        .hero-description {
            font-size: 1.25rem;
            color: var(--text-muted);
            margin-bottom: 3rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            animation: slideUp 0.8s ease 0.6s both;
        }

        .hero-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: slideUp 0.8s ease 0.8s both;
        }

        @keyframes slideUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.4);
            color: white;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text);
            border: 2px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
            color: var(--text);
        }

        /* Floating Elements */
        .floating-element {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(99, 102, 241, 0.1), rgba(6, 182, 212, 0.1));
            backdrop-filter: blur(10px);
            animation: float 6s ease-in-out infinite;
        }

        .floating-element:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: -2s;
        }

        .floating-element:nth-child(2) {
            width: 60px;
            height: 60px;
            top: 60%;
            right: 15%;
            animation-delay: -4s;
        }

        .floating-element:nth-child(3) {
            width: 100px;
            height: 100px;
            bottom: 20%;
            left: 20%;
            animation-delay: -1s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(120deg); }
            66% { transform: translateY(20px) rotate(240deg); }
        }

        /* Cards */
        .card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--gradient-primary);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
            border-color: rgba(99, 102, 241, 0.3);
        }

        .card:hover::before {
            transform: scaleX(1);
        }

        /* Services Section */
        .services {
            padding: 8rem 2rem;
            position: relative;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 5rem;
        }

        .section-subtitle {
            font-size: 0.9rem;
            color: var(--primary-light);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .section-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(2rem, 4vw, 3.5rem);
            font-weight: 700;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #ffffff 0%, #a78bfa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-description {
            font-size: 1.1rem;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .service-card {
            position: relative;
            padding: 3rem 2rem;
            text-align: center;
        }

        .service-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 2rem;
            background: var(--gradient-primary);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .service-icon::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.3s ease;
        }

        .service-card:hover .service-icon::before {
            width: 100px;
            height: 100px;
        }

        .service-card:hover .service-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .service-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text);
        }

        .service-description {
            color: var(--text-muted);
            margin-bottom: 2rem;
        }

        /* About Section */
        .about {
            padding: 8rem 2rem;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(6, 182, 212, 0.05) 100%);
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .about-text {
            animation: slideInLeft 0.8s ease;
        }

        .about-visual {
            position: relative;
            animation: slideInRight 0.8s ease;
        }

        .about-image {
            width: 100%;
            height: 400px;
            background: var(--gradient-secondary);
            border-radius: 20px;
            position: relative;
            overflow: hidden;
        }

        .about-stats {
            position: absolute;
            bottom: -30px;
            right: -30px;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            min-width: 200px;
        }

        .stat-item {
            text-align: center;
            margin-bottom: 1rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-light);
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        /* Team Section */
        .team {
            padding: 8rem 2rem;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .team-card {
            text-align: center;
            position: relative;
        }

        .team-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: var(--gradient-accent);
            margin: 0 auto 1.5rem;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .team-card:hover .team-avatar {
            transform: scale(1.1);
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.3);
        }

        .team-name {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .team-role {
            color: var(--primary-light);
            margin-bottom: 1rem;
        }

        .team-social {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(99, 102, 241, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
        }

        /* Footer */
        .footer {
            background: rgba(15, 23, 42, 0.95);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 4rem 2rem 2rem;
            text-align: center;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-logo {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .footer-link {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: var(--primary-light);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 2rem;
            color: var(--text-muted);
        }

        /* Scroll Animations */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }

        .animate-on-scroll.animated {
            opacity: 1;
            transform: translateY(0);
        }

        @keyframes slideInLeft {
            0% {
                opacity: 0;
                transform: translateX(-50px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            0% {
                opacity: 0;
                transform: translateX(50px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            .hero-content {
                padding: 1rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .about-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .about-stats {
                position: relative;
                bottom: 0;
                right: 0;
                margin-top: 2rem;
            }
            
            .services,
            .team,
            .about {
                padding: 4rem 1rem;
            }
        }

        /* Loading Animation */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--dark);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            transition: opacity 0.5s ease;
        }

        .loader-content {
            text-align: center;
        }

        .loader-spinner {
            width: 60px;
            height: 60px;
            border: 3px solid rgba(99, 102, 241, 0.1);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 2rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .loader-text {
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Particles Background */
        .particles-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
        }
    </style>
</head>
<body>
    <!-- Page Loader -->
    <div class="page-loader" id="pageLoader">
        <div class="loader-content">
            <div class="loader-spinner"></div>
            <div class="loader-text">Loading Experience...</div>
        </div>
    </div>

    <!-- Custom Cursor -->
    <div class="cursor" id="cursor"></div>
    <div class="cursor-follower" id="cursorFollower"></div>

    <!-- Animated Background -->
    <div class="bg-animated"></div>

    <!-- Particles Background -->
    <canvas class="particles-bg" id="particlesCanvas"></canvas>

    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="nav-container">
            <div class="logo">EventPro</div>
            <ul class="nav-links">
                <li><a href="#home" class="nav-link">Home</a></li>
                <li><a href="#services" class="nav-link">Services</a></li>
                <li><a href="#about" class="nav-link">About</a></li>
                <li><a href="#team" class="nav-link">Team</a></li>
                <li><a href="login.php" class="nav-link">Login</a></li>
                <li><a href="user/register.php" class="nav-link">Sign Up</a></li>
                <li><a href="user/register_planner.php" class="nav-link">Planner Sign Up</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-bg"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        
        <div class="hero-content">
            <div class="hero-subtitle">Premium Event Management</div>
            <h1 class="hero-title">Create Unforgettable Experiences</h1>
            <p class="hero-description">
                Transform your vision into reality with our cutting-edge event planning solutions. 
                From intimate gatherings to spectacular celebrations, we craft moments that matter.
            </p>
            <div class="hero-buttons">
                <a href="#services" class="btn btn-primary">
                    <i class="fas fa-rocket"></i>
                    Get Started
                </a>
                <a href="#about" class="btn btn-secondary">
                    <i class="fas fa-play"></i>
                    Watch Demo
                </a>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <div class="section-subtitle">Our Services</div>
                <h2 class="section-title">Comprehensive Event Solutions</h2>
                <p class="section-description">
                    From concept to execution, we provide end-to-end event management services 
                    that exceed expectations and create lasting memories.
                </p>
            </div>
            
            <div class="services-grid">
                <div class="card service-card animate-on-scroll">
                    <div class="service-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 class="service-title">Event Planning</h3>
                    <p class="service-description">
                        Comprehensive planning and coordination services tailored to your unique vision and requirements.
                    </p>
                    <a href="#" class="btn btn-secondary">Learn More</a>
                </div>
                
                <div class="card service-card animate-on-scroll">
                    <div class="service-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="service-title">Wedding Events</h3>
                    <p class="service-description">
                        Make your special day perfect with our dedicated wedding planning and coordination services.
                    </p>
                    <a href="#" class="btn btn-secondary">Learn More</a>
                </div>
                
                <div class="card service-card animate-on-scroll">
                    <div class="service-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3 class="service-title">Corporate Events</h3>
                    <p class="service-description">
                        Professional corporate event planning for conferences, meetings, and business celebrations.
                    </p>
                    <a href="#" class="btn btn-secondary">Learn More</a>
                </div>
                
                <div class="card service-card animate-on-scroll">
                    <div class="service-icon">
                        <i class="fas fa-camera"></i>
                    </div>
                    <h3 class="service-title">Photography</h3>
                    <p class="service-description">
                        Professional photography and videography services to capture your precious moments.
                    </p>
                    <a href="#" class="btn btn-secondary">Learn More</a>
                </div>
                
                <div class="card service-card animate-on-scroll">
                    <div class="service-icon">
                        <i class="fas fa-music"></i>
                    </div>
                    <h3 class="service-title">Entertainment</h3>
                    <p class="service-description">
                        Live bands, DJs, and entertainment services to keep your guests engaged and excited.
                    </p>
                    <a href="#" class="btn btn-secondary">Learn More</a>
                </div>
                
                <div class="card service-card animate-on-scroll">
                    <div class="service-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="service-title">Security</h3>
                    <p class="service-description">
                        Professional security and crowd management services for safe and secure events.
                    </p>
                    <a href="#" class="btn btn-secondary">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <div class="section-subtitle">About EventPro</div>
                    <h2 class="section-title">Leading Event Management Since 2014</h2>
                    <p class="section-description">
                        With over a decade of experience in creating extraordinary events, we've established 
                        ourselves as the premier choice for event planning and coordination. Our team of dedicated 
                        professionals brings creativity, expertise, and passion to every project.
                    </p>
                    <div style="margin-top: 2rem;">
                        <a href="#contact" class="btn btn-primary">
                            <i class="fas fa-arrow-right"></i>
                            Start Your Project
                        </a>
                    </div>
                </div>
                
                <div class="about-visual">
                    <div class="about-image"></div>
                    <div class="about-stats">
                        <div class="stat-item">
                            <span class="stat-number">500+</span>
                            <span class="stat-label">Events Completed</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">10+</span>
                            <span class="stat-label">Years Experience</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">98%</span>
                            <span class="stat-label">Client Satisfaction</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team" id="team">
        <div class="container">
            <div class="section-header animate-on-scroll">
                <div class="section-subtitle">Our Team</div>
                <h2 class="section-title">Meet Our Experts</h2>
                <p class="section-description">
                    Our talented team of event professionals brings years of experience and creative vision 
                    to make your events truly exceptional.
                </p>
            </div>
            
            <div class="team-grid">
                <div class="card team-card animate-on-scroll">
                    <div class="team-avatar"></div>
                    <h3 class="team-name">Sarah Johnson</h3>
                    <p class="team-role">Lead Event Planner</p>
                    <p style="color: var(--text-muted); margin-bottom: 1rem;">
                        10+ years of experience in luxury event planning and coordination.
                    </p>
                    <div class="team-social">
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
                <div class="card team-card animate-on-scroll">
                    <div class="team-avatar"></div>
                    <h3 class="team-name">Michael Chen</h3>
                    <p class="team-role">Creative Director</p>
                    <p style="color: var(--text-muted); margin-bottom: 1rem;">
                        Award-winning designer specializing in event aesthetics and branding.
                    </p>
                    <div class="team-social">
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-behance"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-dribbble"></i></a>
                    </div>
                </div>
                
                <div class="card team-card animate-on-scroll">
                    <div class="team-avatar"></div>
                    <h3 class="team-name">Emily Rodriguez</h3>
                    <p class="team-role">Operations Manager</p>
                    <p style="color: var(--text-muted); margin-bottom: 1rem;">
                        Expert in logistics coordination and vendor management.
                    </p>
                    <div class="team-social">
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>
                
                <div class="card team-card animate-on-scroll">
                    <div class="team-avatar"></div>
                    <h3 class="team-name">David Wilson</h3>
                    <p class="team-role">Technical Specialist</p>
                    <p style="color: var(--text-muted); margin-bottom: 1rem;">
                        Audio-visual expert and technology integration specialist.
                    </p>
                    <div class="team-social">
                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-github"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">EventPro</div>
            <div class="footer-links">
                <a href="#home" class="footer-link">Home</a>
                <a href="#services" class="footer-link">Services</a>
                <a href="#about" class="footer-link">About</a>
                <a href="#team" class="footer-link">Team</a>
                <a href="#contact" class="footer-link">Contact</a>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 EventPro. All rights reserved. | Crafted with passion for extraordinary events.</p>
            </div>
        </div>
    </footer>

    <script>
        // Page Loader
        window.addEventListener('load', () => {
            const loader = document.getElementById('pageLoader');
            setTimeout(() => {
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 500);
            }, 1500);
        });

        // Custom Cursor
        const cursor = document.getElementById('cursor');
        const cursorFollower = document.getElementById('cursorFollower');

        document.addEventListener('mousemove', (e) => {
            cursor.style.left = e.clientX + 'px';
            cursor.style.top = e.clientY + 'px';
            
            setTimeout(() => {
                cursorFollower.style.left = e.clientX + 'px';
                cursorFollower.style.top = e.clientY + 'px';
            }, 100);
        });

        // Interactive elements cursor effects
        const interactiveElements = document.querySelectorAll('a, button, .card, .service-card');
        interactiveElements.forEach(el => {
            el.addEventListener('mouseenter', () => {
                cursor.style.transform = 'scale(1.5)';
                cursorFollower.style.transform = 'scale(1.5)';
            });
            
            el.addEventListener('mouseleave', () => {
                cursor.style.transform = 'scale(1)';
                cursorFollower.style.transform = 'scale(1)';
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(15, 23, 42, 0.98)';
                navbar.style.backdropFilter = 'blur(20px)';
            } else {
                navbar.style.background = 'rgba(15, 23, 42, 0.95)';
                navbar.style.backdropFilter = 'blur(20px)';
            }
        });

        // Smooth scrolling for navigation links
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

        // Scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });

        // Parallax effect for floating elements
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelectorAll('.floating-element');
            const speed = 0.5;

            parallax.forEach((element, index) => {
                const yPos = -(scrolled * speed * (index + 1));
                element.style.transform = `translate3d(0, ${yPos}px, 0)`;
            });
        });

        // Particles animation
        const canvas = document.getElementById('particlesCanvas');
        const ctx = canvas.getContext('2d');

        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }

        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 2 + 1;
                this.speedX = Math.random() * 0.5 - 0.25;
                this.speedY = Math.random() * 0.5 - 0.25;
                this.opacity = Math.random() * 0.5 + 0.2;
            }

            update() {
                this.x += this.speedX;
                this.y += this.speedY;

                if (this.x > canvas.width) this.x = 0;
                if (this.x < 0) this.x = canvas.width;
                if (this.y > canvas.height) this.y = 0;
                if (this.y < 0) this.y = canvas.height;
            }

            draw() {
                ctx.fillStyle = `rgba(99, 102, 241, ${this.opacity})`;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        const particles = [];
        const particleCount = 50;

        for (let i = 0; i < particleCount; i++) {
            particles.push(new Particle());
        }

        function animateParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            for (let i = 0; i < particles.length; i++) {
                particles[i].update();
                particles[i].draw();

                for (let j = i; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < 100) {
                        ctx.strokeStyle = `rgba(99, 102, 241, ${0.1 * (1 - distance / 100)})`;
                        ctx.lineWidth = 1;
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.stroke();
                    }
                }
            }

            requestAnimationFrame(animateParticles);
        }

        // Initialize particles
        resizeCanvas();
        animateParticles();

        window.addEventListener('resize', resizeCanvas);

        // Form submission animation
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            button.disabled = true;
            
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-check"></i> Message Sent!';
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                    this.reset();
                }, 2000);
            }, 2000);
        });

        // Service card hover effects
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-15px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(-10px) scale(1)';
            });
        });

        // Add typing animation to hero title
        const heroTitle = document.querySelector('.hero-title');
        const text = heroTitle.textContent;
        heroTitle.textContent = '';

        let i = 0;
        function typeWriter() {
            if (i < text.length) {
                heroTitle.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            }
        }

        // Start typing animation after page load
        setTimeout(typeWriter, 2000);

        // Add floating animation to stats
        document.querySelectorAll('.stat-number').forEach((stat, index) => {
            const finalValue = parseInt(stat.textContent);
            let currentValue = 0;
            const increment = finalValue / 50;
            
            function updateStats() {
                if (currentValue < finalValue) {
                    currentValue += increment;
                    if (stat.textContent.includes('%')) {
                        stat.textContent = Math.round(currentValue) + '%';
                    } else if (stat.textContent.includes('+')) {
                        stat.textContent = Math.round(currentValue) + '+';
                    } else {
                        stat.textContent = Math.round(currentValue);
                    }
                    requestAnimationFrame(updateStats);
                }
            }
            
            // Start animation when element comes into view
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        setTimeout(() => updateStats(), index * 200);
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            observer.observe(stat);
        });
    </script>
</body>
</html>