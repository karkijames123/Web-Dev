<?php
require 'includes/db.php';
require 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$progId = (int)($_POST['programme_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validate
if ($progId <= 0 || empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: programme.php?id=$progId&error=invalid");
    exit;
}

// Check for duplicate
$stmt = $pdo->prepare("SELECT COUNT(*) FROM InterestedStudents WHERE Email = ? AND ProgrammeID = ?");
$stmt->execute([$email, $progId]);
if ($stmt->fetchColumn() > 0) {
    header("Location: programme.php?id=$progId&error=duplicate");
    exit;
}

// Insert
$stmt = $pdo->prepare("INSERT INTO InterestedStudents (ProgrammeID, StudentName, Email, Phone, Message) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$progId, $name, $email, $phone, $message]);

header("Location: programme.php?id=$progId&success=1");
exit;
