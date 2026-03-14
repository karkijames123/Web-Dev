<?php
// admin/programme_edit.php

require '../includes/db.php';
require '../includes/functions.php';
requireAdmin();  // redirects if not logged in

$errors = [];
$success = false;

// Default values for new programme
$programme = [
    'ProgrammeID'       => 0,
    'ProgrammeName'     => '',
    'LevelID'           => '',
    'ProgrammeLeaderID' => '',
    'Description'       => '',
    'Image'             => '',
    'is_published'      => 0
];

$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);
$programmeId = $isEdit ? (int)$_GET['id'] : 0;

// Load existing programme data if editing
if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM Programmes WHERE ProgrammeID = ?");
    $stmt->execute([$programmeId]);
    $programme = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$programme) {
        $errors[] = "Programme not found.";
        $programme = ['ProgrammeID' => 0]; // prevent undefined errors
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $programme['ProgrammeName']     = trim($_POST['ProgrammeName'] ?? '');
    $programme['LevelID']           = (int)($_POST['LevelID'] ?? 0);
    $programme['ProgrammeLeaderID'] = !empty($_POST['ProgrammeLeaderID']) ? (int)$_POST['ProgrammeLeaderID'] : null;
    $programme['Description']       = trim($_POST['Description'] ?? '');
    $programme['is_published']      = isset($_POST['is_published']) ? 1 : 0;

    // Validation
    if (empty($programme['ProgrammeName'])) {
        $errors[] = "Programme name is required.";
    }
    if ($programme['LevelID'] <= 0) {
        $errors[] = "Please select a level.";
    }

    // Image upload handling
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $newImageName = $programme['Image']; // keep existing if no new upload

    if (!empty($_FILES['Image']['name']) && $_FILES['Image']['error'] === UPLOAD_ERR_OK) {
        $fileTmp  = $_FILES['Image']['tmp_name'];
        $fileName = basename($_FILES['Image']['name']);
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($fileExt, $allowed)) {
            $errors[] = "Only JPG, JPEG, PNG, GIF & WEBP files allowed.";
        } elseif ($_FILES['Image']['size'] > 2000000) { // ~2MB limit
            $errors[] = "Image must be smaller than 2MB.";
        } else {
            $newImageName = uniqid('prog_') . '.' . $fileExt;
            $destination = $uploadDir . $newImageName;

            if (move_uploaded_file($fileTmp, $destination)) {
                // Delete old image if editing and new one uploaded
                if ($isEdit && !empty($programme['Image']) && file_exists($uploadDir . $programme['Image'])) {
                    @unlink($uploadDir . $programme['Image']);
                }
                $programme['Image'] = $newImageName;
            } else {
                $errors[] = "Failed to upload image.";
            }
        }
    }

    // Save to database if no errors
    if (empty($errors)) {
        if ($isEdit) {
            // Update
            $stmt = $pdo->prepare("
                UPDATE Programmes SET
                    ProgrammeName = ?,
                    LevelID = ?,
                    ProgrammeLeaderID = ?,
                    Description = ?,
                    Image = ?,
                    is_published = ?
                WHERE ProgrammeID = ?
            ");
            $stmt->execute([
                $programme['ProgrammeName'],
                $programme['LevelID'],
                $programme['ProgrammeLeaderID'],
                $programme['Description'],
                $programme['Image'] ?: null,
                $programme['is_published'],
                $programmeId
            ]);
        } else {
            // Insert new
            $stmt = $pdo->prepare("
                INSERT INTO Programmes
                (ProgrammeName, LevelID, ProgrammeLeaderID, Description, Image, is_published)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $programme['ProgrammeName'],
                $programme['LevelID'],
                $programme['ProgrammeLeaderID'],
                $programme['Description'],
                $programme['Image'] ?: null,
                $programme['is_published']
            ]);
            $programmeId = $pdo->lastInsertId();
        }

        $success = true;
        header("Refresh: 2; url=programmes.php");
    }
}

// Load dropdown data
$levels = $pdo->query("SELECT LevelID, LevelName FROM Levels ORDER BY LevelID")->fetchAll(PDO::FETCH_ASSOC);
$staff  = $pdo->query("SELECT StaffID, Name FROM Staff ORDER BY Name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $isEdit ? 'Edit' : 'Add New' ?> Programme - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-4">
        <h1><?= $isEdit ? 'Edit Programme' : 'Add New Programme' ?></h1>
        <a href="programmes.php" class="btn btn-secondary mb-3">← Back to List</a>

        <?php if ($success): ?>
            <div class="alert alert-success">
                Programme successfully <?= $isEdit ? 'updated' : 'created' ?>!
                Redirecting...
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
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
                <label for="ProgrammeName" class="form-label">Programme Name *</label>
                <input type="text" class="form-control" id="ProgrammeName" name="ProgrammeName"
                    value="<?= htmlspecialchars($programme['ProgrammeName']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="LevelID" class="form-label">Level *</label>
                <select class="form-select" id="LevelID" name="LevelID" required>
                    <option value="">-- Select Level --</option>
                    <?php foreach ($levels as $level): ?>
                        <option value="<?= $level['LevelID'] ?>"
                            <?= $programme['LevelID'] == $level['LevelID'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($level['LevelName']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="ProgrammeLeaderID" class="form-label">Programme Leader</label>
                <select class="form-select" id="ProgrammeLeaderID" name="ProgrammeLeaderID">
                    <option value="">-- None / Not Assigned --</option>
                    <?php foreach ($staff as $s): ?>
                        <option value="<?= $s['StaffID'] ?>"
                            <?= $programme['ProgrammeLeaderID'] == $s['StaffID'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['Name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="Description" class="form-label">Description</label>
                <textarea class="form-control" id="Description" name="Description" rows="6"><?= htmlspecialchars($programme['Description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="Image" class="form-label">Programme Image</label>
                <?php if ($isEdit && !empty($programme['Image'])): ?>
                    <div class="mb-2">
                        <img src="../uploads/<?= htmlspecialchars($programme['Image']) ?>" alt="Current image" class="img-thumbnail" style="max-height: 180px;">
                        <p class="form-text">Current image. Upload new one to replace.</p>
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control" id="Image" name="Image"
                    accept="image/jpeg,image/png,image/gif,image/webp">
                <div class="form-text">Max 2MB. JPG, PNG, GIF, WEBP.</div>
            </div>

            <div class="mb-3 form-check">
                <input class="form-check-input" type="checkbox" id="is_published" name="is_published"
                    <?= $programme['is_published'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_published">
                    Published (visible to prospective students)
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-lg">
                <?= $isEdit ? 'Update Programme' : 'Create Programme' ?>
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>