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
    <header class="custom-header">
        <div class="container">
            <nav class="navbar navbar-expand-lg py-3">
                <a href="index.php" class="navbar-brand">
                    <h1 class="mb-0">Event<span class="text-gradient">Pro</span></h1>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarCollapse">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link">Home</a>
                        </li>
                
                        <li class="nav-item ms-3">
                            <a href="login.php" class="btn-custom btn-primary-custom">
                                <i class="fas fa-user me-2"></i>Login
                            </a>
                        </li>
                        <li class="nav-item ms-2">
                            <a href="user/register.php" class="btn-custom btn-outline-secondary">
                                <i class="fas fa-user-plus me-2"></i>Sign Up
                            </a>
                        </li>
                        <li class="nav-item ms-2">
                            <a href="user/register_planner.php" class="btn-custom btn-outline-secondary">
                                <i class="fas fa-calendar-check me-2"></i>Planner Sign Up
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>
    <!-- Header End -->

    

<!-- Authentication via dedicated pages: login.php and user/register.php -->


