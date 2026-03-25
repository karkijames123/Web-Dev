<?php
// Set page title depending on whether editing or adding
$pageTitle = isset($_GET['id']) ? 'Edit Programme' : 'Add Programme';

// Include header and check admin access
require '../includes/header.php';
requireAdmin();

// Initialize error array and success flag
$errors = [];
$success = false;

// Default programme values
$programme = [
    'ProgrammeID' => 0,
    'ProgrammeName' => '',
    'LevelID' => '',
    'ProgrammeLeaderID' => null,
    'Description' => '',
    'Image' => '',
    'Duration' => '',
    'Fees' => null,
    'is_published' => 0
];

// Check if editing (ID exists in URL)
$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);
$programmeId = $isEdit ? (int)$_GET['id'] : 0;

// If editing, fetch programme data from database
if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM Programmes WHERE ProgrammeID = ?");
    $stmt->execute([$programmeId]);
    $programme = $stmt->fetch();

    // If not found, redirect back
    if (!$programme) {
        header("Location: programmes.php");
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form inputs
    $programme['ProgrammeName'] = trim($_POST['ProgrammeName'] ?? '');
    $programme['LevelID'] = (int)($_POST['LevelID'] ?? 0);
    $programme['ProgrammeLeaderID'] = !empty($_POST['ProgrammeLeaderID']) ? (int)$_POST['ProgrammeLeaderID'] : null;
    $programme['Description'] = trim($_POST['Description'] ?? '');
    $programme['Duration'] = trim($_POST['Duration'] ?? '');
    $programme['Fees'] = !empty($_POST['Fees']) ? (float)$_POST['Fees'] : null;
    $programme['is_published'] = isset($_POST['is_published']) ? 1 : 0;

    // Validate required fields
    if (empty($programme['ProgrammeName'])) $errors[] = "Programme name is required.";
    if ($programme['LevelID'] <= 0) $errors[] = "Please select a level.";

    // Handle image upload
    if (!empty($_FILES['Image']['name'])) {
        $upload = uploadFile($_FILES['Image']);

        // Check for upload error
        if (isset($upload['error'])) {
            $errors[] = $upload['error'];
        } else {
            // Delete old image if editing
            if ($isEdit && $programme['Image'] && file_exists('../uploads/' . $programme['Image'])) {
                unlink('../uploads/' . $programme['Image']);
            }

            // Save new image filename
            $programme['Image'] = $upload['filename'];
        }
    }

    // If no errors, save to database
    if (empty($errors)) {

        if ($isEdit) {
            // Update existing programme
            $stmt = $pdo->prepare("
                UPDATE Programmes SET 
                    ProgrammeName = ?, LevelID = ?, ProgrammeLeaderID = ?, 
                    Description = ?, Image = ?, Duration = ?, Fees = ?, is_published = ?
                WHERE ProgrammeID = ?
            ");
            $stmt->execute([
                $programme['ProgrammeName'],
                $programme['LevelID'],
                $programme['ProgrammeLeaderID'],
                $programme['Description'],
                $programme['Image'],
                $programme['Duration'],
                $programme['Fees'],
                $programme['is_published'],
                $programmeId
            ]);

        } else {
            // Insert new programme
            $stmt = $pdo->prepare("
                INSERT INTO Programmes (ProgrammeName, LevelID, ProgrammeLeaderID, Description, Image, Duration, Fees, is_published)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $programme['ProgrammeName'],
                $programme['LevelID'],
                $programme['ProgrammeLeaderID'],
                $programme['Description'],
                $programme['Image'],
                $programme['Duration'],
                $programme['Fees'],
                $programme['is_published']
            ]);
        }

        // Show success and redirect
        $success = true;
        header("Refresh: 2; url=programmes.php");
    }
}

// Fetch levels and staff for dropdowns
$levels = $pdo->query("SELECT * FROM Levels ORDER BY LevelID")->fetchAll();
$staff = $pdo->query("SELECT StaffID, Name FROM Staff ORDER BY Name")->fetchAll();
?>

<!-- Page layout -->
<div class="page-container">
    <div class="page-header">
        <h1><?= $isEdit ? 'Edit' : 'Add' ?> Programme</h1>
        <a href="programmes.php" class="back-btn">← Back</a>
    </div>

    <div class="form-card">

        <!-- Success message -->
        <?php if ($success): ?>
            <div class="success-message">Programme saved successfully! Redirecting...</div>
        <?php endif; ?>

        <!-- Error messages -->
        <?php if ($errors): ?>
            <div class="error-messages">
                <?php foreach ($errors as $err): ?>
                    <p><?= e($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Programme form -->
        <form method="POST" enctype="multipart/form-data">

            <!-- Programme name -->
            <div class="form-field">
                <label>Programme Name *</label>
                <input type="text" name="ProgrammeName" value="<?= e($programme['ProgrammeName']) ?>" required>
            </div>

            <!-- Level and duration -->
            <div class="form-row">
                <div class="form-field">
                    <label>Level *</label>
                    <select name="LevelID" required>
                        <option value="">Select Level</option>
                        <?php foreach ($levels as $lvl): ?>
                            <option value="<?= $lvl['LevelID'] ?>" <?= $programme['LevelID'] == $lvl['LevelID'] ? 'selected' : '' ?>>
                                <?= e($lvl['LevelName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-field">
                    <label>Duration</label>
                    <input type="text" name="Duration" placeholder="e.g., 3 Years" value="<?= e($programme['Duration']) ?>">
                </div>
            </div>

            <!-- Leader and fees -->
            <div class="form-row">
                <div class="form-field">
                    <label>Programme Leader</label>
                    <select name="ProgrammeLeaderID">
                        <option value="">None</option>
                        <?php foreach ($staff as $s): ?>
                            <option value="<?= $s['StaffID'] ?>" <?= $programme['ProgrammeLeaderID'] == $s['StaffID'] ? 'selected' : '' ?>>
                                <?= e($s['Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-field">
                    <label>Annual Fees (£)</label>
                    <input type="number" name="Fees" step="0.01" value="<?= e($programme['Fees']) ?>">
                </div>
            </div>

            <!-- Description -->
            <div class="form-field">
                <label>Description</label>
                <textarea name="Description" rows="6"><?= e($programme['Description']) ?></textarea>
            </div>

            <!-- Image upload -->
            <div class="form-field">
                <label>Programme Image</label>

                <!-- Show current image if editing -->
                <?php if ($isEdit && $programme['Image']): ?>
                    <div class="current-image">
                        <img src="../uploads/<?= e($programme['Image']) ?>" alt="Current">
                        <p>Current image</p>
                    </div>
                <?php endif; ?>

                <input type="file" name="Image" accept="image/*">
                <small>Max 2MB. JPG, PNG, GIF, WEBP</small>
            </div>

            <!-- Publish checkbox -->
            <div class="checkbox-field">
                <label>
                    <input type="checkbox" name="is_published" <?= $programme['is_published'] ? 'checked' : '' ?>>
                    Published (visible to students)
                </label>
            </div>

            <!-- Form buttons -->
            <div class="form-actions">
                <button type="submit" class="save-btn">
                    <i class="fas fa-save"></i> <?= $isEdit ? 'Update' : 'Create' ?> Programme
                </button>
                <a href="programmes.php" class="cancel-btn">Cancel</a>
            </div>

        </form>
    </div>
</div>

<?php require '../includes/footer.php'; ?>