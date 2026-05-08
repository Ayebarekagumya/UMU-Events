<?php
require_once '../includes/config.php';
requireLogin();

$db = getDB();
$uid = $_SESSION['user_id'];

// Stats
$totalEvents = $db->query("SELECT COUNT(*) as c FROM events")->fetch_assoc()['c'];
$myRsvps     = $db->query("SELECT COUNT(*) as c FROM rsvps WHERE user_id = $uid")->fetch_assoc()['c'];
$upcoming    = $db->query("SELECT COUNT(*) as c FROM events WHERE event_date >= CURDATE()")->fetch_assoc()['c'];

// Upcoming events not yet RSVP'd, limit 4
$res = $db->query("
    SELECT e.*, 
        (SELECT COUNT(*) FROM rsvps r WHERE r.event_id = e.id) AS rsvp_count,
        (SELECT COUNT(*) FROM rsvps r WHERE r.event_id = e.id AND r.user_id = $uid) AS user_rsvped
    FROM events e
    WHERE e.event_date >= CURDATE()
    ORDER BY e.event_date ASC
    LIMIT 4
");
$events = $res->fetch_all(MYSQLI_ASSOC);

// My recent RSVPs
$rsvpRes = $db->query("
    SELECT e.*, r.rsvp_date
    FROM rsvps r
    JOIN events e ON e.id = r.event_id
    WHERE r.user_id = $uid
    ORDER BY r.rsvp_date DESC
    LIMIT 3
");
$recentRsvps = $rsvpRes->fetch_all(MYSQLI_ASSOC);

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard – UMU Events</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-wrapper">
    <?php require_once '../includes/student_sidebar.php'; ?>
    <div class="main-content">
        <div class="topbar">
            <h1>Welcome, <?= htmlspecialchars(explode(' ', $_SESSION['full_name'])[0]) ?>!</h1>
            <div class="topbar-actions">
                <a href="events.php" class="btn btn-primary btn-sm">Browse Events</a>
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
                        <div class="label">My RSVPs</div>
                        <div class="value"><?= $myRsvps ?></div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="page-header">
                <div>
                    <h2>Upcoming Events</h2>
                    <p>Events you might want to attend</p>
                </div>
                <a href="events.php" class="btn btn-outline btn-sm">See All →</a>
            </div>

            <?php if (empty($events)): ?>
            <div class="empty-state"><div class="icon"></div><h3>No upcoming events</h3><p>Check back soon!</p></div>
            <?php else: ?>
            <div class="events-grid">
                <?php foreach ($events as $ev): 
                    $pct = $ev['capacity'] > 0 ? round(($ev['rsvp_count'] / $ev['capacity']) * 100) : 0;
                    $catClass = 'cat-' . strtolower($ev['category']);
                ?>
                <div class="event-card">
                    <div class="event-card-header">
                        <span class="category-badge <?= $catClass ?>"><?= htmlspecialchars($ev['category']) ?></span>
                        <?php if ($ev['user_rsvped']): ?>
                            <span class="rsvp-badge">✓ RSVP'd</span>
                        <?php endif; ?>
                    </div>
                    <div class="event-card-body">
                        <h3><?= htmlspecialchars($ev['title']) ?></h3>
                        <div class="event-meta">
                            <span> <?= date('D, d M Y', strtotime($ev['event_date'])) ?> at <?= date('g:i A', strtotime($ev['event_time'])) ?></span>
                            <span> <?= htmlspecialchars($ev['venue']) ?></span>
                            <span> <?= $ev['rsvp_count'] ?> / <?= $ev['capacity'] ?> RSVPs</span>
                        </div>
                        <p class="event-desc"><?= htmlspecialchars($ev['description']) ?></p>
                        <div class="capacity-bar">
                            <div class="label">Capacity: <?= $pct ?>% full</div>
                            <div class="progress"><div class="progress-fill <?= $pct >= 100 ? 'full' : '' ?>" style="width:<?= min($pct,100) ?>%"></div></div>
                        </div>
                    </div>
                    <div class="event-card-footer">
                        <?php if ($ev['user_rsvped']): ?>
                            <a href="rsvp_action.php?action=cancel&event_id=<?= $ev['id'] ?>" class="btn btn-outline btn-sm"
                               onclick="return confirm('Cancel your RSVP?')">Cancel RSVP</a>
                        <?php elseif ($ev['rsvp_count'] >= $ev['capacity']): ?>
                            <span class="btn btn-outline btn-sm" style="cursor:not-allowed;opacity:0.5;">Full</span>
                        <?php else: ?>
                            <a href="rsvp_action.php?action=rsvp&event_id=<?= $ev['id'] ?>" class="btn btn-primary btn-sm">RSVP Now</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- My Recent RSVPs -->
            <?php if (!empty($recentRsvps)): ?>
            <div class="page-header" style="margin-top:36px;">
                <div><h2>My Recent RSVPs</h2></div>
                <a href="my_rsvps.php" class="btn btn-outline btn-sm">View All →</a>
            </div>
            <div class="card">
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr><th>Event</th><th>Date</th><th>Venue</th><th>RSVP'd On</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentRsvps as $r): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($r['title']) ?></strong></td>
                                <td><?= date('d M Y', strtotime($r['event_date'])) ?></td>
                                <td><?= htmlspecialchars($r['venue']) ?></td>
                                <td><?= date('d M Y', strtotime($r['rsvp_date'])) ?></td>
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
