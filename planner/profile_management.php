<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'planner') {
    header('Location: ../login.php');
    exit();
}

$userId = (int)$_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $experience_years = (int)($_POST['experience_years'] ?? 0);
    $location = trim($_POST['location'] ?? '');
    $bio = trim($_POST['bio'] ?? '');

    if ($full_name === '' || $email === '') { $error = 'Full name and email are required.'; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $error = 'Enter a valid email.'; }
    else {
        // Update users
        $su = $conn->prepare('UPDATE users SET full_name=?, email=?, phone=? WHERE id=?');
        if ($su) { $su->bind_param('sssi', $full_name, $email, $phone, $userId); $su->execute(); }
        // Update planners
        $sp = $conn->prepare('UPDATE planners SET company_name=?, specialization=?, experience_years=?, location=?, bio=? WHERE user_id=?');
        if ($sp) { $sp->bind_param('ssissi', $company_name, $specialization, $experience_years, $location, $bio, $userId); $sp->execute(); }
        $_SESSION['full_name'] = $full_name;
        $success = 'Profile updated successfully!';
    }
}

// Load current data
$user = ['full_name'=>'','email'=>'','phone'=>''];
$planner = ['company_name'=>'','specialization'=>'','experience_years'=>0,'location'=>'','bio'=>''];
$ru = $conn->prepare('SELECT full_name,email,phone FROM users WHERE id=?');
if ($ru) { $ru->bind_param('i',$userId); $ru->execute(); $res=$ru->get_result(); if($row=$res->fetch_assoc()){$user=$row;} }
$rp = $conn->prepare('SELECT company_name,specialization,experience_years,location,bio FROM planners WHERE user_id=?');
if ($rp) { $rp->bind_param('i',$userId); $rp->execute(); $res=$rp->get_result(); if($row=$res->fetch_assoc()){$planner=$row;} }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planner Profile - EMS</title>
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
                            <div class="card-wrap p-4">
                                <h3 class="mb-3"><i class="fas fa-user-cog me-2"></i>Profile</h3>
                                <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>
                                <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone</label>
                                            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Company Name</label>
                                            <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($planner['company_name']); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Specialization</label>
                                            <input type="text" name="specialization" class="form-control" value="<?php echo htmlspecialchars($planner['specialization']); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Experience (years)</label>
                                            <input type="number" name="experience_years" class="form-control" value="<?php echo (int)$planner['experience_years']; ?>" min="0" max="60">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Location</label>
                                            <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($planner['location']); ?>">
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Bio</label>
                                            <textarea name="bio" class="form-control" rows="3"><?php echo htmlspecialchars($planner['bio']); ?></textarea>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </form>
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
