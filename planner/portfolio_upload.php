<?php
session_start();
require_once '../includes/db.php';

// Ensure portfolio table exists (safety guard)
@$conn->query("CREATE TABLE IF NOT EXISTS planner_portfolio_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    planner_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    caption VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ppi_planner (planner_id),
    CONSTRAINT fk_ppi_planner FOREIGN KEY (planner_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'planner') {
    header('Location: ../login.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please choose an image to upload';
    } else {
        $file = $_FILES['image'];
        $allowed = ['image/jpeg' => '.jpg', 'image/png' => '.png', 'image/gif' => '.gif'];
        $mime = mime_content_type($file['tmp_name']);
        if (!isset($allowed[$mime])) {
            $error = 'Only JPG, PNG, or GIF allowed';
        } else {
            $ext = $allowed[$mime];
            $safeName = 'planner_' . (int)$_SESSION['user_id'] . '_' . time() . '_' . bin2hex(random_bytes(4)) . $ext;
            $uploadDir = '../assets/img/portfolio/';
            if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0775, true); }
            $dest = $uploadDir . $safeName;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $caption = trim($_POST['caption'] ?? '');
                $pathForDb = 'assets/img/portfolio/' . $safeName;
                $stmt = $conn->prepare('INSERT INTO planner_portfolio_images (planner_id, image_path, caption) VALUES (?, ?, ?)');
                if ($stmt) {
                    $stmt->bind_param('iss', $_SESSION['user_id'], $pathForDb, $caption);
                    if ($stmt->execute()) { $success = 'Image uploaded successfully'; }
                    else { $error = 'DB execute error: ' . $stmt->error; }
                } else { $error = 'DB prepare error: ' . $conn->error; }
            } else {
                $error = 'Failed to move uploaded file';
            }
        }
    }
}

// Fetch existing images
$images = [];
$stmt = $conn->prepare('SELECT id, image_path, caption, created_at FROM planner_portfolio_images WHERE planner_id = ? ORDER BY created_at DESC');
if ($stmt) { $stmt->bind_param('i', $_SESSION['user_id']); $stmt->execute(); $res = $stmt->get_result(); while ($row = $res->fetch_assoc()) { $images[] = $row; } } else { $error = $error ?: ('DB prepare error: ' . $conn->error); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Upload - Planner</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin-style.css" rel="stylesheet">
</head>
<body>
    <button class="sidebar-toggle" onclick="document.getElementById('adminSidebar').classList.toggle('show')">
        <i class="fas fa-bars"></i>
    </button>

    <div class="admin-sidebar p-4" id="adminSidebar">
        <div class="mb-4">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="fas fa-briefcase fa-lg text-white-50"></i>
                <h5 class="mb-0">Planner Panel</h5>
            </div>
            <small class="text-white-50">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></small>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="planner_index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <a class="nav-link" href="portfolio.php"><i class="fas fa-id-card"></i><span>Portfolio</span></a> 
            <a class="nav-link" href="my_events.php"><i class="fas fa-calendar-alt"></i><span>My Events</span></a>
            <a class="nav-link" href="messages.php"><i class="fas fa-envelope"></i><span>Messages</span></a>
            <a class="nav-link" href="notifications.php"><i class="fas fa-bell"></i><span>Notifications</span></a>
            <a class="nav-link" href="profile_management.php"><i class="fas fa-user-cog"></i><span>Profile</span></a>
            <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </nav>
    </div>

    <div class="main-content p-4">
        <div class="container">
            <h3 class="mb-3"><i class="fas fa-upload me-2"></i>Upload Your Work</h3>
            <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

            <form method="post" enctype="multipart/form-data" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Choose Image</label>
                        <input type="file" name="image" accept="image/*" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Caption (optional)</label>
                        <input type="text" name="caption" class="form-control" maxlength="255" placeholder="Short description">
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary"><i class="fas fa-cloud-upload-alt me-1"></i>Upload</button>
                </div>
            </form>

            <h5 class="mb-3">Your Portfolio</h5>
            <div class="row">
                <?php foreach ($images as $img): ?>
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" class="card-img-top" alt="">
                            <div class="card-body p-2">
                                <small class="text-muted"><?php echo htmlspecialchars($img['caption'] ?? ''); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($images)): ?><p class="text-muted">No images uploaded yet.</p><?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('adminSidebar');
            const toggle = document.querySelector('.sidebar-toggle');
            if (window.innerWidth <= 768 && sidebar && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    </script>
</body>
</html>


