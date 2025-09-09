<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/mailer.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$identifier = trim($_POST['identifier'] ?? '');
	if ($identifier === '') {
		$error = 'Please enter your username or email.';
	} else {
		// Find user by email or username
		$stmt = $conn->prepare("SELECT id, email, username, full_name FROM users WHERE email = ? OR username = ? LIMIT 1");
		$stmt->bind_param('ss', $identifier, $identifier);
		$stmt->execute();
		$res = $stmt->get_result();
		$user = $res->fetch_assoc();
		if (!$user) {
			$error = 'No account found with that username or email.';
		} else {
			// Generate token
			$token = bin2hex(random_bytes(32));
			$expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour
			
			// Insert into password_resets
			$ins = $conn->prepare("INSERT INTO password_resets (user_id, email, token, expires_at) VALUES (?,?,?,?)");
			$ins->bind_param('isss', $user['id'], $user['email'], $token, $expiresAt);
			if ($ins->execute()) {
				// Send email
				$resetLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/reset_password.php?token=' . urlencode($token) . '&email=' . urlencode($user['email']);
				$subject = 'Password Reset Request';
				$body = "Hello " . htmlspecialchars($user['full_name']) . ",\n\nWe received a request to reset your password. Click the link below to reset it:\n\n" . $resetLink . "\n\nIf you did not request this, please ignore this email.";
				
				if (send_email($user['email'], $user['full_name'] ?: $user['username'], $subject, nl2br($body))) {
					$success = 'A password reset link has been sent to your email.';
				} else {
					$error = 'Failed to send reset email. Please contact support.';
				}
			} else {
				$error = 'Unable to create reset request. Try again later.';
			}
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Forgot Password - EMS</title>
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
						<h5 class="mb-0"><i class="fas fa-key me-2"></i>Forgot Password</h5>
					</div>
					<div class="card-body">
						<?php if ($error): ?>
							<div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
						<?php endif; ?>
						<?php if ($success): ?>
							<div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
						<?php endif; ?>
						<form method="post">
							<div class="mb-3">
								<label for="identifier" class="form-label">Username or Email</label>
								<input type="text" class="form-control" id="identifier" name="identifier" required>
							</div>
							<button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
						</form>
						<div class="text-center mt-3">
							<a href="login.php">Back to login</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
