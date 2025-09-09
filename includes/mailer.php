<?php
// Attempt to load Composer autoloader if present
$composerAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($composerAutoload)) {
	require_once $composerAutoload;
}

// If PHPMailer still not available, try manual include from includes/PHPMailer/src
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
	$phpMailerBase = __DIR__ . '/PHPMailer/src';
	if (is_dir($phpMailerBase)) {
		$files = [
			$phpMailerBase . '/Exception.php',
			$phpMailerBase . '/PHPMailer.php',
			$phpMailerBase . '/SMTP.php'
		];
		foreach ($files as $f) {
			if (file_exists($f)) { require_once $f; }
		}
	}
}

require_once __DIR__ . '/config_email.php';

function ems_log_email($line) {
	$dir = __DIR__ . '/../logs';
	$file = $dir . '/email.log';
	if (!is_dir($dir)) {
		@mkdir($dir, 0775, true);
	}
	$timestamp = date('Y-m-d H:i:s');
	$entry = '[' . $timestamp . "] " . $line . "\n";
	@file_put_contents($file, $entry, FILE_APPEND);
}

function ems_send_email($toEmail, $toName, $subject, $htmlBody, $textBody = '') {
	$usePhpMailer = class_exists('PHPMailer\PHPMailer\PHPMailer');
	if ($usePhpMailer) {
		try {
			$mail = new PHPMailer\PHPMailer\PHPMailer(true);
			$mail->isSMTP();
			$mail->Host = EMAIL_SMTP_HOST;
			$mail->SMTPAuth = true;
			$mail->Username = EMAIL_SMTP_USER;
			$mail->Password = EMAIL_SMTP_PASSWORD;
			$mail->SMTPSecure = EMAIL_SMTP_SSL ? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
			$mail->Port = (int)EMAIL_SMTP_PORT;
			$mail->Timeout = 15;

			$mail->setFrom(EMAIL_FROM_EMAIL, EMAIL_FROM_NAME);
			$mail->addAddress($toEmail, $toName ?: $toEmail);
			$mail->isHTML(true);
			$mail->Subject = $subject;
			$mail->Body = $htmlBody;
			$mail->AltBody = $textBody ?: strip_tags($htmlBody);

			$mail->send();
			ems_log_email('SMTP OK -> to=' . $toEmail . ' subject=' . $subject . ' host=' . EMAIL_SMTP_HOST . ':' . EMAIL_SMTP_PORT . ' ssl=' . (EMAIL_SMTP_SSL ? '1' : '0'));
			return [true, null];
		} catch (Throwable $e) {
			ems_log_email('SMTP ERROR -> to=' . $toEmail . ' subject=' . $subject . ' msg=' . $e->getMessage());
			return [false, $e->getMessage()];
		}
	}

	// PHPMailer not available -> Fallback: PHP mail()
	ems_log_email('PHPMailer not found, using mail() fallback');
	$headers = [];
	$headers[] = 'MIME-Version: 1.0';
	$headers[] = 'Content-type: text/html; charset=UTF-8';
	$headers[] = 'From: ' . EMAIL_FROM_NAME . ' <' . EMAIL_FROM_EMAIL . '>';
	$headersStr = implode("\r\n", $headers);
	$ok = @mail($toEmail, $subject, $htmlBody, $headersStr);
	if ($ok) {
		ems_log_email('mail() OK -> to=' . $toEmail . ' subject=' . $subject);
	} else {
		ems_log_email('mail() FAIL -> to=' . $toEmail . ' subject=' . $subject . ' (likely disabled on local server)');
	}
	return [$ok, $ok ? null : 'mail() failed (likely disabled on local server)'];
}

// Backward-compatible wrapper for existing callers expecting a boolean
if (!function_exists('send_email')) {
	function send_email($toEmail, $toName, $subject, $htmlBody, $textBody = '') {
		list($ok, $err) = ems_send_email($toEmail, $toName, $subject, $htmlBody, $textBody);
		return $ok;
	}
}
