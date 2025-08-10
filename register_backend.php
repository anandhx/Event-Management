<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_type = sanitizeInput($_POST['user_type']);
    
    // Validate user type
    if (!in_array($user_type, ['client', 'planner'])) {
        $error = 'Invalid user type selected.';
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
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($full_name)) {
            $error = 'All required fields must be filled.';
        } elseif (strlen($username) < 3 || strlen($username) > 20) {
            $error = 'Username must be between 3 and 20 characters.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $error = 'Username can only contain letters, numbers, and underscores.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long.';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match.';
        } else {
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error = 'Username or email already exists.';
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
                            $company_name = sanitizeInput($_POST['company_name']);
                            $specialization = sanitizeInput($_POST['specialization']);
                            $experience_years = (int)$_POST['experience_years'];
                            $location = sanitizeInput($_POST['location']);
                            $bio = sanitizeInput($_POST['bio']);
                            
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
if ($error) {
    $_SESSION['error_message'] = $error;
} elseif ($success) {
    $_SESSION['success_message'] = $success;
}

// Redirect back to index page
header('Location: index.php');
exit();
?> 