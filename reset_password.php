<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';
$validRequest = false;

if ($token && $email) {
	// Validate token
	$stmt = $conn->prepare("SELECT pr.id, pr.user_id, pr.token, pr.expires_at, pr.used_at, u.email FROM password_resets pr JOIN users u ON pr.user_id = u.id WHERE pr.token = ? AND pr.email = ? LIMIT 1");
	$stmt->bind_param('ss', $token, $email);
	$stmt->execute();
	$reset = $stmt->get_result()->fetch_assoc();
	if ($reset) {
		if (!empty($reset['used_at'])) {
			$error = 'This reset link has already been used.';
		} elseif (strtotime($reset['expires_at']) < time()) {
			$error = 'This reset link has expired.';
		} else {
			$validRequest = true;
		}
	} else {
		$error = 'Invalid reset link.';
	}
} else {
	$error = 'Invalid reset request.';
}

if ($validRequest && $_SERVER['REQUEST_METHOD'] === 'POST') {
	$newPassword = $_POST['password'] ?? '';
	$confirmPassword = $_POST['confirm_password'] ?? '';
	
	// Basic validation
	if (strlen($newPassword) < 8) {
		$error = 'Password must be at least 8 characters.';
	} elseif ($newPassword !== $confirmPassword) {
		$error = 'Passwords do not match.';
	} else {
		// Update user password
		$hash = password_hash($newPassword, PASSWORD_DEFAULT);
		$upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
		$upd->bind_param('si', $hash, $reset['user_id']);
		if ($upd->execute()) {
			// Mark token used
			$used = $conn->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = ?");
			$used->bind_param('i', $reset['id']);
			$used->execute();
			
			$_SESSION['success_message'] = 'Your password has been reset. Please log in.';
			header('Location: login.php');
			exit();
		} else {
			$error = 'Failed to update password. Please try again.';
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Reset Password - EMS</title>
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/css/style.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
	<div class="container py-5">
		<div class="row justify-content-center">
			<div class="col-md-6 col-lg-5">
				<div class="card shadow-sm">
					<div class="card-header bg-primary text-white">
						<h5 class="mb-0"><i class="fas fa-unlock-alt me-2"></i>Reset Password</h5>
					</div>
					<div class="card-body">
						<?php if ($error): ?>
							<div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
						<?php endif; ?>
						<?php if ($success): ?>
							<div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
						<?php endif; ?>

						<?php if ($validRequest): ?>
						<form method="post">
							<div class="mb-3">
								<label for="password" class="form-label">New Password</label>
								<input type="password" class="form-control" id="password" name="password" required minlength="8">
							</div>
							<div class="mb-3">
								<label for="confirm_password" class="form-label">Confirm Password</label>
								<input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
							</div>
							<button type="submit" class="btn btn-primary w-100">Update Password</button>
						</form>
						<?php else: ?>
						<div class="text-center">
							<a class="btn btn-outline-primary" href="forgot_password.php">Request a new link</a>
							<a class="btn btn-link" href="login.php">Back to login</a>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
