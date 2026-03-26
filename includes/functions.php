<?php
require_once __DIR__ . '/db.php';

// XSS Protection
function e($string)
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

// Admin Authentication
function isAdminLoggedIn()
{
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

function requireAdmin()
{
    if (!isAdminLoggedIn()) {
        header("Location: admin/login.php");
        exit;
    }
}

// File upload helper
function uploadFile($file, $folder = 'uploads')
{
    $uploadDir = __DIR__ . '/../' . $folder . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowed)) {
        return ['error' => 'File type not allowed. Allowed: JPG, PNG, GIF, WEBP'];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return ['error' => 'File size exceeds limit (2MB)'];
    }

    $newName = uniqid() . '.' . $ext;
    $destination = $uploadDir . $newName;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $newName];
    }

    return ['error' => 'Failed to upload file'];
}

// Format date
function formatDate($date, $format = 'M d, Y')
{
    if (!$date) return '';
    return date($format, strtotime($date));
}

// Truncate text
function truncate($text, $length = 100, $suffix = '...')
{
    if (!$text) return '';
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . $suffix;
}
