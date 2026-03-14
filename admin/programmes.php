<?php require '../includes/db.php';
require '../includes/functions.php';
requireAdmin();
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $stmt = $pdo->prepare("UPDATE Programmes SET is_published = NOT is_published WHERE ProgrammeID = ?");
    $stmt->execute([$id]);
    header("Location: programmes.php");
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM Programmes WHERE ProgrammeID = ?");
    $stmt->execute([$id]);
    header("Location: programmes.php");
}
$stmt = $pdo->query("SELECT p.*, l.LevelName FROM Programmes p JOIN Levels l ON p.LevelID = l.LevelID");
?>
<h1>Manage Programmes</h1>
<a href="programme_edit.php" class="btn btn-success">Add New</a>
<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Level</th>
            <th>Published</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($stmt->fetchAll() as $p): ?>
            <tr>
                <td><?= e($p['ProgrammeName']) ?></td>
                <td><?= e($p['LevelName']) ?></td>
                <td><?= $p['is_published'] ? 'Yes' : 'No' ?></td>
                <td>
                    <a href="programme_edit.php?id=<?= $p['ProgrammeID'] ?>">Edit</a> |
                    <a href="?delete=<?= $p['ProgrammeID'] ?>" onclick="return confirm('Delete?')">Delete</a> |
                    <a href="?toggle=<?= $p['ProgrammeID'] ?>"><?= $p['is_published'] ? 'Unpublish' : 'Publish' ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>