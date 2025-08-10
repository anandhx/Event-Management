<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


?>

<?php


// Check if the user is logged in by checking if 'username' session variable is set
if (!isset($_SESSION['username'])) {
    // If not logged in, redirect to the login page
    header("Location: ../index.php");
    exit();
}

// If logged in, proceed with the rest of the page
?>

<?php
// Include the database connection file
include('../includes/db.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>EventPro - Event Management System</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    

    <!-- Libraries Stylesheet -->
    <link href="../assets/lib/animate/animate.min.css" rel="stylesheet">
    <link href="../assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="../assets/css/style.css" rel="stylesheet">
    
    <style>


</style>
<!-- CSS for fade-out effect -->
<style>
    .fade-out {
        opacity: 0;
        transition: opacity 1s ease-out; /* 1 second for fade-out */
    }
</style>
</head>
<body>
    <!-- Include header content -->
    <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
   <!-- Message Display -->
<!-- Trigger button for testing -->

   <!-- Message Display -->
   <?php if (isset($_SESSION['success_message']) || isset($_SESSION['error_message'])): ?>
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
<?php endif; ?>

    <!-- Topbar Start -->
    <div class="container-fluid topbar-top bg-primary">
        <div class="container">
            <div class="d-flex justify-content-between topbar py-2">
                <div class="d-flex align-items-center flex-shrink-0 topbar-info">
                    <a href="#" class="me-4 text-secondary"><i class="fas fa-map-marker-alt me-2 text-dark"></i>123 Street, Kottayam, Kerala</a>
                    <a href="#" class="me-4 text-secondary"><i class="fas fa-phone-alt me-2 text-dark"></i>+01234567890</a>
                    
                </div>
                <div class="text-end pe-4 me-4 border-end border-dark search-btn">
                    <div class="search-form">
                        <form method="post" action="index.php">
                            <div class="form-group">
                                <div class="d-flex">
                                    <input type="search" class="form-control border-0 rounded-pill" name="search-input" value="" placeholder="Search Here" required=""/>
                                    <button type="submit" value="Search Now!" class="btn"><i class="fa fa-search text-dark"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-center topbar-icon">
                    <a href="#" class="me-4"><i class="fab fa-facebook-f text-dark"></i></a>
                    <a href="#" class="me-4"><i class="fab fa-twitter text-dark"></i></a>
                    <a href="#" class="me-4"><i class="fab fa-instagram text-dark"></i></a>
                    <a href="#" class=""><i class="fab fa-linkedin-in text-dark"></i></a>
                </div>
            </div>
        </div>
    </div>
    <!-- Topbar End -->

    <!-- Navbar Start -->
    <div class="container-fluid bg-dark">
        <div class="container">
            <nav class="navbar navbar-dark navbar-expand-lg py-lg-0">
                <a href="index.php" class="navbar-brand">
                    <h1 class="text-primary mb-0 display-5">event<span class="text-white">ms</span></h1>
                </a>
                <button class="navbar-toggler bg-primary" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                    <span class="fa fa-bars text-dark"></span>
                </button>
                
                        <a href="../logout.php" class="nav-item nav-link">Log Out</a>
                    </div>
                </div>
            </nav>
        </div>
    </div>
    <!-- Navbar End -->
