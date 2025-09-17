<?php include 'header.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link href="assets/css/custom-style.css" rel="stylesheet">

<!-- Interactive 3D Section Styles -->
<style>
    /* Interactive 3D Section - From shape.html */
    .interactive-3d-section {
        background-color: #000000;
        color: white;
        font-family: 'Courier New', monospace;
        position: relative;
        overflow: hidden;
        min-height: 100vh;
    }
    
    .interactive-3d-section canvas {
        display: block;
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }
    
    .interactive-3d-section #ui {
        position: absolute;
        top: 15px;
        width: 100%;
        text-align: center;
        z-index: 100;
        pointer-events: none;
    }
    
    .interactive-3d-section #info {
        font-size: 14px;
        padding: 10px 18px;
        background-color: rgba(25, 30, 50, 0.35);
        border-radius: 10px;
        display: inline-block;
        text-shadow: 0 0 5px rgba(0, 128, 255, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        box-shadow: inset 0 0 10px rgba(255, 255, 255, 0.05);
    }
    
    .interactive-3d-section #loading {
        position: fixed;
        width: 100%;
        height: 100%;
        background: #000;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        transition: opacity 0.6s ease-out;
    }
    
    .interactive-3d-section #loading span {
        font-size: 24px;
        letter-spacing: 2px;
        margin-bottom: 15px;
    }
    
    .interactive-3d-section #progress-container {
        width: 60%;
        max-width: 300px;
        height: 6px;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 3px;
        overflow: hidden;
    }
    
    .interactive-3d-section #progress {
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #00a2ff, #00ffea);
        transition: width 0.3s ease;
        border-radius: 3px;
    }
    
    .interactive-3d-section #controls {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 100;
        text-align: center;
        pointer-events: all;
        background-color: rgba(25, 30, 50, 0.4);
        padding: 15px 25px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    
    .interactive-3d-section button {
        background: rgba(0, 80, 180, 0.7);
        color: white;
        border: 1px solid rgba(0, 180, 255, 0.6);
        border-radius: 6px;
        padding: 8px 15px;
        margin: 0 8px;
        cursor: pointer;
        font-family: 'Courier New', monospace;
        font-size: 14px;
        transition: all 0.25s ease;
    }
    
    .interactive-3d-section button:hover {
        background: rgba(0, 110, 220, 0.9);
        border-color: rgba(0, 210, 255, 0.9);
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(0, 150, 255, 0.3);
    }
    
    .interactive-3d-section #color-picker {
        margin-top: 15px;
        display: flex;
        justify-content: center;
        gap: 12px;
    }
    
    .interactive-3d-section .color-option {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.2);
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: inset 0 0 4px rgba(0,0,0,0.4);
    }
    
    .interactive-3d-section .color-option:hover {
        transform: scale(1.15);
        border-color: rgba(255, 255, 255, 0.7);
    }
    
    .interactive-3d-section .color-option.active {
        transform: scale(1.18);
        border-color: white;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.7);
    }
    
    .interactive-3d-section .section-content {
        position: relative;
        z-index: 10;
        padding: 4rem 0;
        text-align: center;
    }
    
    .interactive-3d-section .section-title {
        color: white;
        margin-top: 10rem;
        font-family: 'Inter', sans-serif;
        font-size: 3rem;
        font-weight: 800;
        text-align: center;
        margin-bottom: 1rem;
        text-shadow: 0 0 20px rgba(0, 128, 255, 0.5);
    }

    .interactive-3d-section .section-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1.2rem;
        margin-bottom: 2rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .interactive-3d-section .cta-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 2rem;
    }

    .interactive-3d-section .btn-cta {
        padding: 12px 24px;
        font-size: 1.1rem;
        font-weight: 600;
        text-decoration: none;
        border-radius: 50px;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        pointer-events: all;
    }

    .interactive-3d-section .btn-primary-cta {
        background: linear-gradient(45deg, #00a2ff, #00ffea);
        color: white;
        border: none;
    }

    .interactive-3d-section .btn-primary-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 162, 255, 0.3);
        color: white;
    }

    .interactive-3d-section .btn-secondary-cta {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border: 2px solid white;
    }

    .interactive-3d-section .btn-secondary-cta:hover {
        background: white;
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(255, 255, 255, 0.2);
    }
</style>



<!-- Interactive 3D Hero Section -->
<section class="interactive-3d-section" id="home">
    <div id="loading">
        <span>Initializing Particles...</span>
        <div id="progress-container">
            <div id="progress"></div>
        </div>
    </div>

    <div id="ui">
        <div id="info"> </div>
    </div>

    <div class="section-content">
        <div class="container">
            <h1 class="section-title">Create Unforgettable <span class="text-gradient">Events</span></h1>
            <p class="section-subtitle">
                Connect with professional event planners and bring your vision to life. 
                From intimate gatherings to grand celebrations, we make every event extraordinary.
            </p>
            <div class="cta-buttons">
                <a href="user/register.php" class="btn-cta btn-primary-cta">
                    <i class="fas fa-user-plus"></i>Get Started
                </a>
                <a href="#about" class="btn-cta btn-secondary-cta">
                    <i class="fas fa-play"></i>Learn More
                </a>
            </div>
        </div>
    </div>

    <div id="controls">
        <button id="shape-btn">Change Shape</button>
        <div id="color-picker">
            <div class="color-option active" data-scheme="fire" style="background: linear-gradient(to bottom right, #ff4500, #ffcc00)"></div>
            <div class="color-option" data-scheme="neon" style="background: linear-gradient(to bottom right, #ff00ff, #00ffff)"></div>
            <div class="color-option" data-scheme="nature" style="background: linear-gradient(to bottom right, #00ff00, #66ffcc)"></div>
            <div class="color-option" data-scheme="rainbow" style="background: linear-gradient(to right, red, orange, yellow, green, blue, indigo, violet)"></div>
        </div>
    </div>

    <canvas id="webglCanvas"></canvas>
