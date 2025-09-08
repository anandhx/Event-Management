<?php
session_start();
require_once '../includes/db.php';

// Check if user is logged in and is a planner
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'planner') {
    header('Location: ../login.php');
    exit();
}

// Get planner statistics from database
$planner_stats = [];

// Total events for this planner
$result = $conn->query("SELECT COUNT(*) as count FROM events WHERE planner_id = " . $_SESSION['user_id']);
$planner_stats['total_events'] = $result->fetch_assoc()['count'];

// Completed events
$result = $conn->query("SELECT COUNT(*) as count FROM events WHERE planner_id = " . $_SESSION['user_id'] . " AND status = 'completed'");
$planner_stats['completed_events'] = $result->fetch_assoc()['count'];

// Pending events
$result = $conn->query("SELECT COUNT(*) as count FROM events WHERE planner_id = " . $_SESSION['user_id'] . " AND status = 'pending'");
$planner_stats['pending_events'] = $result->fetch_assoc()['count'];

// Total earnings (from bookings)
$result = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM bookings WHERE planner_id = " . $_SESSION['user_id'] . " AND status = 'paid'");
$planner_stats['total_earnings'] = $result->fetch_assoc()['total'];

// Average rating
$result = $conn->query("SELECT COALESCE(AVG(rating), 0) as avg_rating FROM reviews WHERE planner_id = " . $_SESSION['user_id']);
$planner_stats['average_rating'] = round($result->fetch_assoc()['avg_rating'], 1);

// Total reviews
$result = $conn->query("SELECT COUNT(*) as count FROM reviews WHERE planner_id = " . $_SESSION['user_id']);
$planner_stats['total_reviews'] = $result->fetch_assoc()['count'];

// Events this month
$result = $conn->query("SELECT COUNT(*) as count FROM events WHERE planner_id = " . $_SESSION['user_id'] . " AND MONTH(event_date) = MONTH(CURRENT_DATE()) AND YEAR(event_date) = YEAR(CURRENT_DATE())");
$planner_stats['this_month_events'] = $result->fetch_assoc()['count'];

// Earnings this month
$result = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM bookings WHERE planner_id = " . $_SESSION['user_id'] . " AND MONTH(booking_date) = MONTH(CURRENT_DATE()) AND YEAR(booking_date) = YEAR(CURRENT_DATE()) AND status = 'paid'");
$planner_stats['this_month_earnings'] = $result->fetch_assoc()['total'];

// Get assigned events
$assigned_events = getEventsByUserId($conn, $_SESSION['user_id'], 'planner');

