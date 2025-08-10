<?php
session_start();
include('../includes/db.php');
// Include your database connection file

$user_id = $_SESSION['user_id']; // Assuming user ID is stored in the session

// Dummy notifications data for showcase
// In real implementation, this would fetch from database
$notifications = [
    [
        'notification_id' => 1,
        'message' => 'Your event "Sarah & Mike Wedding" has been assigned to planner Jane Smith.',
        'date_sent' => '2024-01-15 10:30:00',
        'status' => 'unread'
    ],
    [
        'notification_id' => 2,
        'message' => 'New message received from your event planner.',
        'date_sent' => '2024-01-14 15:45:00',
        'status' => 'read'
    ],
    [
        'notification_id' => 3,
        'message' => 'Event "Tech Conference 2024" has been completed successfully.',
        'date_sent' => '2024-01-13 09:20:00',
        'status' => 'read'
    ],
    [
        'notification_id' => 4,
        'message' => 'Payment received for event "Birthday Party".',
        'date_sent' => '2024-01-12 14:15:00',
        'status' => 'read'
    ],
    [
        'notification_id' => 5,
        'message' => 'New event planner available in your area.',
        'date_sent' => '2024-01-11 11:00:00',
        'status' => 'unread'
    ]
];
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
