<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/mailer.php';

if (!isset($_SESSION['pending_verify_email'], $_SESSION['pending_verify_user'])) {
	header('Location: login.php');
	exit();
}

$email = $_SESSION['pending_verify_email'];
$userId = (int)$_SESSION['pending_verify_user'];

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$action = $_POST['action'] ?? 'verify';
	if ($action === 'resend') {
		$otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
		$expiresAt = date('Y-m-d H:i:s', time() + 10 * 60);
		$ins = $conn->prepare("INSERT INTO otp_verifications (user_id, email, otp_code, expires_at) VALUES (?,?,?,?)");
		$ins->bind_param('isss', $userId, $email, $otp, $expiresAt);
		if ($ins->execute()) {
			$subject = 'Your OTP Code - Event Management System';
			$body = "Your verification code is: " . $otp . "\n\nThis code expires in 10 minutes.";
			if (send_email($email, '', $subject, nl2br($body))) {
				$success = 'A new OTP has been sent to your email.';
			} else {
				$error = 'Failed to send OTP. Please try again later.';
			}
		} else {
			$error = 'Unable to generate OTP at this time.';
		}
	} else {
		$otp = trim($_POST['otp'] ?? '');
		if (!preg_match('/^\d{6}$/', $otp)) {
			$error = 'Enter a valid 6-digit code.';
		} else {
			// Get latest valid OTP for this user/email
			$stmt = $conn->prepare("SELECT id, otp_code, expires_at, attempts FROM otp_verifications WHERE user_id = ? AND email = ? AND verified_at IS NULL ORDER BY id DESC LIMIT 1");
			$stmt->bind_param('is', $userId, $email);
			$stmt->execute();
			$rec = $stmt->get_result()->fetch_assoc();
			if (!$rec) {
				$error = 'No active OTP found. Please request a new code.';
			} else {
				if (strtotime($rec['expires_at']) < time()) {
					$error = 'OTP expired. Please request a new code.';
				} elseif (hash_equals($rec['otp_code'], $otp)) {
					// Mark verified
					$upd = $conn->prepare("UPDATE otp_verifications SET verified_at = NOW() WHERE id = ?");
					$upd->bind_param('i', $rec['id']);
					$upd->execute();
					
					// Set user verified
					$uv = $conn->prepare("UPDATE users SET email_verified = 1 WHERE id = ?");
					$uv->bind_param('i', $userId);
					$uv->execute();
					
					unset($_SESSION['pending_verify_email'], $_SESSION['pending_verify_user']);
					$_SESSION['success_message'] = 'Your email has been verified. You may now log in.';
					header('Location: login.php');
					exit();
				} else {
					// Increment attempts
					$at = (int)$rec['attempts'] + 1;
					$upd = $conn->prepare("UPDATE otp_verifications SET attempts = ? WHERE id = ?");
					$upd->bind_param('ii', $at, $rec['id']);
					$upd->execute();
					$error = 'Incorrect code. Please try again.';
				}
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
	<title>Verify Email - EMS</title>
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
						<h5 class="mb-0"><i class="fas 	fa-shield-alt me-2"></i>Verify your email</h5>
					</div>
					<div class="card-body">
						<?php if ($error): ?>
							<div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
						<?php endif; ?>
						<?php if ($success): ?>
							<div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
						<?php endif; ?>

                        <?php if (!empty($_SESSION['dev_otp_code'])): ?>
                            <div class="alert alert-warning">
                                <strong>Dev OTP:</strong> <?php echo htmlspecialchars($_SESSION['dev_otp_code']); ?>
                            </div>
                            <?php unset($_SESSION['dev_otp_code']); ?>
                        <?php endif; ?>

						<p>We sent a 6-digit code to <strong><?php echo htmlspecialchars($email); ?></strong>. Enter it below to verify your account.</p>
						<form method="post" class="mb-3">
							<div class="mb-3">
								<label for="otp" class="form-label">Verification code</label>
								<input type="text" class="form-control" id="otp" name="otp" maxlength="6" pattern="\d{6}" required>
							</div>
							<button type="submit" class="btn btn-primary w-100">Verify</button>
						</form>

						<form method="post">
							<input type="hidden" name="action" value="resend">
							<button type="submit" class="btn btn-outline-secondary w-100">Resend code</button>
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
