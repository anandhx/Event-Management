<?php
session_start();
include '../includes/db.php';

$user_id = $_SESSION['user_id']; // Logged in user

$sql = "SELECT m.*, u1.username AS sender_name, u2.username AS receiver_name
        FROM messages m
        JOIN users u1 ON m.sender_id = u1.user_id
        JOIN users u2 ON m.receiver_id = u2.user_id
        WHERE m.receiver_id = ?
        ORDER BY m.timestamp DESC";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param('i', $user_id); // Bind only the receiver_id parameter
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Indicate if the message has been replied to
            $messageClass = $row['replied'] ? 'replied-message' : 'new-message';
            echo '<div class="message-box mb-4 ' . $messageClass . '">';
            echo '<div class="message-meta">';
            echo '<strong>' . htmlspecialchars($row['sender_name']) . '</strong> to <strong>' . htmlspecialchars($row['receiver_name']) . '</strong>';
            echo '<small class="text-muted float-end">' . $row['timestamp'] . '</small>';
            echo '</div>';
            echo '<div class="message-content">' . htmlspecialchars($row['content']) . '</div>';

            if (!$row['replied']) {
                echo '<button class="btn btn-sm btn-primary mt-2 reply-btn" data-sender="' . $row['sender_id'] . '" data-message-id="' . $row['message_id'] . '">Reply</button>';
            }

            echo '</div>';
        }
    } else {
        echo '<p class="text-muted">No messages found.</p>';
    }

    $stmt->close();
}

$conn->close();
?>
