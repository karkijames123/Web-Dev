<?php
// Set page title based on whether editing or adding staff
$pageTitle = isset($_GET['id']) ? 'Edit Staff' : 'Add Staff';

// Include header and check admin access
require '../includes/header.php';
requireAdmin();

// Initialize error messages and success flag
$errors = [];
$success = false;

// Default staff values
$staff = [
    'StaffID' => 0,
    'Name' => '',
    'Email' => '',
    'Department' => '',
    'Bio' => '',
    'ProfileImage' => ''
];

// Check if editing mode
$isEdit = isset($_GET['id']) && is_numeric($_GET['id']);
$staffId = $isEdit ? (int)$_GET['id'] : 0;

// If editing, fetch staff data from database
if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM Staff WHERE StaffID = ?");
    $stmt->execute([$staffId]);
    $staff = $stmt->fetch();

    // Redirect if staff not found
    if (!$staff) {
        header("Location: staff.php");
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get form inputs
    $staff['Name'] = trim($_POST['Name'] ?? '');
    $staff['Email'] = trim($_POST['Email'] ?? '');
    $staff['Department'] = trim($_POST['Department'] ?? '');
    $staff['Bio'] = trim($_POST['Bio'] ?? '');

    // Validate name
    if (empty($staff['Name'])) $errors[] = "Name is required.";

    // Validate email format
    if (!empty($staff['Email']) && !filter_var($staff['Email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    // Handle profile image upload
    if (!empty($_FILES['ProfileImage']['name'])) {
        $upload = uploadFile($_FILES['ProfileImage']);

        // Check upload error
        if (isset($upload['error'])) {
            $errors[] = $upload['error'];
        } else {

            // Delete old image if editing
            if ($isEdit && $staff['ProfileImage'] && file_exists('../uploads/' . $staff['ProfileImage'])) {
                unlink('../uploads/' . $staff['ProfileImage']);
            }

            // Save new image filename
            $staff['ProfileImage'] = $upload['filename'];
        }
    }

    // If no errors, save to database
    if (empty($errors)) {

        if ($isEdit) {
            // Update existing staff
            $stmt = $pdo->prepare("
                UPDATE Staff 
                SET Name = ?, Email = ?, Department = ?, Bio = ?, ProfileImage = ? 
                WHERE StaffID = ?
            ");
            $stmt->execute([
                $staff['Name'],
                $staff['Email'],
                $staff['Department'],
                $staff['Bio'],
                $staff['ProfileImage'],
                $staffId
            ]);

        } else {
            // Insert new staff member
            $stmt = $pdo->prepare("
                INSERT INTO Staff (Name, Email, Department, Bio, ProfileImage) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $staff['Name'],
                $staff['Email'],
                $staff['Department'],
                $staff['Bio'],
                $staff['ProfileImage']
            ]);
        }

        // Show success message and redirect
        $success = true;
        header("Refresh: 2; url=staff.php");
    }
}
?>

<div class="admin-page">

    <!-- Page header -->
    <div class="page-head">
        <h1><?= $isEdit ? 'Edit' : 'Add' ?> Staff Member</h1>
        <a href="staff.php" class="btn-back">← Back</a>
    </div>

    <div class="form-card">

        <!-- Success message -->
        <?php if ($success): ?>
            <div class="success-box">Staff member saved successfully! Redirecting...</div>
        <?php endif; ?>

        <!-- Error messages -->
        <?php if ($errors): ?>
            <div class="error-box">
                <?php foreach ($errors as $err): ?>
                    <p><?= e($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Staff form -->
        <form method="POST" enctype="multipart/form-data">

            <!-- Name -->
            <div class="form-field">
                <label>Full Name *</label>
                <input type="text" name="Name" value="<?= e($staff['Name']) ?>" required>
            </div>

            <!-- Email -->
            <div class="form-field">
                <label>Email</label>
                <input type="email" name="Email" value="<?= e($staff['Email']) ?>">
            </div>

            <!-- Department -->
            <div class="form-field">
                <label>Department</label>
                <input type="text" name="Department" value="<?= e($staff['Department']) ?>">
            </div>

            <!-- Bio -->
            <div class="form-field">
                <label>Bio</label>
                <textarea name="Bio" rows="4"><?= e($staff['Bio']) ?></textarea>
            </div>

            <!-- Profile image -->
            <div class="form-field">
                <label>Profile Image</label>

                <!-- Show current image if editing -->
                <?php if ($isEdit && $staff['ProfileImage']): ?>
                    <div class="current-img">
                        <img src="../uploads/<?= e($staff['ProfileImage']) ?>" alt="Current">
                    </div>
                <?php endif; ?>

                <input type="file" name="ProfileImage" accept="image/*">
                <small>Max 2MB. JPG, PNG, GIF, WEBP</small>
            </div>

            <!-- Form buttons -->
            <div class="form-buttons">
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> <?= $isEdit ? 'Update' : 'Save' ?>
                </button>
                <a href="staff.php" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
