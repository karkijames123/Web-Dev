<?php
// Set page title
$pageTitle = 'Manage Programmes';

// Include header and check admin access
require '../includes/header.php';
requireAdmin();

// Handle publish/unpublish toggle
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];

    // Switch is_published between 0 and 1
    $stmt = $pdo->prepare("UPDATE Programmes SET is_published = NOT is_published WHERE ProgrammeID = ?");
    $stmt->execute([$id]);

    // Redirect back to avoid resubmission
    header("Location: programmes.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Get image name before deleting
    $stmt = $pdo->prepare("SELECT Image FROM Programmes WHERE ProgrammeID = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetch();

    // Delete image file if it exists
    if ($img && $img['Image'] && file_exists('../uploads/' . $img['Image'])) {
        unlink('../uploads/' . $img['Image']);
    }

    // Delete programme from database
    $stmt = $pdo->prepare("DELETE FROM Programmes WHERE ProgrammeID = ?");
    $stmt->execute([$id]);

    // Redirect after delete
    header("Location: programmes.php");
    exit;
}

// Fetch all programmes with level and leader info
$programmes = $pdo->query("
    SELECT p.*, l.LevelName, s.Name AS LeaderName 
    FROM Programmes p 
    JOIN Levels l ON p.LevelID = l.LevelID 
    LEFT JOIN Staff s ON p.ProgrammeLeaderID = s.StaffID 
    ORDER BY p.ProgrammeName
")->fetchAll();
?>

<div class="admin-page">

    <!-- Page header -->
    <div class="page-head">
        <h1>Manage Programmes</h1>

        <!-- Button to add new programme -->
        <a href="programme_edit.php" class="btn-add">
            <i class="fas fa-plus"></i> Add New Programme
        </a>
    </div>

    <div class="data-card">
        <div class="table-wrap">

            <!-- Programmes table -->
            <table class="simple-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Level</th>
                        <th>Leader</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- Loop through programmes -->
                    <?php foreach ($programmes as $p): ?>
                        <tr>

                            <!-- Programme name and duration -->
                            <td>
                                <strong><?= e($p['ProgrammeName']) ?></strong>
                                <?php if ($p['Duration']): ?>
                                    <br><small><i class="fas fa-clock"></i> <?= e($p['Duration']) ?></small>
                                <?php endif; ?>
                            </td>

                            <!-- Level name -->
                            <td><?= e($p['LevelName']) ?></td>

                            <!-- Programme leader -->
                            <td><?= e($p['LeaderName'] ?? '—') ?></td>

                            <!-- Publish status -->
                            <td>
                                <span class="status-badge <?= $p['is_published'] ? 'status-active' : 'status-inactive' ?>">
                                    <?= $p['is_published'] ? 'Published' : 'Draft' ?>
                                </span>
                            </td>

                            <!-- Action buttons -->
                            <td class="action-icons">

                                <!-- Edit -->
                                <a href="programme_edit.php?id=<?= $p['ProgrammeID'] ?>" class="icon-edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Toggle publish -->
                                <a href="?toggle=<?= $p['ProgrammeID'] ?>" class="icon-toggle">
                                    <i class="fas fa-<?= $p['is_published'] ? 'eye-slash' : 'eye' ?>"></i>
                                </a>

                                <!-- Delete with confirmation -->
                                <a href="?delete=<?= $p['ProgrammeID'] ?>" class="icon-delete" onclick="return confirm('Delete <?= e($p['ProgrammeName']) ?>?')">
                                    <i class="fas fa-trash"></i>
                                </a>

                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Show message if no programmes -->
                    <?php if (empty($programmes)): ?>
                        <tr>
                            <td colspan="5" class="empty-row">No programmes found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
