<?php
// Set page title for browser tab
$pageTitle = 'Admin Dashboard';
require '../includes/header.php';
requireAdmin(); // Check if admin is logged in

// Get statistics for dashboard cards
$stats = [
    'programmes' => $pdo->query("SELECT COUNT(*) FROM Programmes")->fetchColumn(),
    'published' => $pdo->query("SELECT COUNT(*) FROM Programmes WHERE is_published = 1")->fetchColumn(),
    'modules' => $pdo->query("SELECT COUNT(*) FROM Modules")->fetchColumn(),
    'staff' => $pdo->query("SELECT COUNT(*) FROM Staff")->fetchColumn(),
    'students' => $pdo->query("SELECT COUNT(*) FROM InterestedStudents")->fetchColumn(),
    'new_today' => $pdo->query("SELECT COUNT(*) FROM InterestedStudents WHERE DATE(RegisteredAt) = CURDATE()")->fetchColumn(),
];

// Get 5 most recent student registrations
$recent = $pdo->query("
    SELECT i.*, p.ProgrammeName 
    FROM InterestedStudents i 
    JOIN Programmes p ON i.ProgrammeID = p.ProgrammeID 
    ORDER BY i.RegisteredAt DESC 
    LIMIT 5
")->fetchAll();

// Get monthly registration data for chart (last 6 months)
$monthlyRegistrations = $pdo->query("
    SELECT 
        DATE_FORMAT(RegisteredAt, '%Y-%m') as month,
        DATE_FORMAT(RegisteredAt, '%M') as month_name,
        COUNT(*) as count
    FROM InterestedStudents
    WHERE RegisteredAt >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(RegisteredAt, '%Y-%m')
    ORDER BY month
")->fetchAll();
?>

<!-- Dashboard Container -->
<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
        <span class="welcome-badge">
            <i class="fas fa-user-circle"></i> Welcome, <?= e($_SESSION['admin_username']) ?>
        </span>
    </div>

    <!-- Statistics Cards Row -->
    <div class="stats-grid">
        <!-- Programmes Card -->
        <div class="stats-card stats-blue">
            <div class="stats-info">
                <h3><i class="fas fa-graduation-cap"></i> Programmes</h3>
                <div class="stats-number"><?= $stats['programmes'] ?></div>
                <small><?= $stats['published'] ?> published</small>
            </div>
            <i class="fas fa-graduation-cap stats-icon"></i>
        </div>

        <!-- Modules Card -->
        <div class="stats-card stats-green">
            <div class="stats-info">
                <h3><i class="fas fa-book"></i> Modules</h3>
                <div class="stats-number"><?= $stats['modules'] ?></div>
                <small>Total modules</small>
            </div>
            <i class="fas fa-book stats-icon"></i>
        </div>

        <!-- Staff Card -->
        <div class="stats-card stats-teal">
            <div class="stats-info">
                <h3><i class="fas fa-users"></i> Staff</h3>
                <div class="stats-number"><?= $stats['staff'] ?></div>
                <small>Staff members</small>
            </div>
            <i class="fas fa-users stats-icon"></i>
        </div>

        <!-- Student Leads Card -->
        <div class="stats-card stats-orange">
            <div class="stats-info">
                <h3><i class="fas fa-user-graduate"></i> Student Leads</h3>
                <div class="stats-number"><?= $stats['students'] ?></div>
                <small><?= $stats['new_today'] ?> new today</small>
            </div>
            <i class="fas fa-user-graduate stats-icon"></i>
        </div>
    </div>

    <!-- Chart Section - Shows registration trends -->
    <?php if (!empty($monthlyRegistrations)): ?>
        <div class="chart-section" style="background: white; border-radius: 16px; padding: 20px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 20px; font-size: 18px;">
                <i class="fas fa-chart-line" style="color: #667eea;"></i>
                Student Registrations (Last 6 Months)
            </h3>
            <canvas id="registrationsChart" style="max-height: 300px; width: 100%;"></canvas>
        </div>
    <?php endif; ?>

    <!-- Quick Action Buttons -->
    <div class="actions-section">
        <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
        <div class="actions-grid">
            <a href="programmes.php" class="action-btn action-primary">
                <i class="fas fa-list"></i>
                <span>Manage Programmes</span>
            </a>
            <a href="modules.php" class="action-btn action-success">
                <i class="fas fa-cube"></i>
                <span>Manage Modules</span>
            </a>
            <a href="staff.php" class="action-btn action-info">
                <i class="fas fa-chalkboard-user"></i>
                <span>Manage Staff</span>
            </a>
            <a href="students.php?export=1" class="action-btn action-warning">
                <i class="fas fa-download"></i>
                <span>Export Students</span>
            </a>
        </div>
    </div>

    <!-- Recent Student Leads Table -->
    <?php if (!empty($recent)): ?>
        <div class="recent-card">
            <div class="card-title">
                <i class="fas fa-clock"></i>
                <h5>Recent Student Leads</h5>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Programme</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent as $lead): ?>
                            <tr>
                                <td>
                                    <strong><?= e($lead['StudentName']) ?></strong><br>
                                    <small class="text-muted"><?= e($lead['Email']) ?></small>
                                </td>
                                <td><?= e($lead['ProgrammeName']) ?></td>
                                <td><?= isset($lead['RegisteredAt']) ? formatDate($lead['RegisteredAt']) : 'N/A' ?></td>
                                <td>
                                    <?php
                                    // Set status badge color based on status
                                    $status = isset($lead['Status']) ? $lead['Status'] : 'new';
                                    $statusClass = 'status-new';
                                    if ($status == 'new') $statusClass = 'status-new';
                                    elseif ($status == 'contacted') $statusClass = 'status-contacted';
                                    elseif ($status == 'enrolled') $statusClass = 'status-enrolled';
                                    ?>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                                <td class="action-icons">
                                    <a href="students.php" class="icon-edit" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="students.php" class="view-all-btn">
                    View All Students <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Show message when no leads exist -->
        <div class="empty-card">
            <i class="fas fa-inbox"></i>
            <p>No student leads yet. Students will appear here when they register interest.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Chart.js Library and Registration Chart Script -->
<?php if (!empty($monthlyRegistrations)): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Create line chart for registrations
            const ctx = document.getElementById('registrationsChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    // Month names as labels
                    labels: [<?php foreach ($monthlyRegistrations as $reg): ?> '<?= $reg['month_name'] ?>', <?php endforeach; ?>],
                    datasets: [{
                        label: 'New Registrations',
                        data: [<?php foreach ($monthlyRegistrations as $reg): ?><?= $reg['count'] ?>, <?php endforeach; ?>],
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#667eea',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.3, // Smooth curve
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 10
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleColor: '#fff',
                            bodyColor: '#cbd5e1',
                            padding: 10,
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#e2e8f0'
                            },
                            ticks: {
                                stepSize: 1,
                                precision: 0
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
<?php endif; ?>

<?php require '../includes/footer.php'; ?>