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

<div class="container">
    <div class="text-center mb-5">
        <h1>Search Programmes</h1>
        <form method="GET" class="d-flex" style="max-width: 500px; margin: 0 auto;">
            <input type="text" name="q" class="form-control" placeholder="Search by programme name or keyword..." value="<?= e($query) ?>" required>
            <button type="submit" class="btn-primary" style="margin-left: 10px;">
                <i class="fas fa-search"></i> Search
            </button>
        </form>
    </div>

    <?php if ($query !== ''): ?>
        <h3 class="mb-4">Results for "<?= e($query) ?>"</h3>

        <?php if (empty($results)): ?>
            <div class="alert alert-info">
                No programmes found matching your search. Try different keywords.
            </div>
        <?php else: ?>
            <div class="programmes-grid">
                <?php foreach ($results as $prog):
                    // Get image for each programme
                    $image = '';
                    $progName = $prog['ProgrammeName'];

                    // Map programme names to image files
                    $imageMap = [
                        'BSc Computer Science' => 'BSc Computer Science.jpg',
                        'BSc Software Engineering' => 'BSc Software Engineering.jpg',
                        'BSc Artificial Intelligence' => 'BSc Artificial Intelligence.jpg',
                        'BSc Cyber Security' => 'BSc Cyber Security.jpg',
                        'BSc Data Science' => 'BSc Data Science.jpg',
                        'MSc Machine Learning' => 'MSc Machine Learning.jpg',
                        'MSc Cyber Security' => 'MSc Cyber Security.jpg',
                        'MSc Data Science' => 'MSc Data Science.jpg',
                        'MSc Artificial Intelligence' => 'MSc Artificial Intelligence.jpg',
                        'MSc Software Engineering' => 'MSc Software Engineering.jpg',
                    ];

                    if (isset($imageMap[$progName])) {
                        $imagePath = 'uploads/' . $imageMap[$progName];
                        if (file_exists($imagePath)) {
                            $image = $imagePath;
                        }
                    }

                    // Fallback to placeholder
                    if (empty($image)) {
                        $image = 'https://via.placeholder.com/400x250/667eea/ffffff?text=' . urlencode($progName);
                    }
                ?>
                    <div class="programme-card">
                        <div class="card-image">
                            <img src="<?= e($image) ?>" alt="<?= e($progName) ?>">
                            <span class="level-badge"><?= e($prog['LevelName']) ?></span>
                        </div>
                        <div class="card-content">
                            <h3><?= e($progName) ?></h3>
                            <p><?= truncate($prog['Description'], 100) ?></p>
                            <a href="programme.php?id=<?= $prog['ProgrammeID'] ?>" class="btn-view">
                                View Details <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
