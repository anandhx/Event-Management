<?php
// Database configuration for Event Management System
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "event_management_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Helper function to get user by ID (moved to functions.php)
// function getUserById($conn, $id) {
//     $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
//     $stmt->bind_param("i", $id);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     return $result->fetch_assoc();
// }

// Helper function to get planner info by user ID
function getPlannerByUserId($conn, $user_id) {
    $stmt = $conn->prepare("SELECT p.*, u.* FROM planners p JOIN users u ON p.user_id = u.id WHERE u.id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Helper function to get events by user ID
function getEventsByUserId($conn, $user_id, $user_type) {
    if ($user_type == 'client') {
        $stmt = $conn->prepare("SELECT e.*, u.full_name as planner_name FROM events e LEFT JOIN users u ON e.planner_id = u.id WHERE e.client_id = ? ORDER BY e.event_date DESC");
    } else {
        $stmt = $conn->prepare("SELECT e.*, u.full_name as client_name FROM events e LEFT JOIN users u ON e.client_id = u.id WHERE e.planner_id = ? ORDER BY e.event_date DESC");
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Helper function to get notifications count
function getUnreadNotificationsCount($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Helper function to get unread messages count
function getUnreadMessagesCount($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

// Helper function to get event categories
function getEventCategories($conn) {
    $stmt = $conn->prepare("SELECT * FROM event_categories ORDER BY category_name");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Helper function to get event services by category
function getEventServicesByCategory($conn, $category_id) {
    $stmt = $conn->prepare("SELECT * FROM event_services WHERE category_id = ? ORDER BY service_name");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Helper function to get available planners
function getAvailablePlanners($conn) {
    $stmt = $conn->prepare("SELECT p.*, u.full_name, u.email, u.phone FROM planners p JOIN users u ON p.user_id = u.id WHERE p.approval_status = 'approved' AND p.availability = 1 ORDER BY p.experience_years DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Helper function to get all events
function getAllEvents($conn) {
    $stmt = $conn->prepare("SELECT e.*, u.full_name as client_name, ec.category_name FROM events e JOIN users u ON e.client_id = u.id LEFT JOIN event_categories ec ON e.category_id = ec.id ORDER BY e.created_at DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Helper function to get event by ID
function getEventById($conn, $event_id) {
    $stmt = $conn->prepare("SELECT e.*, u.full_name as client_name, ec.category_name FROM events e JOIN users u ON e.client_id = u.id LEFT JOIN event_categories ec ON e.category_id = ec.id WHERE e.id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Helper function to get messages between two users
function getMessagesBetweenUsers($conn, $user1_id, $user2_id, $event_id = null) {
    if ($event_id) {
        $stmt = $conn->prepare("SELECT * FROM messages WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) AND event_id = ? ORDER BY created_at ASC");
        $stmt->bind_param("iiiii", $user1_id, $user2_id, $user2_id, $user1_id, $event_id);
    } else {
        $stmt = $conn->prepare("SELECT * FROM messages WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) ORDER BY created_at ASC");
        $stmt->bind_param("iiii", $user1_id, $user2_id, $user2_id, $user1_id);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Helper function to get user notifications
function getUserNotifications($conn, $user_id) {
    $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Helper function to create notification
function createNotification($conn, $user_id, $message, $event_id = null) {
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, event_id) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $user_id, $message, $event_id);
    return $stmt->execute();
}

// Helper function to mark notification as read
function markNotificationAsRead($conn, $notification_id) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $notification_id);
    return $stmt->execute();
}

// Helper function to send message
function sendMessage($conn, $sender_id, $receiver_id, $message, $event_id = null) {
    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message, event_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisi", $sender_id, $receiver_id, $message, $event_id);
    return $stmt->execute();
}

// Helper function to mark message as read
function markMessageAsRead($conn, $message_id) {
    $stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    return $stmt->execute();
}
?>
