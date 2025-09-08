<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ob_start();

include('../includes/db.php');
session_start();

function redirectToProfileManagement($message = '', $isError = true) {
    if (!empty($message)) {
        if ($isError) { $_SESSION['error_message'] = $message; } else { $_SESSION['success_message'] = $message; }
    }
    header("Location: profile_management.php");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$userId = (int)$_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    $email_pattern = "/^[a-zA-Z0-9]+(?:[._%+-][a-zA-Z0-9]+)*@[a-zA-Z0-9-]+\.[a-zA-Z]{2,}$/";

    if ($full_name === '' || $email === '') {
        redirectToProfileManagement("All required fields must be filled out.");
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $full_name)) {
        redirectToProfileManagement("Full name can only contain alphabets and spaces.");
    } elseif (!preg_match($email_pattern, $email)) {
        redirectToProfileManagement("Invalid email format. Ensure no multiple special characters and proper domain.");
    } elseif ($phone !== '' && !preg_match("/^\d{10}$/", $phone)) {
        redirectToProfileManagement("Phone number must be exactly 10 digits.");
    } elseif ($full_name !== trim($full_name)) {
        redirectToProfileManagement("Full name cannot have leading or trailing white spaces.");
    } else {
        // Persist to DB
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
        if (!$stmt) {
            redirectToProfileManagement('Database error: ' . $conn->error);
        }
        $stmt->bind_param('sssi', $full_name, $email, $phone, $userId);
        if ($stmt->execute()) {
            $_SESSION['full_name'] = $full_name; // keep header greeting updated
            redirectToProfileManagement('Profile updated successfully!', false);
        } else {
            redirectToProfileManagement('Failed to update profile. Please try again.');
        }
    }
}

ob_end_flush();
?>

