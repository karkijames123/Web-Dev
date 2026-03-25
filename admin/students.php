<?php
// Set page title
$pageTitle = 'Student Leads';

// Include header and check admin access
require '../includes/header.php';
requireAdmin();

// Handle CSV export
if (isset($_GET['export'])) {

    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="student_leads_' . date('Y-m-d') . '.csv"');

    // Open output stream
    $output = fopen('php://output', 'w');

    // Add column headers
    fputcsv($output, ['Programme', 'Student Name', 'Email', 'Phone', 'Status', 'Date Registered']);

    // Fetch student leads
    $stmt = $pdo->query("
        SELECT p.ProgrammeName, i.StudentName, i.Email, i.Phone, i.Status, i.RegisteredAt 
        FROM InterestedStudents i 
        JOIN Programmes p ON i.ProgrammeID = p.ProgrammeID 
        ORDER BY i.RegisteredAt DESC
    ");

    // Write each row to CSV
    while ($row = $stmt->fetch()) {
        fputcsv($output, [
            $row['ProgrammeName'],
            $row['StudentName'],
            $row['Email'],
            $row['Phone'] ?? '',
            $row['Status'] ?? 'new',
            $row['RegisteredAt']
        ]);
    }

    // Close file and stop script
    fclose($output);
    exit;
}

// Handle status update (New, Contacted, etc.)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $interestId = (int)$_POST['interest_id'];
    $status = $_POST['status'];

    // Update status in database
    $stmt = $pdo->prepare("UPDATE InterestedStudents SET Status = ? WHERE InterestID = ?");
    $stmt->execute([$status, $interestId]);

    // Redirect after update
    header("Location: students.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM InterestedStudents WHERE InterestID = ?");
    $stmt->execute([(int)$_GET['delete']]);

    // Redirect after delete
    header("Location: students.php");
    exit;
}

// Fetch all student leads with programme name
$students = $pdo->query("
    SELECT i.*, p.ProgrammeName 
    FROM InterestedStudents i 
    JOIN Programmes p ON i.ProgrammeID = p.ProgrammeID 
    ORDER BY i.RegisteredAt DESC
")->fetchAll();
?>

<div class="admin-page">

    <!-- Page header -->
    <div class="page-head">
        <h1>Student Leads</h1>

        <!-- Export button -->
        <a href="?export=1" class="btn-export">
            <i class="fas fa-download"></i> Export CSV
        </a>
    </div>

    <div class="data-card">
        <div class="table-wrap">

            <!-- Student leads table -->
            <table class="simple-table">
                <thead>
                    <tr>
                        <th>Programme</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- Loop through students -->
                    <?php foreach ($students as $s): ?>
                        <tr>

                            <!-- Programme name -->
                            <td><?= e($s['ProgrammeName']) ?></td>

                            <!-- Student name -->
                            <td><strong><?= e($s['StudentName']) ?></strong></td>

                            <!-- Email -->
                            <td><?= e($s['Email']) ?></td>

                            <!-- Phone -->
                            <td><?= e($s['Phone'] ?? '—') ?></td>

                            <!-- Status dropdown (auto-submit on change) -->
                            <td>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="interest_id" value="<?= $s['InterestID'] ?>">

                                    <select name="status" class="status-select" onchange="this.form.submit()">
                                        <option value="new" <?= (isset($s['Status']) && $s['Status'] == 'new') ? 'selected' : '' ?>>New</option>
                                        <option value="contacted" <?= (isset($s['Status']) && $s['Status'] == 'contacted') ? 'selected' : '' ?>>Contacted</option>
                                        <option value="enrolled" <?= (isset($s['Status']) && $s['Status'] == 'enrolled') ? 'selected' : '' ?>>Enrolled</option>
                                        <option value="unsubscribed" <?= (isset($s['Status']) && $s['Status'] == 'unsubscribed') ? 'selected' : '' ?>>Unsubscribed</option>
                                    </select>

                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>

                            <!-- Registration date -->
                            <td><?= isset($s['RegisteredAt']) ? formatDate($s['RegisteredAt']) : 'N/A' ?></td>

                            <!-- Delete action -->
                            <td class="action-icons">
                                <a href="?delete=<?= $s['InterestID'] ?>" class="icon-delete" onclick="return confirm('Delete this lead?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>

                        </tr>
                    <?php endforeach; ?>

                    <!-- Show message if no data -->
                    <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="7" class="empty-row">No student leads yet.</td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>