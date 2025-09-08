<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'planner') {
    header('Location: ../login.php');
    exit();
}

$userId = (int)$_SESSION['user_id'];
$notifications = [];
$stmt = $conn->prepare('SELECT id, message, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC');
if ($stmt) { $stmt->bind_param('i', $userId); $stmt->execute(); $res = $stmt->get_result(); while ($row = $res->fetch_assoc()) { $notifications[] = $row; } }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Planner</title>
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
                        <h4 class="text-white"><i class="fas fa-user-tie me-2"></i>Planner</h4>
                        <p class="text-white-50 small">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="planner_index.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                        <a class="nav-link" href="portfolio.php"><i class="fas fa-briefcase me-2"></i>Portfolio</a>
                        <a class="nav-link" href="my_events.php"><i class="fas fa-calendar me-2"></i>My Events</a>
                        <a class="nav-link" href="messages.php"><i class="fas fa-envelope me-2"></i>Messages</a>
                        <a class="nav-link active" href="notifications.php"><i class="fas fa-bell me-2"></i>Notifications</a>
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
                                <h3 class="mb-3"><i class="fas fa-bell me-2"></i>Notifications</h3>
                                <div class="list-group">
                                    <?php if (!empty($notifications)): ?>
                                        <?php foreach ($notifications as $n): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-start <?php echo ((int)$n['is_read']) === 0 ? 'list-group-item-warning' : ''; ?>">
                                                <div>
                                                    <h6 class="mb-1">Notification</h6>
                                                    <p class="mb-1"><?php echo htmlspecialchars($n['message']); ?></p>
                                                    <small class="text-muted"><?php echo htmlspecialchars($n['created_at']); ?></small>
                                                </div>
                                                <?php if (((int)$n['is_read']) === 0): ?><span class="badge bg-danger">New</span><?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted">No notifications found.</p>
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
