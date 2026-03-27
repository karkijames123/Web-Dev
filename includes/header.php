<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? 'Student Hub';
$baseUrl = rtrim(SITE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(SITE_NAME) ?> - <?= e($pageTitle) ?></title>

    <!-- Font Awesome Icons Only -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css">
</head>

<body>

    <!-- NAVBAR WITH INLINE STYLES -->
    <nav style="background: #0f172a; padding: 15px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <a href="<?= $baseUrl ?>/index.php" style="color: white; font-size: 24px; font-weight: bold; text-decoration: none;">
                <i class="fas fa-graduation-cap"></i> <?= e(SITE_NAME) ?>
            </a>
            <button class="navbar-toggler" id="menuToggle" style="display: none; background: none; border: none; color: white; font-size: 24px; cursor: pointer;">
                <i class="fas fa-bars"></i>
            </button>
            <div class="navbar-nav" id="navbarNav" style="display: flex; gap: 25px; margin: 0; padding: 0;">
                <a href="<?= $baseUrl ?>/index.php" style="color: #cbd5e1; text-decoration: none;">Home</a>
                <a href="<?= $baseUrl ?>/search.php" style="color: #cbd5e1; text-decoration: none;">Search</a>
                <?php if (isAdminLoggedIn()): ?>
                    <a href="<?= $baseUrl ?>/admin/index.php" style="color: #fbbf24; text-decoration: none;">Dashboard</a>
                    <a href="<?= $baseUrl ?>/admin/logout.php" style="color: #f87171; text-decoration: none;">Logout</a>
                <?php else: ?>
                    <a href="<?= $baseUrl ?>/admin/login.php" style="color: #cbd5e1; text-decoration: none;">Admin Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('menuToggle');
            const nav = document.getElementById('navbarNav');
            if (toggle && nav) {
                toggle.addEventListener('click', function() {
                    if (nav.style.display === 'flex') {
                        nav.style.display = 'none';
                    } else {
                        nav.style.display = 'flex';
                    }
                });
            }

            function checkScreenSize() {
                const toggle = document.getElementById('menuToggle');
                const nav = document.getElementById('navbarNav');
                if (window.innerWidth <= 768) {
                    if (toggle) toggle.style.display = 'block';
                    if (nav) nav.style.display = 'none';
                } else {
                    if (toggle) toggle.style.display = 'none';
                    if (nav) nav.style.display = 'flex';
                }
            }

            checkScreenSize();
            window.addEventListener('resize', checkScreenSize);
        });
    </script>
