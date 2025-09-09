

<?php
session_start();
require_once '../includes/db.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'client') {
    header('Location: ../login.php');
    exit();
}

// Get user statistics from database
$user_stats = [];

// Prepared helper
$clientId = (int)$_SESSION['user_id'];
function fetch_single_int($conn, $sql, $types, $params, $column) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) { return 0; }
    if ($types !== '') { $stmt->bind_param($types, ...$params); }
    $stmt->execute();
    $res = $stmt->get_result();
    if (!$res) { return 0; }
    $row = $res->fetch_assoc();
    return isset($row[$column]) ? (int)$row[$column] : 0;
}

// Total events for this user
$user_stats['total_events'] = fetch_single_int(
    $conn,
    "SELECT COUNT(*) AS count FROM events WHERE client_id = ?",
    'i',
    [$clientId],
    'count'
);

// Completed events
$user_stats['completed_events'] = fetch_single_int(
    $conn,
    "SELECT COUNT(*) AS count FROM events WHERE client_id = ? AND status = 'completed'",
    'i',
    [$clientId],
    'count'
);

// Pending events
$user_stats['pending_events'] = fetch_single_int(
    $conn,
    "SELECT COUNT(*) AS count FROM events WHERE client_id = ? AND status = 'pending'",
    'i',
    [$clientId],
    'count'
);

// Total spent (from bookings)
$stmt = $conn->prepare("SELECT COALESCE(SUM(amount), 0) AS total FROM bookings WHERE client_id = ? AND status = 'paid'");
if ($stmt) {
    $stmt->bind_param('i', $clientId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : ['total' => 0];
    $user_stats['total_spent'] = (float)$row['total'];
} else {
    $user_stats['total_spent'] = 0;
}

// Upcoming events
$user_stats['upcoming_events'] = fetch_single_int(
    $conn,
    "SELECT COUNT(*) AS count FROM events WHERE client_id = ? AND event_date >= CURDATE() AND status IN ('confirmed','in_progress')",
    'i',
    [$clientId],
    'count'
);

// Favorite planners (count distinct planners who worked with this client)
$user_stats['favorite_planners'] = fetch_single_int(
    $conn,
    "SELECT COUNT(DISTINCT e.planner_id) AS count FROM events e WHERE e.client_id = ? AND e.planner_id IS NOT NULL",
    'i',
    [$clientId],
    'count'
);

// Get user events
$user_events = getEventsByUserId($conn, $_SESSION['user_id'], 'client');

// Get recommended planners (approved & available)
$recommended_planners = [];
$stmt = $conn->prepare(
    "SELECT u.id, u.full_name, u.username, p.company_name, p.rating, p.total_reviews, p.specialization, p.experience_years, p.hourly_rate
     FROM users u
     JOIN planners p ON u.id = p.user_id
     WHERE u.status = 'active' AND u.user_type = 'planner' AND p.approval_status = 'approved' AND p.availability = 1
     ORDER BY p.rating DESC, p.total_reviews DESC
     LIMIT 6"
);
if ($stmt && $stmt->execute()) {
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $recommended_planners[] = $row;
    }
}

// Get unread notifications count
$notifications_count = getUnreadNotificationsCount($conn, $_SESSION['user_id']);

