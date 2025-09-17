<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

$error = '';
$success = '';
$errors = [
	'full_name' => '',
	'username' => '',
	'email' => '',
	'phone' => '',
	'address' => '',
	'company_name' => '',
	'specialization' => '',
	'experience_years' => '',
	'location' => '',
	'bio' => '',
	'password' => '',
	'confirm_password' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$full_name = trim($_POST['full_name'] ?? '');
	$username = trim($_POST['username'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$phone = trim($_POST['phone'] ?? '');
	$address = trim($_POST['address'] ?? '');
	$company_name = trim($_POST['company_name'] ?? '');
	$specialization = trim($_POST['specialization'] ?? '');
	$experience_years = trim($_POST['experience_years'] ?? '');
	$location = trim($_POST['location'] ?? '');
	$bio = trim($_POST['bio'] ?? '');
	$password = $_POST['password'] ?? '';
	$confirm_password = $_POST['confirm_password'] ?? '';

	// Validations
	if ($full_name === '' || !preg_match('/^[a-zA-Z\s]+$/', $full_name)) { $errors['full_name'] = 'Full name: letters and spaces only.'; }
	if ($username === '' || !preg_match('/^[a-zA-Z_]{3,20}$/', $username)) { $errors['username'] = 'Username: 3-20 letters/underscores.'; }
	if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors['email'] = 'Enter a valid email.'; }
	if ($phone === '' || !preg_match('/^\d{10}$/', $phone)) { $errors['phone'] = 'Phone must be exactly 10 digits.'; }
	if ($company_name === '' || !preg_match('/^[a-zA-Z\s]+$/', $company_name)) { $errors['company_name'] = 'Company: letters and spaces only.'; }
	if ($specialization === '') { $errors['specialization'] = 'Select specialization.'; }
	if ($experience_years === '' || !preg_match('/^\d+$/', $experience_years)) { $errors['experience_years'] = 'Experience must be a number.'; }
	if ($location === '') { $errors['location'] = 'Location is required.'; }
	if ($bio === '' || strlen($bio) < 10) { $errors['bio'] = 'Bio must be at least 10 characters.'; }
	if ($password === '' || strlen($password) < 8) { $errors['password'] = 'Password must be at least 8 characters.'; }
	if ($confirm_password === '' || $confirm_password !== $password) { $errors['confirm_password'] = 'Passwords do not match.'; }

	$hasErrors = implode('', $errors) !== '';

	if (!$hasErrors) {
		// Uniqueness checks
		$stmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
		$stmt->bind_param('ss', $username, $email);
		$stmt->execute();
		$res = $stmt->get_result();
		if ($res->num_rows > 0) {
			// Refine which field
			$stmt2 = $conn->prepare('SELECT id FROM users WHERE username = ?');
			$stmt2->bind_param('s', $username);
			$stmt2->execute();
			if ($stmt2->get_result()->num_rows > 0) { $errors['username'] = 'Username already exists.'; }
			$stmt2 = $conn->prepare('SELECT id FROM users WHERE email = ?');
			$stmt2->bind_param('s', $email);
			$stmt2->execute();
			if ($stmt2->get_result()->num_rows > 0) { $errors['email'] = 'Email already exists.'; }
		} else {
			$conn->begin_transaction();
			try {
				$hashed = password_hash($password, PASSWORD_DEFAULT);
				$user_type = 'planner';
				$status = 'active';
				$stmt = $conn->prepare('INSERT INTO users (username, email, password, full_name, user_type, phone, address, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
				$stmt->bind_param('ssssssss', $username, $email, $hashed, $full_name, $user_type, $phone, $address, $status);
				if (!$stmt->execute()) { throw new Exception('Failed to create user.'); }
				$user_id = $conn->insert_id;
				$expYears = (int)$experience_years;
				$stmt = $conn->prepare("INSERT INTO planners (user_id, company_name, specialization, experience_years, location, bio, approval_status, availability) VALUES (?, ?, ?, ?, ?, ?, 'pending', 1)");
				$stmt->bind_param('ississ', $user_id, $company_name, $specialization, $expYears, $location, $bio);
				if (!$stmt->execute()) { throw new Exception('Failed to create planner profile.'); }
				$conn->commit();
				$success = 'Planner registration submitted! Await admin approval.';
			} catch (Exception $e) {
				$conn->rollback();
				$error = 'Registration failed: ' . $e->getMessage();
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
    <title>Planner Sign Up - Event Management System</title>
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

        @keyframes gradientWave {
            0% { background-position: 0% 50%; }
            25% { background-position: 100% 50%; }
            50% { background-position: 100% 100%; }
            75% { background-position: 0% 100%; }
            100% { background-position: 0% 50%; }
        }

        /* Enhanced background elements */
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
            background: linear-gradient(45deg, rgba(255,255,255,0.15), rgba(255,255,255,0.05));
            backdrop-filter: blur(3px);
            animation: floatComplex 25s ease-in-out infinite;
        }

        .orb-1 {
            width: 350px;
            height: 350px;
            top: -175px;
            right: -175px;
            animation-delay: 0s;
            background: linear-gradient(45deg, rgba(167, 139, 250, 0.2), rgba(96, 165, 250, 0.1));
        }

        .orb-2 {
            width: 250px;
            height: 250px;
            bottom: -125px;
            left: -125px;
            animation-delay: 8s;
            background: linear-gradient(45deg, rgba(240, 147, 251, 0.2), rgba(245, 87, 108, 0.1));
        }

        .orb-3 {
            width: 200px;
            height: 200px;
            top: 30%;
            left: -100px;
            animation-delay: 16s;
            background: linear-gradient(45deg, rgba(79, 172, 254, 0.2), rgba(0, 242, 254, 0.1));
        }

        .orb-4 {
            width: 180px;
            height: 180px;
            top: 20%;
            right: -90px;
            animation-delay: 12s;
            background: linear-gradient(45deg, rgba(132, 250, 176, 0.2), rgba(143, 211, 244, 0.1));
        }

        @keyframes floatComplex {
            0%, 100% { 
                transform: translate(0, 0) rotate(0deg) scale(1);
                opacity: 0.4;
            }
            25% { 
                transform: translate(50px, -40px) rotate(90deg) scale(1.15);
                opacity: 0.7;
            }
            50% { 
                transform: translate(-30px, 30px) rotate(180deg) scale(0.85);
                opacity: 0.5;
            }
            75% { 
                transform: translate(40px, -20px) rotate(270deg) scale(1.05);
                opacity: 0.6;
            }
        }

        /* Advanced particle system */
        .particles {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            animation: particleFloat 12s linear infinite;
        }

        .particle-small {
            width: 3px;
            height: 3px;
            background: rgba(255, 255, 255, 0.6);
        }

        .particle-medium {
            width: 5px;
            height: 5px;
            background: rgba(167, 139, 250, 0.5);
        }

        .particle-large {
            width: 7px;
            height: 7px;
            background: rgba(79, 172, 254, 0.4);
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) translateX(0) scale(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
                transform: translateY(90vh) translateX(20px) scale(1) rotate(90deg);
            }
            90% {
                opacity: 1;
                transform: translateY(10vh) translateX(-20px) scale(1) rotate(270deg);
            }
            100% {
                transform: translateY(-10vh) translateX(0) scale(0) rotate(360deg);
                opacity: 0;
            }
        }

        /* Main content container */
        .main-content {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 3rem 0;
        }

        .planner-card {
            background: var(--glass-bg);
            backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border);
            border-radius: 28px;
            box-shadow: 
                0 30px 60px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 var(--shadow-light);
            overflow: hidden;
            animation: cardEntrance 1s ease-out;
            position: relative;
        }

        @keyframes cardEntrance {
            0% {
                opacity: 0;
                transform: translateY(40px) scale(0.95);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Enhanced header */
        .card-header {
            background: linear-gradient(135deg, rgba(255,255,255,0.18), rgba(255,255,255,0.08));
            border-bottom: 1px solid rgba(255,255,255,0.15);
            padding: 2.5rem 2rem;
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
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            animation: shimmerFlow 4s ease-in-out infinite;
        }

        @keyframes shimmerFlow {
            0% { left: -100%; }
            50% { left: 100%; }
            100% { left: 100%; }
        }

        .header-icon {
            width: 90px;
            height: 90px;
            margin: 0 auto 1.5rem;
            background: var(--accent-gradient);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            color: white;
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
            animation: iconFloat 3s ease-in-out infinite;
            position: relative;
        }

        .header-icon::after {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 25px;
            padding: 3px;
            background: linear-gradient(45deg, rgba(255,255,255,0.3), transparent, rgba(255,255,255,0.3));
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: exclude;
        }

        @keyframes iconFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(5deg); }
        }

        .card-title {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-light);
            margin-bottom: 0.75rem;
            background: linear-gradient(135deg, #ffffff, #e0e7ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: titlePulse 4s ease-in-out infinite alternate;
        }

        @keyframes titlePulse {
            0% { filter: brightness(1); }
            100% { filter: brightness(1.1); }
        }

        .card-subtitle {
            color: var(--text-dim);
            font-size: 1.1rem;
            font-weight: 500;
        }

        /* Enhanced form styling */
        .card-body {
            padding: 2.5rem;
        }

        .form-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--accent-gradient);
            transform: scaleX(0);
            transition: transform 0.3s ease;
            transform-origin: left;
        }

        .form-section:hover::before {
            transform: scaleX(1);
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-light);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-icon {
            width: 24px;
            height: 24px;
            background: var(--accent-gradient);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            color: white;
        }

        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.6rem;
            color: var(--text-light);
            font-weight: 600;
            font-size: 0.95rem;
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 1rem 1.5rem;
            background: var(--input-bg);
            border: 1px solid var(--input-border);
            border-radius: 16px;
            color: var(--text-light);
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(15px);
            position: relative;
        }

        .form-control::placeholder {
            color: var(--text-dim);
            transition: opacity 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 
                0 0 0 3px rgba(102, 126, 234, 0.15),
                0 10px 30px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
            background: rgba(102, 126, 234, 0.08);
        }

        .form-control:focus::placeholder {
            opacity: 0.4;
        }

        /* Enhanced validation states */
        .form-control.is-valid, .form-select.is-valid {
            border-color: var(--success-color);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
        }

        .form-control.is-invalid, .form-select.is-invalid {
            border-color: var(--error-color);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
            animation: shake 0.3s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Input with icons */
        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-dim);
            transition: all 0.3s ease;
            z-index: 2;
        }

        .form-control:focus + .input-icon,
        .form-control:not(:placeholder-shown) + .input-icon {
            color: #667eea;
            transform: translateY(-50%) scale(1.1);
        }

        .form-control.has-icon, .form-select.has-icon {
            padding-left: 3.5rem;
        }

        /* Select dropdown styling */
        .form-select {
            background-image: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIiIGhlaWdodD0iOCIgdmlld0JveD0iMCAwIDEyIDgiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxwYXRoIGQ9Ik0xIDFMNiA2TDExIDEiIHN0cm9rZT0iI2ZmZmZmZiIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiLz4KPHN2Zz4K");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 12px;
            appearance: none;
        }

        .form-select option {
            background: #1e293b;
            color: var(--text-light);
            padding: 0.5rem;
        }

        /* Textarea specific styling */
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* Enhanced feedback styling */
        .invalid-feedback {
            display: block;
            margin-top: 0.6rem;
            font-size: 0.875rem;
            color: #fca5a5;
            font-weight: 500;
            animation: errorSlideIn 0.3s ease-out;
        }

        @keyframes errorSlideIn {
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
            font-size: 0.85rem;
            margin-top: 0.4rem;
            opacity: 0.8;
        }

        /* Alert styling */
        .alert {
            border: none;
            border-radius: 18px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(15px);
            animation: alertFadeIn 0.6s ease-out;
            position: relative;
            overflow: hidden;
        }

        @keyframes alertFadeIn {
            0% {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .alert::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: currentColor;
            opacity: 0.6;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.25);
            color: #d1fae5;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #fecaca;
        }

        /* Enhanced button styling */
        .btn-register {
            width: 100%;
            padding: 1.25rem 2.5rem;
            background: var(--accent-gradient);
            border: none;
            border-radius: 18px;
            color: white;
            font-weight: 800;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        }

        .btn-register::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.25), transparent 50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-register::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.5) 50%, transparent 70%);
            top: 0;
            left: -100%;
            transition: left 0.6s ease;
        }

        .btn-register:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(102, 126, 234, 0.5);
        }

        .btn-register:hover::before {
            opacity: 1;
        }

        .btn-register:hover::after {
            left: 100%;
        }

        .btn-register:active {
            transform: translateY(-2px);
        }

        /* Loading state */
        .btn-register.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-register.loading::before {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translateY(-50%) rotate(0deg); }
            100% { transform: translateY(-50%) rotate(360deg); }
        }

        /* Navigation links */
        .auth-links {
            text-align: center;
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.15);
        }

        .auth-links a {
            color: var(--text-light);
            text-decoration: none;
            font-weight: 600;
            position: relative;
            transition: all 0.3s ease;
            display: inline-block;
            padding: 0.5rem 1rem;
        }

        .auth-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background: var(--accent-gradient);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .auth-links a:hover {
            color: white;
            transform: translateY(-2px);
        }

        .auth-links a:hover::after {
            width: 100%;
        }

        /* Back button */
        .btn-back {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: var(--text-light);
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateY(-1px);
            text-decoration: none;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .main-content {
                padding: 2rem 0;
            }
            
            .card-header, .card-body {
                padding: 2rem 1.5rem;
            }
            
            .form-section {
                padding: 1.25rem;
            }
            
            .header-icon {
                width: 70px;
                height: 70px;
                font-size: 1.8rem;
            }
            
            .card-title {
                font-size: 1.75rem;
            }
            
            .btn-register {
                padding: 1rem 2rem;
                font-size: 1rem;
            }
        }

        @media (max-width: 576px) {
            .planner-card {
                margin: 0 1rem;
                border-radius: 20px;
            }
            
            .form-group {
                margin-bottom: 1.25rem;
            }
        }

        /* Auto-fill styling */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        textarea:-webkit-autofill,
        select:-webkit-autofill {
            -webkit-text-fill-color: var(--text-light) !important;
            -webkit-box-shadow: 0 0 0px 1000px var(--input-bg) inset !important;
            box-shadow: 0 0 0px 1000px var(--input-bg) inset !important;
            transition: background-color 9999s ease-in-out 0s !important;
        }

        /* Firefox auto-fill */
        input:-moz-autofill {
            background-color: var(--input-bg) !important;
            color: var(--text-light) !important;
        }

        /* Placeholder styling */
        ::placeholder {
            color: var(--text-dim) !important;
            opacity: 1;
        }

        /* Progress indicator */
        .progress-bar {
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            margin: 1.5rem 0;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--accent-gradient);
            border-radius: 2px;
            transition: width 0.3s ease;
            width: 0%;
        }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .form-control, .form-select {
                border-width: 2px;
            }
            
            .btn-register {
                border: 2px solid white;
            }
        }
    </style>
