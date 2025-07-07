<?php
session_start();
include('../includes/db.php');
// Include your database connection file

$user_id = $_SESSION['user_id']; // Assuming user ID is stored in the session

// Fetch notifications for the logged-in user
$notifications = [];
$query = "SELECT notification_id, message, date_sent, status FROM notifications WHERE user_id = ? ORDER BY date_sent DESC";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('i', $user_id);
    if ($stmt->execute()) {
        $stmt->bind_result($notification_id, $message, $date_sent, $status);
        while ($stmt->fetch()) {
            $notifications[] = [
                'notification_id' => $notification_id,
                'message' => $message,
                'date_sent' => $date_sent,
                'status' => $status
            ];
        }
        $stmt->close();
    } else {
        echo "Error executing query: " . $stmt->error;
    }
} else {
    echo "Error preparing statement: " . $conn->error;
}
?>

<?php include 'header.php'; ?>  

<div class="container my-5">
    <h1 class="text-center mb-4">Notifications & Alerts</h1>

    <!-- Notifications List -->
    <div class="list-group">
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $notification): ?>
                <a href="#" class="list-group-item list-group-item-action <?php echo $notification['status'] == 'unread' ? 'list-group-item-warning' : ''; ?>">
                    <h5 class="mb-1">Notification</h5>
                    <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                    <small><?php echo time_elapsed_string($notification['date_sent']); ?></small>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">No notifications found.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>

<?php
// Helper function to calculate time elapsed since the notification was sent
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = [
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    ];
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>
