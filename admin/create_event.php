<?php
require_once '../includes/config.php';
requireAdmin();
$db = getDB();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $event_date  = trim($_POST['event_date'] ?? '');
    $event_time  = trim($_POST['event_time'] ?? '');
    $venue       = trim($_POST['venue'] ?? '');
    $capacity    = (int)($_POST['capacity'] ?? 0);
    $admin_id    = $_SESSION['user_id'];

    if (empty($title))       $errors[] = 'Event title is required.';
    if (empty($category))    $errors[] = 'Category is required.';
    if (empty($event_date))  $errors[] = 'Event date is required.';
    if (empty($event_time))  $errors[] = 'Event time is required.';
    if (empty($venue))       $errors[] = 'Venue is required.';
    if ($capacity <= 0)      $errors[] = 'Capacity must be greater than 0.';

    if (empty($errors)) {
        $stmt = $db->prepare("INSERT INTO events (title, description, category, event_date, event_time, venue, capacity, created_by)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssii", $title, $description, $category, $event_date, $event_time, $venue, $capacity, $admin_id);
        $stmt->execute();
        redirect('events.php?msg=event_created');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Event – Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="app-wrapper">
    <?php require_once '../includes/admin_sidebar.php'; ?>
    <div class="main-content">
        <div class="topbar">
            <h1>Create New Event</h1>
            <div class="topbar-actions">
                <a href="events.php" class="btn btn-outline btn-sm">← Back to Events</a>
            </div>
        </div>
        <div class="page-content">
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $e): echo "<div>⚠ " . htmlspecialchars($e) . "</div>"; endforeach; ?>
            </div>
            <?php endif; ?>

            <div style="max-width:640px;">
                <div class="card">
                    <h2 class="card-title">Event Details</h2>
                    <form method="POST" action="create_event.php">
                        <div class="form-group">
                            <label>Event Title *</label>
                            <input type="text" name="title" placeholder="e.g. Annual Tech Summit 2026"
                                value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" placeholder="Describe the event..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Category *</label>
                                <select name="category" required>
                                    <option value="">Select category...</option>
                                    <?php foreach (['Academic','Social','Sports','Career','Guild','Other'] as $cat): ?>
                                    <option value="<?= $cat ?>" <?= ($_POST['category'] ?? '') === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Capacity *</label>
                                <input type="number" name="capacity" min="1" placeholder="e.g. 200"
                                    value="<?= htmlspecialchars($_POST['capacity'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Event Date *</label>
                                <input type="date" name="event_date"
                                    value="<?= htmlspecialchars($_POST['event_date'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Event Time *</label>
                                <input type="time" name="event_time"
                                    value="<?= htmlspecialchars($_POST['event_time'] ?? '') ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Venue *</label>
                            <input type="text" name="venue" placeholder="e.g. UMU Main Hall"
                                value="<?= htmlspecialchars($_POST['venue'] ?? '') ?>" required>
                        </div>
                        <div style="display:flex; gap:12px; margin-top:8px;">
                            <button type="submit" class="btn btn-primary">Create Event</button>
                            <a href="events.php" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
