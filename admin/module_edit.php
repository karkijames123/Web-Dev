<?php
$pageTitle = isset($_GET['id']) ? 'Edit Module' : 'Add Module'; // Set page title
require '../includes/header.php'; // Include header
requireAdmin(); // Check admin access

$errors = []; // Array for form errors
$success = false; // Success flag

// Default module values
$module = [
    'ModuleID' => 0,
    'ModuleName' => '',
    'ModuleLeaderID' => null,
    'Description' => '',
    'Credits' => 15,
    'Image' => ''
];

$isEdit = isset($_GET['id']) && is_numeric($_GET['id']); // Check if editing
$moduleId = $isEdit ? (int)$_GET['id'] : 0;

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM Modules WHERE ModuleID = ?"); // Fetch module
    $stmt->execute([$moduleId]);
    $module = $stmt->fetch();
    if (!$module) { // Redirect if module not found
        header("Location: modules.php");
        exit;
    }
    if (!isset($module['Credits']) || empty($module['Credits'])) {
        $module['Credits'] = 15; // Default credits
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Form submitted
    $module['ModuleName'] = trim($_POST['ModuleName'] ?? ''); // Get name
    $module['ModuleLeaderID'] = !empty($_POST['ModuleLeaderID']) ? (int)$_POST['ModuleLeaderID'] : null; // Leader
    $module['Description'] = trim($_POST['Description'] ?? ''); // Description
    $module['Credits'] = (int)($_POST['Credits'] ?? 15); // Credits

    // Validate fields
    if (empty($module['ModuleName'])) $errors[] = "Module name is required.";
    if ($module['Credits'] <= 0) $errors[] = "Credits must be a positive number.";

    // Handle image upload
    if (!empty($_FILES['Image']['name'])) {
        $upload = uploadFile($_FILES['Image']); // Upload file
        if (isset($upload['error'])) {
            $errors[] = $upload['error']; // Upload error
        } else {
            if ($isEdit && !empty($module['Image']) && file_exists('../uploads/' . $module['Image'])) {
                unlink('../uploads/' . $module['Image']); // Delete old image
            }
            $module['Image'] = $upload['filename']; // Save new image
        }
    }

    if (empty($errors)) { // No errors, save module
        if ($isEdit) { // Update existing module
            $sql = "UPDATE Modules SET ModuleName = ?, ModuleLeaderID = ?, Description = ?, Credits = ?";
            $params = [$module['ModuleName'], $module['ModuleLeaderID'], $module['Description'], $module['Credits']];
            if (isset($module['Image'])) {
                $sql .= ", Image = ?";
                $params[] = $module['Image'];
            }
            $sql .= " WHERE ModuleID = ?";
            $params[] = $moduleId;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        } else { // Insert new module
            $sql = "INSERT INTO Modules (ModuleName, ModuleLeaderID, Description, Credits";
            $params = [$module['ModuleName'], $module['ModuleLeaderID'], $module['Description'], $module['Credits']];
            if (isset($module['Image'])) {
                $sql .= ", Image";
                $params[] = $module['Image'];
            }
            $sql .= ") VALUES (" . implode(',', array_fill(0, count($params), '?')) . ")";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        }
        $success = true; // Set success
        header("Refresh: 2; url=modules.php"); // Redirect after 2 seconds
    }
}

// Fetch staff for module leader dropdown
$staff = $pdo->query("SELECT StaffID, Name FROM Staff ORDER BY Name")->fetchAll();
?>

<div class="page-container">
    <div class="page-header">
        <h1><?= $isEdit ? 'Edit' : 'Add' ?> Module</h1>
        <a href="modules.php" class="back-btn">← Back</a> <!-- Back button -->
    </div>

    <div class="form-card">
        <?php if ($success): ?>
            <div class="success-message">Module saved successfully! Redirecting...</div> <!-- Success -->
        <?php endif; ?>

        <?php if ($errors): ?>
            <div class="error-messages">
                <?php foreach ($errors as $err): ?>
                    <p><?= e($err) ?></p> <!-- Display errors -->
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-field">
                <label>Module Name *</label>
                <input type="text" name="ModuleName" value="<?= e($module['ModuleName']) ?>" required> <!-- Name -->
            </div>

            <div class="form-row">
                <div class="form-field">
                    <label>Module Leader</label>
                    <select name="ModuleLeaderID"> <!-- Leader dropdown -->
                        <option value="">Select Leader</option>
                        <?php foreach ($staff as $s): ?>
                            <option value="<?= $s['StaffID'] ?>" <?= $module['ModuleLeaderID'] == $s['StaffID'] ? 'selected' : '' ?>>
                                <?= e($s['Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label>Credits</label>
                    <input type="number" name="Credits" value="<?= e($module['Credits']) ?>" min="1" max="60"> <!-- Credits -->
                </div>
            </div>

            <div class="form-field">
                <label>Description</label>
                <textarea name="Description" rows="5"><?= e($module['Description']) ?></textarea> <!-- Description -->
            </div>

            <div class="form-field">
                <label>Module Image</label>
                <?php if ($isEdit && !empty($module['Image'])): ?>
                    <div class="current-image">
                        <img src="../uploads/<?= e($module['Image']) ?>" alt="Current">
                        <p>Current image</p> <!-- Show existing image -->
                    </div>
                <?php endif; ?>
                <input type="file" name="Image" accept="image/*"> <!-- Upload new image -->
                <small>Max 2MB. JPG, PNG, GIF, WEBP</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="save-btn">
                    <i class="fas fa-save"></i> <?= $isEdit ? 'Update' : 'Create' ?> Module <!-- Submit -->
                </button>
                <a href="modules.php" class="cancel-btn">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require '../includes/footer.php'; ?> <!-- Include footer -->
