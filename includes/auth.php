<?php
// includes/auth.php
require_once __DIR__ . '/config.php';

function registerUser($full_name, $email, $password, $reg_number) {
    $db = getDB();
    $errors = [];

    // Validate
    if (empty($full_name))   $errors[] = 'Full name is required.';
    if (empty($email))       $errors[] = 'Email is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if (empty($reg_number))  $errors[] = 'Registration number is required.';

    if (!empty($errors)) return ['success' => false, 'errors' => $errors];

    // Check email exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return ['success' => false, 'errors' => ['This email is already registered.']];
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (full_name, email, password, role, reg_number) VALUES (?, ?, ?, 'student', ?)");
    $stmt->bind_param("ssss", $full_name, $email, $hash, $reg_number);

    if ($stmt->execute()) {
        return ['success' => true];
    }
    return ['success' => false, 'errors' => ['Registration failed. Try again.']];
}

function loginUser($email, $password) {
    $db = getDB();

    if (empty($email) || empty($password)) {
        return ['success' => false, 'errors' => ['Email and password are required.']];
    }

    $stmt = $db->prepare("SELECT id, full_name, email, password, role, reg_number FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return ['success' => false, 'errors' => ['Invalid email or password.']];
    }

    $user = $result->fetch_assoc();

    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'errors' => ['Invalid email or password.']];
    }

    // Set session
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['email']     = $user['email'];
    $_SESSION['role']      = $user['role'];
    $_SESSION['reg_number']= $user['reg_number'];

    return ['success' => true, 'role' => $user['role']];
}

function logoutUser() {
    session_destroy();
}
?>
