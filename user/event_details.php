<?php
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'client') {
    header('Location: ../login.php');
    exit();
}

$event = $event ?? [ 'id'=>1, 'title'=>'Event', 'date'=>'', 'time'=>'', 'venue'=>'', 'status'=>'pending', 'budget'=>0 ];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - EMS</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar { position: sticky; top:0; height:100vh; overflow-y:auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding:12px 20px; margin:5px 0; border-radius:10px; transition:all .3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color:#fff; background:rgba(255,255,255,.1); transform: translateX(5px); }
        .main-content { background:#f8f9fa; min-height:100vh; }
        .card-wrap { background:#fff; border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,0.08); }
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
                        <a class="nav-link" href="my_events.php"><i class="fas fa-calendar me-2"></i>My Events</a>
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
                            <div class="card-wrap p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <h3 class="mb-1"><?php echo htmlspecialchars($event['title']); ?></h3>
                                        <small class="text-muted"><i class="fas fa-calendar me-1"></i><?php echo htmlspecialchars($event['date'] ?? ''); ?> · <i class="fas fa-clock ms-2 me-1"></i><?php echo htmlspecialchars($event['time'] ?? ''); ?></small>
                                    </div>
                                    <span class="badge bg-success"><?php echo ucfirst($event['status']); ?></span>
                                </div>
                                <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($event['venue'] ?? ''); ?></p>
                                <hr>
                                <p><strong>Budget:</strong> $<?php echo number_format((float)($event['budget'] ?? 0)); ?></p>
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