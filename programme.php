<?php
// Set page title for the browser tab
$pageTitle = 'Programme Details';
require 'includes/header.php';

// Check if programme ID is provided in URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

// Get programme ID from URL
$programmeId = (int)$_GET['id'];

// Fetch programme details from database
$stmt = $pdo->prepare("
    SELECT p.*, l.LevelName, s.Name AS LeaderName 
    FROM Programmes p 
    JOIN Levels l ON p.LevelID = l.LevelID 
    LEFT JOIN Staff s ON p.ProgrammeLeaderID = s.StaffID 
    WHERE p.ProgrammeID = ? AND p.is_published = 1
");
$stmt->execute([$programmeId]);
$programme = $stmt->fetch();

// If programme not found, redirect to homepage
if (!$programme) {
    header("Location: index.php");
    exit;
}

// Fetch all modules for this programme
$modulesStmt = $pdo->prepare("
    SELECT pm.Year, m.ModuleName, m.Credits, m.Description, s.Name AS LeaderName
    FROM ProgrammeModules pm
    JOIN Modules m ON pm.ModuleID = m.ModuleID
    LEFT JOIN Staff s ON m.ModuleLeaderID = s.StaffID
    WHERE pm.ProgrammeID = ?
    ORDER BY pm.Year, m.ModuleName
");
$modulesStmt->execute([$programmeId]);
$modules = $modulesStmt->fetchAll();

// Group modules by academic year
$modulesByYear = [];
foreach ($modules as $module) {
    $modulesByYear[$module['Year']][] = $module;
}

// Array of background images for different programme types
$backgroundImages = [
    'computer' => 'https://images.pexels.com/photos/577585/pexels-photo-577585.jpeg?auto=compress&cs=tinysrgb&w=1600',
    'software' => 'https://images.pexels.com/photos/1181244/pexels-photo-1181244.jpeg?auto=compress&cs=tinysrgb&w=1600',
    'cyber' => 'https://images.pexels.com/photos/5380642/pexels-photo-5380642.jpeg?auto=compress&cs=tinysrgb&w=1600',
    'data' => 'https://images.pexels.com/photos/669615/pexels-photo-669615.jpeg?auto=compress&cs=tinysrgb&w=1600',
    'ai' => 'https://images.pexels.com/photos/8386440/pexels-photo-8386440.jpeg?auto=compress&cs=tinysrgb&w=1600',
    'default' => 'https://images.pexels.com/photos/256490/pexels-photo-256490.jpeg?auto=compress&cs=tinysrgb&w=1600'
];

// Choose background image based on programme name
$programmeName = strtolower($programme['ProgrammeName']);
$bgImage = $backgroundImages['default'];

if (strpos($programmeName, 'computer') !== false) {
    $bgImage = $backgroundImages['computer'];
} elseif (strpos($programmeName, 'software') !== false) {
    $bgImage = $backgroundImages['software'];
} elseif (strpos($programmeName, 'cyber') !== false || strpos($programmeName, 'security') !== false) {
    $bgImage = $backgroundImages['cyber'];
} elseif (strpos($programmeName, 'data') !== false) {
    $bgImage = $backgroundImages['data'];
} elseif (strpos($programmeName, 'ai') !== false || strpos($programmeName, 'artificial') !== false) {
    $bgImage = $backgroundImages['ai'];
} elseif (strpos($programmeName, 'machine') !== false || strpos($programmeName, 'learning') !== false) {
    $bgImage = $backgroundImages['ai'];
}
?>

<!-- Hero Section with Dynamic Background -->
<div class="programme-hero" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.7)), url('<?= $bgImage ?>'); background-size: cover; background-position: center; min-height: 400px;">
    <div class="container">
        <div class="hero-content">
            <h1><?= e($programme['ProgrammeName']) ?></h1>
            <p class="level"><?= e($programme['LevelName']) ?></p>
            <?php if ($programme['LeaderName']): ?>
                <p><i class="fas fa-user"></i> Programme Leader: <?= e($programme['LeaderName']) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main Content - Two Column Layout -->
<div class="container">
    <div class="two-column-layout">
        
        <!-- Left Column: Programme Info and Modules -->
        <div class="left-column">
            <!-- Programme Overview -->
            <div class="info-card">
                <h3>Programme Overview</h3>
                <p><?= nl2br(e($programme['Description'])) ?></p>
            </div>

            <!-- Module Structure (Grouped by Year) -->
            <div class="info-card">
                <h3>Module Structure</h3>
                <?php if (empty($modulesByYear)): ?>
                    <p>Module details coming soon.</p>
                <?php else: ?>
                    <?php foreach ($modulesByYear as $year => $yearModules): ?>
                        <h4>Year <?= $year ?></h4>
                        <div class="module-list">
                            <?php foreach ($yearModules as $module): ?>
                                <div class="module-item">
                                    <h5><?= e($module['ModuleName']) ?></h5>
                                    <div class="module-meta">
                                        <span><i class="fas fa-user"></i> Leader: <?= e($module['LeaderName'] ?? 'TBC') ?></span>
                                        <span><i class="fas fa-star"></i> Credits: <?= $module['Credits'] ?? 'TBC' ?></span>
                                    </div>
                                    <p><?= truncate($module['Description'] ?? '', 100) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Interest Registration Form -->
        <div class="right-column">
            <div class="interest-card">
                <h4>Register Your Interest</h4>

                <!-- Success Message -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">Thank you! We'll contact you soon.</div>
                <?php endif; ?>

                <!-- Error Message (duplicate or invalid) -->
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?= $_GET['error'] == 'duplicate' ? 'You already registered for this programme.' : 'Please check your details and try again.' ?>
                    </div>
                <?php endif; ?>

                <!-- Registration Form -->
                <form action="interest.php" method="POST">
                    <input type="hidden" name="programme_id" value="<?= $programmeId ?>">

                    <div class="form-field">
                        <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                    </div>

                    <div class="form-field">
                        <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                    </div>

                    <div class="form-field">
                        <input type="tel" name="phone" class="form-control" placeholder="Phone (optional)">
                    </div>

                    <div class="form-field">
                        <textarea name="message" class="form-control" rows="2" placeholder="Message (optional)"></textarea>
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%;">Submit Interest</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>
