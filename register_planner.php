<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

$error = '';
$success = '';
$errors = [
	'full_name' => '',
	'username' => '',
	'email' => '',
	'phone' => '',
	'address' => '',
	'company_name' => '',
	'specialization' => '',
	'experience_years' => '',
	'location' => '',
	'bio' => '',
	'password' => '',
	'confirm_password' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$full_name = trim($_POST['full_name'] ?? '');
	$username = trim($_POST['username'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$phone = trim($_POST['phone'] ?? '');
	$address = trim($_POST['address'] ?? '');
	$company_name = trim($_POST['company_name'] ?? '');
	$specialization = trim($_POST['specialization'] ?? '');
	$experience_years = trim($_POST['experience_years'] ?? '');
	$location = trim($_POST['location'] ?? '');
	$bio = trim($_POST['bio'] ?? '');
	$password = $_POST['password'] ?? '';
	$confirm_password = $_POST['confirm_password'] ?? '';

	// Validations
	if ($full_name === '' || !preg_match('/^[a-zA-Z\s]+$/', $full_name)) { $errors['full_name'] = 'Full name: letters and spaces only.'; }
	if ($username === '' || !preg_match('/^[a-zA-Z_]{3,20}$/', $username)) { $errors['username'] = 'Username: 3-20 letters/underscores.'; }
	if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors['email'] = 'Enter a valid email.'; }
	if ($phone === '' || !preg_match('/^\d{10}$/', $phone)) { $errors['phone'] = 'Phone must be exactly 10 digits.'; }
	if ($company_name === '' || !preg_match('/^[a-zA-Z\s]+$/', $company_name)) { $errors['company_name'] = 'Company: letters and spaces only.'; }
	if ($specialization === '') { $errors['specialization'] = 'Select specialization.'; }
	if ($experience_years === '' || !preg_match('/^\d+$/', $experience_years)) { $errors['experience_years'] = 'Experience must be a number.'; }
	if ($location === '') { $errors['location'] = 'Location is required.'; }
	if ($bio === '' || strlen($bio) < 10) { $errors['bio'] = 'Bio must be at least 10 characters.'; }
	if ($password === '' || strlen($password) < 8) { $errors['password'] = 'Password must be at least 8 characters.'; }
	if ($confirm_password === '' || $confirm_password !== $password) { $errors['confirm_password'] = 'Passwords do not match.'; }

	$hasErrors = implode('', $errors) !== '';

	if (!$hasErrors) {
		// Uniqueness checks
		$stmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
		$stmt->bind_param('ss', $username, $email);
		$stmt->execute();
		$res = $stmt->get_result();
		if ($res->num_rows > 0) {
			// Refine which field
			$stmt2 = $conn->prepare('SELECT id FROM users WHERE username = ?');
			$stmt2->bind_param('s', $username);
			$stmt2->execute();
			if ($stmt2->get_result()->num_rows > 0) { $errors['username'] = 'Username already exists.'; }
			$stmt2 = $conn->prepare('SELECT id FROM users WHERE email = ?');
			$stmt2->bind_param('s', $email);
			$stmt2->execute();
			if ($stmt2->get_result()->num_rows > 0) { $errors['email'] = 'Email already exists.'; }
		} else {
			$conn->begin_transaction();
			try {
				$hashed = password_hash($password, PASSWORD_DEFAULT);
				$user_type = 'planner';
				$status = 'active';
				$stmt = $conn->prepare('INSERT INTO users (username, email, password, full_name, user_type, phone, address, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
				$stmt->bind_param('ssssssss', $username, $email, $hashed, $full_name, $user_type, $phone, $address, $status);
				if (!$stmt->execute()) { throw new Exception('Failed to create user.'); }
				$user_id = $conn->insert_id;
				$expYears = (int)$experience_years;
				$stmt = $conn->prepare("INSERT INTO planners (user_id, company_name, specialization, experience_years, location, bio, approval_status, availability) VALUES (?, ?, ?, ?, ?, ?, 'pending', 1)");
				$stmt->bind_param('ississ', $user_id, $company_name, $specialization, $expYears, $location, $bio);
				if (!$stmt->execute()) { throw new Exception('Failed to create planner profile.'); }
				$conn->commit();
				$success = 'Planner registration submitted! Await admin approval.';
			} catch (Exception $e) {
				$conn->rollback();
				$error = 'Registration failed: ' . $e->getMessage();
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
    <title>Planner Sign Up - Event Management System</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-9">
                <div class="register-container">
                    <div class="register-header">
                        <h2><i class="fas fa-calendar-check me-2"></i>Planner Sign Up</h2>
                        <p class="mb-0">Join as an Event Planner</p>
                    </div>

                    <div class="register-form">
                        <div class="mb-3">
                            <a href="../index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back Home
                            </a>
                        </div>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Full Name *</label>
                                        <input type="text" name="full_name" class="form-control<?php echo $errors['full_name'] ? ' is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" required>
                                        <?php if ($errors['full_name']): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['full_name']); ?></div><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Username *</label>
                                        <input type="text" name="username" class="form-control<?php echo $errors['username'] ? ' is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                                        <?php if ($errors['username']): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['username']); ?></div><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email *</label>
                                        <input type="email" name="email" class="form-control<?php echo $errors['email'] ? ' is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                        <?php if ($errors['email']): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['email']); ?></div><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Phone *</label>
                                        <input type="tel" name="phone" class="form-control<?php echo $errors['phone'] ? ' is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
                                        <?php if ($errors['phone']): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['phone']); ?></div><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Company Name *</label>
                                        <input type="text" name="company_name" class="form-control<?php echo $errors['company_name'] ? ' is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>" required>
                                        <?php if ($errors['company_name']): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['company_name']); ?></div><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Specialization *</label>
                                        <select name="specialization" class="form-control<?php echo $errors['specialization'] ? ' is-invalid' : ''; ?>" required>
                                            <option value="">Select Specialization</option>
                                            <?php
                                            $options = ['Weddings','Corporate Events','Birthday Parties','Anniversaries','Graduations','Other'];
                                            $selected = $_POST['specialization'] ?? '';
                                            foreach ($options as $opt) {
                                                $sel = ($selected === $opt) ? ' selected' : '';
                                                echo '<option value="' . htmlspecialchars($opt) . '"' . $sel . '>' . htmlspecialchars($opt) . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <?php if ($errors['specialization']): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['specialization']); ?></div><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Years of Experience *</label>
                                        <input type="number" name="experience_years" class="form-control<?php echo $errors['experience_years'] ? ' is-invalid' : ''; ?>" min="0" max="50" value="<?php echo htmlspecialchars($_POST['experience_years'] ?? ''); ?>" required>
                                        <?php if ($errors['experience_years']): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['experience_years']); ?></div><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Location *</label>
                                        <input type="text" name="location" class="form-control<?php echo $errors['location'] ? ' is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>" required>
                                        <?php if ($errors['location']): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['location']); ?></div><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Bio *</label>
                                <textarea name="bio" class="form-control<?php echo $errors['bio'] ? ' is-invalid' : ''; ?>" rows="3" required><?php echo htmlspecialchars($_POST['bio'] ?? ''); ?></textarea>
                                <?php if ($errors['bio']): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['bio']); ?></div><?php endif; ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Password *</label>
                                        <input type="password" name="password" class="form-control<?php echo $errors['password'] ? ' is-invalid' : ''; ?>" required>
                                        <?php if ($errors['password']): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['password']); ?></div><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Confirm Password *</label>
                                        <input type="password" name="confirm_password" class="form-control<?php echo $errors['confirm_password'] ? ' is-invalid' : ''; ?>" required>
                                        <?php if ($errors['confirm_password']): ?><div class="invalid-feedback"><?php echo htmlspecialchars($errors['confirm_password']); ?></div><?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-register">
                                    <i class="fas fa-calendar-check me-2"></i>Create Planner Account
                                </button>
                            </div>
                            <div class="text-center mt-3">
                                <a href="../index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-home me-2"></i>Back Home
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>


