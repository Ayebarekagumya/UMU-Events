<?php
require_once 'includes/auth.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') redirect('admin/dashboard.php');
    else redirect('student/dashboard.php');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = loginUser(trim($_POST['email'] ?? ''), $_POST['password'] ?? '');
    if ($result['success']) {
        if ($result['role'] === 'admin') redirect('admin/dashboard.php');
        else redirect('student/dashboard.php');
    } else {
        $errors = $result['errors'];
    }
}
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login – UMU Event Management</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-left">
        <p class="school-name">Uganda Martyrs University</p>
        <h1>Event<br>Management<br>System</h1>
        <p>Discover, RSVP, and manage university events all in one place. Never miss what's happening on campus.</p>
    </div>
    <div class="auth-right">
        <div class="auth-box">
            <h2>Welcome</h2>
            <p class="subtitle">Sign in to your account to continue</p>

            <?php if ($msg): echo flashMessage($msg); endif; ?>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <div>
                    <?php foreach ($errors as $e): ?>
                        <div> <?= htmlspecialchars($e) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <form method="POST" action="index.php">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="you@umu.ac.ug"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:8px;">
                    Sign In
                </button>
            </form>

            <div class="auth-link">
                Don't have an account? <a href="register.php">Create one here</a>
            </div>
     </div>
    </div>
</div>
</body>
</html>
