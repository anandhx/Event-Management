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
	$tables = ['users','planners','event_categories','events','event_services','bookings','reviews','messages','notifications','payments','event_tasks','event_gallery'];
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

} catch (Throwable $e) {
	echo '<div class="card err">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
	echo '<pre class="card" style="white-space:pre-wrap">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
}

echo '</body></html>';