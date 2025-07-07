<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $user_type = $_POST['user_type'];
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Passwords do not match!";
        header("Location: register.php");
        exit();
    }
    
    // Check if username already exists
    $check_username = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $check_username->bind_param("s", $username);
    $check_username->execute();
    $result = $check_username->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = "Username already exists!";
        header("Location: register.php");
        exit();
    }
    
    // Check if email already exists
    $check_email = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = "Email already exists!";
        header("Location: register.php");
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $insert_user = $conn->prepare("INSERT INTO users (username, email, password, full_name, phone, user_type) VALUES (?, ?, ?, ?, ?, ?)");
    $insert_user->bind_param("ssssss", $username, $email, $hashed_password, $full_name, $phone, $user_type);
    
    if ($insert_user->execute()) {
        $user_id = $conn->insert_id;
        
        // If user is an event planner, create planner profile
        if ($user_type == 'event_planner') {
            $company_name = $_POST['company_name'];
            $expertise = $_POST['expertise'];
            $experience_years = $_POST['experience_years'];
            $location = $_POST['location'];
            $bio = $_POST['bio'];
            
            $insert_planner = $conn->prepare("INSERT INTO event_planners (user_id, company_name, expertise, experience_years, location, bio) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_planner->bind_param("isssss", $user_id, $company_name, $expertise, $experience_years, $location, $bio);
            $insert_planner->execute();
        }
        
        $_SESSION['success_message'] = "Registration successful! Please wait for admin approval.";
        header("Location: ../index.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Registration failed! Please try again.";
        header("Location: register.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Event Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .user-type-card {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        .user-type-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
        }
        .user-type-card.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .planner-fields {
            display: none;
        }
        .planner-fields.show {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="register-card p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-primary">Join EMS</h2>
                        <p class="text-muted">Create your account to get started</p>
                    </div>

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <!-- User Type Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">I want to register as:</label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="user-type-card" onclick="selectUserType('client')">
                                        <i class="fas fa-user fa-3x text-primary mb-3"></i>
                                        <h5>Event Client</h5>
                                        <p class="small text-muted">I want to organize events</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="user-type-card" onclick="selectUserType('event_planner')">
                                        <i class="fas fa-calendar-alt fa-3x text-primary mb-3"></i>
                                        <h5>Event Planner</h5>
                                        <p class="small text-muted">I want to provide event services</p>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="user_type" id="user_type" value="client">
                        </div>

                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>

                        <!-- Event Planner Specific Fields -->
                        <div id="planner_fields" class="planner-fields">
                            <hr class="my-4">
                            <h5 class="mb-3">Event Planner Information</h5>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="company_name" class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="experience_years" class="form-label">Years of Experience</label>
                                    <input type="number" class="form-control" id="experience_years" name="experience_years" min="0" max="50">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="expertise" class="form-label">Areas of Expertise</label>
                                <textarea class="form-control" id="expertise" name="expertise" rows="3" placeholder="e.g., Weddings, Corporate Events, Birthday Parties"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="location" class="form-label">Service Location</label>
                                <input type="text" class="form-control" id="location" name="location" placeholder="City, State">
                            </div>

                            <div class="mb-3">
                                <label for="bio" class="form-label">Bio/Description</label>
                                <textarea class="form-control" id="bio" name="bio" rows="4" placeholder="Tell us about your experience and what makes you unique"></textarea>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Create Account
                            </button>
                        </div>

                        <div class="text-center mt-4">
                            <p class="mb-0">Already have an account? <a href="../index.php" class="text-primary fw-bold">Sign In</a></p>
                        </div>
    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectUserType(type) {
            // Remove selected class from all cards
            document.querySelectorAll('.user-type-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            event.currentTarget.classList.add('selected');
            
            // Update hidden input
            document.getElementById('user_type').value = type;
            
            // Show/hide planner fields
            const plannerFields = document.getElementById('planner_fields');
            if (type === 'event_planner') {
                plannerFields.classList.add('show');
            } else {
                plannerFields.classList.remove('show');
            }
        }

        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
