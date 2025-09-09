<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo 'Unauthorized';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = (int)$_SESSION['user_id'];
    $receiver_id = (int)($_POST['receiver_id'] ?? 0);
    $content = trim($_POST['message'] ?? '');
    $event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : null;

    if ($receiver_id <= 0 || $content === '') {
        $_SESSION['error_message'] = 'Please provide a recipient and a message.';
    } else {
        $stmt = $conn->prepare('INSERT INTO messages (sender_id, receiver_id, message, event_id) VALUES (?, ?, ?, ?)');
        if ($stmt) {
            // Allow null event_id
            if ($event_id) { $stmt->bind_param('iisi', $sender_id, $receiver_id, $content, $event_id); }
            else { $null = null; $stmt->bind_param('iisi', $sender_id, $receiver_id, $content, $null); }
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Message sent successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to send message.';
            }
        } else {
            $_SESSION['error_message'] = 'Database error: ' . $conn->error;
        }
    }

    // Redirect back to referrer or a default
    $redirect = $_SERVER['HTTP_REFERER'] ?? 'messages.php';
    header('Location: ' . $redirect);
    exit();
}

echo 'Invalid request';
?>
