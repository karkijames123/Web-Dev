<?php
// admin/modules.php

require '../includes/db.php';
require '../includes/functions.php';
requireAdmin();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM Modules WHERE ModuleID = ?");
    $stmt->execute([$id]);
    header("Location: modules.php");
    exit;
}

$stmt = $pdo->query("
    SELECT m.ModuleID, m.ModuleName, m.Description, s.Name AS LeaderName
    FROM Modules m
    LEFT JOIN Staff s ON m.ModuleLeaderID = s.StaffID
    ORDER BY m.ModuleName
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Modules - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-4">
        <h1>Manage Modules</h1>
        <a href="module_edit.php" class="btn btn-success mb-3">Add New Module</a>
        <a href="index.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Module Name</th>
                    <th>Leader</th>
                    <th>Description (short)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $module): ?>
                    <tr>
                        <td><?= htmlspecialchars($module['ModuleName']) ?></td>
                        <td><?= htmlspecialchars($module['LeaderName'] ?? '—') ?></td>
                        <td><?= htmlspecialchars(substr($module['Description'] ?? '', 0, 80)) ?>...</td>
                        <td>
                            <a href="module_edit.php?id=<?= $module['ModuleID'] ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="?delete=<?= $module['ModuleID'] ?>"
                                class="btn btn-sm btn-danger delete-confirm"
                                data-name="<?= htmlspecialchars($module['ModuleName']) ?>">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/script.js"></script>
</body>

</html>