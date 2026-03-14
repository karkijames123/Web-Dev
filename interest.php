<?php require 'includes/db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') header("Location: index.php");
$progId = (int)$_POST['programme_id'];
$name = trim($_POST['name']);
$email = trim($_POST['email']);
if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: programme.php?id=$progId&error=invalid");
    exit;
}
$stmt = $pdo->prepare("SELECT COUNT(*) FROM InterestedStudents WHERE Email = ? AND ProgrammeID = ?");
$stmt->execute([$email, $progId]);
if ($stmt->fetchColumn() > 0) {
    header("Location: programme.php?id=$progId&error=duplicate");
    exit;
}
$stmt = $pdo->prepare("INSERT INTO InterestedStudents (ProgrammeID, StudentName, Email) VALUES (?, ?, ?)");
$stmt->execute([$progId, $name, $email]);
header("Location: programme.php?id=$progId&success=1");
