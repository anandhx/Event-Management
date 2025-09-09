<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'planner') {
    header('Location: ../login.php');
    exit();
}

$plannerUserId = (int)$_SESSION['user_id'];

// Fetch planner basic info (users + planners)
$plannerUser = ['full_name'=>'','email'=>'','phone'=>'','profile_image'=>null];
$planner = ['company_name'=>'','specialization'=>'','experience_years'=>0,'location'=>'','bio'=>'','rating'=>0,'total_reviews'=>0,'services_offered'=>''];

if ($stmt = $conn->prepare('SELECT full_name, email, phone, profile_image FROM users WHERE id = ?')) {
    $stmt->bind_param('i', $plannerUserId);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) { $plannerUser = $row; }
}
if ($stmt = $conn->prepare('SELECT company_name, specialization, experience_years, location, bio, rating, total_reviews, services_offered FROM planners WHERE user_id = ?')) {
    $stmt->bind_param('i', $plannerUserId);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) { $planner = $row; }
}

$displayName = $plannerUser['full_name'] ?: ($_SESSION['full_name'] ?? 'Planner');
$companyName = $planner['company_name'] ?: ($_SESSION['company_name'] ?? '');

// Stats
$stats = ['total_events'=>0,'completed_events'=>0,'happy_clients'=>0,'average_rating'=>0,'years_experience'=>(int)$planner['experience_years']];
if ($stmt = $conn->prepare("SELECT COUNT(*) AS c FROM events WHERE planner_id = ?")) { $stmt->bind_param('i',$plannerUserId); $stmt->execute(); $stats['total_events'] = (int)$stmt->get_result()->fetch_assoc()['c']; }
if ($stmt = $conn->prepare("SELECT COUNT(*) AS c FROM events WHERE planner_id = ? AND status = 'completed'")) { $stmt->bind_param('i',$plannerUserId); $stmt->execute(); $stats['completed_events'] = (int)$stmt->get_result()->fetch_assoc()['c']; }
$stats['happy_clients'] = $stats['total_events'] > 0 ? (int)round(($stats['completed_events'] / $stats['total_events']) * 100) : 0;
if ($stmt = $conn->prepare("SELECT COALESCE(AVG(rating),0) AS r, COUNT(*) AS cnt FROM reviews WHERE planner_id = ?")) { $stmt->bind_param('i',$plannerUserId); $stmt->execute(); $row = $stmt->get_result()->fetch_assoc(); $stats['average_rating'] = round((float)$row['r'], 1); $planner['total_reviews'] = (int)$row['cnt']; }

// Featured events: latest 3 for this planner
$featured_events = [];
$sql = "SELECT e.id, e.title, e.event_type, e.description, e.event_date, e.venue, e.budget, ec.category_name
        FROM events e
        LEFT JOIN event_categories ec ON e.category_id = ec.id
        WHERE e.planner_id = ?
        ORDER BY e.event_date DESC, e.created_at DESC
        LIMIT 3";
if ($stmt = $conn->prepare($sql)) { $stmt->bind_param('i', $plannerUserId); $stmt->execute(); $res = $stmt->get_result(); while ($row = $res->fetch_assoc()) { $featured_events[] = $row; } }

// Event galleries for featured events (up to 3 images per event)
$event_images = [];
if (!empty($featured_events)) {
    $ids = array_column($featured_events, 'id');
    $in  = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    if ($stmt = $conn->prepare("SELECT event_id, image_path FROM event_gallery WHERE event_id IN ($in) ORDER BY uploaded_at DESC")) {
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $eid = (int)$row['event_id'];
            if (!isset($event_images[$eid])) { $event_images[$eid] = []; }
            if (count($event_images[$eid]) < 3) { $event_images[$eid][] = $row['image_path']; }
        }
    }
}

// Recent reviews
$recent_reviews = [];
$sql = "SELECT r.rating, r.comment, r.review_date, e.title AS event_title, u.full_name AS client_name
        FROM reviews r
        JOIN events e ON r.event_id = e.id
        JOIN users u ON r.client_id = u.id
        WHERE r.planner_id = ?
        ORDER BY r.review_date DESC
        LIMIT 4";
if ($stmt = $conn->prepare($sql)) { $stmt->bind_param('i', $plannerUserId); $stmt->execute(); $res = $stmt->get_result(); while ($row = $res->fetch_assoc()) { $recent_reviews[] = $row; } }

