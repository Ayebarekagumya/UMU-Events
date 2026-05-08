<?php
require_once 'includes/auth.php';

if (isset($_SESSION['user_id'])) {
    redirect($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php');
}

$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = registerUser(
        trim($_POST['full_name'] ?? ''),
        trim($_POST['email'] ?? ''),
        $_POST['password'] ?? '',
        trim($_POST['reg_number'] ?? '')
    );
    if ($result['success']) {
        $success = true;
    } else {
        $errors = $result['errors'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register – UMU Event Management</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-left">
        <p class="school-name">Uganda Martyrs University</p>
        <h1>Join<br>Campus<br>Events</h1>
        <p>Create your student account to RSVP to events, track your history, and stay connected with campus life.</p>
    </div>
    <div class="auth-right">
        <div class="auth-box">
            <h2>Create Account</h2>
            <p class="subtitle">Register as a student to get started</p>

            <?php if ($success): ?>
            <div class="alert alert-success">
                Registration successful! <a href="index.php" style="font-weight:600;">Sign in here</a>
            </div>
            <?php else: ?>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e): ?>
                    <div> <?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" placeholder="e.g. John Mukasa"
                        value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Registration Number</label>
                    <input type="text" name="reg_number" placeholder="e.g. 2024/BSC/001"
                        value="<?= htmlspecialchars($_POST['reg_number'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="you@umu.ac.ug"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Password <span style="color:#aaa;font-weight:400;">(min. 6 characters)</span></label>
                    <input type="password" name="password" placeholder="Create a strong password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top:8px;">
                    Create Account
                </button>
            </form>

            <?php endif; ?>

            <div class="auth-link">
                Already have an account? <a href="index.php">Sign in</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
