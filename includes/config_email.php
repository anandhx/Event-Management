<?php
// Email (SMTP) settings - Gmail style (used by includes/mailer.php)
 
 
// Email (SMTP) settings - Gmail
define('EMAIL_SMTP_HOST', getenv('EMAIL_SMTP_HOST') ?: 'smtp.gmail.com');
define('EMAIL_SMTP_PORT', getenv('EMAIL_SMTP_PORT') ?: 465);
define('EMAIL_SMTP_SSL', true);
define('EMAIL_SMTP_USER', getenv('EMAIL_SMTP_USER') ?: 'anandhuaskmg@gmail.com');
define('EMAIL_SMTP_PASSWORD', getenv('EMAIL_SMTP_PASSWORD') ?: 'leyd rbij nwzh lbyl');
define('EMAIL_FROM_EMAIL', getenv('EMAIL_FROM_EMAIL') ?: 'anandhuaskmg@gmail.com');
define('EMAIL_FROM_NAME', getenv('EMAIL_FROM_NAME') ?: 'Event Managment');