</section>

        
        <!-- Get In Touch Start -->
        <!-- <div class="container-fluid py-5 wow fadeInUp" data-wow-delay=".3s">
            <div class="container py-5">
                <div class="bg-light px-4 py-5 rounded">
                    <div class="text-center">
                        <h1 class="display-5 mb-5">Find Your Event Services</h1>
                    </div>
                    <form class="text-center mb-4" action="#">
                        <div class="row g-4">
                            <div class="col-xl-10 col-lg-12">
                                <div class="row g-4">
                                    <div class="col-md-6 col-xl-3">
                                        <select class="form-select p-3 border-0">
                                            <option value="Type Of Service" class="">Type Of Service</option>
                                            <option value="Pest Control-2">Washing </option>
                                            <option value="Pest Control-3">cleaning</option>
                                           
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <input type="text" class="form-control p-3 border-0" placeholder="Name">
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <input type="text" class="form-control p-3 border-0" placeholder="Phone">
                                    </div>
                                    <div class="col-md-6 col-xl-3">
                                        <input type="email" class="form-control p-3 border-0" placeholder="Email">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-2 col-lg-12">
                                <input type="button" class="btn btn-primary w-100 p-3 border-0" value="GET STARTED">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div> -->
        <!-- Get In Touch End -->

<!-- About Section -->
<section class="section" id="about">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="position-relative">
                    <div class="bg-gradient-primary rounded-custom p-5 shadow-custom">
                        <div class="position-relative">
                            <div class="bg-white rounded-custom p-4 shadow-custom">
                                <div class="row text-center">
                                    <div class="col-4 mb-3">
                                        <div class="card-icon mx-auto">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <h6 class="fw-bold">Event Planning</h6>
                                    </div>
                                    <div class="col-4 mb-3">
                                        <div class="card-icon mx-auto">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <h6 class="fw-bold">Coordination</h6>
                                    </div>
                                    <div class="col-4 mb-3">
                                        <div class="card-icon mx-auto">
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <h6 class="fw-bold">Premium Services</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="position-absolute top-0 start-0 translate-middle">
                        <div class="bg-gradient-secondary rounded-circle p-3 shadow-custom">
                            <i class="fas fa-trophy fa-2x text-white"></i>
                        </div>
                    </div>
                    <div class="position-absolute bottom-0 end-0 translate-middle">
                        <div class="bg-gradient-accent rounded-circle p-3 shadow-custom">
                            <i class="fas fa-award fa-2x text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ps-lg-5">
                    <h6 class="text-gradient fw-bold mb-3">ABOUT EVENTPRO</h6>
                    <h2 class="section-title text-start mb-4">
                        Leading Event Management Solutions Since 2014
                    </h2>
                    <p class="text-secondary mb-4">
                        At EventPro, we provide comprehensive event management solutions designed to create unforgettable experiences. 
                        With a decade of experience in the industry, we offer a range of services including event planning, 
                        coordination, and execution to ensure your events are memorable and successful.
                    </p>
                    <div class="row mb-4">
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-gradient-primary rounded-circle p-2 me-3">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                                <span class="fw-semibold">Professional Planners</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-gradient-primary rounded-circle p-2 me-3">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                                <span class="fw-semibold">24/7 Support</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-gradient-primary rounded-circle p-2 me-3">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                                <span class="fw-semibold">Custom Solutions</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-gradient-primary rounded-circle p-2 me-3">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                                <span class="fw-semibold">Quality Guarantee</span>
                            </div>
                        </div>
                    </div>
                    <a href="#services" class="btn-custom btn-primary-custom">
                        <i class="fas fa-arrow-right me-2"></i>Explore Services
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Services Section -->
<section class="section" id="services">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-gradient fw-bold mb-3">OUR SERVICES</h6>
            <h2 class="section-title">Event Management Services</h2>
            <p class="section-subtitle">Comprehensive solutions for every type of event</p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="custom-card text-center h-100">
               
                <div class="card-image mb-3">
                        <img src="assets/img/entertainment.jpg" alt="Wedding Events" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;">
                    </div>
                    <h4 class="mb-3">Event Planning</h4>
                    <p class="text-muted mb-4">Comprehensive event planning and coordination services for all occasions.</p>
                    <a href="#" class="btn-custom btn-primary-custom">Learn More</a>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="custom-card text-center h-100">
                    <div class="card-image mb-3">
                        <img src="assets/img/new/rings%20png.png" alt="Wedding Events" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;">
                    </div>
                    <h4 class="mb-3">Wedding Events</h4>
                    <p class="text-muted mb-4">Make your special day unforgettable with our wedding planning services.</p>
                    <a href="#" class="btn-custom btn-primary-custom">Learn More</a>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="custom-card text-center h-100">
                    <div class="card-image mb-3">
                        <img src="assets/img/new/scrren%20img.png" alt="Corporate Events" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;">
                    </div>
                    <h4 class="mb-3">Corporate Events</h4>
                    <p class="text-muted mb-4">Professional corporate event planning for conferences and meetings.</p>
                    <a href="#" class="btn-custom btn-primary-custom">Learn More</a>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="custom-card text-center h-100">
                    <div class="card-image mb-3">
                        <img src="assets/img/photography.jpg" alt="Photography & Video" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;">
                    </div>
                    <h4 class="mb-3">Photography & Video</h4>
                    <p class="text-muted mb-4">Professional photography and videography services for your events.</p>
                    <a href="#" class="btn-custom btn-primary-custom">Learn More</a>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="custom-card text-center h-100">
                    <div class="card-image mb-3">
                        <img src="assets/img/new/baloons%20png%20.png" alt="Entertainment" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;">
                    </div>
                    <h4 class="mb-3">Entertainment</h4>
                    <p class="text-muted mb-4">Live bands, DJs, and entertainment services for your events.</p>
                    <a href="#" class="btn-custom btn-primary-custom">Learn More</a>
                </div>
            </div>
            
          
            
            <div class="col-lg-3 col-md-6">
                <div class="custom-card text-center h-100">
                    <div class="card-image mb-3">
                        <img src="assets/img/security.jpg" alt="Security Services" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover;">
                    </div>
                    <h4 class="mb-3">Security Services</h4>
                    <p class="text-muted mb-4">Professional security and crowd management for your events.</p>
                    <a href="#" class="btn-custom btn-primary-custom">Learn More</a>
                </div>
            </div>
        </div>
        
      
    </div>
</section>




