<?php require 'includes/db.php';
require 'includes/header.php'; ?>
<h1>Search Programmes</h1>
<form method="GET">
    <input type="text" name="q" class="form-control d-inline-block w-50" placeholder="Search by keyword" value="<?= e($_GET['q'] ?? '') ?>">
    <button type="submit" class="btn btn-primary">Search</button>
</form>
<div class="row mt-4">
    <?php
    if (!empty($_GET['q'])) {
        $query = "%" . $_GET['q'] . "%";
        $stmt = $pdo->prepare("SELECT p.*, l.LevelName FROM Programmes p JOIN Levels l ON p.LevelID = l.LevelID WHERE is_published = 1 AND (ProgrammeName LIKE ? OR Description LIKE ?) ORDER BY ProgrammeName");
        $stmt->execute([$query, $query]);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $prog) {
            echo '<div class="col-md-4 mb-4"><div class="card">';
            echo '<div class="card-body"><h5>' . e($prog['ProgrammeName']) . '</h5><a href="programme.php?id=' . $prog['ProgrammeID'] . '">Details</a></div></div></div>';
        }
    }
    ?>
</div>
<?php require 'includes/footer.php'; ?>