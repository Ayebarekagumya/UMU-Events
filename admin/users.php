<?php
require_once '../includes/config.php';
requireAdmin();
$db = getDB();

// Delete user (only students, not admins)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->query("DELETE FROM users WHERE id = $id AND role = 'student'");
    redirect('users.php?msg=user_deleted');
}

$search = trim($_GET['search'] ?? '');
$role   = trim($_GET['role'] ?? '');

$where = ["1=1"]; $params = []; $types = '';
if ($search) {
    $where[] = "(full_name LIKE ? OR email LIKE ? OR reg_number LIKE ?)";
    $like = "%$search%"; $params = array_merge($params, [$like, $like, $like]); $types .= 'sss';
}
if ($role) {
    $where[] = "role = ?"; $params[] = $role; $types .= 's';
}

$sql = "SELECT u.*, (SELECT COUNT(*) FROM rsvps r WHERE r.user_id = u.id) as rsvp_count
        FROM users u WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC";

$stmt = $db->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$msg   = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users – Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-wrapper">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="main-content">
        <div class="topbar"><h1>Manage Users</h1></div>
        <div class="page-content">
            <?php if ($msg === 'user_deleted'): ?>
            <div class="alert alert-success"> User deleted successfully.</div>
            <?php endif; ?>

            <form method="GET" action="users.php" class="search-bar">
                <input type="text" name="search" placeholder=" Search by name, email, reg number..."
                    value="<?= htmlspecialchars($search) ?>">
                <select name="role">
                    <option value="">All Roles</option>
                    <option value="student" <?= $role === 'student' ? 'selected' : '' ?>>Students</option>
                    <option value="admin"   <?= $role === 'admin'   ? 'selected' : '' ?>>Admins</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
                <?php if ($search || $role): ?>
                    <a href="users.php" class="btn btn-outline">Clear</a>
                <?php endif; ?>
            </form>

            <div class="card">
                <div class="table-wrapper">
                    <?php if (empty($users)): ?>
                    <div class="empty-state"><div class="icon"></div><h3>No users found</h3></div>
                    <?php else: ?>
                    <table>
                        <thead>
                            <tr><th>#</th><th>Name</th><th>Email</th><th>Reg Number</th><th>Role</th><th>RSVPs</th><th>Joined</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $i => $u): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><strong><?= htmlspecialchars($u['full_name']) ?></strong></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><?= htmlspecialchars($u['reg_number']) ?></td>
                                <td>
                                    <?php if ($u['role'] === 'admin'): ?>
                                        <span style="background:#fce8f0;color:#c7275c;padding:3px 10px;border-radius:20px;font-size:0.78rem;font-weight:600;">Admin</span>
                                    <?php else: ?>
                                        <span style="background:#e8f4fd;color:#0c63a4;padding:3px 10px;border-radius:20px;font-size:0.78rem;font-weight:600;">Student</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $u['rsvp_count'] ?></td>
                                <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                                <td>
                                    <?php if ($u['role'] !== 'admin'): ?>
                                    <a href="users.php?delete=<?= $u['id'] ?>" class="btn btn-danger btn-sm"
                                       onclick="return confirm('Delete user \'<?= addslashes($u['full_name']) ?>\'? All their RSVPs will also be removed.')">
                                       Delete
                                    </a>
                                    <?php else: ?>
                                        <span style="color:#aaa; font-size:0.8rem;">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
