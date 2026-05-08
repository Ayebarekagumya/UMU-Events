<?php
// includes/config.php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Litokoto01$');
define('DB_NAME', 'event_management');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Create DB connection
function getDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("<div style='padding:20px;background:#fee;color:#c00;font-family:sans-serif;'>
            <strong>Database Connection Failed:</strong> " . $conn->connect_error . "
            <p>Make sure XAMPP/WAMP is running and you have imported <code>event_db.sql</code>.</p>
        </div>");
    }
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit();
}

// Check if logged in
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        redirect('../index.php?msg=login_required');
    }
}

// Check if admin
function requireAdmin() {
    requireLogin();
    if ($_SESSION['role'] !== 'admin') {
        redirect('../student/dashboard.php?msg=access_denied');
    }
}

// Flash message helper
function flashMessage($msg) {
    $messages = [
        'login_required'   => ['warning', 'Please log in to access that page.'],
        'access_denied'    => ['danger',  'You do not have permission to do that.'],
        'logout_success'   => ['success', 'You have been logged out successfully.'],
        'rsvp_success'     => ['success', 'RSVP confirmed! See you at the event.'],
        'rsvp_exists'      => ['warning', 'You have already RSVP\'d to this event.'],
        'rsvp_full'        => ['danger',  'Sorry, this event has reached full capacity.'],
        'rsvp_cancelled'   => ['success', 'Your RSVP has been cancelled.'],
        'event_created'    => ['success', 'Event created successfully.'],
        'event_updated'    => ['success', 'Event updated successfully.'],
        'event_deleted'    => ['success', 'Event deleted successfully.'],
        'profile_updated'  => ['success', 'Profile updated successfully.'],
    ];
    if (isset($messages[$msg])) {
        [$type, $text] = $messages[$msg];
        return "<div class='alert alert-$type alert-dismissible'>
            <button type='button' class='close' data-dismiss='alert'>&times;</button>
            $text
        </div>";
    }
    return '';
}
?>
