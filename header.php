<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Event Management System</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">


    <!-- Libraries Stylesheet -->
    <link href="assets/lib/animate/animate.min.css" rel="stylesheet">
    <link href="assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="assets/css/style.css" rel="stylesheet">

    
<style>
    .custom-modal-bg {
        background: linear-gradient(to right, #0BF90B, #100DE6);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Modal Styles */
    .modal-content {
        background: var(--dark-tertiary);
        border-radius: 21px;
        padding: 0;
        color: var(--text-primary);
        border: 1px solid var(--gray-300);
        overflow: hidden;
    }
    
    .custom-modal .modal-content {
        background: var(--dark-tertiary);
        border: 1px solid var(--gray-300);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    .custom-modal .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom: 1px solid var(--gray-300);
        padding: 1.5rem 2rem;
    }
    
    .custom-modal .modal-header .btn-close {
        filter: invert(1);
        opacity: 0.8;
    }
    
    .custom-modal .modal-header .btn-close:hover {
        opacity: 1;
    }
    
    .custom-modal .modal-body {
        padding: 2rem;
        background: var(--dark-tertiary);
    }
    
    .modal-content .tab {
        font-size: 22px;
        margin-right: 15px;
        padding-bottom: 5px;
        display: inline-block;
        border-bottom: 2px solid transparent;
        cursor: pointer;
    }

    .modal-content .tab.active {
        color: #6dff16;
        border-color: #e5e9f0;
    }

    .modal-content .form-control {
        border: none;
        padding: 15px 20px;
        border-radius: 25px;
        background: var(--dark-secondary);
        color: var(--text-primary);
        border: 1px solid var(--gray-400);
    }

    .modal-content .btn-primary {
        background: #FFFFFF;
        border: none;
        padding: 10px 20px;
        border-radius: 25px;
        transition: background 0.3s;
    }

    .modal-content .btn-primary:hover {
        background: #35393C;
    }

    .hr {
        height: 2px;
        margin: 60px 0 50px 0;
        background: rgba(255,255,255,.2);
    }

    .foot-lnk {
        text-align: center;
    }

/* Blurred background when modal is active */
.modal-backdrop {
     background-color: rgba(8, 8, 8, 0.8);  */
   
}

.blurred-bg {
    filter: blur(25px); /* Max reasonable blur */
    transition: filter 0.6s ease;
}

    /* Glassmorphic Header Styles */
    .glass-header {
        position: sticky;
        top: 0;
        z-index: 1030;
        background: rgba(20, 24, 35, 0.35);
        -webkit-backdrop-filter: blur(12px);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .glass-header .navbar { background: transparent; }
    .brand-text {
        font-weight: 800;
        font-size: 1.5rem;
        letter-spacing: 0.5px;
        color: #fff;
    }
    .brand-text .accent { color: #6dd6ff; }
    .brand-logo {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: linear-gradient(135deg, rgba(109,214,255,0.9), rgba(118,75,162,0.9));
        box-shadow: 0 6px 18px rgba(109,214,255,0.35);
        display: inline-block;
    }
    .glass-link {
        color: rgba(255,255,255,0.85);
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        transition: color .2s ease, background .2s ease, transform .2s ease;
    }
    .glass-link:hover {
        color: #fff;
        background: rgba(255,255,255,0.08);
        transform: translateY(-1px);
    }
    .divider-dot {
        width: 6px; height: 6px; border-radius: 50%; display: inline-block;
        background: rgba(255,255,255,0.25);
        margin: 0 8px;
    }
    .glass-btn-primary {
        background: linear-gradient(135deg, rgba(109,214,255,0.9), rgba(118,75,162,0.9));
        color: #0b1220;
        border: 0;
        border-radius: 12px;
        padding: 8px 16px;
        box-shadow: 0 8px 24px rgba(109,214,255,0.35);
    }
    .glass-btn-primary:hover { filter: brightness(1.05); color: #0b1220; }
    .glass-btn-outline {
        background: rgba(255,255,255,0.06);
        color: #e8f3ff;
        border: 1px solid rgba(255,255,255,0.18);
        border-radius: 12px;
        padding: 8px 16px;
        backdrop-filter: blur(6px);
    }
    .glass-btn-outline:hover { background: rgba(255,255,255,0.12); color: #fff; }
    .navbar-toggler { filter: invert(1) contrast(2) saturate(0.6); }
    @media (max-width: 991.98px) {
        .glass-header .navbar-collapse {
            background: rgba(20, 24, 35, 0.55);
            border-radius: 12px;
            -webkit-backdrop-filter: blur(10px);
            backdrop-filter: blur(10px);
            padding: 12px;
            margin-top: 10px;
        }
    }

       

</style>
<!-- CSS for fade-out effect -->
<style>
    .fade-out {
        opacity: 0;
        transition: opacity 1s ease-out; /* 1 second for fade-out */
    }
    .fade {
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
}

.fade.show {
    opacity: 1;
}

    .diagonal-triangle {
    position: absolute;
    top: 18px;
    left: 0px;
    width: 100%;
    height: 97%;
    clip-path: polygon(-1% 7%, 102% 103%, -13% 201%);
    background: rgb(183 181 215 / 7%);
    z-index: 1;
    border-radius: 19px;
}


</style>
</head>
<body>
    <!-- Include header content -->
    <div id="spinner" class="show w-100 vh-100 position-fixed translate-middle top-50 start-50 d-flex align-items-center justify-content-center" style="background: var(--dark-color);">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
<!-- Message Display -->









<?php /* Flash messages are now displayed within individual pages (e.g., login/register). */ ?>




<?php /* Old modal-based message UI removed to avoid duplication. */ ?>






    <!-- Header Start -->
    <header class="glass-header fixed-top">
        <div class="container">
            <nav class="navbar navbar-expand-lg py-3">
                <a href="index.php" class="navbar-brand d-flex align-items-center">
                    <span class="brand-logo me-2"></span>
                    <span class="brand-text">Event<span class="accent">Pro</span></span>
                </a>
                <button class="navbar-toggler shadow-none border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav ms-auto align-items-center gap-lg-2">
                 

                        <li><a href="#home" class="nav-link">Home</a></li>
                <li><a href="#services" class="nav-link">Services</a></li>
                <li><a href="#about" class="nav-link">About</a></li> 
                <li><a href="login.php" class="nav-link">Login</a></li>
                <li><a href="user/register.php" class="nav-link">Sign Up</a></li>
                <li><a href="user/register_planner.php" class="nav-link">Planner Sign Up</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>
    <!-- Header End -->

    

<!-- Authentication via dedicated pages: login.php and user/register.php -->


