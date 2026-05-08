<?php
require_once '../includes/config.php';
requireLogin();

$db       = getDB();
$uid      = (int)$_SESSION['user_id'];
$event_id = (int)($_GET['event_id'] ?? 0);
$action   = $_GET['action'] ?? '';
$from     = $_GET['from'] === 'events' ? 'events' : 'dashboard';
$redirect = $from === 'events' ? 'events.php' : 'dashboard.php';

if (!$event_id) redirect("$redirect?msg=error");

// Get event
$stmt = $db->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
if (!$event) redirect("$redirect?msg=error");

if ($action === 'rsvp') {
    // Check already RSVP'd
    $check = $db->prepare("SELECT id FROM rsvps WHERE user_id = ? AND event_id = ?");
    $check->bind_param("ii", $uid, $event_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        redirect("$redirect?msg=rsvp_exists");
    }
    // Check capacity
    $cnt = $db->query("SELECT COUNT(*) as c FROM rsvps WHERE event_id = $event_id")->fetch_assoc()['c'];
    if ($cnt >= $event['capacity']) {
        redirect("$redirect?msg=rsvp_full");
    }
    // Insert
    $ins = $db->prepare("INSERT INTO rsvps (user_id, event_id) VALUES (?, ?)");
    $ins->bind_param("ii", $uid, $event_id);
    $ins->execute();
    redirect("$redirect?msg=rsvp_success");

} elseif ($action === 'cancel') {
    $del = $db->prepare("DELETE FROM rsvps WHERE user_id = ? AND event_id = ?");
    $del->bind_param("ii", $uid, $event_id);
    $del->execute();
    redirect("$redirect?msg=rsvp_cancelled");
} else {
    redirect($redirect);
}
?>
