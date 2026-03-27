<?php
$pageTitle = 'Available Programmes';
require 'includes/header.php';

$level = $_GET['level'] ?? '';
$levels = $pdo->query("SELECT * FROM Levels")->fetchAll();

$sql = "SELECT p.*, l.LevelName, s.Name AS LeaderName 
        FROM Programmes p 
        JOIN Levels l ON p.LevelID = l.LevelID 
        LEFT JOIN Staff s ON p.ProgrammeLeaderID = s.StaffID 
        WHERE p.is_published = 1";

$params = [];

if ($level !== '') {
    $sql .= " AND p.LevelID = ?";
    $params[] = (int)$level;
}
$sql .= " ORDER BY p.ProgrammeName";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$programmes = $stmt->fetchAll();

function getProgrammeImage($programmeName)
{
    $uploadDir = 'uploads/';
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

    if (isset($imageMap[$programmeName])) {
        $imagePath = $uploadDir . $imageMap[$programmeName];
        if (file_exists($imagePath)) {
            return $imagePath;
        }
    }
    return 'https://via.placeholder.com/800x400/667eea/ffffff?text=' . urlencode($programmeName);
}
?>

<div class="hero-section">
    <div class="container text-center">
        <h1>Welcome to <?= e(SITE_NAME) ?></h1>
        <p>Discover your path to success with our world-class programmes</p>
        <div class="search-form">
            <form action="search.php" method="GET" class="d-flex">
                <input type="text" name="q" class="form-control" placeholder="Search programmes...">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<div class="container">
    <div class="mb-4">
        <h2>Our Programmes</h2>
        <form method="GET">
            <select name="level" class="form-select" onchange="this.form.submit()">
                <option value="">All Levels</option>
                <?php foreach ($levels as $lvl): ?>
                    <option value="<?= $lvl['LevelID'] ?>" <?= $level == $lvl['LevelID'] ? 'selected' : '' ?>>
                        <?= e($lvl['LevelName']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if (empty($programmes)): ?>
        <div class="alert alert-info">No programmes found matching your criteria.</div>
    <?php else: ?>
        <div class="programmes-grid">
            <?php foreach ($programmes as $prog):
                $cardImage = getProgrammeImage($prog['ProgrammeName']);
            ?>
                <div class="programme-card">
                    <div class="card-image">
                        <img src="<?= e($cardImage) ?>" alt="<?= e($prog['ProgrammeName']) ?>">
                        <span class="level-badge"><?= e($prog['LevelName']) ?></span>
                    </div>
                    <div class="card-content">
                        <h3><?= e($prog['ProgrammeName']) ?></h3>
                        <p><?= truncate($prog['Description'], 100) ?></p>
                        <?php if ($prog['LeaderName']): ?>
                            <div class="leader-info">
                                <i class="fas fa-user-graduate"></i> Lead: <?= e($prog['LeaderName']) ?>
                            </div>
                        <?php endif; ?>
                        <a href="programme.php?id=<?= $prog['ProgrammeID'] ?>" class="btn-view">
                            View Details <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>
