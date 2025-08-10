<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_SESSION['user_id']; // Assuming the sender is logged in
    $receiver_id = $_POST['receiver_id'];
    $content = $_POST['message'];

    // Simulate message sending for showcase
    // In real implementation, this would insert into database
    echo "Message sent successfully!";
}
?>