</head>
<body>
    <!-- Background Elements -->
    <div class="bg-elements">
        <div class="floating-orb orb-1"></div>
        <div class="floating-orb orb-2"></div>
        <div class="floating-orb orb-3"></div>
        <div class="floating-orb orb-4"></div>
    </div>

    <!-- Particles -->
    <div class="particles" id="particles"></div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-9">
                    <div class="planner-card">
                        <!-- Header -->
                        <div class="card-header">
                            <div class="header-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <h1 class="card-title">Event Planner Registration</h1>
                            <p class="card-subtitle">Join our platform as a professional event planner</p>
                        </div>

                        <!-- Body -->
                        <div class="card-body">
                            <div class="mb-4">
                                <a href="../index.php" class="btn-back">
                                    <i class="fas fa-arrow-left"></i>
                                    Back to Home
                                </a>
                            </div>

                            <?php if ($error): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($success): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                                </div>
                            <?php endif; ?>

                            <!-- Progress Bar -->
                            <div class="progress-bar">
                                <div class="progress-fill" id="progressFill"></div>
                            </div>

                            <form method="POST" action="../register_backend.php" id="plannerForm" novalidate>
                                <input type="hidden" name="user_type" value="planner">

                                <!-- Personal Information Section -->
                                <div class="form-section" data-section="1">
                                    <div class="section-title">
                                        <div class="section-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        Personal Information
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
                                                           value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" 
                                                           placeholder="Your full name" 
                                                           required>
                                                    <i class="fas fa-user input-icon"></i>
                                                </div>
                                                <?php if ($errors['full_name']): ?>
                                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['full_name']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="username" class="form-label">Username *</label>
                                                <div class="input-wrapper">
                                                    <input type="text" 
                                                           class="form-control has-icon<?php echo $errors['username'] ? ' is-invalid' : ''; ?>" 
                                                           id="username" 
                                                           name="username" 
                                                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                                                           placeholder="Choose username" 
                                                           required>
                                                    <i class="fas fa-at input-icon"></i>
                                                </div>
                                                <small class="form-text">3-20 characters, letters and underscores only</small>
                                                <?php if ($errors['username']): ?>
                                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['username']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email" class="form-label">Email Address *</label>
                                                <div class="input-wrapper">
                                                    <input type="email" 
                                                           class="form-control has-icon<?php echo $errors['email'] ? ' is-invalid' : ''; ?>" 
                                                           id="email" 
                                                           name="email" 
                                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                                           placeholder="your@email.com" 
                                                           required>
                                                    <i class="fas fa-envelope input-icon"></i>
                                                </div>
                                                <?php if ($errors['email']): ?>
                                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone" class="form-label">Phone Number *</label>
                                                <div class="input-wrapper">
                                                    <input type="tel" 
                                                           class="form-control has-icon<?php echo $errors['phone'] ? ' is-invalid' : ''; ?>" 
                                                           id="phone" 
                                                           name="phone" 
                                                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" 
                                                           placeholder="10 digit number" 
                                                           required>
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
                                            <textarea class="form-control has-icon" 
                                                      id="address" 
                                                      name="address" 
                                                      rows="2" 
                                                      placeholder="Your address (optional)"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                                            <i class="fas fa-map-marker-alt input-icon"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Professional Information Section -->
                                <div class="form-section" data-section="2">
                                    <div class="section-title">
                                        <div class="section-icon">
                                            <i class="fas fa-briefcase"></i>
                                        </div>
                                        Professional Information
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="company_name" class="form-label">Company Name *</label>
                                                <div class="input-wrapper">
                                                    <input type="text" 
                                                           class="form-control has-icon<?php echo $errors['company_name'] ? ' is-invalid' : ''; ?>" 
                                                           id="company_name" 
                                                           name="company_name" 
                                                           value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>" 
                                                           placeholder="Your company/brand name" 
                                                           required>
                                                    <i class="fas fa-building input-icon"></i>
                                                </div>
                                                <?php if ($errors['company_name']): ?>
                                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['company_name']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="specialization" class="form-label">Specialization *</label>
                                                <div class="input-wrapper">
                                                    <select class="form-select has-icon<?php echo $errors['specialization'] ? ' is-invalid' : ''; ?>" 
                                                            id="specialization" 
                                                            name="specialization" 
                                                            required>
                                                        <option value="">Choose your specialization</option>
                                                        <?php
                                                        $options = ['Weddings','Corporate Events','Birthday Parties','Anniversaries','Graduations','Other'];
                                                        $selected = $_POST['specialization'] ?? '';
                                                        foreach ($options as $opt) {
                                                            $sel = ($selected === $opt) ? ' selected' : '';
                                                            echo '<option value="' . htmlspecialchars($opt) . '"' . $sel . '>' . htmlspecialchars($opt) . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                    <i class="fas fa-star input-icon"></i>
                                                </div>
                                                <?php if ($errors['specialization']): ?>
                                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['specialization']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="experience_years" class="form-label">Years of Experience *</label>
                                                <div class="input-wrapper">
                                                    <input type="number" 
                                                           class="form-control has-icon<?php echo $errors['experience_years'] ? ' is-invalid' : ''; ?>" 
                                                           id="experience_years" 
                                                           name="experience_years" 
                                                           min="0" 
                                                           max="50" 
                                                           value="<?php echo htmlspecialchars($_POST['experience_years'] ?? ''); ?>" 
                                                           placeholder="e.g., 5" 
                                                           required>
                                                    <i class="fas fa-calendar-alt input-icon"></i>
                                                </div>
                                                <?php if ($errors['experience_years']): ?>
                                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['experience_years']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="location" class="form-label">Service Location *</label>
                                                <div class="input-wrapper">
                                                    <input type="text" 
                                                           class="form-control has-icon<?php echo $errors['location'] ? ' is-invalid' : ''; ?>" 
                                                           id="location" 
                                                           name="location" 
                                                           value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>" 
                                                           placeholder="City, State/Country" 
                                                           required>
                                                    <i class="fas fa-map-pin input-icon"></i>
                                                </div>
                                                <?php if ($errors['location']): ?>
                                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['location']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="bio" class="form-label">Professional Bio *</label>
                                        <div class="input-wrapper">
                                            <textarea class="form-control has-icon<?php echo $errors['bio'] ? ' is-invalid' : ''; ?>" 
                                                      id="bio" 
                                                      name="bio" 
                                                      rows="4" 
                                                      placeholder="Tell clients about your expertise, services, and what makes you unique as an event planner..." 
                                                      required><?php echo htmlspecialchars($_POST['bio'] ?? ''); ?></textarea>
                                            <i class="fas fa-info-circle input-icon"></i>
                                        </div>
                                        <small class="form-text">Minimum 10 characters</small>
                                        <?php if ($errors['bio']): ?>
                                            <div class="invalid-feedback"><?php echo htmlspecialchars($errors['bio']); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Security Section -->
                                <div class="form-section" data-section="3">
                                    <div class="section-title">
                                        <div class="section-icon">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        Account Security
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
                                                           placeholder="Create secure password" 
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
                                                           placeholder="Confirm your password" 
                                                           required>
                                                    <i class="fas fa-shield-alt input-icon"></i>
                                                </div>
                                                <?php if ($errors['confirm_password']): ?>
                                                    <div class="invalid-feedback"><?php echo htmlspecialchars($errors['confirm_password']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn-register" id="submitBtn">
                                    <i class="fas fa-calendar-check me-2"></i>
                                    Create Planner Account
                                </button>

                                <div class="auth-links">
                                    <p style="color: var(--text-dim); margin-bottom: 1rem;">Already have an account?</p>
                                    <a href="../login.php">
                                        <i class="fas fa-sign-in-alt me-2"></i>Sign In Here
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
        // Enhanced particle system
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 20;
            const particleTypes = ['particle-small', 'particle-medium', 'particle-large'];

            for (let i = 0; i < particleCount; i++) {
                setTimeout(() => {
                    const particle = document.createElement('div');
                    const randomType = particleTypes[Math.floor(Math.random() * particleTypes.length)];
                    particle.className = `particle ${randomType}`;
                    particle.style.left = Math.random() * 100 + 'vw';
                    particle.style.animationDelay = Math.random() * 12 + 's';
                    particle.style.animationDuration = (Math.random() * 8 + 8) + 's';
                    particlesContainer.appendChild(particle);

                    // Remove particle after animation
                    setTimeout(() => {
                        if (particle.parentNode) {
                            particle.parentNode.removeChild(particle);
                        }
                    }, 16000);
                }, i * 150);
            }
        }

        // Start particle system
        createParticles();
        setInterval(createParticles, 5000);

        // Form progress tracking
        function updateProgress() {
            const form = document.getElementById('plannerForm');
            const formData = new FormData(form);
            const requiredFields = form.querySelectorAll('[required]');
            let filledFields = 0;

            requiredFields.forEach(field => {
                if (field.value.trim() !== '') {
                    filledFields++;
                }
            });

            const progress = (filledFields / requiredFields.length) * 100;
            document.getElementById('progressFill').style.width = progress + '%';
        }

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
            } else if (input.hasAttribute('required')) {
                input.setCustomValidity('This field is required');
            } else {
                input.setCustomValidity('');
            }
            
            updateProgress();
        }

        // Validation event listeners
        document.getElementById('full_name').addEventListener('input', function() {
            validateField(this, 
                (value) => /^[a-zA-Z\s]+$/.test(value),
                'Full name can only contain letters and spaces'
            );
        });

        document.getElementById('username').addEventListener('input', function() {
            validateField(this, 
                (value) => /^[a-zA-Z_]{3,20}$/.test(value),
                'Username must be 3-20 characters, letters and underscores only'
            );
        });

        document.getElementById('email').addEventListener('input', function() {
            validateField(this,
                (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
                'Please enter a valid email address'
            );
        });

        document.getElementById('phone').addEventListener('input', function() {
            validateField(this,
                (value) => /^\d{10}$/.test(value),
                'Phone number must be exactly 10 digits'
            );
        });

        document.getElementById('company_name').addEventListener('input', function() {
            validateField(this,
                (value) => /^[a-zA-Z\s]+$/.test(value),
                'Company name can only contain letters and spaces'
            );
        });

        document.getElementById('experience_years').addEventListener('input', function() {
            validateField(this,
                (value) => /^\d+$/.test(value) && parseInt(value) >= 0,
                'Experience must be a valid number'
            );
        });

        document.getElementById('bio').addEventListener('input', function() {
            validateField(this,
                (value) => value.length >= 10,
                'Bio must be at least 10 characters long'
            );
        });

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

        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            
            validateField(this,
                (value) => value === password,
                'Passwords do not match'
            );
        });

        // Required field validation
        document.querySelectorAll('[required]').forEach(field => {
            field.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.classList.add('is-invalid');
                    this.setCustomValidity('This field is required');
                } else {
                    this.classList.remove('is-invalid');
                    this.setCustomValidity('');
                }
                updateProgress();
            });

            field.addEventListener('input', updateProgress);
        });

        // Form submission with loading state
        const form = document.getElementById('plannerForm');
        const submitBtn = document.getElementById('submitBtn');

        form.addEventListener('submit', function(e) {
            // Add loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            
            // Change button text
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner me-2"></i>Creating Account...';
            
            // Add a slight delay to show the loading animation
            setTimeout(() => {
                // The form will submit naturally after this timeout
            }, 800);
        });

        // Staggered form section animations
        const sections = document.querySelectorAll('.form-section');
        sections.forEach((section, index) => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(30px)';
            section.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            
            setTimeout(() => {
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            }, 200 + (index * 150));
        });

        // Enhanced input focus animations
        document.querySelectorAll('.form-control, .form-select').forEach(input => {
            input.addEventListener('focus', function() {
                this.closest('.form-group').classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.closest('.form-group').classList.remove('focused');
            });
        });

        // Auto-resize textarea
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
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
            }, 10000);
        }

        // Initial progress update
        updateProgress();

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl + Enter to submit form
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                form.dispatchEvent(new Event('submit'));
            }
        });

        // Header icon easter egg
        let iconClickCount = 0;
        document.querySelector('.header-icon').addEventListener('click', function() {
            iconClickCount++;
            if (iconClickCount === 3) {
                this.style.animation = 'iconFloat 0.5s ease-in-out 5';
                setTimeout(() => {
                    this.style.animation = 'iconFloat 3s ease-in-out infinite';
                }, 2500);
                iconClickCount = 0;
            }
        });

        // Form validation on submit
        form.addEventListener('submit', function(e) {
            let hasErrors = false;
            const requiredFields = this.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (field.value.trim() === '') {
                    field.classList.add('is-invalid');
                    field.setCustomValidity('This field is required');
                    hasErrors = true;
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-calendar-check me-2"></i>Create Planner Account';
                
                // Scroll to first error
                const firstError = this.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    </script>
</body>
</html>