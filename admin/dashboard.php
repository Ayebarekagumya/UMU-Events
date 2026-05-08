<?php
require_once '../includes/config.php';
requireAdmin();
$db = getDB();

$totalEvents   = $db->query("SELECT COUNT(*) as c FROM events")->fetch_assoc()['c'];
$totalStudents = $db->query("SELECT COUNT(*) as c FROM users WHERE role='student'")->fetch_assoc()['c'];
$totalRsvps    = $db->query("SELECT COUNT(*) as c FROM rsvps")->fetch_assoc()['c'];
$upcoming      = $db->query("SELECT COUNT(*) as c FROM events WHERE event_date >= CURDATE()")->fetch_assoc()['c'];

// Recent events with RSVP count
$recentEvents = $db->query("
    SELECT e.*, (SELECT COUNT(*) FROM rsvps r WHERE r.event_id = e.id) AS rsvp_count
    FROM events e
    ORDER BY e.created_at DESC
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

// Recent registrations
$recentUsers = $db->query("
    SELECT * FROM users WHERE role='student' ORDER BY created_at DESC LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard – UMU Events</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-wrapper">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="main-content">
        <div class="topbar">
            <h1>Admin Dashboard</h1>
            <div class="topbar-actions">
                <a href="create_event.php" class="btn btn-primary btn-sm">+ New Event</a>
            </div>
        </div>
        <div class="page-content">
            <?php if ($msg): echo flashMessage($msg); endif; ?>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <div class="label">Total Events</div>
                        <div class="value"><?= $totalEvents ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <div class="label">Upcoming</div>
                        <div class="value"><?= $upcoming ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <div class="label">Students</div>
                        <div class="value"><?= $totalStudents ?></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <div class="label">Total RSVPs</div>
                        <div class="value"><?= $totalRsvps ?></div>
                    </div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; flex-wrap:wrap;">
                <!-- Recent Events -->
                <div class="card">
                    <div class="d-flex align-center" style="justify-content:space-between; margin-bottom:16px;">
                        <h3 class="card-title mb-0">Recent Events</h3>
                        <a href="events.php" class="btn btn-outline btn-sm">View All</a>
                    </div>
                    <div class="table-wrapper">
                        <table>
                            <thead><tr><th>Event</th><th>Date</th><th>RSVPs</th><th></th></tr></thead>
                            <tbody>
                                <?php foreach ($recentEvents as $ev): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($ev['title']) ?></strong></td>
                                    <td style="font-size:0.82rem;"><?= date('d M Y', strtotime($ev['event_date'])) ?></td>
                                    <td><?= $ev['rsvp_count'] ?>/<?= $ev['capacity'] ?></td>
                                    <td>
                                        <a href="edit_event.php?id=<?= $ev['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Students -->
                <div class="card">
                    <div class="d-flex align-center" style="justify-content:space-between; margin-bottom:16px;">
                        <h3 class="card-title mb-0">Recent Registrations</h3>
                        <a href="users.php" class="btn btn-outline btn-sm">View All</a>
                    </div>
                    <div class="table-wrapper">
                        <table>
                            <thead><tr><th>Name</th><th>Reg No.</th><th>Joined</th></tr></thead>
                            <tbody>
                                <?php foreach ($recentUsers as $u): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($u['full_name']) ?></strong></td>
                                    <td style="font-size:0.82rem;"><?= htmlspecialchars($u['reg_number']) ?></td>
                                    <td style="font-size:0.82rem;"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
