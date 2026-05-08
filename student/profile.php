<?php
require_once '../includes/config.php';
requireLogin();
$db  = getDB();
$uid = $_SESSION['user_id'];
$errors = []; $success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name  = trim($_POST['full_name'] ?? '');
    $reg_number = trim($_POST['reg_number'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';

    if (empty($full_name))   $errors[] = 'Full name is required.';
    if (empty($reg_number))  $errors[] = 'Registration number is required.';
    if ($password && strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password && $password !== $confirm) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET full_name=?, reg_number=?, password=? WHERE id=?");
            $stmt->bind_param("sssi", $full_name, $reg_number, $hash, $uid);
        } else {
            $stmt = $db->prepare("UPDATE users SET full_name=?, reg_number=? WHERE id=?");
            $stmt->bind_param("ssi", $full_name, $reg_number, $uid);
        }
        $stmt->execute();
        $_SESSION['full_name'] = $full_name;
        $success = true;
    }
}

$user = $db->query("SELECT * FROM users WHERE id = $uid")->fetch_assoc();
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile – UMU Events</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-wrapper">
    <?php require_once '../includes/student_sidebar.php'; ?>
    <div class="main-content">
        <div class="topbar"><h1>My Profile</h1></div>
        <div class="page-content">
            <?php if ($success): ?>
            <div class="alert alert-success">✅ Profile updated successfully.</div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e): echo "<div>⚠ " . htmlspecialchars($e) . "</div>"; endforeach; ?>
            </div>
            <?php endif; ?>

            <div style="max-width:540px;">
                <div class="card">
                    <h2 class="card-title">Account Details</h2>
                    <form method="POST" action="profile.php">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Registration Number</label>
                            <input type="text" name="reg_number" value="<?= htmlspecialchars($user['reg_number']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled style="opacity:0.6;cursor:not-allowed;">
                            <small style="color:#aaa;font-size:0.78rem;">Email cannot be changed.</small>
                        </div>
                        <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">
                        <p style="font-size:0.85rem; color:var(--muted); margin-bottom:14px;">Leave password fields blank to keep your current password.</p>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="password" placeholder="Leave blank to keep current">
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" placeholder="Re-enter new password">
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
