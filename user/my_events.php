<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'client') {
    header('Location: ../login.php');
    exit();
}

$clientId = (int)$_SESSION['user_id'];
$events_result = [];
$stmt = $conn->prepare("SELECT e.id AS event_id, e.title, e.event_date, e.event_time, e.venue, e.guest_count AS expected_guests, e.budget, e.status,
                               ec.category_name, up.full_name AS planner_name
                        FROM events e
                        LEFT JOIN event_categories ec ON e.category_id = ec.id
                        LEFT JOIN users up ON e.planner_id = up.id
                        WHERE e.client_id = ?
                        ORDER BY e.event_date DESC, e.created_at DESC");
if ($stmt) {
    $stmt->bind_param('i', $clientId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) { $events_result[] = $row; }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events - EMS</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar { position: sticky; top:0; height:100vh; overflow-y:auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding:12px 20px; margin:5px 0; border-radius:10px; transition:all .3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color:#fff; background:rgba(255,255,255,.1); transform: translateX(5px); }
        .main-content { background:#f8f9fa; min-height:100vh; }
        .events-card { background:#fff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
        .event-card { border:2px solid #e9ecef; border-radius:15px; transition:all .3s; margin-bottom:20px; }
        .event-card:hover { border-color:#667eea; transform:translateY(-3px); box-shadow:0 10px 20px rgba(0,0,0,0.1); }
        .status-badge { font-size:.8rem; padding:5px 12px; border-radius:20px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white"><i class="fas fa-user me-2"></i>User Dashboard</h4>
                        <p class="text-white-50 small">Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?></p>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="user_index.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                        <a class="nav-link" href="create_event.php"><i class="fas fa-plus me-2"></i>Create Event</a>
                        <a class="nav-link active" href="my_events.php"><i class="fas fa-calendar me-2"></i>My Events</a>
                        <a class="nav-link" href="messages.php"><i class="fas fa-envelope me-2"></i>Messages</a>
                        <a class="nav-link" href="notifications.php"><i class="fas fa-bell me-2"></i>Notifications</a>
                        <a class="nav-link" href="profile_management.php"><i class="fas fa-user-cog me-2"></i>Profile</a>
                        <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </nav>
                </div>
            </div>
            <div class="col-md-9 col-lg-10 px-0">
                <div class="main-content p-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="events-card p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <h2 class="fw-bold text-primary"><i class="fas fa-calendar-alt me-2"></i>My Events</h2>
                                        <p class="text-muted mb-0">Manage and track your events</p>
                                    </div>
                                    <a href="create_event.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Create New Event</a>
                                </div>

                                <?php if (!empty($events_result)): ?>
                                    <div class="row">
                                        <?php foreach ($events_result as $event): ?>
                                            <div class="col-md-6 col-lg-4">
                                                <div class="event-card p-4">
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($event['title']); ?></h5>
                                                        <span class="status-badge badge bg-<?php 
                                                            echo $event['status'] == 'pending' ? 'secondary' : 
                                                                ($event['status'] == 'confirmed' ? 'success' : 
                                                                ($event['status'] == 'completed' ? 'info' : 'warning')); 
                                                        ?>">
                                                            <?php echo ucfirst(str_replace('_', ' ', $event['status'])); ?>
                                                        </span>
                                                    </div>
                                                    <div class="mb-3">
                                                        <small class="text-muted"><i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($event['category_name'] ?? ''); ?></small>
                                                    </div>
                                                    <div class="mb-3">
                                                        <p class="mb-2"><i class="fas fa-calendar me-2 text-primary"></i><?php echo date('M d, Y', strtotime($event['event_date'])); ?></p>
                                                        <p class="mb-2"><i class="fas fa-clock me-2 text-primary"></i><?php echo date('g:i A', strtotime($event['event_time'])); ?></p>
                                                        <p class="mb-2"><i class="fas fa-map-marker-alt me-2 text-primary"></i><?php echo htmlspecialchars($event['venue'] ?? ''); ?></p>
                                                        <?php if (!empty($event['expected_guests'])): ?>
                                                            <p class="mb-2"><i class="fas fa-users me-2 text-primary"></i><?php echo (int)$event['expected_guests']; ?> guests</p>
                                                        <?php endif; ?>
                                                        <?php if (!empty($event['budget'])): ?>
                                                            <p class="mb-2"><i class="fas fa-dollar-sign me-2 text-primary"></i>$<?php echo number_format($event['budget'], 2); ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php if (!empty($event['planner_name'])): ?>
                                                        <div class="mb-3 p-3 bg-light rounded">
                                                            <small class="text-muted">Assigned to:</small>
                                                            <p class="mb-0 fw-bold"><?php echo htmlspecialchars($event['planner_name']); ?></p>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="d-flex gap-2">
                                                        <a href="event_details.php?id=<?php echo (int)$event['event_id']; ?>" class="btn btn-outline-primary btn-sm"><i class="fas fa-eye me-1"></i>View</a>
                                                        <?php if ($event['status'] == 'pending'): ?>
                                                            <a href="edit_event.php?id=<?php echo (int)$event['event_id']; ?>" class="btn btn-outline-secondary btn-sm"><i class="fas fa-edit me-1"></i>Edit</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center p-5 text-muted">
                                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                        <h4>No Events Yet</h4>
                                        <p class="mb-4">You haven't created any events yet. Start by creating your first event!</p>
                                        <a href="create_event.php" class="btn btn-primary btn-lg"><i class="fas fa-plus me-2"></i>Create Your First Event</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html> 