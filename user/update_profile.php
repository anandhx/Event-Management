<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $contact_info = $_POST['contact_info'];

    // Simulate profile update for showcase
    // In real implementation, this would update the database
    echo "Profile updated!";
}

// Dummy user data for showcase
// In real implementation, this would fetch from database
$user = ['contact_info' => 'john@example.com'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Profile</title>
</head>
<body>
    <h1>Update Profile</h1>
    <form method="post">
        <label for="contact_info">Contact Info:</label>
        <input type="text" name="contact_info" id="contact_info" value="<?php echo htmlspecialchars($user['contact_info']); ?>" required>
        <button type="submit" name="update_profile">Update</button>
    </form>
</body>
</html>
