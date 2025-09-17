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
            // Ensure email verified
            $emailVerifiedOk = true;
            $colCheck = $conn->query("SHOW COLUMNS FROM users LIKE 'email_verified'");
            if ($colCheck && $colCheck->num_rows > 0) {
                $verStmt = $conn->prepare("SELECT email_verified FROM users WHERE id = ?");
                if ($verStmt) {
                    $verStmt->bind_param('i', $user['id']);
                    $verStmt->execute();
                    $verRes = $verStmt->get_result()->fetch_assoc();
                    $emailVerifiedOk = ((int)($verRes['email_verified'] ?? 0) === 1);
                } else {
                    $emailVerifiedOk = true; // fail open to avoid blocking login due to schema mismatch
                }
            }
            if (!$emailVerifiedOk) {
                $error = 'Please verify your email before logging in.';
            } else {
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
        :root { --bg-1:#6a11cb; --bg-2:#2575fc; --glass-bg:rgba(255,255,255,0.1); --glass-stroke:rgba(255,255,255,0.35); --text-on-dark:#ffffff; --input-border:rgba(255,255,255,0.35); --input-focus:#a2b6ff; }
        body {
            min-height: 100vh;
            padding: 40px 0;
            color: var(--text-on-dark);
            background: linear-gradient(120deg, var(--bg-1), var(--bg-2));
            background-size: 200% 200%;
            animation: gradientShift 12s ease infinite;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            display: flex;
            align-items: center;
        }
        @keyframes gradientShift{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .floating-shapes span{position:absolute;display:block;width:180px;height:180px;background:radial-gradient(circle at 30% 30%,rgba(255,255,255,.45),rgba(255,255,255,.1));filter:blur(2px);border-radius:50%;animation:float 18s ease-in-out infinite;mix-blend-mode:screen}
        .floating-shapes span:nth-child(1){top:-40px;left:-40px;animation-delay:0s}
        .floating-shapes span:nth-child(2){bottom:-60px;right:-60px;animation-delay:4s;width:240px;height:240px}
        .floating-shapes span:nth-child(3){top:50%;left:-80px;animation-delay:8s;width:200px;height:200px}
        @keyframes float{0%,100%{transform:translateY(0) translateX(0) scale(1)}50%{transform:translateY(-25px) translateX(15px) scale(1.05)}}
        .login-container { backdrop-filter: blur(14px); background: var(--glass-bg); border-radius: 24px; border: 1px solid var(--glass-stroke); box-shadow: 0 20px 60px rgba(0,0,0,0.35); overflow: hidden; }
        .login-header { position: relative; background: linear-gradient(135deg, rgba(255,255,255,0.25), rgba(255,255,255,0.05)); color: var(--text-on-dark); padding: 28px 30px; text-align: center; }
        .login-header h2{letter-spacing:.5px;margin-bottom:6px}
        .login-header p{opacity:.85;margin:0}
        .header-glow{position:absolute;inset:0;pointer-events:none;background:radial-gradient(650px 150px at 50% -20%,rgba(255,255,255,.35),transparent 60%)}
        .login-form { padding: 34px 40px 40px; }
        .form-label{color:#e9eefb;font-weight:600}
        .form-control{color:#e8eeff;background:rgba(255,255,255,.06);border-radius:12px;border:1px solid var(--input-border);padding:12px 14px;transition:border-color .25s ease,box-shadow .25s ease,transform .12s ease}
        .form-control::placeholder{color:rgba(233,238,251,.65)}
        .form-control:focus{border-color:var(--input-focus);box-shadow:0 0 0 .25rem rgba(162,182,255,.25);transform:translateY(-1px)}
        .input-group-text{background:rgba(255,255,255,.08); border:1px solid var(--input-border); color:#e8eeff; border-right:0; border-radius:12px 0 0 12px}
        .input-group .form-control{border-left:0;border-radius:0 12px 12px 0}
        .btn-login{background:linear-gradient(135deg,#a78bfa,#60a5fa);border:none;border-radius:12px;padding:12px 28px;font-weight:700;letter-spacing:.2px;transition:transform .15s ease,box-shadow .25s ease,filter .25s ease;box-shadow:0 12px 30px rgba(96,165,250,.35)}
        .btn-login:hover{transform:translateY(-2px);filter:brightness(1.04)}
        .btn-login:active{transform:translateY(0)}
        /* Prevent white background on focus/autofill */
        .form-control,.form-control:focus,.form-control:active{background:rgba(255,255,255,.06)!important;color:#e8eeff!important;caret-color:#e8eeff}
        input:-webkit-autofill,input:-webkit-autofill:hover,input:-webkit-autofill:focus,textarea:-webkit-autofill,select:-webkit-autofill{-webkit-text-fill-color:#e8eeff!important;-webkit-box-shadow:0 0 0px 1000px rgba(255,255,255,.06) inset!important;box-shadow:0 0 0px 1000px rgba(255,255,255,.06) inset!important;transition:background-color 9999s ease-in-out 0s}
        input:-moz-autofill{background-color:rgba(255,255,255,.06)!important;color:#e8eeff!important}
    </style>
</head>
<body>
    <div class="container position-relative" style="z-index:2;">
        <div class="floating-shapes" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-container">
                    <div class="login-header">
                        <div class="header-glow"></div>
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
                            <a href="forgot_password.php" class="text-decoration-none">Forgot password?</a>
                        </div>
                        
                        <div class="text-center mt-3">
                            <p>Don't have an account? <a href="user/register.php" class="text-decoration-none">Register here</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html> 