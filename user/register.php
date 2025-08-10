<?php
session_start();
require_once '../includes/db.php';

$error = '';
$success = '';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: user_index.php');
    exit();
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    // Debug: Log the registration attempt
    error_log("Registration attempt for username: $username, email: $email");
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (!preg_match('/^[a-zA-Z_]+$/', $username)) {
        $error = 'Username can only contain letters and underscores. No numbers or symbols allowed.';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters long.';
    } elseif (strlen($username) > 20) {
        $error = 'Username cannot exceed 20 characters.';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $full_name)) {
        $error = 'Full name can only contain letters and spaces.';
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        if (!$stmt) {
            $error = 'Database error: ' . $conn->error;
        } else {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $error = 'Username already exists. Please choose another one.';
            } else {
                // Check if email already exists
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                if (!$stmt) {
                    $error = 'Database error: ' . $conn->error;
                } else {
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    if ($stmt->get_result()->num_rows > 0) {
                        $error = 'Email already exists. Please use another email address.';
                    } else {
                        // Insert user (only clients can register directly)
                        $user_type = 'client';
                        $status = 'active';
                        
                        // Hash the password for security
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, user_type, phone, address, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("ssssssss", $username, $email, $hashed_password, $full_name, $user_type, $phone, $address, $status);
                        
                        if ($stmt->execute()) {
                            $success = 'Registration successful! You can now login.';
                        } else {
                            $error = 'Registration failed: ' . $stmt->error . '. Please try again.';
                        }
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Event Management System</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 0;
        }
        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .register-form {
            padding: 40px;
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
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .info-box {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-box h6 {
            color: #1976d2;
            margin-bottom: 15px;
        }
        .info-box ul {
            margin-bottom: 0;
            padding-left: 20px;
        }
        .info-box li {
            color: #424242;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .validation-rules {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
        }
        .validation-rules h6 {
            color: #856404;
            margin-bottom: 10px;
        }
        .validation-rules ul {
            margin-bottom: 0;
            padding-left: 20px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="register-container">
                    <div class="register-header">
                        <h2><i class="fas fa-user-plus me-2"></i>Create Client Account</h2>
                        <p class="mb-0">Join our Event Management System as a client!</p>
                    </div>
                    
                    <div class="register-form">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                                <br><a href="../login.php" class="alert-link">Click here to login</a>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Information Box -->
                        <div class="info-box">
                            <h6><i class="fas fa-info-circle me-2"></i>Client Account Information</h6>
                            <ul>
                                <li>Create and manage your events</li>
                                <li>Browse and hire event planners</li>
                                <li>Track event progress and communicate with planners</li>
                                <li>Provide feedback and reviews</li>
                            </ul>
                        </div>
                        
                        <!-- Validation Rules -->
                        <div class="validation-rules">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Registration Rules</h6>
                            <ul>
                                <li>Username: Only letters and underscores, 3-20 characters</li>
                                <li>Full Name: Only letters and spaces</li>
                                <li>Password: Minimum 6 characters</li>
                                <li>Email: Must be valid email format</li>
                            </ul>
                        </div>
                        
                        <form method="POST" action="" id="registrationForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username *</label>
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                               placeholder="Only letters and underscores" required>
                                        <small class="form-text text-muted">3-20 characters, letters and underscores only</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                               placeholder="Enter your email" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password *</label>
                                        <input type="password" class="form-control" id="password" name="password" 
                                               placeholder="Minimum 6 characters" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirm Password *</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                               placeholder="Confirm your password" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" 
                                               value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" 
                                               placeholder="Letters and spaces only" required>
                                        <small class="form-text text-muted">Letters and spaces only</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" 
                                               placeholder="Enter phone number">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2" 
                                          placeholder="Enter your address"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-register">
                                    <i class="fas fa-user-plus me-2"></i>Create Client Account
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p>Already have an account? <a href="../login.php" class="text-decoration-none">Login here</a></p>
                        </div>
                        
                        <!-- Planner Registration Notice -->
                        <div class="alert alert-info mt-3">
                            <h6><i class="fas fa-briefcase me-2"></i>Want to become an Event Planner?</h6>
                            <p class="mb-0">Event planners need to be approved by administrators. Please contact the admin team to apply for a planner account.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Real-time username validation
        document.getElementById('username').addEventListener('input', function() {
            const username = this.value;
            const isValid = /^[a-zA-Z_]+$/.test(username);
            
            if (username.length > 0) {
                if (!isValid) {
                    this.setCustomValidity('Username can only contain letters and underscores');
                    this.classList.add('is-invalid');
                } else if (username.length < 3) {
                    this.setCustomValidity('Username must be at least 3 characters');
                    this.classList.add('is-invalid');
                } else if (username.length > 20) {
                    this.setCustomValidity('Username cannot exceed 20 characters');
                    this.classList.add('is-invalid');
                } else {
                    this.setCustomValidity('');
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
        
        // Real-time full name validation
        document.getElementById('full_name').addEventListener('input', function() {
            const fullName = this.value;
            const isValid = /^[a-zA-Z\s]+$/.test(fullName);
            
            if (fullName.length > 0) {
                if (!isValid) {
                    this.setCustomValidity('Full name can only contain letters and spaces');
                    this.classList.add('is-invalid');
                } else {
                    this.setCustomValidity('');
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
        
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword.length > 0) {
                if (password !== confirmPassword) {
                    this.setCustomValidity('Passwords do not match');
                    this.classList.add('is-invalid');
                } else {
                    this.setCustomValidity('');
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
        
        // Password validation
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            
            if (password.length > 0) {
                if (password.length < 6) {
                    this.setCustomValidity('Password must be at least 6 characters');
                    this.classList.add('is-invalid');
                } else {
                    this.setCustomValidity('');
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                }
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
    </script>
</body>
</html>