<!-- Blog Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mb-5 wow fadeInUp" data-wow-delay=".3s">
            <h5 class="mb-2 px-3 py-1 text-dark rounded-pill d-inline-block border border-2 border-primary">Our Blog</h5>
            <h1 class="display-5">Latest Updates & Insights</h1>
        </div>
        <div class="owl-carousel blog-carousel wow fadeInUp" data-wow-delay=".5s">
            <div class="blog-item">
            <img src="assets/img/blog-2.jpg" class="img-fluid w-100 rounded-top" alt="Wedding Planning Tips">
                <div class="rounded-bottom bg-light">
                    <div class="d-flex justify-content-between p-4 pb-2">
                        <span class="pe-2 text-dark"><i class="fa fa-user me-2"></i>By Admin</span>
                        <span class="text-dark"><i class="fas fa-calendar-alt me-2"></i>10 Feb, 2024</span>
                    </div>
                    <div class="px-4 pb-0">
                        <h4>Top Event Planning Trends for 2024</h4>
                        <p>Explore the latest trends and innovations transforming event planning in 2024.</p>
                    </div>
                    <div class="p-4 py-2 d-flex justify-content-between bg-primary rounded-bottom blog-btn">
                        <a href="#" type="button" class="btn btn-primary border-0">Learn More</a>
                        <a href="#" class="my-auto text-dark"><i class="fa fa-comments me-2"></i>15 Comments</a>
                    </div>
                </div>
            </div>
            <div class="blog-item">
                <img src="assets/img/blog-2.jpg" class="img-fluid w-100 rounded-top" alt="Wedding Planning Tips">
                <div class="rounded-bottom bg-light">
                    <div class="d-flex justify-content-between p-4 pb-2">
                        <span class="pe-2 text-dark"><i class="fa fa-user me-2"></i>By Admin</span>
                        <span class="text-dark"><i class="fas fa-calendar-alt me-2"></i>15 Jan, 2024</span>
                    </div>
                    <div class="px-4 pb-0">
                        <h4>Essential Wedding Planning Checklist</h4>
                        <p>Complete checklist to ensure your wedding planning goes smoothly and stress-free.</p>
                    </div>
                    <div class="p-4 py-2 d-flex justify-content-between bg-primary rounded-bottom blog-btn">
                        <a href="#" type="button" class="btn btn-primary border-0">Learn More</a>
                        <a href="#" class="my-auto text-dark"><i class="fa fa-comments me-2"></i>10 Comments</a>
                    </div>
                </div>
            </div>
            <div class="blog-item">
                <img src="assets/img/blog-3.jpg" class="img-fluid w-100 rounded-top" alt="Corporate Event Strategies">
                <div class="rounded-bottom bg-light">
                    <div class="d-flex justify-content-between p-4 pb-2">
                        <span class="pe-2 text-dark"><i class="fa fa-user me-2"></i>By Admin</span>
                        <span class="text-dark"><i class="fas fa-calendar-alt me-2"></i>22 Dec, 2023</span>
                    </div>
                    <div class="px-4 pb-0">
                        <h4>Successful Corporate Event Strategies</h4>
                        <p>Strategies to create memorable and impactful corporate events that achieve your goals.</p>
                    </div>
                    <div class="p-4 py-2 d-flex justify-content-between bg-primary rounded-bottom blog-btn">
                        <a href="#" type="button" class="btn btn-primary border-0">Learn More</a>
                        <a href="#" class="my-auto text-dark"><i class="fa fa-comments me-2"></i>8 Comments</a>
                    </div>
                </div>
            </div>
            <div class="blog-item">
            <img src="assets/img/blog-3.jpg" class="img-fluid w-100 rounded-top" alt="Corporate Event Strategies">
                <div class="rounded-bottom bg-light">
                    <div class="d-flex justify-content-between p-4 pb-2">
                        <span class="pe-2 text-dark"><i class="fa fa-user me-2"></i>By Admin</span>
                        <span class="text-dark"><i class="fas fa-calendar-alt me-2"></i>05 Dec, 2023</span>
                    </div>
                    <div class="px-4 pb-0">
                        <h4>Latest Event Technology Innovations</h4>
                        <p>Discover the latest technology innovations that can enhance your event experience.</p>
                    </div>
                    <div class="p-4 py-2 d-flex justify-content-between bg-primary rounded-bottom blog-btn">
                        <a href="#" type="button" class="btn btn-primary border-0">Learn More</a>
                        <a href="#" class="my-auto text-dark"><i class="fa fa-comments me-2"></i>12 Comments</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Blog End -->


        <!-- Call To Action Start -->
        <div class="container-fluid py-5 call-to-action wow fadeInUp" data-wow-delay=".3s" style="margin: 6rem 0;">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <img src="assets/img/car (2).jpg" class="img-fluid w-100 rounded-circle p-5" alt="">
                    </div>
                    <div class="col-lg-6 my-auto">
                        <div class="text-start mt-4">
                            <h1 class="pb-4 text-white">Sign Up To Our Newsletter To Get The Latest Offers</h1>
                        </div>
                        <form method="post" action="index.php">
                            <div class="form-group">
                                <div class="d-flex call-btn">
                                    <input type="search" class="form-control py-3 px-4 w-100 border-0 rounded-0 rounded-end rounded-pill" name="search-input" value="" placeholder="Enter Your Email Address" required="Please enter a valid email"/>
                                    <button type="email" value="Search Now!" class="btn btn-primary border-0 rounded-pill rounded rounded-start px-5">Subscribe</button>
                                </div>
                            </div>
                        </form>  
                    </div>
                </div> 
            </div>
        </div>
        <!-- Call To Action End -->

 

        <!-- Team Start -->
