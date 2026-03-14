<?php
// Notice the ../ to go UP one folder to find includes
require '../includes/db.php';
require '../includes/functions.php';
requireAdmin(); // Security check: redirects to login if not admin

// CSV Export Logic: If ?export=1 is in the URL, download the file
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="student_leads.csv"');
    $output = fopen('php://output', 'w');

    // Header Row
    fputcsv($output, ['Programme', 'Student Name', 'Email Address', 'Date Registered']);

    $stmt = $pdo->query("SELECT p.ProgrammeName, i.StudentName, i.Email, i.RegisteredAt 
                        FROM InterestedStudents i 
                        JOIN Programmes p ON i.ProgrammeID = p.ProgrammeID");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, $row);
    }
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-5">
    <h1>Lead Generation List</h1>
    <div class="mb-3">
        <a href="index.php" class="btn btn-secondary">Dashboard</a>
        <a href="?export=1" class="btn btn-success">Download Excel (CSV)</a>
    </div>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Programme</th>
                <th>Student</th>
                <th>Email</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT p.ProgrammeName, i.* FROM InterestedStudents i 
                                JOIN Programmes p ON i.ProgrammeID = p.ProgrammeID 
                                ORDER BY i.RegisteredAt DESC");
            while ($r = $stmt->fetch()): ?>
                <tr>
                    <td><?= e($r['ProgrammeName']) ?></td>
                    <td><?= e($r['StudentName']) ?></td>
                    <td><?= e($r['Email']) ?></td>
                    <td><?= $r['RegisteredAt'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>

</html>