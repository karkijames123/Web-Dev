<?php
// admin/module_edit.php

require '../includes/db.php';
require '../includes/functions.php';
requireAdmin();

$errors = [];
$success = false;

$module = [
    'ModuleID'        => 0,
    'ModuleName'      => '',
    'ModuleLeaderID'  => '',
    'Description'     => '',
    'Image'           => ''
];

$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);
$moduleId = $isEdit ? (int)$_GET['id'] : 0;

// Load existing data if editing
if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM Modules WHERE ModuleID = ?");
    $stmt->execute([$moduleId]);
    $module = $stmt->fetch(PDO::FETCH_ASSOC) ?: $module;
    if (!$module['ModuleID']) $errors[] = "Module not found.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module['ModuleName']     = trim($_POST['ModuleName'] ?? '');
    $module['ModuleLeaderID'] = !empty($_POST['ModuleLeaderID']) ? (int)$_POST['ModuleLeaderID'] : null;
    $module['Description']    = trim($_POST['Description'] ?? '');

    if (empty($module['ModuleName'])) {
        $errors[] = "Module name is required.";
    }

    // Image upload
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $newImage = $module['Image'];

    if (!empty($_FILES['Image']['name']) && $_FILES['Image']['error'] === UPLOAD_ERR_OK) {
        $fileExt = strtolower(pathinfo($_FILES['Image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($fileExt, $allowed)) {
            $errors[] = "Allowed formats: JPG, PNG, GIF, WEBP";
        } elseif ($_FILES['Image']['size'] > 2000000) {
            $errors[] = "Max file size 2MB";
        } else {
            $newImage = uniqid('mod_') . '.' . $fileExt;
            $dest = $uploadDir . $newImage;

            if (move_uploaded_file($_FILES['Image']['tmp_name'], $dest)) {
                if ($isEdit && $module['Image'] && file_exists($uploadDir . $module['Image'])) {
                    @unlink($uploadDir . $module['Image']);
                }
                $module['Image'] = $newImage;
            } else {
                $errors[] = "Image upload failed.";
            }
        }
    }

    if (empty($errors)) {
        if ($isEdit) {
            $stmt = $pdo->prepare("
                UPDATE Modules SET
                    ModuleName = ?,
                    ModuleLeaderID = ?,
                    Description = ?,
                    Image = ?
                WHERE ModuleID = ?
            ");
            $stmt->execute([
                $module['ModuleName'],
                $module['ModuleLeaderID'],
                $module['Description'],
                $module['Image'] ?: null,
                $moduleId
            ]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO Modules
                (ModuleName, ModuleLeaderID, Description, Image)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $module['ModuleName'],
                $module['ModuleLeaderID'],
                $module['Description'],
                $module['Image'] ?: null
            ]);
        }

        $success = true;
        header("Refresh: 2; url=modules.php");
    }
}

// Load staff for dropdown
$staff = $pdo->query("SELECT StaffID, Name FROM Staff ORDER BY Name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $isEdit ? 'Edit' : 'Add New' ?> Module - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-4">
        <h1><?= $isEdit ? 'Edit Module' : 'Add New Module' ?></h1>
        <a href="modules.php" class="btn btn-secondary mb-3">← Back to Modules</a>

        <?php if ($success): ?>
            <div class="alert alert-success">Module saved successfully! Redirecting...</div>
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow">
            <div class="mb-3">
                <label class="form-label">Module Name *</label>
                <input type="text" name="ModuleName" class="form-control" required
                    value="<?= htmlspecialchars($module['ModuleName']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Module Leader</label>
                <select name="ModuleLeaderID" class="form-select">
                    <option value="">— Not assigned —</option>
                    <?php foreach ($staff as $s): ?>
                        <option value="<?= $s['StaffID'] ?>"
                            <?= $module['ModuleLeaderID'] == $s['StaffID'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['Name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="Description" class="form-control" rows="5"><?= htmlspecialchars($module['Description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Module Image</label>
                <?php if ($isEdit && $module['Image']): ?>
                    <div class="mb-2">
                        <img src="../uploads/<?= htmlspecialchars($module['Image']) ?>" alt="Current" class="img-thumbnail" style="max-height: 160px;">
                    </div>
                <?php endif; ?>
                <input type="file" name="Image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
                <small class="form-text text-muted">Max 2MB. JPG/PNG/GIF/WEBP</small>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">
                <?= $isEdit ? 'Update Module' : 'Create Module' ?>
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>

</html>