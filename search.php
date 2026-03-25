<?php
$pageTitle = 'Search Programmes';
require 'includes/header.php';

$query = trim($_GET['q'] ?? '');
$results = [];

if ($query !== '') {
    $searchTerm = "%$query%";
    $stmt = $pdo->prepare("
        SELECT p.*, l.LevelName, s.Name AS LeaderName
        FROM Programmes p
        JOIN Levels l ON p.LevelID = l.LevelID
        LEFT JOIN Staff s ON p.ProgrammeLeaderID = s.StaffID
        WHERE p.is_published = 1 
        AND (p.ProgrammeName LIKE ? OR p.Description LIKE ?)
        ORDER BY p.ProgrammeName
    ");
    $stmt->execute([$searchTerm, $searchTerm]);
    $results = $stmt->fetchAll();
}
?>

<div class="container py-5">
    <div class="row justify-content-center mb-5">
        <div class="col-md-8">
            <h1 class="text-center mb-4">Search Programmes</h1>
            <form method="GET" class="d-flex">
                <input type="text" name="q" class="form-control form-control-lg me-2"
                    placeholder="Search by programme name or keyword..."
                    value="<?= e($query) ?>" required>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
    </div>

    <?php if ($query !== ''): ?>
        <div class="row">
            <div class="col">
                <h3 class="mb-4">Results for "<?= e($query) ?>"</h3>

                <?php if (empty($results)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No programmes found matching your search. Try different keywords.
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($results as $prog):
                            $image = !empty($prog['Image']) ? 'uploads/' . $prog['Image'] : 'https://via.placeholder.com/400x250?text=' . urlencode($prog['ProgrammeName']);
                        ?>
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm">
                                    <div class="row g-0">
                                        <div class="col-md-4">
                                            <img src="<?= e($image) ?>" class="img-fluid rounded-start h-100" style="object-fit: cover;" alt="<?= e($prog['ProgrammeName']) ?>">
                                        </div>
                                        <div class="col-md-8">
                                            <div class="card-body">
                                                <h5 class="card-title"><?= e($prog['ProgrammeName']) ?></h5>
                                                <span class="badge bg-info mb-2"><?= e($prog['LevelName']) ?></span>
                                                <p class="card-text small"><?= truncate($prog['Description'], 100) ?></p>
                                                <a href="programme.php?id=<?= $prog['ProgrammeID'] ?>" class="btn btn-sm btn-primary">
                                                    View Details <i class="fas fa-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>