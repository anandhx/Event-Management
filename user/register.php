<?php
session_start();
require_once '../includes/db.php';

$error = '';
$success = '';
$errors = [
	'username' => '',
	'email' => '',
	'password' => '',
	'confirm_password' => '',
	'full_name' => '',
	'phone' => '',
	'address' => ''
];

// Flash messages from redirects
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// If backend set session-based errors/old input, hydrate them
if (isset($_SESSION['form_errors']) && is_array($_SESSION['form_errors'])) {
	foreach ($errors as $k => $_) {
		if (isset($_SESSION['form_errors'][$k])) {
			$errors[$k] = $_SESSION['form_errors'][$k];
		}
	}
	unset($_SESSION['form_errors']);
}

if (isset($_SESSION['old']) && is_array($_SESSION['old'])) {
	foreach (['username','email','full_name','phone','address'] as $field) {
		if (isset($_SESSION['old'][$field]) && !isset($_POST[$field])) {
			$_POST[$field] = $_SESSION['old'][$field];
		}
	}
	unset($_SESSION['old']);
}

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

    // Field-level validation
    if ($username === '') {
        $errors['username'] = 'Username is required.';
    } elseif (!preg_match('/^[a-zA-Z_]{3,20}$/', $username)) {
        $errors['username'] = '3-20 chars, letters and underscores only.';
    }

    if ($email === '') {
        $errors['email'] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }

    if ($password === '') {
        $errors['password'] = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    }

    if ($confirm_password === '') {
        $errors['confirm_password'] = 'Please confirm your password.';
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }

    if ($full_name === '') {
        $errors['full_name'] = 'Full name is required.';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $full_name)) {
        $errors['full_name'] = 'Letters and spaces only.';
    }

    if ($phone !== '') {
        if (!preg_match('/^\d{10}$/', $phone)) {
            $errors['phone'] = 'Phone must be exactly 10 digits.';
        }
    }

    $hasErrors = implode('', $errors) !== '';

    if (!$hasErrors) {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        if (!$stmt) {
            $error = 'Database error: ' . $conn->error;
        } else {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $errors['username'] = 'Username already exists. Please choose another one.';
            } else {
                // Check if email already exists
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                if (!$stmt) {
                    $error = 'Database error: ' . $conn->error;
                } else {
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    if ($stmt->get_result()->num_rows > 0) {
                        $errors['email'] = 'Email already exists. Please use another email address.';
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
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --glass-bg: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.18);
            --text-light: rgba(255, 255, 255, 0.9);
            --text-dim: rgba(255, 255, 255, 0.6);
            --input-bg: rgba(255, 255, 255, 0.05);
            --input-border: rgba(255, 255, 255, 0.2);
            --success-color: #10b981;
            --error-color: #ef4444;
            --shadow-light: rgba(255, 255, 255, 0.1);
            --shadow-dark: rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #667eea 100%);
            background-size: 400% 400%;
            animation: gradientFlow 15s ease infinite;
            overflow-x: hidden;
            position: relative;
        }

        @keyframes gradientFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Animated background elements */
        .bg-elements {
            position: fixed;
            inset: 0;
            pointer-events: none;
            overflow: hidden;
            z-index: 0;
        }

        .floating-orb {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.03));
            backdrop-filter: blur(2px);
            animation: floatOrb 20s ease-in-out infinite;
        }

        .orb-1 {
            width: 300px;
            height: 300px;
            top: -150px;
            right: -150px;
            animation-delay: 0s;
        }

        .orb-2 {
            width: 200px;
            height: 200px;
            bottom: -100px;
            left: -100px;
            animation-delay: 7s;
        }

        .orb-3 {
            width: 150px;
            height: 150px;
            top: 50%;
            left: -75px;
            animation-delay: 14s;
        }

        @keyframes floatOrb {
            0%, 100% { 
                transform: translate(0, 0) rotate(0deg) scale(1);
                opacity: 0.3;
            }
            33% { 
                transform: translate(30px, -30px) rotate(120deg) scale(1.1);
                opacity: 0.6;
            }
            66% { 
                transform: translate(-20px, 20px) rotate(240deg) scale(0.9);
                opacity: 0.4;
            }
        }

        /* Particle system */
        .particles {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            animation: particleFloat 8s linear infinite;
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) translateX(0) scale(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
                transform: translateY(90vh) translateX(10px) scale(1);
            }
            90% {
                opacity: 1;
                transform: translateY(10vh) translateX(-10px) scale(1);
            }
            100% {
                transform: translateY(-10vh) translateX(0) scale(0);
                opacity: 0;
            }
        }

        /* Main content */
        .main-content {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }

        .register-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.25),
                inset 0 1px 0 var(--shadow-light);
            overflow: hidden;
            animation: cardSlideIn 0.8s ease-out;
            position: relative;
        }

        @keyframes cardSlideIn {
            0% {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Header styling */
        .card-header {
            background: linear-gradient(135deg, rgba(255,255,255,0.15), rgba(255,255,255,0.05));
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            50% { left: 100%; }
            100% { left: 100%; }
        }

        .header-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            background: var(--accent-gradient);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            box-shadow: 0 10px 30px rgba(79, 172, 254, 0.3);
            animation: iconPulse 2s ease-in-out infinite;
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .card-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-light);
            margin-bottom: 0.5rem;
            animation: titleGlow 3s ease-in-out infinite alternate;
        }

        @keyframes titleGlow {
            0% { text-shadow: 0 0 20px rgba(255,255,255,0.1); }
            100% { text-shadow: 0 0 20px rgba(255,255,255,0.3); }
        }

        .card-subtitle {
            color: var(--text-dim);
            font-size: 0.95rem;
        }

        /* Form styling */
        .card-body {
            padding: 2rem;
        }

        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-light);
            font-weight: 600;
            font-size: 0.9rem;
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1.25rem;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 16px;
            color: var(--text-light);
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            position: relative;
        }

        .form-control::placeholder {
            color: var(--text-dim);
            transition: opacity 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #4facfe;
            box-shadow: 
                0 0 0 3px rgba(79, 172, 254, 0.15),
                0 10px 30px rgba(79, 172, 254, 0.1);
            transform: translateY(-2px);
            background: rgba(79, 172, 254, 0.05);
        }

        .form-control:focus::placeholder {
            opacity: 0.3;
        }

        .form-control.is-valid {
            border-color: var(--success-color);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }

        .form-control.is-invalid {
            border-color: var(--error-color);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
        }

        /* Input animations */
        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-dim);
            transition: all 0.3s ease;
            z-index: 2;
        }

        .form-control:focus + .input-icon,
        .form-control:not(:placeholder-shown) + .input-icon {
            color: #4facfe;
            transform: translateY(-50%) scale(1.1);
        }

        .form-control.has-icon {
            padding-left: 3rem;
        }

        /* Feedback styling */
        .invalid-feedback {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: #fca5a5;
            animation: errorSlide 0.3s ease-out;
        }

        @keyframes errorSlide {
            0% {
                opacity: 0;
                transform: translateY(-10px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-text {
            color: var(--text-dim);
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }

        /* Alert styling */
        .alert {
            border: none;
            border-radius: 16px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
            animation: alertSlide 0.5s ease-out;
        }

        @keyframes alertSlide {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #d1fae5;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fecaca;
        }

        /* Button styling */
        .btn-register {
            width: 100%;
            padding: 1rem 2rem;
            background: var(--accent-gradient);
            border: none;
            border-radius: 16px;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(79, 172, 254, 0.3);
        }

        .btn-register::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.2), transparent 50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-register:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(79, 172, 254, 0.4);
        }

        .btn-register:hover::before {
            opacity: 1;
        }

        .btn-register:active {
            transform: translateY(-1px);
        }

        /* Loading state */
        .btn-register.loading {
            pointer-events: none;
        }

        .btn-register.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translateY(-50%) rotate(0deg); }
            100% { transform: translateY(-50%) rotate(360deg); }
        }

        /* Links */
        .auth-links {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .auth-links a {
            color: var(--text-light);
            text-decoration: none;
            font-weight: 600;
            position: relative;
            transition: all 0.3s ease;
        }

        .auth-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 50%;
            background: var(--accent-gradient);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .auth-links a:hover {
            color: white;
            transform: translateY(-1px);
        }

        .auth-links a:hover::after {
            width: 100%;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .card-header, .card-body {
                padding: 1.5rem;
            }
            
            .header-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
            
            .card-title {
                font-size: 1.5rem;
            }
        }

        /* Auto-fill styling */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-text-fill-color: var(--text-light) !important;
            -webkit-box-shadow: 0 0 0px 1000px var(--input-bg) inset !important;
            transition: background-color 5000s ease-in-out 0s !important;
        }
    </style>
