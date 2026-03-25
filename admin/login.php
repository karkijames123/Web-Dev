
<?php
$pageTitle = 'Admin Login'; // Page title
require '../includes/header.php'; // Include header

if (isAdminLoggedIn()) { // If already logged in
    header("Location: index.php"); // Redirect to dashboard
    exit;
}

$error = ''; // Initialize error message

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Form submitted
    $username = trim($_POST['username'] ?? ''); // Get username
    $password = $_POST['password'] ?? ''; // Get password

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password."; // Validation
    } else {
        $stmt = $pdo->prepare("SELECT AdminID, Username, PasswordHash FROM Admins WHERE Username = ?"); // Prepare SQL
        $stmt->execute([$username]); // Execute query
        $admin = $stmt->fetch(); // Fetch admin record

        if ($admin && password_verify($password, $admin['PasswordHash'])) { // Verify password
            session_regenerate_id(true); // Secure session
            $_SESSION['admin_id'] = $admin['AdminID']; // Store admin ID
            $_SESSION['admin_username'] = $admin['Username']; // Store username
            header("Location: index.php"); // Redirect to dashboard
            exit;
        } else {
            $error = "Invalid username or password."; // Login failed
        }
    }
}
?>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-lock"></i>
            <h2>Admin Login</h2>
        </div>

        <?php if ($error): ?>
            <div class="error-box">
                <?= e($error) ?> <!-- Display error -->
            </div>
        <?php endif; ?>

        <form method="POST" class="login-form">
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus> <!-- Username input -->
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required> <!-- Password input -->
            </div>

            <button type="submit" class="login-btn">Login</button> <!-- Submit button -->
        </form>
    </div>
</div>

<?php require '../includes/footer.php'; ?> <!-- Include footer -->

