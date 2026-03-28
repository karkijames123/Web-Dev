<?php
// Set page title
$pageTitle = 'Manage Staff';

// Include header and check admin access
require '../includes/header.php';
requireAdmin();

// Handle delete request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Check if staff is assigned to any modules
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Modules WHERE ModuleLeaderID = ?");
    $stmt->execute([$id]);
    $moduleCount = $stmt->fetchColumn();

    // Check if staff is assigned to any programmes
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Programmes WHERE ProgrammeLeaderID = ?");
    $stmt->execute([$id]);
    $progCount = $stmt->fetchColumn();

    // Prevent deletion if staff is still assigned
    if ($moduleCount > 0 || $progCount > 0) {
        $error = "Cannot delete: This staff member is assigned as leader to $moduleCount modules and $progCount programmes.";
    } else {
        // Delete staff from database
        $stmt = $pdo->prepare("DELETE FROM Staff WHERE StaffID = ?");
        $stmt->execute([$id]);

        // Redirect after delete
        header("Location: staff.php");
        exit;
    }
}

// Fetch all staff with counts of modules and programmes
$staff = $pdo->query("
    SELECT s.*,
        (SELECT COUNT(*) FROM Modules WHERE ModuleLeaderID = s.StaffID) as module_count,
        (SELECT COUNT(*) FROM Programmes WHERE ProgrammeLeaderID = s.StaffID) as programme_count
    FROM Staff s
    ORDER BY s.Name
")->fetchAll();
?>

<div class="admin-page">

    <!-- Page header -->
    <div class="page-head">
        <h1>Manage Staff</h1>

        <!-- Button to add new staff -->
        <a href="staff_edit.php" class="btn-add">
            <i class="fas fa-plus"></i> Add Staff Member
        </a>
    </div>

    <!-- Show error if delete is not allowed -->
    <?php if (isset($error)): ?>
        <div class="error-box"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="data-card">
        <div class="table-wrap">

            <!-- Staff table -->
            <table class="simple-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Modules Led</th>
                        <th>Programmes Led</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- Loop through staff -->
                    <?php foreach ($staff as $s): ?>
                        <tr>

                            <!-- Name and short bio -->
                            <td>
                                <strong><?= e($s['Name']) ?></strong>
                                <?php if ($s['Bio']): ?>
                                    <br><small><?= truncate($s['Bio'], 50) ?></small>
                                <?php endif; ?>
                            </td>

                            <!-- Email -->
                            <td><?= e($s['Email'] ?? '—') ?></td>

                            <!-- Department -->
                            <td><?= e($s['Department'] ?? '—') ?></td>

                            <!-- Number of modules led -->
                            <td><span class="count-badge"><?= $s['module_count'] ?></span></td>

                            <!-- Number of programmes led -->
                            <td><span class="count-badge"><?= $s['programme_count'] ?></span></td>

                            <!-- Action buttons -->
                            <td class="action-icons">

                                <!-- Edit -->
                                <a href="staff_edit.php?id=<?= $s['StaffID'] ?>" class="icon-edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Delete with confirmation -->
                                <a href="?delete=<?= $s['StaffID'] ?>" class="icon-delete" onclick="return confirm('Delete <?= e($s['Name']) ?>?')">
                                    <i class="fas fa-trash"></i>
                                </a>

                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Show message if no staff -->
                    <?php if (empty($staff)): ?>
                        <tr>
                            <td colspan="6" class="empty-row">No staff members found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
