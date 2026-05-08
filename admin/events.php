<?php
require_once '../includes/config.php';
requireAdmin();
$db = getDB();

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->query("DELETE FROM events WHERE id = $id");
    redirect('events.php?msg=event_deleted');
}

$search   = trim($_GET['search']   ?? '');
$category = trim($_GET['category'] ?? '');

$where = ["1=1"]; $params = []; $types = '';
if ($search) {
    $where[] = "(title LIKE ? OR venue LIKE ?)";
    $like = "%$search%"; $params = array_merge($params, [$like, $like]); $types .= 'ss';
}
if ($category) {
    $where[] = "category = ?"; $params[] = $category; $types .= 's';
}

$sql = "SELECT e.*, (SELECT COUNT(*) FROM rsvps r WHERE r.event_id = e.id) AS rsvp_count
        FROM events e WHERE " . implode(' AND ', $where) . " ORDER BY event_date DESC";

$stmt = $db->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$cats = $db->query("SELECT DISTINCT category FROM events ORDER BY category")->fetch_all(MYSQLI_ASSOC);
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Events – Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-wrapper">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="main-content">
        <div class="topbar">
            <h1>Manage Events</h1>
            <div class="topbar-actions">
                <a href="create_event.php" class="btn btn-primary btn-sm">+ New Event</a>
            </div>
        </div>
        <div class="page-content">
            <?php if ($msg): echo flashMessage($msg); endif; ?>

            <form method="GET" action="events.php" class="search-bar">
                <input type="text" name="search" placeholder=" Search events..." value="<?= htmlspecialchars($search) ?>">
                <select name="category">
                    <option value="">All Categories</option>
                    <?php foreach ($cats as $c): ?>
                    <option value="<?= $c['category'] ?>" <?= $category === $c['category'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['category']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
                <?php if ($search || $category): ?>
                    <a href="events.php" class="btn btn-outline">Clear</a>
                <?php endif; ?>
            </form>

            <div class="card">
                <div class="table-wrapper">
                    <?php if (empty($events)): ?>
                    <div class="empty-state"><div class="icon"></div><h3>No events found</h3></div>
                    <?php else: ?>
                    <table>
                        <thead>
                            <tr><th>#</th><th>Title</th><th>Category</th><th>Date</th><th>Venue</th><th>RSVPs</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $i => $ev): 
                                $catClass = 'cat-' . strtolower($ev['category']);
                            ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><strong><?= htmlspecialchars($ev['title']) ?></strong></td>
                                <td><span class="category-badge <?= $catClass ?>"><?= htmlspecialchars($ev['category']) ?></span></td>
                                <td><?= date('d M Y', strtotime($ev['event_date'])) ?></td>
                                <td><?= htmlspecialchars($ev['venue']) ?></td>
                                <td><?= $ev['rsvp_count'] ?> / <?= $ev['capacity'] ?></td>
                                <td style="display:flex; gap:6px;">
                                    <a href="attendees.php?event_id=<?= $ev['id'] ?>" class="btn btn-outline btn-sm"> Attendees</a>
                                    <a href="edit_event.php?id=<?= $ev['id'] ?>" class="btn btn-warning btn-sm"> Edit</a>
                                    <a href="events.php?delete=<?= $ev['id'] ?>" class="btn btn-danger btn-sm"
                                       onclick="return confirm('Delete \'<?= addslashes($ev['title']) ?>\'? This cannot be undone.')"> Delete</a>
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