</head>
<body>
    <!-- Background Elements -->
    <div class="bg-elements">
        <div class="floating-orb orb-1"></div>
        <div class="floating-orb orb-2"></div>
        <div class="floating-orb orb-3"></div>
    </div>

    <!-- Particles -->
    <div class="particles" id="particles"></div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    <div class="register-card">
                        <!-- Header -->
                        <div class="card-header">
                            <div class="header-icon">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <h1 class="card-title">Create Account</h1>
                            <p class="card-subtitle">Join us and start managing your events</p>
                        </div>

                        <!-- Body -->
                        <div class="card-body">
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

                            <form method="POST" action="../register_backend.php" id="registrationForm" novalidate>
                                <input type="hidden" name="user_type" value="client">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username" class="form-label">Username *</label>
                                            <div class="input-wrapper">
                                                <input type="text" 
                                                       class="form-control has-icon<?php echo $errors['username'] ? ' is-invalid' : ''; ?>" 
                                                       id="username" 
                                                       name="username" 
                                                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                                       placeholder="Enter username" 
                                                       required>
                                                <i class="fas fa-user input-icon"></i>
                                            </div>
                                            <small class="form-text">3-20 characters, letters and underscores only</small>
                                            <?php if ($errors['username']): ?>
                                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['username']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="form-label">Email Address *</label>
                                            <div class="input-wrapper">
                                                <input type="email" 
                                                       class="form-control has-icon<?php echo $errors['email'] ? ' is-invalid' : ''; ?>" 
                                                       id="email" 
                                                       name="email" 
                                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                                       placeholder="your@email.com" 
                                                       required>
                                                <i class="fas fa-envelope input-icon"></i>
                                            </div>
                                            <?php if ($errors['email']): ?>
                                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password" class="form-label">Password *</label>
                                            <div class="input-wrapper">
                                                <input type="password" 
                                                       class="form-control has-icon<?php echo $errors['password'] ? ' is-invalid' : ''; ?>" 
                                                       id="password" 
                                                       name="password" 
                                                       placeholder="Create password" 
                                                       required>
                                                <i class="fas fa-lock input-icon"></i>
                                            </div>
                                            <small class="form-text">Minimum 8 characters</small>
                                            <?php if ($errors['password']): ?>
                                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['password']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="confirm_password" class="form-label">Confirm Password *</label>
                                            <div class="input-wrapper">
                                                <input type="password" 
                                                       class="form-control has-icon<?php echo $errors['confirm_password'] ? ' is-invalid' : ''; ?>" 
                                                       id="confirm_password" 
                                                       name="confirm_password" 
                                                       placeholder="Confirm password" 
                                                       required>
                                                <i class="fas fa-shield-alt input-icon"></i>
                                            </div>
                                            <?php if ($errors['confirm_password']): ?>
                                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['confirm_password']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="full_name" class="form-label">Full Name *</label>
                                            <div class="input-wrapper">
                                                <input type="text" 
                                                       class="form-control has-icon<?php echo $errors['full_name'] ? ' is-invalid' : ''; ?>" 
                                                       id="full_name" 
                                                       name="full_name" 
                                                       value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" 
                                                       placeholder="Your full name" 
                                                       required>
                                                <i class="fas fa-id-card input-icon"></i>
                                            </div>
                                            <small class="form-text">Letters and spaces only</small>
                                            <?php if ($errors['full_name']): ?>
                                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['full_name']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <div class="input-wrapper">
                                                <input type="tel" 
                                                       class="form-control has-icon<?php echo $errors['phone'] ? ' is-invalid' : ''; ?>" 
                                                       id="phone" 
                                                       name="phone" 
                                                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" 
                                                       placeholder="10 digit number">
                                                <i class="fas fa-phone input-icon"></i>
                                            </div>
                                            <?php if ($errors['phone']): ?>
                                                <div class="invalid-feedback"><?php echo htmlspecialchars($errors['phone']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="address" class="form-label">Address</label>
                                    <div class="input-wrapper">
                                        <textarea class="form-control has-icon<?php echo $errors['address'] ? ' is-invalid' : ''; ?>" 
                                                  id="address" 
                                                  name="address" 
                                                  rows="3" 
                                                  placeholder="Your address (optional)"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                                        <i class="fas fa-map-marker-alt input-icon"></i>
                                    </div>
                                    <?php if ($errors['address']): ?>
                                        <div class="invalid-feedback"><?php echo htmlspecialchars($errors['address']); ?></div>
                                    <?php endif; ?>
                                </div>

                                <button type="submit" class="btn-register" id="submitBtn">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Create My Account
                                </button>

                                <div class="auth-links">
                                    <p class="mb-2" style="color: var(--text-dim);">Already have an account?</p>
                                    <a href="../login.php">
                                        <i class="fas fa-sign-in-alt me-2"></i>Sign In Here
                                    </a>
                                    <br><br>
                                    <a href="../index.php">
                                        <i class="fas fa-home me-2"></i>Back to Home
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Particle system
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 15;

            for (let i = 0; i < particleCount; i++) {
                setTimeout(() => {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    particle.style.left = Math.random() * 100 + 'vw';
                    particle.style.animationDelay = Math.random() * 8 + 's';
                    particle.style.animationDuration = (Math.random() * 6 + 6) + 's';
                    particlesContainer.appendChild(particle);

                    // Remove particle after animation
                    setTimeout(() => {
                        if (particle.parentNode) {
                            particle.parentNode.removeChild(particle);
                        }
                    }, 12000);
                }, i * 200);
            }
        }

        // Start particle system
        createParticles();
        setInterval(createParticles, 4000);

        // Form validation with animations
        const form = document.getElementById('registrationForm');
        const submitBtn = document.getElementById('submitBtn');

        // Real-time validation functions
        function validateField(input, validationFn, errorMessage) {
            const value = input.value.trim();
            const isValid = validationFn(value);
            
            input.classList.remove('is-valid', 'is-invalid');
            
            if (value) {
                if (isValid) {
                    input.classList.add('is-valid');
                    input.setCustomValidity('');
                } else {
                    input.classList.add('is-invalid');
                    input.setCustomValidity(errorMessage);
                }
            } else {
                input.setCustomValidity('');
            }
        }

        // Username validation
        document.getElementById('username').addEventListener('input', function() {
            validateField(this, 
                (value) => /^[a-zA-Z_]{3,20}$/.test(value),
                'Username must be 3-20 characters, letters and underscores only'
            );
        });

        // Email validation
        document.getElementById('email').addEventListener('input', function() {
            validateField(this,
                (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
                'Please enter a valid email address'
            );
        });

        // Password validation
        document.getElementById('password').addEventListener('input', function() {
            const confirmPassword = document.getElementById('confirm_password');
            
            validateField(this,
                (value) => value.length >= 8,
                'Password must be at least 8 characters'
            );

            // Also validate confirm password if it has a value
            if (confirmPassword.value) {
                validateField(confirmPassword,
                    (value) => value === this.value,
                    'Passwords do not match'
                );
            }
        });

        // Confirm password validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            
            validateField(this,
                (value) => value === password,
                'Passwords do not match'
            );
        });

        // Full name validation
        document.getElementById('full_name').addEventListener('input', function() {
            validateField(this,
                (value) => /^[a-zA-Z\s]+$/.test(value),
                'Full name can only contain letters and spaces'
            );
        });

        // Phone validation (optional field)
        document.getElementById('phone').addEventListener('input', function() {
            if (this.value.trim()) {
                validateField(this,
                    (value) => /^\d{10}$/.test(value),
                    'Phone number must be exactly 10 digits'
                );
            } else {
                this.classList.remove('is-valid', 'is-invalid');
                this.setCustomValidity('');
            }
        });

        // Enhanced form submission with loading state
        form.addEventListener('submit', function(e) {
            // Add loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            // Add a slight delay to show the loading animation
            setTimeout(() => {
                // The form will submit naturally after this timeout
            }, 500);
        });

        // Input focus animations
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });

        // Intersection Observer for form animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationDelay = Math.random() * 0.3 + 's';
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        // Observe form groups for staggered animations
        document.querySelectorAll('.form-group').forEach((group, index) => {
            group.style.opacity = '0';
            group.style.transform = 'translateY(20px)';
            group.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            
            setTimeout(() => {
                group.style.opacity = '1';
                group.style.transform = 'translateY(0)';
            }, 100 + (index * 100));
        });

        // Password strength indicator (visual feedback)
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            updatePasswordStrengthIndicator(this, strength);
        });

        function calculatePasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            return strength;
        }

        function updatePasswordStrengthIndicator(input, strength) {
            // Remove existing strength classes
            input.classList.remove('strength-weak', 'strength-medium', 'strength-strong');
            
            if (input.value.length > 0) {
                if (strength < 2) {
                    input.classList.add('strength-weak');
                } else if (strength < 4) {
                    input.classList.add('strength-medium');
                } else {
                    input.classList.add('strength-strong');
                }
            }
        }

        // Add some easter eggs for user engagement
        let clickCount = 0;
        document.querySelector('.header-icon').addEventListener('click', function() {
            clickCount++;
            if (clickCount === 5) {
                this.style.animation = 'iconPulse 0.5s ease-in-out 3';
                setTimeout(() => {
                    this.style.animation = 'iconPulse 2s ease-in-out infinite';
                }, 1500);
                clickCount = 0;
            }
        });

        // Auto-resize textarea
        const textarea = document.getElementById('address');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });

        // Prevent form resubmission on page reload
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Alt + Enter to submit form
            if (e.altKey && e.key === 'Enter') {
                e.preventDefault();
                form.dispatchEvent(new Event('submit'));
            }
        });

        // Success message auto-hide
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'all 0.5s ease-out';
                successAlert.style.transform = 'translateY(-20px)';
                successAlert.style.opacity = '0';
                setTimeout(() => {
                    if (successAlert.parentNode) {
                        successAlert.parentNode.removeChild(successAlert);
                    }
                }, 500);
            }, 8000);
        }
    </script>

    <style>
        /* Additional CSS for password strength indicator */
        .form-control.strength-weak {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
            border-color: #ef4444 !important;
        }

        .form-control.strength-medium {
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15) !important;
            border-color: #f59e0b !important;
        }

        .form-control.strength-strong {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
            border-color: #10b981 !important;
        }

        /* Textarea specific styling */
        textarea.form-control {
            resize: vertical;
            min-height: 80px;
        }

        /* Enhanced button states */
        .btn-register:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Mobile responsiveness improvements */
        @media (max-width: 576px) {
            .main-content {
                padding: 1rem 0;
            }
            
            .register-card {
                margin: 0 0.5rem;
            }
            
            .form-group {
                margin-bottom: 1.25rem;
            }
            
            .btn-register {
                padding: 0.875rem 1.5rem;
                font-size: 1rem;
            }
            
            .card-title {
                font-size: 1.375rem;
            }
        }

        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .form-control {
                border-width: 2px;
            }
            
            .btn-register {
                border: 2px solid white;
            }
        }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</body>
</html>