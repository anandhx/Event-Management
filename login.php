<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

// Flash messages from redirects
if (isset($_SESSION['error_message'])) {
    $error = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    $user_type = $_SESSION['user_type'];
    if ($user_type == 'admin') {
        header('Location: admin/admin_index.php');
    } elseif ($user_type == 'planner') {
        header('Location: planner/planner_index.php');
    } else {
        header('Location: user/user_index.php');
    }
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        // Use the new authentication function
        $user = authenticate($conn, $username, $password);
        
        if ($user) {
            // For planners, enforce approval before logging in
            if ($user['user_type'] == 'planner') {
                $planner_info = getPlannerInfo($conn, $user['id']);
                $status = $planner_info['approval_status'] ?? 'pending';
                if ($status !== 'approved') {
                    if ($status === 'rejected') {
                        $error = 'Your planner account has been rejected. Please contact support.';
                    } else {
                        $error = 'Your planner account is pending approval.';
                    }
                }
            }

            if (empty($error)) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['user_type'] = $user['user_type'];

                // Additional info for planners
                if (isset($planner_info) && $planner_info) {
                    $_SESSION['company_name'] = $planner_info['company_name'] ?? null;
                    $_SESSION['planner_id'] = $planner_info['id'] ?? null;
                }

                // Redirect based on user type
                if ($user['user_type'] == 'admin') {
                    header('Location: admin/admin_index.php');
                } elseif ($user['user_type'] == 'planner') {
                    header('Location: planner/planner_index.php');
                } else {
                    header('Location: user/user_index.php');
                }
                exit();
            }
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

// (Kept) Any other page may still set $_SESSION['error_message'] before reaching here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Event Management System</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .login-form {
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
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .demo-credentials {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-container">
                    <div class="login-header">
                        <h2><i class="fas fa-calendar-check me-2"></i>EMS Login</h2>
                        <p class="mb-0">Event Management System</p>
                    </div>
                    
                    <div class="login-form">
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username or Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                           required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-login">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <p>Don't have an account? <a href="user/register.php" class="text-decoration-none">Register here</a></p>
                        </div>
                        
                        <div class="demo-credentials">
                            <h6 class="text-muted mb-2"><i class="fas fa-info-circle me-2"></i>Demo Credentials:</h6>
                            <div class="row">
                                <div class="col-6">
                                    <small><strong>Admin:</strong><br>admin / admin123</small>
                                </div>
                                <div class="col-6">
                                    <small><strong>Planner:</strong><br>planner1 / admin123</small>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <small><strong>Client:</strong><br>client1 / admin123</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html> 