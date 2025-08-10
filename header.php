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
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content custom-modal">
            <div class="modal-header border-0">
                <h5 class="modal-title text-gradient fw-bold">Welcome to EventPro</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- User Type Selection -->
                <div id="userTypeSelection" class="text-center">
                    <h6 class="mb-4">Choose your account type</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <button type="button" class="btn-custom btn-primary-custom w-100 py-4" onclick="selectUserType('client')">
                                <i class="fas fa-user fa-2x mb-3"></i>
                                <h6>Client</h6>
                                <p class="small mb-0">I want to plan events</p>
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn-custom btn-secondary-custom w-100 py-4" onclick="selectUserType('planner')">
                                <i class="fas fa-calendar-check fa-2x mb-3"></i>
                                <h6>Event Planner</h6>
                                <p class="small mb-0">I provide event planning services</p>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <p class="text-muted mb-2">Already have an account?</p>
                        <button type="button" class="btn-custom btn-outline-secondary" onclick="toggleTab('login')">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>
                    </div>
                </div>

                <!-- Login Form -->
                <div id="loginForm" style="display: none;">
                    <div class="text-center mb-4">
                        <h6 class="text-muted">Welcome back!</h6>
                        <p class="small text-muted">Sign in to your account</p>
                    </div>
                    
                    <form action="login.php" method="POST" onsubmit="return validateLoginForm()">
                        <div class="mb-3">
                            <label for="loginUsername" class="form-label fw-semibold">Username</label>
                            <input type="text" class="form-control-custom w-100" id="loginUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label fw-semibold">Password</label>
                            <input type="password" class="form-control-custom w-100" id="loginPassword" name="password" required>
                        </div>
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="keepSignedIn">
                            <label class="form-check-label" for="keepSignedIn">Keep me signed in</label>
                        </div>
                        <button type="submit" class="btn-custom btn-primary-custom w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>
                    </form>
                    
                    <!-- Demo Credentials Info -->
                    <div class="mt-3 p-3 bg-light rounded">
                        <small class="text-muted">
                            <strong>Demo Credentials:</strong><br>
                            <strong>Admin:</strong> admin / password<br>
                            <strong>Planner:</strong> planner1 / password<br>
                            <strong>Client:</strong> client1 / password
                        </small>
                    </div>
                    
                    <div class="text-center mt-3">
                        <button type="button" class="btn-custom btn-outline-secondary" onclick="goBackToUserType()">
                            <i class="fas fa-arrow-left me-2"></i>Back to Selection
                        </button>
                    </div>
                </div>

                <!-- Client Signup Form -->
                <div id="clientSignupForm" style="display: none;">
                    <div class="text-center mb-4">
                        <h6 class="text-muted">Create Client Account</h6>
                        <p class="small text-muted">Join our Event Management System as a client</p>
                    </div>
                    
                    <form action="register_backend.php" method="POST" onsubmit="return validateClientSignupForm()">
                        <input type="hidden" name="user_type" value="client">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="clientFullName" class="form-label fw-semibold">Full Name *</label>
                                    <input type="text" class="form-control-custom w-100" id="clientFullName" name="full_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="clientUsername" class="form-label fw-semibold">Username *</label>
                                    <input type="text" class="form-control-custom w-100" id="clientUsername" name="username" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="clientEmail" class="form-label fw-semibold">Email *</label>
                                    <input type="email" class="form-control-custom w-100" id="clientEmail" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="clientPhone" class="form-label fw-semibold">Phone *</label>
                                    <input type="tel" class="form-control-custom w-100" id="clientPhone" name="phone" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mt-3">
                            <label for="clientAddress" class="form-label fw-semibold">Address *</label>
                            <textarea class="form-control-custom w-100" id="clientAddress" name="address" rows="2" required></textarea>
                        </div>
                        
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="clientPassword" class="form-label fw-semibold">Password *</label>
                                    <input type="password" class="form-control-custom w-100" id="clientPassword" name="password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="clientConfirmPassword" class="form-label fw-semibold">Confirm Password *</label>
                                    <input type="password" class="form-control-custom w-100" id="clientConfirmPassword" name="confirm_password" required>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-custom btn-primary-custom w-100 mt-3">
                            <i class="fas fa-user-plus me-2"></i>Create Client Account
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <button type="button" class="btn-custom btn-outline-secondary" onclick="goBackToUserType()">
                            <i class="fas fa-arrow-left me-2"></i>Back to Selection
                        </button>
                    </div>
                </div>

                <!-- Planner Signup Form -->
                <div id="plannerSignupForm" style="display: none;">
                    <div class="text-center mb-4">
                        <h6 class="text-muted">Create Planner Account</h6>
                        <p class="small text-muted">Join our Event Management System as an event planner</p>
                    </div>
                    
                    <form action="register_backend.php" method="POST" onsubmit="return validatePlannerSignupForm()">
                        <input type="hidden" name="user_type" value="planner">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plannerFullName" class="form-label fw-semibold">Full Name *</label>
                                    <input type="text" class="form-control-custom w-100" id="plannerFullName" name="full_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plannerUsername" class="form-label fw-semibold">Username *</label>
                                    <input type="text" class="form-control-custom w-100" id="plannerUsername" name="username" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plannerEmail" class="form-label fw-semibold">Email *</label>
                                    <input type="email" class="form-control-custom w-100" id="plannerEmail" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plannerPhone" class="form-label fw-semibold">Phone *</label>
                                    <input type="tel" class="form-control-custom w-100" id="plannerPhone" name="phone" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plannerCompany" class="form-label fw-semibold">Company Name *</label>
                                    <input type="text" class="form-control-custom w-100" id="plannerCompany" name="company_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plannerSpecialization" class="form-label fw-semibold">Specialization *</label>
                                    <select class="form-control-custom w-100" id="plannerSpecialization" name="specialization" required>
                                        <option value="">Select Specialization</option>
                                        <option value="Weddings">Weddings</option>
                                        <option value="Corporate Events">Corporate Events</option>
                                        <option value="Birthday Parties">Birthday Parties</option>
                                        <option value="Anniversaries">Anniversaries</option>
                                        <option value="Graduations">Graduations</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plannerExperience" class="form-label fw-semibold">Years of Experience *</label>
                                    <input type="number" class="form-control-custom w-100" id="plannerExperience" name="experience_years" min="0" max="50" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plannerLocation" class="form-label fw-semibold">Location *</label>
                                    <input type="text" class="form-control-custom w-100" id="plannerLocation" name="location" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mt-3">
                            <label for="plannerBio" class="form-label fw-semibold">Bio *</label>
                            <textarea class="form-control-custom w-100" id="plannerBio" name="bio" rows="3" required></textarea>
                        </div>
                        
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plannerPassword" class="form-label fw-semibold">Password *</label>
                                    <input type="password" class="form-control-custom w-100" id="plannerPassword" name="password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plannerConfirmPassword" class="form-label fw-semibold">Confirm Password *</label>
                                    <input type="password" class="form-control-custom w-100" id="plannerConfirmPassword" name="confirm_password" required>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-custom btn-primary-custom w-100 mt-3">
                            <i class="fas fa-calendar-check me-2"></i>Create Planner Account
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <button type="button" class="btn-custom btn-outline-secondary" onclick="goBackToUserType()">
                            <i class="fas fa-arrow-left me-2"></i>Back to Selection
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include custom JavaScript for authentication -->
<script src="assets/js/custom.js"></script>


