<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function authenticate($username, $password) {
    global $conn;
    $stmt = $conn->prepare('SELECT password, role FROM Users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($hashed_password, $role);
    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
        return $role;
    }
    return false;
}

function getUserRole($user_id) {
    global $conn;
    $stmt = $conn->prepare('SELECT role FROM Users WHERE user_id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($role);
    $stmt->fetch();
    return $role;
}
?>
