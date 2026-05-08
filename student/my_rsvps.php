<?php
require_once '../includes/config.php';
requireLogin();
$db  = getDB();
$uid = $_SESSION['user_id'];

$res = $db->query("
    SELECT e.*, r.rsvp_date, r.id as rsvp_id
    FROM rsvps r
    JOIN events e ON e.id = r.event_id
    WHERE r.user_id = $uid
    ORDER BY e.event_date ASC
");
$rsvps = $res->fetch_all(MYSQLI_ASSOC);
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My RSVPs – UMU Events</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-wrapper">
    <?php require_once '../includes/student_sidebar.php'; ?>
    <div class="main-content">
        <div class="topbar">
            <h1>My RSVPs</h1>
        </div>
        <div class="page-content">
            <?php if ($msg): echo flashMessage($msg); endif; ?>

            <?php if (empty($rsvps)): ?>
            <div class="empty-state">
                <div class="icon"></div>
                <h3>No RSVPs yet</h3>
                <p>Browse upcoming events and RSVP to ones you'd like to attend.</p>
                <a href="events.php" class="btn btn-primary" style="margin-top:16px;">Browse Events</a>
            </div>
            <?php else: ?>
            <div class="card">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Event</th>
                                <th>Category</th>
                                <th>Date & Time</th>
                                <th>Venue</th>
                                <th>RSVP'd On</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rsvps as $i => $r):
                                $isPast = strtotime($r['event_date']) < strtotime('today');
                                $catClass = 'cat-' . strtolower($r['category']);
                            ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><strong><?= htmlspecialchars($r['title']) ?></strong></td>
                                <td><span class="category-badge <?= $catClass ?>"><?= htmlspecialchars($r['category']) ?></span></td>
                                <td><?= date('d M Y', strtotime($r['event_date'])) ?> at <?= date('g:i A', strtotime($r['event_time'])) ?></td>
                                <td><?= htmlspecialchars($r['venue']) ?></td>
                                <td><?= date('d M Y', strtotime($r['rsvp_date'])) ?></td>
                                <td>
                                    <?php if ($isPast): ?>
                                        <span style="color:#aaa; font-size:0.8rem;">Completed</span>
                                    <?php else: ?>
                                        <span class="rsvp-badge">Upcoming</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!$isPast): ?>
                                    <a href="rsvp_action.php?action=cancel&event_id=<?= $r['id'] ?>&from=my_rsvps"
                                       class="btn btn-outline btn-sm"
                                       onclick="return confirm('Cancel RSVP for \'<?= addslashes($r['title']) ?>\'?')">
                                       Cancel
                                    </a>
                                    <?php else: ?>
                                        <span style="color:#aaa; font-size:0.8rem;">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
