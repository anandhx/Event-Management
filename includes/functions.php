<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function authenticate($conn, $username, $password) {
    // Real authentication against database with hashed passwords
    $stmt = $conn->prepare("SELECT id, username, email, password, user_type, full_name FROM users WHERE (username = ? OR email = ?) AND status = 'active'");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Check hashed password using password_verify
        if (password_verify($password, $user['password'])) {
            return [
                'id' => $user['id'],
                'username' => $user['username'],
                'user_type' => $user['user_type'],
                'full_name' => $user['full_name']
            ];
        }
    }
    return false;
}

function getUserRole($conn, $user_id) {
    $stmt = $conn->prepare("SELECT user_type FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->bind_result($user_type);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
    
    return $user_type ?? 'client';
}

function getUserById($conn, $user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getPlannerInfo($conn, $user_id) {
    $stmt = $conn->prepare("SELECT p.*, u.full_name, u.email, u.phone FROM planners p JOIN users u ON p.user_id = u.id WHERE p.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../login.php");
        exit();
    }
}

function requireRole($required_role) {
    if (!isLoggedIn()) {
        header("Location: ../login.php");
        exit();
    }
    
    if ($_SESSION['user_type'] !== $required_role) {
        header("Location: ../index.php");
        exit();
    }
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function formatDate($date) {
    return date('F j, Y', strtotime($date));
}

function formatDateTime($datetime) {
    return date('F j, Y g:i A', strtotime($datetime));
}

function getEventStatusBadge($status) {
    $badges = [
        'pending' => '<span class="badge bg-warning">Pending</span>',
        'confirmed' => '<span class="badge bg-success">Confirmed</span>',
        'in_progress' => '<span class="badge bg-info">In Progress</span>',
        'completed' => '<span class="badge bg-primary">Completed</span>',
        'cancelled' => '<span class="badge bg-danger">Cancelled</span>'
    ];
    
    return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
}

function getPriorityBadge($priority) {
    $badges = [
        'low' => '<span class="badge bg-success">Low</span>',
        'medium' => '<span class="badge bg-warning">Medium</span>',
        'high' => '<span class="badge bg-danger">High</span>',
        'urgent' => '<span class="badge bg-danger">Urgent</span>'
    ];
    
    return $badges[$priority] ?? '<span class="badge bg-secondary">Unknown</span>';
}
?>
