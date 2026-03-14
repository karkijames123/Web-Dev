<?php
// Start the session at the very beginning so login states are remembered
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // Connect using UTF8MB4 to support all characters (like emojis or special symbols)
    $pdo = new PDO("mysql:host=localhost;dbname=student_course_hub;charset=utf8mb4", "root", "");

    // Set Error Mode to Exception so we see clear error messages during development
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}

/**
 * The e() function: Cross-Site Scripting (XSS) Protection
 * It converts characters like < and > into safe text.
 * Without this, a hacker could put a script in your DB that steals passwords.
 */
function e($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

$universityName = "Global Tech University";
