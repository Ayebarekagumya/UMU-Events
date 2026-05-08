<?php
// includes/student_sidebar.php
requireLogin();
$initial = strtoupper(substr($_SESSION['full_name'], 0, 1));
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-logo">
        <h2>UMU <span>Events</span></h2>
        <p>Student Portal</p>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">Menu</div>
        <a href="dashboard.php" class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
             Dashboard
        </a>
        <a href="events.php" class="<?= $currentPage === 'events.php' ? 'active' : '' ?>">
             Browse Events
        </a>
        <a href="my_rsvps.php" class="<?= $currentPage === 'my_rsvps.php' ? 'active' : '' ?>">
             My RSVPs
        </a>
        <a href="profile.php" class="<?= $currentPage === 'profile.php' ? 'active' : '' ?>">
             My Profile
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?= $initial ?></div>
            <div>
                <div class="name"><?= htmlspecialchars($_SESSION['full_name']) ?></div>
                <div class="role">Student</div>
            </div>
        </div>
        <a href="../logout.php"> Sign Out</a>
    </div>
</div>
