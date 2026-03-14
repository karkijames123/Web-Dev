<?php
require 'includes/db.php';
require 'includes/header.php';

// Fix for the "Undefined index" warning
$level = $_GET['level'] ?? '';
?>

<h1>Available Programmes</h1>

<form method="GET" class="mb-4">
    <select name="level" class="form-select d-inline-block w-auto">
        <option value="">All Levels</option>
        <option value="1" <?= $level == '1' ? 'selected' : '' ?>>Undergraduate</option>
        <option value="2" <?= $level == '2' ? 'selected' : '' ?>>Postgraduate</option>
    </select>
    <button type="submit" class="btn btn-primary">Filter</button>
</form>

<div class="row">
    <?php
    $where = "WHERE p.is_published = 1";
    $params = [];
    if (!empty($level)) {
        $where .= " AND p.LevelID = ?";
        $params[] = (int)$level;
    }

    $stmt = $pdo->prepare("SELECT p.*, l.LevelName FROM Programmes p JOIN Levels l ON p.LevelID = l.LevelID $where ORDER BY ProgrammeName");
    $stmt->execute($params);

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $prog) {
        // IMAGE LOGIC: Check if image exists, else use placeholder
        $img = !empty($prog['Image']) ? $prog['Image'] : 'placeholder.jpg';

        echo '<div class="col-md-4 mb-4"><div class="card h-100 shadow-sm">';
        echo '  <img src="uploads/' . e($img) . '" class="card-img-top" style="height:200px; object-fit:cover;" alt="' . e($prog['ProgrammeName']) . '">';
        echo '  <div class="card-body">';
        echo '    <h5 class="card-title">' . e($prog['ProgrammeName']) . '</h5>';
        echo '    <p class="badge bg-secondary">' . e($prog['LevelName']) . '</p>';
        echo '    <a href="programme.php?id=' . $prog['ProgrammeID'] . '" class="btn btn-outline-primary d-block">Details</a>';
        echo '  </div>';
        echo '</div></div>';
    }
    ?>
</div>

<?php require 'includes/footer.php'; ?>