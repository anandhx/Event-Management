<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'admin') {
	header('Location: ../login.php');
	exit();
}

header('Location: admin_index.php');
exit();
