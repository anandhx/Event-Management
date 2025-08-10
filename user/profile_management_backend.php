<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start(); // Start output buffering

// Include database connection
include('../includes/db.php');
session_start();

// Helper function for consistent redirection with exit
function redirectToProfileManagement($message = '') {
    if (!empty($message)) {
        $_SESSION['error_message'] = $message;
    }
    header("Location: profile_management.php");
    exit();
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

// Get the logged-in user's username
$username = $_SESSION['username'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and trim spaces
    $full_name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Enhanced email pattern
    $email_pattern = "/^[a-zA-Z0-9]+(?:[._%+-][a-zA-Z0-9]+)*@[a-zA-Z0-9-]+\.[a-zA-Z]{2,}$/";

    // Validate input
    if (empty($full_name) || empty($email)) {
        redirectToProfileManagement("All required fields must be filled out.");
    } 
    elseif (!preg_match("/^[a-zA-Z\s]+$/", $full_name)) {
        redirectToProfileManagement("Full name can only contain alphabets and spaces.");
    } 
    elseif (!preg_match($email_pattern, $email)) {
        redirectToProfileManagement("Invalid email format. Ensure no multiple special characters and proper domain.");
    } 
    elseif (!preg_match("/^\d{10}$/", $phone)) { // Validates 10-digit phone number
        redirectToProfileManagement("Phone number must be exactly 10 digits.");
    } 
    elseif ($full_name !== trim($full_name)) { // Checks for leading/trailing spaces
        redirectToProfileManagement("Full name cannot have leading or trailing white spaces.");
    } else {
        // Simulate profile update for showcase
        // In real implementation, this would update the database
        $_SESSION['success_message'] = "Profile updated successfully!";
        
        // Redirect to profile_management.php and exit script
        header("Location: profile_management.php");
        exit();
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

ob_end_flush(); // Flush the output buffer and send headers
?>