// Get unread messages count
$messages_count = getUnreadMessagesCount($conn, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Event Management System</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .main-content {
            background: #f8f9fa;
            min-height: 100vh;
        }
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        .event-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .event-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .planner-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        .planner-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white"><i class="fas fa-user me-2"></i>User Dashboard</h4>
                        <p class="text-white-50 small">Welcome, <?php echo $_SESSION['full_name']; ?></p>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="user_index.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="create_event.php">
                            <i class="fas fa-plus me-2"></i>Create Event
                        </a>
                        <a class="nav-link" href="my_events.php">
                            <i class="fas fa-calendar me-2"></i>My Events
                        </a>
                        <a class="nav-link" href="messages.php">
                            <i class="fas fa-envelope me-2"></i>Messages
                            <?php if ($messages_count > 0): ?>
                                <span class="badge bg-danger ms-2"></span>
                            <?php endif; ?>
                        </a>
                        <a class="nav-link" href="notifications.php">
                            <i class="fas fa-bell me-2"></i>Notifications
                            <?php if ($notifications_count > 0): ?>
                                <span class="badge bg-danger ms-2"><?php echo $notifications_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <a class="nav-link" href="profile_management.php">
                            <i class="fas fa-user-cog me-2"></i>Profile
                        </a>
                        <a class="nav-link" href="../logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-0">
                <div class="main-content p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-tachometer-alt me-2"></i>Welcome back, <?php echo $_SESSION['full_name']; ?>!</h2>
                        <div class="d-flex gap-2">
                            <a href="create_event.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create New Event
                            </a>
                        </div>
                    </div>
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-2 mb-3">
                            <div class="stat-card text-center">
                                <div class="stat-icon bg-primary mx-auto mb-3">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <h4 class="mb-1"><?php echo $user_stats['total_events']; ?></h4>
                                <p class="text-muted mb-0 small">Total Events</p>
                            </div>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <div class="stat-card text-center">
                                <div class="stat-icon bg-success mx-auto mb-3">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <h4 class="mb-1"><?php echo $user_stats['completed_events']; ?></h4>
                                <p class="text-muted mb-0 small">Completed</p>
                            </div>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <div class="stat-card text-center">
                                <div class="stat-icon bg-warning mx-auto mb-3">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h4 class="mb-1"><?php echo $user_stats['pending_events']; ?></h4>
                                <p class="text-muted mb-0 small">Pending</p>
                            </div>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <div class="stat-card text-center">
                                <div class="stat-icon bg-info mx-auto mb-3">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <h4 class="mb-1"><?php echo $user_stats['upcoming_events']; ?></h4>
                                <p class="text-muted mb-0 small">Upcoming</p>
                            </div>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <div class="stat-card text-center">
                                <div class="stat-icon bg-secondary mx-auto mb-3">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <h4 class="mb-1">$<?php echo number_format($user_stats['total_spent']); ?></h4>
                                <p class="text-muted mb-0 small">Total Spent</p>
                            </div>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <div class="stat-card text-center">
                                <div class="stat-icon bg-dark mx-auto mb-3">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <h4 class="mb-1"><?php echo $user_stats['favorite_planners']; ?></h4>
                                <p class="text-muted mb-0 small">Planners</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Events and Recommended Planners -->
                    <div class="row">
                        <div class="col-md-8 mb-4">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="fas fa-calendar me-2"></i>Recent Events</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($user_events)): ?>
                                        <p class="text-muted text-center">No events yet. <a href="create_event.php">Create your first event!</a></p>
                                    <?php else: ?>
                                        <?php foreach (array_slice($user_events, 0, 4) as $event): ?>
                                        <div class="event-card">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($event['title']); ?></h6>
                                                    <p class="text-muted small mb-1">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?php echo date('M d, Y', strtotime($event['event_date'])); ?> at <?php echo date('g:i A', strtotime($event['event_time'])); ?>
                                                    </p>
                                                    <p class="text-muted small mb-1">
                                                        <i class="fas fa-map-marker-alt me-1"></i>
                                                        <?php echo htmlspecialchars($event['venue'] ?? 'TBD'); ?>
                                                    </p>
                                                    <p class="text-muted small mb-0">
                                                        <i class="fas fa-dollar-sign me-1"></i>
                                                        Budget: $<?php echo number_format($event['budget']); ?>
                                                    </p>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <span class="badge bg-<?php 
                                                        echo $event['status'] == 'confirmed' ? 'success' : 
                                                            ($event['status'] == 'pending' ? 'warning' : 
                                                            ($event['status'] == 'completed' ? 'info' : 'secondary')); 
                                                    ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $event['status'])); ?>
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo ucfirst($event['event_type']); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                        
                                        <?php if (count($user_events) > 4): ?>
                                            <div class="text-center">
                                                <a href="my_events.php" class="btn btn-outline-primary">View All Events</a>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="fas fa-star me-2"></i>Recommended Planners</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($recommended_planners)): ?>
                                        <p class="text-muted text-center">No planners available</p>
                                    <?php else: ?>
                                        <?php foreach (array_slice($recommended_planners, 0, 3) as $planner): ?>
                                        <div class="planner-card">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                        <i class="fas fa-user-tie text-white"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($planner['full_name']); ?></h6>
                                                    <p class="text-muted small mb-1"><?php echo htmlspecialchars($planner['company_name']); ?></p>
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-warning me-2">
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <i class="fas fa-star<?php echo $i <= $planner['rating'] ? '' : '-o'; ?>"></i>
                                                            <?php endfor; ?>
                                                        </div>
                                                        <small class="text-muted">(<?php echo $planner['total_reviews']; ?> reviews)</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="text-muted small mb-2">
                                                <i class="fas fa-tags me-1"></i>
                                                <?php echo htmlspecialchars($planner['specialization']); ?>
                                            </p>
                                            <p class="text-muted small mb-2">
                                                <i class="fas fa-clock me-1"></i>
                                                <?php echo $planner['experience_years']; ?> years experience
                                            </p>
                                            <p class="text-muted small mb-2">
                                                <i class="fas fa-dollar-sign me-1"></i>
                                                $<?php echo $planner['hourly_rate']; ?>/hour
                                            </p>
                                            <a href="create_event.php?planner_id=<?php echo $planner['id']; ?>" class="btn btn-primary btn-sm w-100">
                                                <i class="fas fa-calendar-plus me-1"></i>Book This Planner
                                            </a>
                                        </div>
                                        <?php endforeach; ?>
                                        
                                        <div class="text-center">
                                            <a href="#" class="btn btn-outline-success">View All Planners</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
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