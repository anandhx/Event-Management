<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'planner') {
    header('Location: ../login.php');
    exit();
}

$plannerId = (int)$_SESSION['user_id'];
$events = [];
$stmt = $conn->prepare("SELECT e.id, e.title, e.event_date, e.event_time, e.venue, e.status, e.budget,
                               u.full_name AS client_name
                        FROM events e
                        JOIN users u ON e.client_id = u.id
                        WHERE e.planner_id = ?
                        ORDER BY e.event_date DESC, e.created_at DESC");
if ($stmt) {
    $stmt->bind_param('i', $plannerId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) { $events[] = $row; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events - Planner</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar { position: sticky; top:0; height:100vh; overflow-y:auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding:12px 20px; margin:5px 0; border-radius:10px; transition:all .3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color:#fff; background:rgba(255,255,255,.1); transform: translateX(5px); }
        .main-content { background:#f8f9fa; min-height:100vh; }
        .event-card { background:#fff; border-radius:15px; padding:20px; margin-bottom:20px; box-shadow:0 5px 15px rgba(0,0,0,0.08); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white"><i class="fas fa-user-tie me-2"></i>Planner</h4>
                        <p class="text-white-50 small">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="planner_index.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                        <a class="nav-link" href="portfolio.php"><i class="fas fa-briefcase me-2"></i>Portfolio</a>
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
                    <h3 class="mb-3"><i class="fas fa-calendar me-2"></i>My Events</h3>
                    <?php if (empty($events)): ?>
                        <p class="text-muted">No events assigned yet.</p>
                    <?php else: ?>
                        <?php foreach ($events as $e): ?>
                            <div class="event-card">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="mb-1"><?php echo htmlspecialchars($e['title']); ?></h5>
                                        <small class="text-muted d-block"><i class="fas fa-user me-1"></i>Client: <?php echo htmlspecialchars($e['client_name']); ?></small>
                                        <small class="text-muted d-block"><i class="fas fa-calendar me-1"></i><?php echo date('M d, Y', strtotime($e['event_date'])); ?> · <i class="fas fa-clock ms-2 me-1"></i><?php echo date('g:i A', strtotime($e['event_time'])); ?></small>
                                        <small class="text-muted d-block"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($e['venue'] ?? 'TBD'); ?></small>
                                        <small class="text-muted d-block"><i class="fas fa-dollar-sign me-1"></i>Budget: $<?php echo number_format($e['budget']); ?></small>
                                    </div>
                                    <span class="badge bg-<?php echo $e['status'] == 'confirmed' ? 'success' : ($e['status'] == 'pending' ? 'warning' : ($e['status'] == 'completed' ? 'info' : 'secondary')); ?>"><?php echo ucfirst(str_replace('_',' ', $e['status'])); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