// Get available opportunities (events without planners)
$available_opportunities = [];
$result = $conn->query("
    SELECT e.*, u.full_name as client_name 
    FROM events e 
    JOIN users u ON e.client_id = u.id 
    WHERE e.planner_id IS NULL AND e.status = 'pending' 
    ORDER BY e.event_date ASC 
    LIMIT 5
");
while ($row = $result->fetch_assoc()) {
    $available_opportunities[] = $row;
}

// Get planner info
$planner_info = getPlannerByUserId($conn, $_SESSION['user_id']);

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
    <title>Planner Dashboard - Event Management System</title>
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
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
        .stat-icon { width: 60px; height: 60px; border-radius: 15px; display:flex; align-items:center; justify-content:center; font-size:24px; color:#fff; }
        .event-card { background:#fff; border-radius:15px; padding:20px; margin-bottom:20px; box-shadow:0 5px 15px rgba(0,0,0,0.08); transition:all .3s; }
        .event-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
        .opportunity-card { background:#fff; border-radius:15px; padding:20px; margin-bottom:20px; box-shadow:0 5px 15px rgba(0,0,0,0.08); transition:all .3s; border-left:4px solid #28a745; }
        .opportunity-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
        .notification-badge { position:absolute; top:-5px; right:-5px; background:#dc3545; color:#fff; border-radius:50%; width:20px; height:20px; font-size:12px; display:flex; align-items:center; justify-content:center; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white"><i class="fas fa-user-tie me-2"></i>Planner Dashboard</h4>
                        <p class="text-white-50 small">Welcome, <?php echo $_SESSION['full_name']; ?></p>
                        <p class="text-white-50 small"><?php echo htmlspecialchars($planner_info['company_name'] ?? ''); ?></p>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="planner_index.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                        <a class="nav-link" href="portfolio.php"><i class="fas fa-briefcase me-2"></i>Portfolio</a>
                        <a class="nav-link" href="my_events.php"><i class="fas fa-calendar me-2"></i>My Events</a>
                        <a class="nav-link" href="messages.php"><i class="fas fa-envelope me-2"></i>Messages</a>
                        <a class="nav-link" href="notifications.php"><i class="fas fa-bell me-2"></i>Notifications</a>
                        <a class="nav-link" href="profile_management.php"><i class="fas fa-user-cog me-2"></i>Profile</a>
                        <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
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
                            <a href="portfolio.php" class="btn btn-primary"><i class="fas fa-edit me-2"></i>Update Portfolio</a>
                        </div>
                    </div>
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-2 mb-3"><div class="stat-card text-center"><div class="stat-icon bg-primary mx-auto mb-3"><i class="fas fa-calendar-check"></i></div><h4 class="mb-1"><?php echo $planner_stats['total_events']; ?></h4><p class="text-muted mb-0 small">Total Events</p></div></div>
                        <div class="col-md-2 mb-3"><div class="stat-card text-center"><div class="stat-icon bg-success mx-auto mb-3"><i class="fas fa-check-circle"></i></div><h4 class="mb-1"><?php echo $planner_stats['completed_events']; ?></h4><p class="text-muted mb-0 small">Completed</p></div></div>
                        <div class="col-md-2 mb-3"><div class="stat-card text-center"><div class="stat-icon bg-warning mx-auto mb-3"><i class="fas fa-clock"></i></div><h4 class="mb-1"><?php echo $planner_stats['pending_events']; ?></h4><p class="text-muted mb-0 small">Pending</p></div></div>
                        <div class="col-md-2 mb-3"><div class="stat-card text-center"><div class="stat-icon bg-info mx-auto mb-3"><i class="fas fa-star"></i></div><h4 class="mb-1"><?php echo $planner_stats['average_rating']; ?></h4><p class="text-muted mb-0 small">Rating</p></div></div>
                        <div class="col-md-2 mb-3"><div class="stat-card text-center"><div class="stat-icon bg-secondary mx-auto mb-3"><i class="fas fa-dollar-sign"></i></div><h4 class="mb-1">$<?php echo number_format($planner_stats['total_earnings']); ?></h4><p class="text-muted mb-0 small">Total Earnings</p></div></div>
                        <div class="col-md-2 mb-3"><div class="stat-card text-center"><div class="stat-icon bg-dark mx-auto mb-3"><i class="fas fa-comments"></i></div><h4 class="mb-1"><?php echo $planner_stats['total_reviews']; ?></h4><p class="text-muted mb-0 small">Reviews</p></div></div>
                    </div>
                    
                    <!-- Monthly Stats -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3"><div class="stat-card text-center"><h4 class="text-primary"><?php echo $planner_stats['this_month_events']; ?></h4><p class="text-muted mb-0">Events This Month</p></div></div>
                        <div class="col-md-6 mb-3"><div class="stat-card text-center"><h4 class="text-success">$<?php echo number_format($planner_stats['this_month_earnings']); ?></h4><p class="text-muted mb-0">Earnings This Month</p></div></div>
                    </div>
                    
                    <!-- Assigned Events and Opportunities -->
                    <div class="row">
                        <div class="col-md-8 mb-4">
                            <div class="card">
                                <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="fas fa-calendar me-2"></i>My Assigned Events</h5></div>
                                <div class="card-body">
                                    <?php if (empty($assigned_events)): ?>
                                        <p class="text-muted text-center">No events assigned yet. Check available opportunities below!</p>
                                    <?php else: ?>
                                        <?php foreach (array_slice($assigned_events, 0, 4) as $event): ?>
                                        <div class="event-card">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($event['title']); ?></h6>
                                                    <p class="text-muted small mb-1"><i class="fas fa-user me-1"></i>Client: <?php echo htmlspecialchars($event['client_name']); ?></p>
                                                    <p class="text-muted small mb-1"><i class="fas fa-calendar me-1"></i><?php echo date('M d, Y', strtotime($event['event_date'])); ?> at <?php echo date('g:i A', strtotime($event['event_time'])); ?></p>
                                                    <p class="text-muted small mb-1"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($event['venue'] ?? 'TBD'); ?></p>
                                                    <p class="text-muted small mb-0"><i class="fas fa-dollar-sign me-1"></i>Budget: $<?php echo number_format($event['budget']); ?></p>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <span class="badge bg-<?php echo $event['status'] == 'confirmed' ? 'success' : ($event['status'] == 'pending' ? 'warning' : ($event['status'] == 'completed' ? 'info' : 'secondary')); ?>"><?php echo ucfirst(str_replace('_', ' ', $event['status'])); ?></span>
                                                    <br>
                                                    <small class="text-muted"><?php echo ucfirst($event['event_type']); ?></small>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                        <?php if (count($assigned_events) > 4): ?><div class="text-center"><a href="my_events.php" class="btn btn-outline-primary">View All Events</a></div><?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Available Opportunities</h5></div>
                                <div class="card-body">
                                    <?php if (empty($available_opportunities)): ?>
                                        <p class="text-muted text-center">No opportunities available at the moment</p>
                                    <?php else: ?>
                                        <?php foreach ($available_opportunities as $opportunity): ?>
                                        <div class="opportunity-card">
                                            <h6 class="mb-2"><?php echo htmlspecialchars($opportunity['title']); ?></h6>
                                            <p class="text-muted small mb-2"><i class="fas fa-user me-1"></i><?php echo htmlspecialchars($opportunity['client_name']); ?></p>
                                            <p class="text-muted small mb-2"><i class="fas fa-calendar me-1"></i><?php echo date('M d, Y', strtotime($opportunity['event_date'])); ?></p>
                                            <p class="text-muted small mb-2"><i class="fas fa-dollar-sign me-1"></i>Budget: $<?php echo number_format($opportunity['budget']); ?></p>
                                            <p class="text-muted small mb-2"><i class="fas fa-tags me-1"></i><?php echo ucfirst($opportunity['event_type']); ?></p>
                                            <button class="btn btn-success btn-sm w-100" onclick="applyForEvent(<?php echo $opportunity['id']; ?>)"><i class="fas fa-hand-paper me-1"></i>Apply for Event</button>
                                        </div>
                                        <?php endforeach; ?>
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
    <script>
        function applyForEvent(eventId) {
            if (confirm('Are you sure you want to apply for this event?')) {
                fetch('apply_for_event.php', { method: 'POST', headers: { 'Content-Type': 'application/json', }, body: JSON.stringify({ event_id: eventId }) })
                .then(response => response.json())
                .then(data => { if (data.success) { alert('Application submitted successfully!'); location.reload(); } else { alert('Error: ' + data.message); } });
            }
        }
    </script>
</body>
</html>
