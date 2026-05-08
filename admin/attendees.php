<?php
require_once '../includes/config.php';
requireAdmin();
$db = getDB();

$event_id = (int)($_GET['event_id'] ?? 0);
$events   = $db->query("SELECT id, title FROM events ORDER BY event_date DESC")->fetch_all(MYSQLI_ASSOC);

$attendees = [];
$selectedEvent = null;
if ($event_id) {
    $selectedEvent = $db->query("SELECT * FROM events WHERE id = $event_id")->fetch_assoc();
    $res = $db->query("
        SELECT u.full_name, u.email, u.reg_number, r.rsvp_date
        FROM rsvps r
        JOIN users u ON u.id = r.user_id
        WHERE r.event_id = $event_id
        ORDER BY r.rsvp_date ASC
    ");
    $attendees = $res->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Event Attendees – Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-wrapper">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="main-content">
        <div class="topbar"><h1>Event Attendees</h1></div>
        <div class="page-content">
            <div class="card" style="margin-bottom:24px; max-width:480px;">
                <h3 class="card-title">Select Event</h3>
                <form method="GET" action="attendees.php" style="display:flex; gap:12px;">
                    <select name="event_id" style="flex:1; padding:10px; border:1.5px solid #e0e0e0; border-radius:8px; font-family:inherit;">
                        <option value="">-- Select an event --</option>
                        <?php foreach ($events as $ev): ?>
                        <option value="<?= $ev['id'] ?>" <?= $event_id == $ev['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ev['title']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary">View</button>
                </form>
            </div>

            <?php if ($event_id && $selectedEvent): ?>
            <div class="page-header">
                <div>
                    <h2><?= htmlspecialchars($selectedEvent['title']) ?></h2>
                    <p>
                         <?= date('D, d M Y', strtotime($selectedEvent['event_date'])) ?> &nbsp;|&nbsp;
                         <?= htmlspecialchars($selectedEvent['venue']) ?> &nbsp;|&nbsp;
                         <?= count($attendees) ?> / <?= $selectedEvent['capacity'] ?> attending
                    </p>
                </div>
            </div>

            <?php if (empty($attendees)): ?>
            <div class="empty-state"><div class="icon"></div><h3>No RSVPs yet</h3><p>No students have RSVP'd to this event.</p></div>
            <?php else: ?>
            <div class="card">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr><th>#</th><th>Full Name</th><th>Reg Number</th><th>Email</th><th>RSVP Date</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendees as $i => $a): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><strong><?= htmlspecialchars($a['full_name']) ?></strong></td>
                                <td><?= htmlspecialchars($a['reg_number']) ?></td>
                                <td><?= htmlspecialchars($a['email']) ?></td>
                                <td><?= date('d M Y, g:i A', strtotime($a['rsvp_date'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