<div class="container-fluid py-5">
    <div class="container py-5">
        <div class="text-center mb-5 wow fadeInUp" data-wow-delay=".3s">
            <h5 class="mb-2 px-3 py-1 text-dark rounded-pill d-inline-block border border-2 border-primary">Our Team</h5>
            <h1 class="display-5 w-50 mx-auto">Meet Our Team</h1>
        </div>
        <div class="row g-5">
            <div class="col-xxl-3 col-lg-6 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay=".3s">
                <div class="rounded team-item">
                    <img src="assets/img/team-1.jpg" class="img-fluid w-100 rounded-top border border-bottom-0" alt="Team Member 1">
                    <div class="team-content bg-primary text-dark text-center py-3">
                        <span class="fs-4 fw-bold">John Doe</span>
                        <p class="text-muted mb-0">Event Specialist</p>
                    </div>
                    <div class="team-icon d-flex flex-column">
                        <a href="#" class="btn btn-primary border-0 mb-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-primary border-0 mb-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-primary border-0 mb-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="btn btn-primary border-0"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-lg-6 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay=".5s">
                <div class="rounded team-item">
                    <img src="assets/img/team-1.jpg" class="img-fluid w-100 rounded-top border border-bottom-0" alt="Team Member 2">
                    <div class="team-content bg-primary text-dark text-center py-3">
                        <span class="fs-4 fw-bold">Jane Smith</span>
                        <p class="text-muted mb-0">Event Manager</p>
                    </div>
                    <div class="team-icon d-flex flex-column">
                        <a href="#" class="btn btn-primary border-0 mb-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-primary border-0 mb-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-primary border-0 mb-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="btn btn-primary border-0"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-lg-6 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay=".7s">
                <div class="rounded team-item">
                    <img src="assets/img/team-3.jpg" class="img-fluid w-100 rounded-top border border-bottom-0" alt="Team Member 3">
                    <div class="team-content bg-primary text-dark text-center py-3">
                        <span class="fs-4 fw-bold">Michael Brown</span>
                        <p class="text-muted mb-0">Coordination Expert</p>
                    </div>
                    <div class="team-icon d-flex flex-column">
                        <a href="#" class="btn btn-primary border-0 mb-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-primary border-0 mb-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-primary border-0 mb-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="btn btn-primary border-0"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-lg-6 col-md-6 col-sm-12 wow fadeInUp" data-wow-delay=".9s">
                <div class="rounded team-item">
                    <img src="assets/img/team-4.jpg" class="img-fluid w-100 rounded-top border border-bottom-0" alt="Team Member 4">
                    <div class="team-content bg-primary text-dark text-center py-3">
                        <span class="fs-4 fw-bold">Emily Davis</span>
                        <p class="text-muted mb-0">Customer Relations</p>
                    </div>
                    <div class="team-icon d-flex flex-column">
                        <a href="#" class="btn btn-primary border-0 mb-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-primary border-0 mb-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-primary border-0 mb-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="btn btn-primary border-0"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Team End -->





















<?php include 'footer.php'; ?>

<!-- Back to Top Button -->
<a href="#" id="backToTopBtn" class="btn-custom btn-primary-custom rounded-circle position-fixed bottom-0 end-0 m-4" style="width: 50px; height: 50px; z-index: 1100; display: none; align-items: center; justify-content: center;">
    <i class="fas fa-arrow-up"></i>
    <span class="visually-hidden">Back to top</span>
    </a>

<!-- Import Map for Three.js modules -->
<script type="importmap">
    {
        "imports": {
            "three": "https://cdn.jsdelivr.net/npm/three@0.163.0/build/three.module.js",
            "three/addons/": "https://cdn.jsdelivr.net/npm/three@0.163.0/examples/jsm/",
            "animejs": "https://cdn.jsdelivr.net/npm/animejs@3.2.2/lib/anime.es.js",
            "simplex-noise": "https://cdn.skypack.dev/simplex-noise@4.0.1"
        }
    }
</script>

