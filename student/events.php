<?php
require_once '../includes/config.php';
requireLogin();

$db  = getDB();
$uid = $_SESSION['user_id'];

$search   = trim($_GET['search']   ?? '');
$category = trim($_GET['category'] ?? '');
$date     = trim($_GET['date']     ?? '');

$where = ["1=1"];
$params = []; $types = '';

if ($search !== '') {
    $where[] = "(e.title LIKE ? OR e.description LIKE ? OR e.venue LIKE ?)";
    $like = "%$search%";
    $params = array_merge($params, [$like, $like, $like]);
    $types .= 'sss';
}
if ($category !== '') {
    $where[] = "e.category = ?";
    $params[] = $category; $types .= 's';
}
if ($date === 'upcoming') {
    $where[] = "e.event_date >= CURDATE()";
} elseif ($date === 'past') {
    $where[] = "e.event_date < CURDATE()";
}

$sql = "
    SELECT e.*,
        (SELECT COUNT(*) FROM rsvps r WHERE r.event_id = e.id) AS rsvp_count,
        (SELECT COUNT(*) FROM rsvps r WHERE r.event_id = e.id AND r.user_id = ?) AS user_rsvped
    FROM events e
    WHERE " . implode(' AND ', $where) . "
    ORDER BY e.event_date ASC
";

$stmt = $db->prepare($sql);
$allParams = array_merge([$uid], $params);
$allTypes  = 'i' . $types;
if (!empty($allParams)) $stmt->bind_param($allTypes, ...$allParams);
$stmt->execute();
$events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Categories for filter
$cats = $db->query("SELECT DISTINCT category FROM events ORDER BY category")->fetch_all(MYSQLI_ASSOC);
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Browse Events – UMU Events</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-wrapper">
    <?php require_once '../includes/student_sidebar.php'; ?>
    <div class="main-content">
        <div class="topbar">
            <h1>Browse Events</h1>
        </div>
        <div class="page-content">
            <?php if ($msg): echo flashMessage($msg); endif; ?>

            <form method="GET" action="events.php" class="search-bar">
                <input type="text" name="search" placeholder="Search by title, venue, description..." value="<?= htmlspecialchars($search) ?>">
                <select name="category">
                    <option value="">All Categories</option>
                    <?php foreach ($cats as $c): ?>
                    <option value="<?= $c['category'] ?>" <?= $category === $c['category'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['category']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <select name="date">
                    <option value="">All Dates</option>
                    <option value="upcoming" <?= $date === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                    <option value="past"     <?= $date === 'past'     ? 'selected' : '' ?>>Past Events</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
                <?php if ($search || $category || $date): ?>
                    <a href="events.php" class="btn btn-outline">Clear</a>
                <?php endif; ?>
            </form>

            <p class="text-muted" style="margin-bottom:20px; font-size:0.88rem;">
                Showing <strong><?= count($events) ?></strong> event(s)
            </p>

            <?php if (empty($events)): ?>
            <div class="empty-state">
                <div class="icon"></div>
                <h3>No events found</h3>
                <p>Try a different search or filter.</p>
            </div>
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
                            <span><?= date('D, d M Y', strtotime($ev['event_date'])) ?> at <?= date('g:i A', strtotime($ev['event_time'])) ?></span>
                            <span><?= htmlspecialchars($ev['venue']) ?></span>
                            <span><?= $ev['rsvp_count'] ?> / <?= $ev['capacity'] ?> attending</span>
                        </div>
                        <p class="event-desc"><?= htmlspecialchars($ev['description']) ?></p>
                        <div class="capacity-bar">
                            <div class="label">Capacity: <?= $pct ?>% full</div>
                            <div class="progress"><div class="progress-fill <?= $pct >= 100 ? 'full' : '' ?>" style="width:<?= min($pct,100) ?>%"></div></div>
                        </div>
                    </div>
                    <div class="event-card-footer">
                        <?php if ($ev['user_rsvped']): ?>
                            <a href="rsvp_action.php?action=cancel&event_id=<?= $ev['id'] ?>&from=events"
                               class="btn btn-outline btn-sm" onclick="return confirm('Cancel your RSVP for this event?')">Cancel RSVP</a>
                        <?php elseif ($ev['rsvp_count'] >= $ev['capacity']): ?>
                            <span class="btn btn-outline btn-sm" style="cursor:not-allowed;opacity:0.5;">Event Full</span>
                        <?php elseif (strtotime($ev['event_date']) < strtotime('today')): ?>
                            <span class="btn btn-outline btn-sm" style="cursor:not-allowed;opacity:0.5;">Past Event</span>
                        <?php else: ?>
                            <a href="rsvp_action.php?action=rsvp&event_id=<?= $ev['id'] ?>&from=events" class="btn btn-primary btn-sm">RSVP Now</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