// Services offered
$services = [];
if (!empty($planner['services_offered'])) {
    foreach (explode(',', $planner['services_offered']) as $svc) {
        $svc = trim($svc);
        if ($svc !== '') { $services[] = $svc; }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Planner</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/css/lightbox.min.css" rel="stylesheet">
    <link href="../assets/css/admin-style.css" rel="stylesheet">
    <style>
        .sidebar { position: sticky; top:0; height:100vh; overflow-y:auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding:12px 20px; margin:5px 0; border-radius:10px; transition:all .3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color:#fff; background:rgba(255,255,255,.1); transform: translateX(5px); }
        .main-content { background:#f8f9fa; min-height:100vh; }
        .messages-container { background:#fff; border-radius:20px; box-shadow:0 10px 30px rgba(0,0,0,0.08); height: 75vh; }
        .conversation-list { border-right: 2px solid #e9ecef; height:100%; overflow-y:auto; }
        .chat-area { height:100%; display:flex; flex-direction:column; }
        .chat-header { padding: 16px 20px; border-bottom:2px solid #e9ecef; background:#f8f9fa; }
        .chat-messages { flex:1; padding:20px; overflow-y:auto; background:#f8f9fa; }
        .chat-input { padding:16px 20px; border-top:2px solid #e9ecef; background:#fff; }
        .planner-avatar { width:40px; height:40px; border-radius:50%; object-fit:cover; }
        .message-bubble { margin-bottom:12px; max-width:70%; }
        .message-bubble.self { margin-left:auto; }
        .message-content { padding:12px 16px; border-radius:18px; }
        .message-bubble.self .message-content { background:#667eea; color:#fff; }
        .message-bubble.other .message-content { background:#fff; border:1px solid #e9ecef; }
        /* Use shared admin sidebar theme via admin-style.css */
        .main-content { margin-left: 280px; }
        .sidebar-toggle { position: fixed; top: 12px; left: 12px; z-index: 1040; background: #111827; color: #fff; border: none; border-radius: 8px; padding: 8px 10px; display: none; }
        @media (max-width: 768px) { .main-content { margin-left: 0; } .sidebar-toggle { display: inline-flex; } body { padding-top: 48px; } }
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
                        <a class="nav-link active" href="portfolio.php"><i class="fas fa-briefcase me-2"></i>Portfolio</a>
                        <a class="nav-link" href="my_events.php"><i class="fas fa-calendar me-2"></i>My Events</a>
                        <a class="nav-link" href="messages.php"><i class="fas fa-envelope me-2"></i>Messages</a>
                        <a class="nav-link " href="notifications.php"><i class="fas fa-bell me-2"></i>Notifications</a>
                        <a class="nav-link" href="profile_management.php"><i class="fas fa-user-cog me-2"></i>Profile</a>
                        <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </nav>
                </div>
            </div>
            <div class="col-md-9 col-lg-10 px-0">
                    <!-- Planner Header -->
                    <div class="planner-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?php echo htmlspecialchars($plannerUser['profile_image'] ?? '../assets/img/team-1.jpg'); ?>" alt="<?php echo htmlspecialchars($displayName); ?>" class="planner-image me-4">
                                    <div>
                                        <h1 class="fw-bold mb-2"><?php echo htmlspecialchars($displayName); ?></h1>
                                        <h4 class="mb-2"><?php echo htmlspecialchars($companyName); ?></h4>
                                        <div class="mb-2">
                                            <span class="text-warning me-2">
                                                <?php for ($i=1;$i<=5;$i++): ?>
                                                    <i class="fas fa-star<?php echo $i <= round($stats['average_rating']) ? '' : ' text-muted'; ?>"></i>
                                            <?php endfor; ?>
                                            </span>
                                            <span><?php echo number_format($stats['average_rating'],1); ?> (<?php echo (int)$planner['total_reviews']; ?> reviews)</span>
                                        </div>
                                        <p class="mb-0 opacity-75">
                                            <i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($planner['location']); ?> |
                                            <i class="fas fa-star me-2"></i><?php echo (int)$stats['years_experience']; ?> years experience
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="../user/messages.php" class="btn btn-light btn-custom me-2"><i class="fas fa-envelope me-1"></i>Contact</a>
                                
                        <a class="nav-link" href="portfolio_upload.php"><i class="fas fa-upload me-2"></i>Upload Work</a>
                                <a href="profile_management.php" class="btn btn-outline-light btn-custom"><i class="fas fa-edit me-1"></i>Edit Profile</a>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <!-- Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-3"><div class="stats-card text-center"><h3 class="fw-bold text-primary"><?php echo (int)$stats['total_events']; ?></h3><p class=" mb-0">Total Events</p></div></div>
                            <div class="col-md-3"><div class="stats-card text-center"><h3 class="fw-bold text-success"><?php echo (int)$stats['completed_events']; ?></h3><p class=" mb-0">Completed Events</p></div></div>
                            <div class="col-md-3"><div class="stats-card text-center"><h3 class="fw-bold text-info"><?php echo (int)$stats['happy_clients']; ?>%</h3><p class="t mb-0">Happy Clients</p></div></div>
                            <div class="col-md-3"><div class="stats-card text-center"><h3 class="fw-bold text-warning"><?php echo number_format($stats['average_rating'],1); ?></h3><p class=" mb-0">Average Rating</p></div></div>
                        </div>

                        <!-- About & Specializations -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h4 class="fw-bold mb-3"><i class="fas fa-user me-2"></i>About Me</h4>
                                <p class="text-muted mb-3"><?php echo nl2br(htmlspecialchars($planner['bio'])); ?></p>

                                <?php if (!empty($planner['specialization'])): ?>
                                <h5 class="fw-bold mb-3">Specializations</h5>
                                <div class="mb-3">
                                        <?php foreach (explode(',', $planner['specialization']) as $spec): ?>
                                            <span class="specialization-tag"><?php echo htmlspecialchars(trim($spec)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($services)): ?>
                                    <h5 class="fw-bold mb-3">Services</h5>
                                <div class="mb-3">
                                        <?php foreach ($services as $svc): ?>
                                            <span class="specialization-tag"><?php echo htmlspecialchars($svc); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-2">Contact Information</h6>
                                        <p class="mb-1"><i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($plannerUser['email']); ?></p>
                                        <p class="mb-1"><i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($plannerUser['phone']); ?></p>
                                        <p class="mb-0"><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($planner['location']); ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <h4 class="fw-bold mb-3"><i class="fas fa-cogs me-2"></i>Services Offered</h4>
                                <?php if (!empty($services)): ?>
                                    <?php foreach ($services as $svc): ?>
                                        <div class="service-card"><i class="fas fa-briefcase service-icon"></i><h6 class="fw-bold mb-2"><?php echo htmlspecialchars($svc); ?></h6></div>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">No services listed.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Featured Events -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="fw-bold mb-3"><i class="fas fa-star me-2"></i>Featured Events</h4>
                                <?php if (empty($featured_events)): ?>
                                    <p class="text-muted">No events to display.</p>
                                <?php else: ?>
                                    <?php foreach ($featured_events as $fe): ?>
                                    <div class="event-card">
                                        <div class="row">
                                            <div class="col-md-8">
                                                    <h5 class="fw-bold mb-2"><?php echo htmlspecialchars($fe['title']); ?></h5>
                                                <p class="text-muted mb-2">
                                                        <i class="fas fa-tag me-1"></i><?php echo htmlspecialchars($fe['category_name'] ?: ucfirst($fe['event_type'])); ?> |
                                                        <i class="fas fa-calendar me-1"></i><?php echo htmlspecialchars($fe['event_date']); ?> |
                                                        <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($fe['venue']); ?> |
                                                        <i class="fas fa-dollar-sign me-1"></i>$<?php echo number_format((float)$fe['budget']); ?>
                                                    </p>
                                                    <p class="text-muted mb-3"><?php echo htmlspecialchars($fe['description'] ?? ''); ?></p>
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="fw-bold mb-2">Event Gallery</h6>
                                                <div class="event-gallery">
                                                        <?php $imgs = $event_images[$fe['id']] ?? []; if (empty($imgs)) { $imgs = ['../assets/img/event-planning.jpg']; } ?>
                                                        <?php foreach ($imgs as $i => $img): ?>
                                                        <div class="gallery-item">
                                                                <a href="<?php echo htmlspecialchars($img); ?>" data-lightbox="event-<?php echo (int)$fe['id']; ?>">
                                                                    <img src="<?php echo htmlspecialchars($img); ?>" alt="Event Image <?php echo $i+1; ?>">
                                                            </a>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Recent Reviews -->
                        <div class="row">
                            <div class="col-12">
                                <h4 class="fw-bold mb-3"><i class="fas fa-comments me-2"></i>Recent Reviews</h4>
                                <div class="row">
                                    <?php if (empty($recent_reviews)): ?>
                                        <p class="text-muted">No reviews yet.</p>
                                    <?php else: ?>
                                        <?php foreach ($recent_reviews as $rv): ?>
                                        <div class="col-md-6">
                                            <div class="review-card">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($rv['client_name']); ?></h6>
                                                        <div class="text-warning">
                                                            <?php for ($i=1;$i<=5;$i++): ?>
                                                                <i class="fas fa-star<?php echo $i <= (int)$rv['rating'] ? '' : ' text-muted'; ?>"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                                    <p class="text-muted mb-1"><i class="fas fa-calendar me-1"></i><?php echo htmlspecialchars($rv['event_title']); ?></p>
                                                    <p class="mb-2">"<?php echo htmlspecialchars($rv['comment']); ?>"</p>
                                                    <small class="text-muted"><i class="fas fa-clock me-1"></i><?php echo htmlspecialchars($rv['review_date']); ?></small>
                                            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/js/lightbox.min.js"></script>
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