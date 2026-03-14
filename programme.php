<?php require 'includes/db.php';
require 'includes/header.php';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) die("Invalid ID");
$stmt = $pdo->prepare("SELECT p.*, l.LevelName, s.Name AS Leader FROM Programmes p JOIN Levels l ON p.LevelID = l.LevelID LEFT JOIN Staff s ON p.ProgrammeLeaderID = s.StaffID WHERE p.ProgrammeID = ? AND is_published = 1");
$stmt->execute([$_GET['id']]);
$prog = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$prog) die("Not found");
?>
<h1><?= e($prog['ProgrammeName']) ?></h1>
<p>Level: <?= e($prog['LevelName']) ?></p>
<p>Leader: <?= e($prog['Leader'] ?? 'None') ?></p>
<img src="uploads/<?= e($prog['Image'] ?? 'placeholder.jpg') ?>" alt="<?= e($prog['ProgrammeName']) ?>" class="img-fluid">
<p><?= nl2br(e($prog['Description'])) ?></p>
<h3>Modules</h3>
<?php
$stmt = $pdo->prepare("SELECT Year, m.ModuleName FROM ProgrammeModules pm JOIN Modules m ON pm.ModuleID = m.ModuleID WHERE ProgrammeID = ? ORDER BY Year");
$stmt->execute([$_GET['id']]);
$modules = $stmt->fetchAll(PDO::FETCH_GROUP);
foreach ($modules as $year => $mods) {
    echo "<h4>Year $year</h4><ul>";
    foreach ($mods as $mod) echo "<li>" . e($mod['ModuleName']) . "</li>";
    echo "</ul>";
}
?>
<?php
if (isset($_GET['success'])) echo '<div class="alert alert-success">Interest registered!</div>';
if (isset($_GET['error']) && $_GET['error'] == 'duplicate') echo '<div class="alert alert-warning">Already registered.</div>';
if (isset($_GET['error']) && $_GET['error'] == 'invalid') echo '<div class="alert alert-danger">Invalid input.</div>';
?>

<!-- Interest form (Person B will fill) -->
<form method="POST" action="interest.php">
    <input type="hidden" name="programme_id" value="<?= $_GET['id'] ?>">
    <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
    <button type="submit" class="btn btn-success">Register Interest</button>
</form>
<?php require 'includes/footer.php'; ?>