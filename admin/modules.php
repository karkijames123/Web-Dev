<?php
$pageTitle = 'Manage Modules'; // Page title
require '../includes/header.php'; // Include header
requireAdmin(); // Check admin access

// Handle module deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Fetch module image
    $stmt = $pdo->prepare("SELECT Image FROM Modules WHERE ModuleID = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetch();

    // Delete image file if exists
    if ($img && isset($img['Image']) && $img['Image'] && file_exists('../uploads/' . $img['Image'])) {
        unlink('../uploads/' . $img['Image']);
    }

    // Delete module from database
    $stmt = $pdo->prepare("DELETE FROM Modules WHERE ModuleID = ?");
    $stmt->execute([$id]);
    header("Location: modules.php"); // Redirect after deletion
    exit;
}

// Fetch all modules with leader names
$modules = $pdo->query("
    SELECT m.*, s.Name AS LeaderName
    FROM Modules m
    LEFT JOIN Staff s ON m.ModuleLeaderID = s.StaffID
    ORDER BY m.ModuleName
")->fetchAll();
?>

<div class="page-container">
    <div class="page-header">
        <h1>Manage Modules</h1>
        <a href="module_edit.php" class="add-btn">
            <i class="fas fa-plus"></i> Add New Module <!-- Add button -->
        </a>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Module Name</th>
                        <th>Leader</th>
                        <th>Credits</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modules as $m): ?>
                        <tr>
                            <td><strong><?= e($m['ModuleName']) ?></strong></td> <!-- Module name -->
                            <td><?= e($m['LeaderName'] ?? '—') ?></td> <!-- Leader name -->
                            <td>
                                <?php if (isset($m['Credits']) && $m['Credits']): ?>
                                    <span class="credit-badge"><?= $m['Credits'] ?> credits</span>
                                <?php else: ?>
                                    <span class="credit-badge">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?= truncate($m['Description'] ?? '', 60) ?></td> <!-- Truncated description -->
                            <td class="action-buttons">
                                <!-- Edit button -->
                                <a href="module_edit.php?id=<?= $m['ModuleID'] ?>" class="edit-link">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <!-- Delete button with confirmation -->
                                <a href="?delete=<?= $m['ModuleID'] ?>" class="delete-link" onclick="return confirm('Delete <?= e($m['ModuleName']) ?>?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($modules)): ?>
                        <tr>
                            <td colspan="5" class="empty-row">No modules found.</td> <!-- No data -->
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?> <!-- Include footer -->