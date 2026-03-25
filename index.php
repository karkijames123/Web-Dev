<?php

$pageTitle = 'Available Programmes';
require 'includes/header.php';

// Get selected level from URL parameter 
$level = $_GET['level'] ?? '';
// Fetch all programme levels from database
$levels = $pdo->query("SELECT * FROM Levels")->fetchAll();

// SQL query to get published programmes with level and leader info
$sql = "SELECT p.*, l.LevelName, s.Name AS LeaderName 
        FROM Programmes p 
        JOIN Levels l ON p.LevelID = l.LevelID 
        LEFT JOIN Staff s ON p.ProgrammeLeaderID = s.StaffID 
        WHERE p.is_published = 1";

// Array to hold query parameters
$params = [];

if ($level !== '') {
    $sql .= " AND p.LevelID = ?";
    $params[] = (int)$level;
}
// Order programmes alphabetically
$sql .= " ORDER BY p.ProgrammeName";

// Prepare and execute the query
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$programmes = $stmt->fetchAll();

// Function to get appropriate image for each programme
function getProgrammeImage($programmeName)
{
    // Path to uploads folder
    $uploadDir = 'uploads/';

    // Map specific programme names to their image files
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

    // If exact match found in map, use that image
    if (isset($imageMap[$programmeName])) {
        $imagePath = $uploadDir . $imageMap[$programmeName];
        if (file_exists($imagePath)) {
            return $imagePath;
        }
    }

    // Try to find image by partial keyword match
    $nameLower = strtolower($programmeName);
    $files = glob($uploadDir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

    // Loop through all images to find matching keywords
    foreach ($files as $file) {
        $fileName = pathinfo($file, PATHINFO_FILENAME);
        $fileLower = strtolower($fileName);

        // Check for computer science keywords
        if (strpos($nameLower, 'computer') !== false && strpos($fileLower, 'computer') !== false) {
            return $file;
        }
        // Check for software engineering keywords
        if (strpos($nameLower, 'software') !== false && strpos($fileLower, 'software') !== false) {
            return $file;
        }
        // Check for artificial intelligence keywords
        if (strpos($nameLower, 'artificial') !== false && strpos($fileLower, 'artificial') !== false) {
            return $file;
        }
        // Check for cyber security keywords
        if (strpos($nameLower, 'cyber') !== false && strpos($fileLower, 'cyber') !== false) {
            return $file;
        }
        // Check for data science keywords
        if (strpos($nameLower, 'data') !== false && strpos($fileLower, 'data') !== false) {
            return $file;
        }
        // Check for machine learning keywords
        if (strpos($nameLower, 'machine') !== false && strpos($fileLower, 'machine') !== false) {
            return $file;
        }
    }
    // If any image exists, return the first one
    if (!empty($files)) {
        return $files[0];
    }

    // Default fallback image from external source
    return 'https://images.pexels.com/photos/256490/pexels-photo-256490.jpeg?auto=compress&cs=tinysrgb&w=600&h=400&fit=crop';
}
?>

<!-- Hero Section - Welcome Banner -->
<div class="hero-section bg-primary text-white py-5 mb-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container text-center py-5">
        <h1 class="display-3 fw-bold mb-4">Welcome to <?= e(SITE_NAME) ?></h1>
        <p class="lead mb-4">Discover your path to success with our world-class programmes</p>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <!-- Search Form -->
                <form action="search.php" method="GET" class="d-flex">
                    <input type="text" name="q" class="form-control form-control-lg me-2" placeholder="Search programmes...">
                    <button type="submit" class="btn btn-light btn-lg">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-3">Our Programmes</h2>
            <!-- Level Filter Form -->
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <select name="level" class="form-select" onchange="this.form.submit()">
                        <option value="">All Levels</option>
                        <!-- Loop through levels to populate dropdown -->
                        <?php foreach ($levels as $lvl): ?>
                            <option value="<?= $lvl['LevelID'] ?>" <?= $level == $lvl['LevelID'] ? 'selected' : '' ?>>
                                <?= e($lvl['LevelName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Check if programmes exist -->
    <?php if (empty($programmes)): ?>
        <!-- Show message if no programmes found -->
        <div class="alert alert-info">No programmes found matching your criteria.</div>
    <?php else: ?>
        <!-- Display programmes in a responsive grid -->
        <div class="row g-4">
            <?php foreach ($programmes as $prog):

                $cardImage = getProgrammeImage($prog['ProgrammeName']);
            ?>
                <div class="col-md-6 col-lg-4">
                    <!-- Programme Card -->
                    <div class="card programme-card h-100 shadow-sm">
                        <!-- Card Image Section -->
                        <div class="card-img-top-wrapper" style="height: 200px; overflow: hidden; position: relative;">
                            <img src="<?= e($cardImage) ?>" class="card-img-top" alt="<?= e($prog['ProgrammeName']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <!-- Level Badge Overlay -->
                            <div class="overlay" style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.7)); padding: 20px;">
                                <span class="badge bg-light text-dark"><?= e($prog['LevelName']) ?></span>
                            </div>
                        </div>
                        <!-- Card Content -->
                        <div class="card-body">
                            <h5 class="card-title"><?= e($prog['ProgrammeName']) ?></h5>
                            <p class="card-text text-muted"><?= truncate($prog['Description'], 100) ?></p>
                            <!-- Programme Leader (if exists) -->
                            <?php if ($prog['LeaderName']): ?>
                                <small class="text-muted">
                                    <i class="fas fa-user-graduate me-1"></i> Lead: <?= e($prog['LeaderName']) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                        <!-- Card Footer with View Button -->
                        <div class="card-footer bg-transparent border-top-0 pb-3">
                            <a href="programme.php?id=<?= $prog['ProgrammeID'] ?>" class="btn btn-primary w-100">
                                View Details <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Inline CSS for Programme Card Styling -->
<style>
    /* Programme card container */
    .programme-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        border: none;
    }

    /* Hover effect - card lifts up */
    .programme-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    /* Image wrapper for positioning */
    .card-img-top-wrapper {
        position: relative;
    }

    /* Card image styling */
    .card-img-top {
        transition: transform 0.5s ease;
    }

    /* Image zoom on card hover */
    .programme-card:hover .card-img-top {
        transform: scale(1.05);
    }

    /* Card footer background */
    .card-footer {
        background: white;
    }

    /* Primary button with gradient */
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        padding: 10px;
        font-weight: 500;
    }

    /* Button hover effect */
    .btn-primary:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46a0 100%);
        transform: translateY(-2px);
    }

    /* Light button styling */
    .btn-light {
        border-radius: 8px;
    }

    /* Form input styling */
    .form-select,
    .form-control {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    /* Form input focus effect */
    .form-select:focus,
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Card body padding */
    .card-body {
        padding: 1.25rem;
    }

    /* Card title styling */
    .card-title {
        font-weight: 600;
        margin-bottom: 0.75rem;
    }

    /* Badge styling */
    .badge {
        font-weight: 500;
        padding: 0.5rem 1rem;
    }
</style>

<!-- Include footer -->
<?php require 'includes/footer.php'; ?>
