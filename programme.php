<?php

// PROGRAMME DETAILS PAGE
// Displays complete information about a specific programme
// including modules, leader, and registration form


// Set page title for browser tab and header
$pageTitle = 'Programme Details';
require 'includes/header.php';


// VALIDATE PROGRAMME ID

// Check if programme ID is provided in URL and is a valid number
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Invalid ID - redirect to homepage
    header("Location: index.php");
    exit;
}

// Get and sanitize programme ID from URL
$programmeId = (int)$_GET['id'];

// FETCH PROGRAMME DETAILS

// Query to get programme information with level and leader details
$stmt = $pdo->prepare("
    SELECT p.*, l.LevelName, s.Name AS LeaderName 
    FROM Programmes p 
    JOIN Levels l ON p.LevelID = l.LevelID 
    LEFT JOIN Staff s ON p.ProgrammeLeaderID = s.StaffID 
    WHERE p.ProgrammeID = ? AND p.is_published = 1
");
$stmt->execute([$programmeId]);
$programme = $stmt->fetch();

// If programme not found or not published, redirect to homepage
if (!$programme) {
    header("Location: index.php");
    exit;
}


// FETCH MODULES FOR THIS PROGRAMME

// Get all modules linked to this programme with year, credits, and leader
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


// GROUP MODULES BY ACADEMIC YEAR

// Organize modules into arrays grouped by year (Year 1, Year 2, etc.)
$modulesByYear = [];
foreach ($modules as $module) {
    $modulesByYear[$module['Year']][] = $module;
}


// DYNAMIC HERO BACKGROUND IMAGES

// Array of background images for different programme categories
$backgroundImages = [
    // Computer Science related programmes
    'computer' => 'https://images.pexels.com/photos/577585/pexels-photo-577585.jpeg?auto=compress&cs=tinysrgb&w=1600',
    'software' => 'https://images.pexels.com/photos/1181244/pexels-photo-1181244.jpeg?auto=compress&cs=tinysrgb&w=1600',

    // Cyber Security related programmes
    'cyber' => 'https://images.pexels.com/photos/5380642/pexels-photo-5380642.jpeg?auto=compress&cs=tinysrgb&w=1600',

    // Data Science related programmes
    'data' => 'https://images.pexels.com/photos/669615/pexels-photo-669615.jpeg?auto=compress&cs=tinysrgb&w=1600',

    // AI and Machine Learning related programmes
    'ai' => 'https://images.pexels.com/photos/8386440/pexels-photo-8386440.jpeg?auto=compress&cs=tinysrgb&w=1600',

    // Default fallback image
    'default' => 'https://images.pexels.com/photos/256490/pexels-photo-256490.jpeg?auto=compress&cs=tinysrgb&w=1600'
];

// Select appropriate image based on programme name keywords
$programmeName = strtolower($programme['ProgrammeName']);
$bgImage = $backgroundImages['default'];

// Check programme name for keywords and assign matching background
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


<!-- HERO SECTION WITH BACKGROUND IMAGE -->

<div class="hero-section" style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.7)), url('<?= $bgImage ?>'); background-size: cover; background-position: center; min-height: 400px;">
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8 text-white">
                <!-- Programme Title -->
                <h1 class="display-4 fw-bold"><?= e($programme['ProgrammeName']) ?></h1>
                <!-- Programme Level (Undergraduate/Postgraduate) -->
                <p class="lead"><?= e($programme['LevelName']) ?></p>
                <!-- Programme Leader (if assigned) -->
                <?php if ($programme['LeaderName']): ?>
                    <p><i class="fas fa-user"></i> Programme Leader: <?= e($programme['LeaderName']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="container py-4">
    <div class="row">

        <!-- LEFT COLUMN - PROGRAMME DETAILS AND MODULES -->

        <div class="col-lg-8">
            <!-- Programme Overview Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h3>Programme Overview</h3>
                    <!-- nl2br converts newlines to HTML line breaks -->
                    <p><?= nl2br(e($programme['Description'])) ?></p>
                </div>
            </div>

            <!-- Module Structure Card -->
            <div class="card">
                <div class="card-body">
                    <h3>Module Structure</h3>

                    <!-- Check if modules exist for this programme -->
                    <?php if (empty($modulesByYear)): ?>
                        <p>Module details coming soon.</p>
                    <?php else: ?>
                        <!-- Loop through each year and display modules -->
                        <?php foreach ($modulesByYear as $year => $yearModules): ?>
                            <h4 class="mt-3">Year <?= $year ?></h4>
                            <div class="list-group mb-3">
                                <!-- Display each module in this academic year -->
                                <?php foreach ($yearModules as $module): ?>
                                    <div class="list-group-item">
                                        <h5><?= e($module['ModuleName']) ?></h5>
                                        <!-- Module metadata: Leader and Credits -->
                                        <small class="text-muted">
                                            Leader: <?= e($module['LeaderName'] ?? 'TBC') ?> |
                                            Credits: <?= $module['Credits'] ?? 'TBC' ?>
                                        </small>
                                        <!-- Short module description (truncated to 100 chars) -->
                                        <p class="mb-0 small"><?= truncate($module['Description'] ?? '', 100) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>


        <!-- RIGHT COLUMN - INTEREST REGISTRATION FORM -->

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5>Register Your Interest</h5>

                    <!-- Success message after form submission -->
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">Thank you! We'll contact you soon.</div>
                    <?php endif; ?>

                    <!-- Error message for duplicate or invalid submission -->
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_GET['error'] == 'duplicate' ? 'You already registered for this programme.' : 'Please check your details and try again.' ?>
                        </div>
                    <?php endif; ?>

                    <!-- Interest Registration Form -->
                    <form action="interest.php" method="POST">
                        <!-- Hidden field to store programme ID -->
                        <input type="hidden" name="programme_id" value="<?= $programmeId ?>">

                        <!-- Student Name Field (Required) -->
                        <div class="mb-3">
                            <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                        </div>

                        <!-- Email Field (Required with validation) -->
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                        </div>

                        <!-- Phone Field (Optional) -->
                        <div class="mb-3">
                            <input type="tel" name="phone" class="form-control" placeholder="Phone (optional)">
                        </div>

                        <!-- Message Field (Optional) -->
                        <div class="mb-3">
                            <textarea name="message" class="form-control" rows="2" placeholder="Message (optional)"></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary w-100">Submit Interest</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- CUSTOM CSS FOR THIS PAGE -->

<style>
    /* Hero section positioning context */
    .hero-section {
        position: relative;
    }

    /* Dark overlay on hero image to make text readable */
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        z-index: 0;
    }

    /* Ensure content appears above the overlay */
    .hero-section>* {
        position: relative;
        z-index: 1;
    }

    /* Card styling with rounded corners and shadow */
    .card {
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    /* List group items styling */
    .list-group-item {
        border-left: none;
        border-right: none;
    }

    /* Remove top border from first item */
    .list-group-item:first-child {
        border-top: none;
    }

    /* Remove bottom border from last item */
    .list-group-item:last-child {
        border-bottom: none;
    }
</style>

<!-- Include footer -->
<?php require 'includes/footer.php'; ?>