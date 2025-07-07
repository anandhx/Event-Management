<?php
session_start();
include '../includes/db.php';

$user_id = $_SESSION['user_id']; // Logged in user

$sql = "SELECT user_id, username FROM users WHERE user_id != ?"; // Exclude the logged-in user
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['user_id'] . '">' . htmlspecialchars($row['username']) . '</option>';
    }

    $stmt->close();
}

$conn->close();
?>
