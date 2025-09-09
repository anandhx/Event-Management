<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

$_SESSION['old_contact'] = [
	'name' => $name,
	'email' => $email,
	'subject' => $subject,
	'message' => $message
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	$_SESSION['error_message'] = 'Invalid request.';
	header('Location: contact.php');
	exit();
}

if ($name === '' || $email === '' || $subject === '' || $message === '') {
	$_SESSION['error_message'] = 'All fields are required.';
	header('Location: contact.php');
	exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	$_SESSION['error_message'] = 'Please enter a valid email address.';
	header('Location: contact.php');
	exit();
}

$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

$stmt = $conn->prepare("INSERT INTO contact_messages (user_id, name, email, subject, message) VALUES (?,?,?,?,?)");
$stmt->bind_param('issss', $userId, $name, $email, $subject, $message);
if ($stmt->execute()) {
	unset($_SESSION['old_contact']);
	$_SESSION['success_message'] = 'Thanks! Your message has been sent.';
} else {
	$_SESSION['error_message'] = 'Failed to submit your message. Please try again later.';
}

header('Location: contact.php');
exit();
