<?php
session_start();

// Destroy the session and clear session data
session_unset();
session_destroy();

// Redirect to the login page with a success message
header("Location: index.php?logout=success");
exit();
?>
