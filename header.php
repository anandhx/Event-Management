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
    padding: 2rem;
    color: var(--text-primary);
    border: 1px solid var(--gray-300);
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









<?php if (isset($_SESSION['success_message']) || isset($_SESSION['error_message'])): ?>
    <script>
        <?php if (isset($_SESSION['success_message'])): ?>
            alert("<?php echo $_SESSION['success_message']; ?>");
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            alert("<?php echo $_SESSION['error_message']; ?>");
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
    </script>
<?php endif; ?>




<!-- old message modal un commen this for old message  -->
<?php


/*
if (isset($_SESSION['success_message']) || isset($_SESSION['error_message'])): ?>
    <div id="message-box" class="position-fixed top-0 right-0 m-3">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success_message']; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error_message']; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
    </div>
endif;
*/
?>






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
                        <li class="nav-item">
                            <a href="#about" class="nav-link">About</a>
                        </li>
                        <li class="nav-item">
                            <a href="#services" class="nav-link">Services</a>
                        </li>
                        <li class="nav-item">
                            <a href="#projects" class="nav-link">Projects</a>
                        </li>
                        <li class="nav-item">
                            <a href="contact.php" class="nav-link">Contact</a>
                        </li>
                        <li class="nav-item ms-3">
                            <button type="button" class="btn-custom btn-primary-custom" data-bs-toggle="modal" data-bs-target="#authModal">
                                <i class="fas fa-user me-2"></i>Login / Sign Up
                            </button>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>
    <!-- Header End -->

    

<!-- Authentication Modal -->
<div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header border-0">
                <h5 class="modal-title text-gradient fw-bold">Welcome to EventPro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="d-flex justify-content-center">
                        <span class="tab active px-4 py-2 rounded-pill me-2" id="loginTab" onclick="toggleTab('login')">Sign In</span>
                        <span class="tab px-4 py-2 rounded-pill" id="signupTab" onclick="toggleTab('signup')">Sign Up</span>
                    </div>
                </div>

                <!-- Login Form -->
                <form id="loginForm" style="display: block;" action="login_backend.php" method="POST" onsubmit="return validateLoginForm()">
                    <div class="mb-3">
                        <label for="loginUsername" class="form-label fw-semibold">Username</label>
                        <input type="text" class="form-control-custom" id="loginUsername" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="loginPassword" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control-custom" id="loginPassword" name="password" required>
                    </div>
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="keepSignedIn">
                        <label class="form-check-label" for="keepSignedIn">Keep me signed in</label>
                    </div>
                    <button type="submit" class="btn-custom btn-primary-custom w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </form>

                <!-- Signup Form -->
                <form id="signupForm" style="display: none;" action="signup_backend.php" method="POST" onsubmit="return validateSignupForm()">
                    <div class="mb-3">
                        <label for="signupUsername" class="form-label fw-semibold">Username</label>
                        <input type="text" class="form-control-custom" id="signupUsername" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="signupPassword" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control-custom" id="signupPassword" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="signupConfirmPassword" class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" class="form-control-custom" id="signupConfirmPassword" name="confirm_password" required>
                    </div>
                    <div class="mb-4">
                        <label for="signupEmail" class="form-label fw-semibold">Email Address</label>
                        <input type="email" class="form-control-custom" id="signupEmail" name="email" required>
                    </div>
                    <button type="submit" class="btn-custom btn-primary-custom w-100">
                        <i class="fas fa-user-plus me-2"></i>Sign Up
                    </button>
                </form>

                <div class="text-center mt-4">
                    <p class="text-muted mb-0">Or continue with</p>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <button class="btn-custom btn-secondary-custom">
                            <i class="fab fa-google me-2"></i>Google
                        </button>
                        <button class="btn-custom btn-accent-custom">
                            <i class="fab fa-facebook-f me-2"></i>Facebook
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


