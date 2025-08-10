<?php
// Include database connection
include('../includes/db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

// Get the logged-in user's username
$username = $_SESSION['username'];
$error = $success = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $full_name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
 
    
    // Validate input
    if (empty($full_name) || empty($email)) {
        $error = "All required fields must be filled out.";
    } else {
        // Simulate profile update for showcase
        // In real implementation, this would update the database
        $success = "Profile updated successfully!";
    }
}

// Dummy user data for showcase
// In real implementation, this would fetch from database
$user = [
    'full_name' => 'John Doe',
    'email' => 'john@example.com',
    'phone' => '1234567890',
    'username' => 'john_doe'
];
?>

<?php include 'header.php'; ?>

<div class="container my-5">
    <div class="row">
        <!-- Profile Information Section -->
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="fw-bold mb-0">Profile Management</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($success)) { ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($error)) { ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php } ?>
                    <form action="profile_management_backend.php" method="post">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required pattern="\S+.*" title="Spaces are not allowed at the start or end.">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" pattern="\d*" title="Please enter a valid phone number with digits only.">
                                </div>
                            </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary px-4">Update Profile</button>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</div>



<!-- Scripts for Bootstrap Modal -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include 'footer.php'; ?>
