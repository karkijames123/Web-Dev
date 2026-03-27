<?php
// Configuration file
define('SITE_NAME', 'Global Tech University');
define('SITE_URL', 'http://localhost/Web-Dev-Crew');
define('ADMIN_EMAIL', 'admin@university.com');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 2097152);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Europe/London');;
