<?php
session_start();

// Store logout message
$_SESSION['success_message'] = 'You have successfully logged out.';

// Destroy the session and clear session data
session_unset();
session_destroy();

// Redirect to the index page
header("Location: index.php");
exit();
?>
