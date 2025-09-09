<?php
// Event Management System - Run Idempotent Schema with UI
// Ensures database exists and executes database_setup.sql

$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$dbName = getenv('DB_NAME') ?: 'event_management_system';
$schemaPath = __DIR__ . DIRECTORY_SEPARATOR . 'database_setup.sql';

header('Content-Type: text/html; charset=utf-8');

echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Run Schema</title>';
echo '<style>body{font-family:Arial,sans-serif;background:#f5f5f5;padding:20px;max-width:860px;margin:0 auto}';
echo '.card{background:#fff;border:1px solid #eee;border-radius:8px;padding:16px;margin:12px 0}';
echo '.ok{color:#0a7d16}.err{color:#b00020}.warn{color:#b36b00}.muted{color:#666}.btn{display:inline-block;background:#1b74e4;color:#fff;padding:8px 12px;border-radius:6px;text-decoration:none;margin-top:8px}</style>';
echo '</head><body>';
echo '<h2>Event Management System - Apply Idempotent Schema</h2>';

echo '<div class="card">';
echo '<div><strong>Connection</strong></div>';
echo '<div class="muted">Host: ' . htmlspecialchars($host) . ' | User: ' . htmlspecialchars($username) . ' | DB: ' . htmlspecialchars($dbName) . '</div>';
echo '</div>';

