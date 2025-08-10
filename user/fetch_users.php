<?php
session_start();
include '../includes/db.php';

$user_id = $_SESSION['user_id']; // Logged in user

// Dummy users data for showcase
// In real implementation, this would fetch from database
$dummy_users = [
    ['user_id' => 2, 'username' => 'Jane Smith'],
    ['user_id' => 3, 'username' => 'Mike Wilson'],
    ['user_id' => 4, 'username' => 'Sarah Johnson'],
    ['user_id' => 5, 'username' => 'David Lee']
];

// Display dummy users (excluding the logged-in user)
foreach ($dummy_users as $user) {
    if ($user['user_id'] != $user_id) {
        echo '<option value="' . $user['user_id'] . '">' . htmlspecialchars($user['username']) . '</option>';
    }
}
?>
