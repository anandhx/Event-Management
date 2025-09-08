<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

$error = '';
$success = '';
$formErrors = [
	'user_type' => '',
	'username' => '',
	'email' => '',
	'password' => '',
	'confirm_password' => '',
	'full_name' => '',
	'phone' => '',
	'address' => '',
	'company_name' => '',
	'specialization' => '',
	'experience_years' => '',
	'location' => '',
	'bio' => ''
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_type = sanitizeInput($_POST['user_type']);
    
    // Validate user type
    if (!in_array($user_type, ['client', 'planner'])) {
        $formErrors['user_type'] = 'Invalid user type selected.';
    } else {
        // Common validation for both user types
        $username = sanitizeInput($_POST['username']);
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $full_name = sanitizeInput($_POST['full_name']);
        $phone = sanitizeInput($_POST['phone']);
        $address = sanitizeInput($_POST['address']);
        
        // Validation
        if ($username === '') { $formErrors['username'] = 'Username is required.'; }
        elseif (strlen($username) < 3 || strlen($username) > 20) { $formErrors['username'] = 'Username must be 3-20 characters.'; }
        elseif (!preg_match('/^[a-zA-Z_]+$/', $username)) { $formErrors['username'] = 'Only letters and underscores allowed.'; }

        if ($email === '') { $formErrors['email'] = 'Email is required.'; }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $formErrors['email'] = 'Invalid email address.'; }

        if ($password === '') { $formErrors['password'] = 'Password is required.'; }
        elseif (strlen($password) < 8) { $formErrors['password'] = 'At least 8 characters.'; }

        if ($confirm_password === '') { $formErrors['confirm_password'] = 'Please confirm your password.'; }
        elseif ($password !== $confirm_password) { $formErrors['confirm_password'] = 'Passwords do not match.'; }

        if ($full_name === '') { $formErrors['full_name'] = 'Full name is required.'; }
        elseif (!preg_match('/^[a-zA-Z\s]+$/', $full_name)) { $formErrors['full_name'] = 'Letters and spaces only.'; }

        if ($phone !== '' && !preg_match('/^\d{10}$/', $phone)) { $formErrors['phone'] = 'Phone must be exactly 10 digits.'; }

        // Planner-only extra validations
        if ($user_type === 'planner') {
            $company_name = sanitizeInput($_POST['company_name'] ?? '');
            $specialization = sanitizeInput($_POST['specialization'] ?? '');
            $experience_years = isset($_POST['experience_years']) ? trim((string)$_POST['experience_years']) : '';
            $location = sanitizeInput($_POST['location'] ?? '');
            $bio = sanitizeInput($_POST['bio'] ?? '');

            if ($company_name === '') { $formErrors['company_name'] = 'Company name is required.'; }
            elseif (!preg_match('/^[a-zA-Z\s]+$/', $company_name)) { $formErrors['company_name'] = 'Only letters and spaces (no special characters).'; }

            if ($experience_years === '') { $formErrors['experience_years'] = 'Experience years is required.'; }
            elseif (!preg_match('/^\d+$/', $experience_years)) { $formErrors['experience_years'] = 'Experience must be a number.'; }

            if ($bio === '' || strlen($bio) < 10) { $formErrors['bio'] = 'Bio must be at least 10 characters.'; }
        }

        $hasFormErrors = implode('', $formErrors) !== '';

        if (!$hasFormErrors) {
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = 'Username or email already exists.';
                // Assign field-level messages for better UX
                while ($row = $result->fetch_assoc()) {}
                // We cannot know which one conflicts without extra query; check separately
                $stmt2 = $conn->prepare("SELECT id FROM users WHERE username = ?");
                $stmt2->bind_param("s", $username);
                $stmt2->execute();
                if ($stmt2->get_result()->num_rows > 0) { $formErrors['username'] = 'Username already exists.'; }
                $stmt2 = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt2->bind_param("s", $email);
                $stmt2->execute();
                if ($stmt2->get_result()->num_rows > 0) { $formErrors['email'] = 'Email already exists.'; }
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Begin transaction
                $conn->begin_transaction();
                
                try {
                    // Insert into users table
                    $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, user_type, phone, address, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
                    $stmt->bind_param("sssssss", $username, $email, $hashed_password, $full_name, $user_type, $phone, $address);
                    
                    if ($stmt->execute()) {
                        $user_id = $conn->insert_id;
                        
                        // If planner, insert into planners table
                        if ($user_type == 'planner') {
                            // Use already sanitized/validated variables
                            $experience_years = (int)$experience_years;
                            
                            $stmt = $conn->prepare("INSERT INTO planners (user_id, company_name, specialization, experience_years, location, bio, approval_status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
                            $stmt->bind_param("ississ", $user_id, $company_name, $specialization, $experience_years, $location, $bio);
                            
                            if (!$stmt->execute()) {
                                throw new Exception('Failed to create planner profile.');
                            }
                        }
                        
                        // Commit transaction
                        $conn->commit();
                        
                        $success = 'Registration successful! You can now login.';
                        
                        // Redirect to login page after 2 seconds
                        header("refresh:2;url=login.php");
                        
                    } else {
                        throw new Exception('Failed to create user account.');
                    }
                    
                } catch (Exception $e) {
                    // Rollback transaction on error
                    $conn->rollback();
                    $error = 'Registration failed: ' . $e->getMessage();
                }
            }
        }
    }
}

// Store messages in session for display
// Store messages and field-level errors for the form page
if ($error) { $_SESSION['error_message'] = $error; }
if ($success) { $_SESSION['success_message'] = $success; }
$_SESSION['form_errors'] = $formErrors;
$_SESSION['old'] = [
	'user_type' => $user_type ?? '',
	'username' => $username ?? '',
	'email' => $email ?? '',
	'full_name' => $full_name ?? '',
	'phone' => $phone ?? '',
	'address' => $address ?? ''
];

// Redirect appropriately
if ($success) {
    header('Location: login.php');
} else {
    header('Location: user/register.php');
}
exit();
?> 