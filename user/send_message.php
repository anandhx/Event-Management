<?php
session_start();
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_SESSION['user_id']; // Assuming the sender is logged in
    $receiver_id = $_POST['receiver_id'];
    $content = $_POST['message'];

    // Insert the message into the database
    $sql = "INSERT INTO messages (sender_id, receiver_id, content, timestamp) VALUES (?, ?, ?, NOW())";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('iis', $sender_id, $receiver_id, $content);
        if ($stmt->execute()) {
            echo "Message sent successfully!";
            $message_id = $stmt->insert_id; // Get the ID of the newly inserted message
            
            // Check if this is a reply to an original message
            if (isset($_POST['original_message_id']) && !empty($_POST['original_message_id'])) {
                $original_message_id = $_POST['original_message_id']; // Get the original message ID

                // Update the original message to mark it as replied
                $update_sql = "UPDATE messages SET replied = 1 WHERE message_id = ?";
                if ($update_stmt = $conn->prepare($update_sql)) {
                    $update_stmt->bind_param('i', $original_message_id);
                    $update_stmt->execute();
                    $update_stmt->close();
                }
            }
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }

    $conn->close();
}
?>
