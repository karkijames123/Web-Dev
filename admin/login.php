<?php require '../includes/db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM Admins WHERE Username = ?");
    $stmt->execute([$user]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($pass, $admin['PasswordHash'])) {
        $_SESSION['admin_id'] = $admin['AdminID'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>
<div class="container mt-5">
    <h2>Admin Login</h2>
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="POST">
        <div class="mb-3"><label>Username</label><input type="text" name="username" class="form-control" required></div>
        <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>