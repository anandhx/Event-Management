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

   
    $sql = "UPDATE users SET contact_info = ".$contact_info ;

    $result = mysqli_query($conn, $sql);


 

    echo "Profile updated!";
}

// Fetch the current contact_info
$stmt = $conn->prepare('SELECT contact_info FROM users WHERE user_id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Check if user data is retrieved
if (!$user) {
    // Handle the case where user data is not found
    $user = ['contact_info' => '']; // Default value or handle the case appropriately
}
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
