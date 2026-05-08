<?php
// includes/admin_sidebar.php
requireAdmin();
$initial = strtoupper(substr($_SESSION['full_name'], 0, 1));
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-logo">
        <h2>UMU <span>Events</span></h2>
        <p>Admin Panel</p>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">Overview</div>
        <a href="dashboard.php" class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
             Dashboard
        </a>
        <div class="nav-section">Events</div>
        <a href="events.php" class="<?= $currentPage === 'events.php' ? 'active' : '' ?>">
             All Events
        </a>
        <a href="create_event.php" class="<?= $currentPage === 'create_event.php' ? 'active' : '' ?>">
             Create Event
        </a>
        <div class="nav-section">Users</div>
        <a href="attendees.php" class="<?= $currentPage === 'attendees.php' ? 'active' : '' ?>">
             View Attendees
        </a>
        <a href="users.php" class="<?= $currentPage === 'users.php' ? 'active' : '' ?>">
             Manage Users
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar" style="background:#e94560;"><?= $initial ?></div>
            <div>
                <div class="name"><?= htmlspecialchars($_SESSION['full_name']) ?></div>
                <div class="role">Administrator</div>
            </div>
        </div>
        <a href="../logout.php"> Sign Out</a>
    </div>
</div>