try {
	if (!file_exists($schemaPath)) {
		throw new RuntimeException('Schema file not found: ' . $schemaPath);
	}

	$dsn = 'mysql:host=' . $host . ';charset=utf8mb4';
	$options = [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false,
	];
	$pdo = new PDO($dsn, $username, $password, $options);
	echo '<div class="card ok">Connected to MySQL server</div>';

	// Ensure database exists
	$pdo->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`','``',$dbName) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
	echo '<div class="card ok">Database ensured: ' . htmlspecialchars($dbName) . '</div>';

	$pdo->exec('USE `' . str_replace('`','``',$dbName) . '`');

	// Load and execute schema file as a whole
	$schemaSql = file_get_contents($schemaPath);
	if ($schemaSql === false) {
		throw new RuntimeException('Failed to read schema file');
	}

	// Execute statements one-by-one and ignore idempotent errors
	$removeBlockComments = function($sql) {
		return preg_replace('/\/\*.*?\*\//s', '', $sql);
	};
	$schemaSql = $removeBlockComments($schemaSql);
	$lines = preg_split('/\r?\n/', $schemaSql);
	$cleanLines = [];
	foreach ($lines as $line) {
		$trim = ltrim($line);
		if (strpos($trim, '--') === 0) { continue; }
		$cleanLines[] = $line;
	}
	$schemaSql = implode("\n", $cleanLines);

	$statements = array_filter(array_map('trim', preg_split('/;\s*(?:\r?\n|$)/m', $schemaSql)));
	$executed = 0; $warnings = 0; $errors = [];
	$ignorable = [1050,1061,1062,1091];

	foreach ($statements as $stmt) {
		if ($stmt === '') { continue; }
		try {
			$pdo->exec($stmt);
			$executed++;
		} catch (PDOException $e) {
			$code = (int)($e->errorInfo[1] ?? 0);
			if (in_array($code, $ignorable, true)) {
				$warnings++;
				continue;
			}
			$errors[] = 'Error (' . $code . '): ' . $e->getMessage();
		}
	}

	if (empty($errors)) {
		echo '<div class="card ok">Schema executed successfully. Statements: ' . (int)$executed . ' | Ignored: ' . (int)$warnings . '</div>';
	} else {
		echo '<div class="card warn">Schema completed with errors. Executed: ' . (int)$executed . ' | Ignored: ' . (int)$warnings . ' | Errors: ' . count($errors) . '</div>';
		echo '<pre class="card" style="white-space:pre-wrap">' . htmlspecialchars(implode("\n", $errors)) . '</pre>';
	}

	// Verify required tables
	$tables = ['users','planners','event_categories','events','event_services','bookings','reviews','messages','notifications','payments','event_tasks','event_gallery','planner_portfolio_images'];
	$missing = [];
	foreach ($tables as $t) {
		$stmt = $pdo->query("SHOW TABLES LIKE '" . str_replace("'","''", $t) . "'");
		if ($stmt->rowCount() === 0) { $missing[] = $t; }
	}
	if (empty($missing)) {
		echo '<div class="card ok">All required tables are present.</div>';
	} else {
		echo '<div class="card warn">Missing tables: ' . htmlspecialchars(implode(', ', $missing)) . '</div>';
	}

	// Lightweight migrations for admin features
	echo '<div class="card"><strong>Migrations</strong><br/>';
	try {
		// Ensure settings table exists for admin/settings.php
		$settingsCheck = $pdo->query("SHOW TABLES LIKE 'settings'");
		if ($settingsCheck->rowCount() === 0) {
			$pdo->exec("CREATE TABLE IF NOT EXISTS settings (
				setting_key VARCHAR(100) PRIMARY KEY,
				setting_value TEXT NULL,
				updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
			echo '<div class="ok">+ Created settings table</div>';
		} else {
			echo '<div class="muted">= settings table exists</div>';
		}

		// Seed default settings
		$defaults = [
			['site_name','Event Management System'],
			['site_description','Professional event management system for planning and organizing events'],
			['contact_email','admin@ems.com'],
			['contact_phone','+91 00000 00000'],
			['timezone','UTC'],
			['smtp_host','smtp.gmail.com'],
			['smtp_port','587'],
			['smtp_username','noreply@ems.com'],
			['from_email','noreply@ems.com'],
			['email_notifications','1'],
			['new_user_notifications','1'],
			['event_notifications','1'],
			['planner_notifications','1'],
			['sms_notifications','0'],
			['push_notifications','0'],
			['max_file_upload_size','10'],
			['session_timeout','3600'],
			['enable_registration','1'],
			['enable_guest_events','0'],
			['maintenance_mode','0']
		];
		$ins = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
		foreach ($defaults as $row) { $ins->execute($row); }
		echo '<div class="ok">+ Seeded default settings</div>';

		// Ensure password_resets table exists
		$pwdResetCheck = $pdo->query("SHOW TABLES LIKE 'password_resets'");
		if ($pwdResetCheck->rowCount() === 0) {
			$pdo->exec("CREATE TABLE IF NOT EXISTS password_resets (
				id INT AUTO_INCREMENT PRIMARY KEY,
				user_id INT NOT NULL,
				email VARCHAR(191) NOT NULL,
				token VARCHAR(191) NOT NULL,
				expires_at DATETIME NOT NULL,
				used_at DATETIME NULL,
				created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				INDEX idx_email (email),
				INDEX idx_token (token),
				CONSTRAINT fk_password_resets_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
			echo '<div class="ok">+ Created password_resets table</div>';
		} else {
			echo '<div class="muted">= password_resets table exists</div>';
		}

		// Ensure users.email_verified column exists
		$col = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='" . str_replace("'","''", $dbName) . "' AND TABLE_NAME='users' AND COLUMN_NAME='email_verified'");
		if ($col && $col->rowCount() === 0) {
			$pdo->exec("ALTER TABLE users ADD COLUMN email_verified TINYINT(1) NOT NULL DEFAULT 0 AFTER status");
			echo '<div class="ok">+ Added users.email_verified</div>';
		} else {
			echo '<div class="muted">= users.email_verified exists</div>';
		}

		// Ensure otp_verifications table exists
		$otpCheck = $pdo->query("SHOW TABLES LIKE 'otp_verifications'");
		if ($otpCheck->rowCount() === 0) {
			$pdo->exec("CREATE TABLE IF NOT EXISTS otp_verifications (
				id INT AUTO_INCREMENT PRIMARY KEY,
				user_id INT NOT NULL,
				email VARCHAR(191) NOT NULL,
				otp_code VARCHAR(10) NOT NULL,
				expires_at DATETIME NOT NULL,
				attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
				verified_at DATETIME NULL,
				created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				INDEX idx_otp_email (email),
				INDEX idx_otp_user (user_id),
				CONSTRAINT fk_otp_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
			echo '<div class="ok">+ Created otp_verifications table</div>';
		} else {
			echo '<div class="muted">= otp_verifications table exists</div>';
		}

		// Ensure contact_messages table exists
		$contactCheck = $pdo->query("SHOW TABLES LIKE 'contact_messages'");
		if ($contactCheck->rowCount() === 0) {
			$pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
				id INT AUTO_INCREMENT PRIMARY KEY,
				user_id INT NULL,
				name VARCHAR(120) NOT NULL,
				email VARCHAR(150) NOT NULL,
				subject VARCHAR(200) NOT NULL,
				message TEXT NOT NULL,
				status ENUM('new','read','closed') DEFAULT 'new',
				created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				INDEX idx_cm_email (email),
				CONSTRAINT fk_cm_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
			echo '<div class="ok">+ Created contact_messages table</div>';
		} else {
			echo '<div class="muted">= contact_messages table exists</div>';
		}

		// Ensure planner_portfolio_images table exists
		$portfolioCheck = $pdo->query("SHOW TABLES LIKE 'planner_portfolio_images'");
		if ($portfolioCheck->rowCount() === 0) {
			$pdo->exec("CREATE TABLE IF NOT EXISTS planner_portfolio_images (
				id INT AUTO_INCREMENT PRIMARY KEY,
				planner_id INT NOT NULL,
				image_path VARCHAR(255) NOT NULL,
				caption VARCHAR(255) NULL,
				created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				INDEX idx_ppi_planner (planner_id),
				CONSTRAINT fk_ppi_planner FOREIGN KEY (planner_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
			echo '<div class="ok">+ Created planner_portfolio_images table</div>';
		} else {
			echo '<div class=\"muted\">= planner_portfolio_images table exists</div>';
		}

	} catch (Throwable $m) {
		echo '<div class="warn">! Migration check failed: ' . htmlspecialchars($m->getMessage()) . '</div>';
	}
	echo '</div>';

} catch (Throwable $e) {
	echo '<div class="card err">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
	echo '<pre class="card" style="white-space:pre-wrap">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
}

echo '</body></html>';