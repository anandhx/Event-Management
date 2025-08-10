<?php
session_start();
include '../includes/db.php';

$user_id = $_SESSION['user_id']; // Logged in user

// Dummy messages data for showcase
// In real implementation, this would fetch from database
$dummy_messages = [
    [
        'message_id' => 1,
        'sender_id' => 2,
        'receiver_id' => 1,
        'sender_name' => 'Jane Smith',
        'receiver_name' => 'John Doe',
        'content' => 'Hi John! I have some updates about your wedding event.',
        'timestamp' => '2024-01-15 10:30:00',
        'replied' => false
    ],
    [
        'message_id' => 2,
        'sender_id' => 1,
        'receiver_id' => 2,
        'sender_name' => 'John Doe',
        'receiver_name' => 'Jane Smith',
        'content' => 'Thanks Jane! Looking forward to the updates.',
        'timestamp' => '2024-01-15 11:00:00',
        'replied' => true
    ],
    [
        'message_id' => 3,
        'sender_id' => 2,
        'receiver_id' => 1,
        'sender_name' => 'Jane Smith',
        'receiver_name' => 'John Doe',
        'content' => 'Great! I\'ll send you the detailed plan by tomorrow.',
        'timestamp' => '2024-01-15 11:15:00',
        'replied' => false
    ]
];

// Display dummy messages
if (!empty($dummy_messages)) {
    foreach ($dummy_messages as $row) {
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
?>