<!-- Three.js Particle System -->
<script type="module">
    import * as THREE from 'three';
    import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
    import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
    import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
    import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';
    import anime from 'animejs';
    import { createNoise3D, createNoise4D } from 'simplex-noise';

    let scene, camera, renderer, controls, clock;
    let composer, bloomPass;
    let particlesGeometry, particlesMaterial, particleSystem;
    let currentPositions, sourcePositions, targetPositions, swarmPositions;
    let particleSizes, particleOpacities, particleEffectStrengths;
    let noise3D, noise4D;
    let morphTimeline = null;
    let isInitialized = false;
    let isMorphing = false;

    const CONFIG = {
        particleCount: 15000,
        shapeSize: 14,
        swarmDistanceFactor: 1.5,
        swirlFactor: 4.0,
        noiseFrequency: 0.1,
        noiseTimeScale: 0.04,
        noiseMaxStrength: 2.8,
        colorScheme: 'fire',
        morphDuration: 4000,
        particleSizeRange: [0.08, 0.25],
        starCount: 18000,
        bloomStrength: 1.3,
        bloomRadius: 0.5,
        bloomThreshold: 0.05,
        idleFlowStrength: 0.25,
        idleFlowSpeed: 0.08,
        idleRotationSpeed: 0.02,
        morphSizeFactor: 0.5,
        morphBrightnessFactor: 0.6
    };

    const SHAPES = [
        { name: 'Sphere', generator: generateSphere },
        { name: 'Cube', generator: generateCube },
        { name: 'Pyramid', generator: generatePyramid },
        { name: 'Torus', generator: generateTorus },
        { name: 'Galaxy', generator: generateGalaxy },
        { name: 'Wave', generator: generateWave }
    ];
    let currentShapeIndex = 0;

    const morphState = { progress: 0.0 };

    const COLOR_SCHEMES = {
        fire: { startHue: 0, endHue: 45, saturation: 0.95, lightness: 0.6 },
        neon: { startHue: 300, endHue: 180, saturation: 1.0, lightness: 0.65 },
        nature: { startHue: 90, endHue: 160, saturation: 0.85, lightness: 0.55 },
        rainbow: { startHue: 0, endHue: 360, saturation: 0.9, lightness: 0.6 }
    };

    const tempVec = new THREE.Vector3();
    const sourceVec = new THREE.Vector3();
    const targetVec = new THREE.Vector3();
    const swarmVec = new THREE.Vector3();
    const noiseOffset = new THREE.Vector3();
    const flowVec = new THREE.Vector3();
    const bezPos = new THREE.Vector3();
    const swirlAxis = new THREE.Vector3();
    const currentVec = new THREE.Vector3();

    // Shape generators
    function generateSphere(count, size) {
        const points = new Float32Array(count * 3);
        const phi = Math.PI * (Math.sqrt(5) - 1);
        for (let i = 0; i < count; i++) {
            const y = 1 - (i / (count - 1)) * 2;
            const radius = Math.sqrt(1 - y * y);
            const theta = phi * i;
            const x = Math.cos(theta) * radius;
            const z = Math.sin(theta) * radius;
            points[i * 3] = x * size;
            points[i * 3 + 1] = y * size;
            points[i * 3 + 2] = z * size;
        }
        return points;
    }

    function generateCube(count, size) {
        const points = new Float32Array(count * 3);
        const halfSize = size / 2;
        for (let i = 0; i < count; i++) {
            const face = Math.floor(Math.random() * 6);
            const u = Math.random() * size - halfSize;
            const v = Math.random() * size - halfSize;
            switch (face) {
                case 0: points.set([halfSize, u, v], i * 3); break;
                case 1: points.set([-halfSize, u, v], i * 3); break;
                case 2: points.set([u, halfSize, v], i * 3); break;
                case 3: points.set([u, -halfSize, v], i * 3); break;
                case 4: points.set([u, v, halfSize], i * 3); break;
                case 5: points.set([u, v, -halfSize], i * 3); break;
            }
        }
        return points;
    }

    function generatePyramid(count, size) {
        const points = new Float32Array(count * 3);
        const halfBase = size / 2;
        const height = size * 1.2;
        const apex = new THREE.Vector3(0, height / 2, 0);
        const baseVertices = [
            new THREE.Vector3(-halfBase, -height / 2, -halfBase),
            new THREE.Vector3(halfBase, -height / 2, -halfBase),
            new THREE.Vector3(halfBase, -height / 2, halfBase),
            new THREE.Vector3(-halfBase, -height / 2, halfBase)
        ];
        
        for (let i = 0; i < count; i++) {
            const r = Math.random();
            let p = new THREE.Vector3();
            
            if (r < 0.3) {
                // Base
                const u = Math.random();
                const v = Math.random();
                p.lerpVectors(baseVertices[0], baseVertices[1], u);
                const p2 = new THREE.Vector3().lerpVectors(baseVertices[3], baseVertices[2], u);
                p.lerp(p2, v);
            } else {
                // Sides
                const faceIndex = Math.floor((r - 0.3) / 0.175);
                const v1 = baseVertices[faceIndex % 4];
                const v2 = baseVertices[(faceIndex + 1) % 4];
                let u = Math.random();
                let v = Math.random();
                if (u + v > 1) { u = 1 - u; v = 1 - v; }
                p.addVectors(v1, tempVec.subVectors(v2, v1).multiplyScalar(u));
                p.add(tempVec.subVectors(apex, v1).multiplyScalar(v));
            }
            points.set([p.x, p.y, p.z], i * 3);
        }
        return points;
    }

    function generateTorus(count, size) {
        const points = new Float32Array(count * 3);
        const R = size * 0.7;
        const r = size * 0.3;
        for (let i = 0; i < count; i++) {
            const theta = Math.random() * Math.PI * 2;
            const phi = Math.random() * Math.PI * 2;
            const x = (R + r * Math.cos(phi)) * Math.cos(theta);
            const y = r * Math.sin(phi);
            const z = (R + r * Math.cos(phi)) * Math.sin(theta);
            points[i * 3] = x;
            points[i * 3 + 1] = y;
            points[i * 3 + 2] = z;
        }
        return points;
    }

    function generateGalaxy(count, size) {
        const points = new Float32Array(count * 3);
        const arms = 4;
        const armWidth = 0.6;
        const bulgeFactor = 0.3;
        
        for (let i = 0; i < count; i++) {
            const t = Math.pow(Math.random(), 1.5);
            const radius = t * size;
            const armIndex = Math.floor(Math.random() * arms);
            const armOffset = (armIndex / arms) * Math.PI * 2;
            const rotationAmount = radius / size * 6;
            const angle = armOffset + rotationAmount;
            const spread = (Math.random() - 0.5) * armWidth * (1 - radius / size);
            const theta = angle + spread;
            const x = radius * Math.cos(theta);
            const z = radius * Math.sin(theta);
            const y = (Math.random() - 0.5) * size * 0.1 * (1 - radius / size * bulgeFactor);
            points[i * 3] = x;
            points[i * 3 + 1] = y;
            points[i * 3 + 2] = z;
        }
        return points;
    }

    function generateWave(count, size) {
        const points = new Float32Array(count * 3);
        const waveScale = size * 0.4;
        const frequency = 3;
        for (let i = 0; i < count; i++) {
            const u = Math.random() * 2 - 1;
            const v = Math.random() * 2 - 1;
            const x = u * size;
            const z = v * size;
            const dist = Math.sqrt(u * u + v * v);
            const angle = Math.atan2(v, u);
            const y = Math.sin(dist * Math.PI * frequency) * Math.cos(angle * 2) * waveScale * (1 - dist);
            points[i * 3] = x;
            points[i * 3 + 1] = y;
            points[i * 3 + 2] = z;
        }
        return points;
    }

    function init() {
        let progress = 0;
        const progressBar = document.getElementById('progress');
        const loadingScreen = document.getElementById('loading');
        
        function updateProgress(increment) {
            progress += increment;
            progressBar.style.width = `${Math.min(100, progress)}%`;
            if (progress >= 100) {
                setTimeout(() => {
                    loadingScreen.style.opacity = '0';
                    setTimeout(() => { loadingScreen.style.display = 'none'; }, 600);
                }, 200);
            }
        }

        clock = new THREE.Clock();
        noise3D = createNoise3D(() => Math.random());
        noise4D = createNoise4D(() => Math.random());
        scene = new THREE.Scene();
        scene.fog = new THREE.FogExp2(0x000308, 0.03);
        updateProgress(5);

        camera = new THREE.PerspectiveCamera(70, window.innerWidth / window.innerHeight, 0.1, 1000);
        camera.position.set(0, 1, 10);
        camera.lookAt(scene.position);
        updateProgress(5);

        const canvas = document.getElementById('webglCanvas');
        renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true, powerPreference: 'high-performance' });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.toneMapping = THREE.ACESFilmicToneMapping;
        renderer.toneMappingExposure = 1.1;
        updateProgress(10);

        controls = new OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;
        controls.minDistance = 5;
        controls.maxDistance = 80;
        controls.autoRotate = true;
        controls.autoRotateSpeed = 0.3;
        updateProgress(5);

        scene.add(new THREE.AmbientLight(0x404060));
        const dirLight1 = new THREE.DirectionalLight(0xffffff, 1.5);
        dirLight1.position.set(15, 20, 10);
        scene.add(dirLight1);
        const dirLight2 = new THREE.DirectionalLight(0x88aaff, 0.9);
        dirLight2.position.set(-15, -10, -15);
        scene.add(dirLight2);
        updateProgress(10);

        setupPostProcessing();
        updateProgress(10);
        createStarfield();
        updateProgress(15);
        setupParticleSystem();
        updateProgress(25);

        window.addEventListener('resize', onWindowResize);
        window.addEventListener('click', onCanvasClick);
        document.getElementById('shape-btn').addEventListener('click', triggerMorph);
        document.querySelectorAll('.color-option').forEach(option => {
            option.addEventListener('click', (e) => {
                document.querySelectorAll('.color-option').forEach(o => o.classList.remove('active'));
                e.target.classList.add('active');
                CONFIG.colorScheme = e.target.dataset.scheme;
                updateColors();
            });
        });
        document.querySelector(`.color-option[data-scheme="${CONFIG.colorScheme}"]`).classList.add('active');
        updateProgress(15);

        isInitialized = true;
        animate();
        console.log("Initialization complete.");
    }

    function setupPostProcessing() {
        composer = new EffectComposer(renderer);
        composer.addPass(new RenderPass(scene, camera));
        bloomPass = new UnrealBloomPass(
            new THREE.Vector2(window.innerWidth, window.innerHeight),
            CONFIG.bloomStrength,
            CONFIG.bloomRadius,
            CONFIG.bloomThreshold
        );
        composer.addPass(bloomPass);
    }

    function createStarfield() {
        const starVertices = [];
        const starSizes = [];
        const starColors = [];
        const starGeometry = new THREE.BufferGeometry();
        
        for (let i = 0; i < CONFIG.starCount; i++) {
            tempVec.set(
                THREE.MathUtils.randFloatSpread(400),
                THREE.MathUtils.randFloatSpread(400),
                THREE.MathUtils.randFloatSpread(400)
            );
            if (tempVec.length() < 100) tempVec.setLength(100 + Math.random() * 300);
            starVertices.push(tempVec.x, tempVec.y, tempVec.z);
            starSizes.push(Math.random() * 0.15 + 0.05);
            
            const color = new THREE.Color();
            if (Math.random() < 0.1) {
                color.setHSL(Math.random(), 0.7, 0.65);
            } else {
                color.setHSL(0.6, Math.random() * 0.1, 0.8 + Math.random() * 0.2);
            }
            starColors.push(color.r, color.g, color.b);
        }
        
        starGeometry.setAttribute('position', new THREE.Float32BufferAttribute(starVertices, 3));
        starGeometry.setAttribute('color', new THREE.Float32BufferAttribute(starColors, 3));
        starGeometry.setAttribute('size', new THREE.Float32BufferAttribute(starSizes, 1));
        
        const starMaterial = new THREE.ShaderMaterial({
            uniforms: { pointTexture: { value: createStarTexture() } },
            vertexShader: `
                attribute float size;
                varying vec3 vColor;
                varying float vSize;
                void main() {
                    vColor = color;
                    vSize = size;
                    vec4 mvPosition = modelViewMatrix * vec4(position, 1.0);
                    gl_PointSize = size * (400.0 / -mvPosition.z);
                    gl_Position = projectionMatrix * mvPosition;
                }
            `,
            fragmentShader: `
                uniform sampler2D pointTexture;
                varying vec3 vColor;
                varying float vSize;
                void main() {
                    float alpha = texture2D(pointTexture, gl_PointCoord).a;
                    if (alpha < 0.1) discard;
                    gl_FragColor = vec4(vColor, alpha * 0.9);
                }
            `,
            blending: THREE.AdditiveBlending,
            depthWrite: false,
            transparent: true,
            vertexColors: true
        });
        scene.add(new THREE.Points(starGeometry, starMaterial));
    }

    function createStarTexture() {
        const size = 64;
        const canvas = document.createElement('canvas');
        canvas.width = size;
        canvas.height = size;
        const context = canvas.getContext('2d');
        const gradient = context.createRadialGradient(size / 2, size / 2, 0, size / 2, size / 2, size / 2);
        gradient.addColorStop(0, 'rgba(255,255,255,1)');
        gradient.addColorStop(0.2, 'rgba(255,255,255,0.8)');
        gradient.addColorStop(0.5, 'rgba(255,255,255,0.3)');
        gradient.addColorStop(1, 'rgba(255,255,255,0)');
        context.fillStyle = gradient;
        context.fillRect(0, 0, size, size);
        return new THREE.CanvasTexture(canvas);
    }

    function setupParticleSystem() {
        targetPositions = SHAPES.map(shape => shape.generator(CONFIG.particleCount, CONFIG.shapeSize));
        particlesGeometry = new THREE.BufferGeometry();

        currentPositions = new Float32Array(targetPositions[0]);
        sourcePositions = new Float32Array(targetPositions[0]);
        swarmPositions = new Float32Array(CONFIG.particleCount * 3);
        particlesGeometry.setAttribute('position', new THREE.BufferAttribute(currentPositions, 3));

        particleSizes = new Float32Array(CONFIG.particleCount);
        particleOpacities = new Float32Array(CONFIG.particleCount);
        particleEffectStrengths = new Float32Array(CONFIG.particleCount);
        
        for (let i = 0; i < CONFIG.particleCount; i++) {
            particleSizes[i] = THREE.MathUtils.randFloat(CONFIG.particleSizeRange[0], CONFIG.particleSizeRange[1]);
            particleOpacities[i] = 1.0;
            particleEffectStrengths[i] = 0.0;
        }
        
        particlesGeometry.setAttribute('size', new THREE.BufferAttribute(particleSizes, 1));
        particlesGeometry.setAttribute('opacity', new THREE.BufferAttribute(particleOpacities, 1));
        particlesGeometry.setAttribute('aEffectStrength', new THREE.BufferAttribute(particleEffectStrengths, 1));

        const colors = new Float32Array(CONFIG.particleCount * 3);
        updateColorArray(colors, currentPositions);
        particlesGeometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));

        particlesMaterial = new THREE.ShaderMaterial({
            uniforms: {
                pointTexture: { value: createStarTexture() }
            },
            vertexShader: `
                attribute float size;
                attribute float opacity;
                attribute float aEffectStrength;
                varying vec3 vColor;
                varying float vOpacity;
                varying float vEffectStrength;

                void main() {
                    vColor = color;
                    vOpacity = opacity;
                    vEffectStrength = aEffectStrength;

                    vec4 mvPosition = modelViewMatrix * vec4(position, 1.0);

                    float sizeScale = 1.0 - vEffectStrength * ${CONFIG.morphSizeFactor.toFixed(2)};
                    gl_PointSize = size * sizeScale * (400.0 / -mvPosition.z);

                    gl_Position = projectionMatrix * mvPosition;
                }
            `,
            fragmentShader: `
                uniform sampler2D pointTexture;
                varying vec3 vColor;
                varying float vOpacity;
                varying float vEffectStrength;

                void main() {
                    float alpha = texture2D(pointTexture, gl_PointCoord).a;
                    if (alpha < 0.05) discard;

                    vec3 finalColor = vColor * (1.0 + vEffectStrength * ${CONFIG.morphBrightnessFactor.toFixed(2)});

                    gl_FragColor = vec4(finalColor, alpha * vOpacity);
                }
            `,
            blending: THREE.AdditiveBlending,
            depthTest: true,
            depthWrite: false,
            transparent: true,
            vertexColors: true
        });

        particleSystem = new THREE.Points(particlesGeometry, particlesMaterial);
        scene.add(particleSystem);
    }

    function updateColorArray(colors, positionsArray) {
        const colorScheme = COLOR_SCHEMES[CONFIG.colorScheme];
        const center = new THREE.Vector3(0, 0, 0);
        const maxRadius = CONFIG.shapeSize * 1.1;
        
        for (let i = 0; i < CONFIG.particleCount; i++) {
            const i3 = i * 3;
            tempVec.fromArray(positionsArray, i3);
            const dist = tempVec.distanceTo(center);
            let hue;
            
            if (CONFIG.colorScheme === 'rainbow') {
                const normX = (tempVec.x / maxRadius + 1) / 2;
                const normY = (tempVec.y / maxRadius + 1) / 2;
                const normZ = (tempVec.z / maxRadius + 1) / 2;
                hue = (normX * 120 + normY * 120 + normZ * 120) % 360;
            } else {
                hue = THREE.MathUtils.mapLinear(
                    dist, 0, maxRadius,
                    colorScheme.startHue, colorScheme.endHue
                );
            }
            
            const noiseValue = (noise3D(tempVec.x * 0.2, tempVec.y * 0.2, tempVec.z * 0.2) + 1) * 0.5;
            const saturation = THREE.MathUtils.clamp(
                colorScheme.saturation * (0.9 + noiseValue * 0.2), 0, 1
            );
            const lightness = THREE.MathUtils.clamp(
                colorScheme.lightness * (0.85 + noiseValue * 0.3), 0.1, 0.9
            );
            
            const color = new THREE.Color().setHSL(hue / 360, saturation, lightness);
            color.toArray(colors, i3);
        }
    }

    function updateColors() {
        const colors = particlesGeometry.attributes.color.array;
        updateColorArray(colors, particlesGeometry.attributes.position.array);
        particlesGeometry.attributes.color.needsUpdate = true;
    }

    function triggerMorph() {
        if (isMorphing) return;
        isMorphing = true;
        controls.autoRotate = false;
         
        document.getElementById('info').style.textShadow = '0 0 8px rgba(255, 150, 50, 0.9)';
        
        sourcePositions.set(currentPositions);
        const nextShapeIndex = (currentShapeIndex + 1) % SHAPES.length;
        const nextTargetPositions = targetPositions[nextShapeIndex];
        const centerOffsetAmount = CONFIG.shapeSize * CONFIG.swarmDistanceFactor;
        
        for (let i = 0; i < CONFIG.particleCount; i++) {
            const i3 = i * 3;
            sourceVec.fromArray(sourcePositions, i3);
            targetVec.fromArray(nextTargetPositions, i3);
            swarmVec.lerpVectors(sourceVec, targetVec, 0.5);
            
            const offsetDir = tempVec.set(
                noise3D(i * 0.05, 10, 10),
                noise3D(20, i * 0.05, 20),
                noise3D(30, 30, i * 0.05)
            ).normalize();
            
            const distFactor = sourceVec.distanceTo(targetVec) * 0.1 + centerOffsetAmount;
            swarmVec.addScaledVector(offsetDir, distFactor * (0.5 + Math.random() * 0.8));
            
            swarmPositions[i3] = swarmVec.x;
            swarmPositions[i3 + 1] = swarmVec.y;
            swarmPositions[i3 + 2] = swarmVec.z;
        }
        
        currentShapeIndex = nextShapeIndex;
        morphState.progress = 0;
        
        if (morphTimeline) morphTimeline.pause();
        morphTimeline = anime({
            targets: morphState,
            progress: 1,
            duration: CONFIG.morphDuration,
            easing: 'cubicBezier(0.4, 0.0, 0.2, 1.0)',
            complete: () => {
                document.getElementById('info').style.textShadow = '0 0 5px rgba(0, 128, 255, 0.8)';
                
                currentPositions.set(targetPositions[currentShapeIndex]);
                particlesGeometry.attributes.position.needsUpdate = true;
                
                particleEffectStrengths.fill(0.0);
                particlesGeometry.attributes.aEffectStrength.needsUpdate = true;
                
                sourcePositions.set(targetPositions[currentShapeIndex]);
                updateColors();
                
                isMorphing = false;
                controls.autoRotate = true;
            }
        });
    }

    function animate() {
        requestAnimationFrame(animate);
        if (!isInitialized) return;
        
        const elapsedTime = clock.getElapsedTime();
        const deltaTime = clock.getDelta();
        controls.update();
        
        const positions = particlesGeometry.attributes.position.array;
        const effectStrengths = particlesGeometry.attributes.aEffectStrength.array;

        if (isMorphing) {
            updateMorphAnimation(positions, effectStrengths, elapsedTime, deltaTime);
        } else {
            updateIdleAnimation(positions, effectStrengths, elapsedTime, deltaTime);
        }
        
        particlesGeometry.attributes.position.needsUpdate = true;
        if (isMorphing || particlesGeometry.attributes.aEffectStrength.needsUpdate) {
            particlesGeometry.attributes.aEffectStrength.needsUpdate = true;
        }
        
        composer.render(deltaTime);
    }

    function updateMorphAnimation(positions, effectStrengths, elapsedTime, deltaTime) {
        const t = morphState.progress;
        const targets = targetPositions[currentShapeIndex];
        const effectStrength = Math.sin(t * Math.PI);
        const currentSwirl = effectStrength * CONFIG.swirlFactor * deltaTime * 50;
        const currentNoise = effectStrength * CONFIG.noiseMaxStrength;

        for (let i = 0; i < CONFIG.particleCount; i++) {
            const i3 = i * 3;
            sourceVec.fromArray(sourcePositions, i3);
            swarmVec.fromArray(swarmPositions, i3);
            targetVec.fromArray(targets, i3);

            const t_inv = 1.0 - t;
            const t_inv_sq = t_inv * t_inv;
            const t_sq = t * t;
            bezPos.copy(sourceVec).multiplyScalar(t_inv_sq);
            bezPos.addScaledVector(swarmVec, 2.0 * t_inv * t);
            bezPos.addScaledVector(targetVec, t_sq);

            if (currentSwirl > 0.01) {
                tempVec.subVectors(bezPos, sourceVec);
                swirlAxis.set(
                    noise3D(i * 0.02, elapsedTime * 0.1, 0),
                    noise3D(0, i * 0.02, elapsedTime * 0.1 + 5),
                    noise3D(elapsedTime * 0.1 + 10, 0, i * 0.02)
                ).normalize();
                tempVec.applyAxisAngle(swirlAxis, currentSwirl * (0.5 + Math.random() * 0.5));
                bezPos.copy(sourceVec).add(tempVec);
            }

            if (currentNoise > 0.01) {
                const noiseTime = elapsedTime * CONFIG.noiseTimeScale;
                noiseOffset.set(
                    noise4D(bezPos.x * CONFIG.noiseFrequency, bezPos.y * CONFIG.noiseFrequency, bezPos.z * CONFIG.noiseFrequency, noiseTime),
                    noise4D(bezPos.x * CONFIG.noiseFrequency + 100, bezPos.y * CONFIG.noiseFrequency + 100, bezPos.z * CONFIG.noiseFrequency + 100, noiseTime),
                    noise4D(bezPos.x * CONFIG.noiseFrequency + 200, bezPos.y * CONFIG.noiseFrequency + 200, bezPos.z * CONFIG.noiseFrequency + 200, noiseTime)
                );
                bezPos.addScaledVector(noiseOffset, currentNoise);
            }

            positions[i3] = bezPos.x;
            positions[i3 + 1] = bezPos.y;
            positions[i3 + 2] = bezPos.z;

            effectStrengths[i] = effectStrength;
        }
        particlesGeometry.attributes.aEffectStrength.needsUpdate = true;
    }

    function updateIdleAnimation(positions, effectStrengths, elapsedTime, deltaTime) {
        const breathScale = 1.0 + Math.sin(elapsedTime * 0.5) * 0.015;
        const timeScaled = elapsedTime * CONFIG.idleFlowSpeed;
        const freq = 0.1;

        let needsEffectStrengthReset = false;

        for (let i = 0; i < CONFIG.particleCount; i++) {
            const i3 = i * 3;
            sourceVec.fromArray(sourcePositions, i3);
            tempVec.copy(sourceVec).multiplyScalar(breathScale);
            
            flowVec.set(
                noise4D(tempVec.x * freq, tempVec.y * freq, tempVec.z * freq, timeScaled),
                noise4D(tempVec.x * freq + 10, tempVec.y * freq + 10, tempVec.z * freq + 10, timeScaled),
                noise4D(tempVec.x * freq + 20, tempVec.y * freq + 20, tempVec.z * freq + 20, timeScaled)
            );
            tempVec.addScaledVector(flowVec, CONFIG.idleFlowStrength);
            
            currentVec.fromArray(positions, i3);
            currentVec.lerp(tempVec, 0.05);
            
            positions[i3] = currentVec.x;
            positions[i3 + 1] = currentVec.y;
            positions[i3 + 2] = currentVec.z;

            if (effectStrengths[i] !== 0.0) {
                effectStrengths[i] = 0.0;
                needsEffectStrengthReset = true;
            }
        }
        
        if (needsEffectStrengthReset) {
            particlesGeometry.attributes.aEffectStrength.needsUpdate = true;
        }
    }

    function onCanvasClick(event) {
        if (event.target.closest('#controls') || event.target.closest('.section-content')) {
            return;
        }
        triggerMorph();
    }

    function onWindowResize() {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
        composer.setSize(window.innerWidth, window.innerHeight);
    }

    // Initialize the scene
    init();
</script>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/custom.js"></script>

<script>
// Back to Top logic
(function() {
    const btn = document.getElementById('backToTopBtn');
    if (!btn) return;
    const showAt = 200;
    function toggleBtn() {
        if (window.scrollY > showAt) {
            btn.style.display = 'flex';
        } else {
            btn.style.display = 'none';
        }
    }
    window.addEventListener('scroll', toggleBtn, { passive: true });
    toggleBtn();
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();

// Display notifications from session
<?php if (isset($_SESSION['error_message'])): ?>
    showNotification('<?php echo addslashes($_SESSION['error_message']); ?>', 'error');
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success_message'])): ?>
    showNotification('<?php echo addslashes($_SESSION['success_message']); ?>', 'success');
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>
</script>
         