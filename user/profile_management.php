<?php
// Include database connection
include('../includes/db.php');
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'client') {
    header('Location: ../login.php');
    exit();
}

$userId = (int)($_SESSION['user_id'] ?? 0);
$error = $_SESSION['error_message'] ?? '';
$success = $_SESSION['success_message'] ?? '';
unset($_SESSION['error_message'], $_SESSION['success_message']);

// Load real user data
$user = [ 'full_name' => '', 'email' => '', 'phone' => '', 'username' => '' ];
$stmt = $conn->prepare('SELECT username, full_name, email, phone FROM users WHERE id = ?');
if ($stmt) {
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $user = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Management - EMS</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar { position: sticky; top:0; height:100vh; overflow-y:auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding:12px 20px; margin:5px 0; border-radius:10px; transition:all .3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color:#fff; background:rgba(255,255,255,.1); transform: translateX(5px); }
        .main-content { background:#f8f9fa; min-height:100vh; }
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
                        <a class="nav-link active" href="profile_management.php"><i class="fas fa-user-cog me-2"></i>Profile</a>
                        <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </nav>
                </div>
            </div>
            <div class="col-md-9 col-lg-10 px-0">
                <div class="main-content p-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-lg border-0 rounded-lg">
                                <div class="card-header bg-primary text-white text-center py-4">
                                    <h3 class="fw-bold mb-0">Profile Management</h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($success)) { ?><div class="alert alert-success" role="alert"><?php echo htmlspecialchars($success); ?></div><?php } ?>
                                    <?php if (!empty($error)) { ?><div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div><?php } ?>
                                    <form action="profile_management_backend.php" method="post">
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label">Username</label>
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email" class="form-label">Email Address</label>
                                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                            </div>
                                        </div>
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <label for="name" class="form-label">Full Name</label>
                                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required pattern="\S+.*" title="Spaces are not allowed at the start or end.">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="phone" class="form-label">Phone Number</label>
                                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" pattern="\d*" title="Please enter a valid phone number with digits only.">
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary px-4">Update Profile</button>
                                        </div>
                                    </form>
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
