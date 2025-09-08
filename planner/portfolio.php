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
    <title>Portfolio - <?php echo htmlspecialchars($displayName); ?> - EventPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/css/lightbox.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px 0; }
        .portfolio-container { background: rgba(255,255,255,.95); border-radius:20px; box-shadow: 0 15px 35px rgba(0,0,0,.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,.2); }
        .planner-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:#fff; border-radius:20px 20px 0 0; padding:40px; }
        .stats-card { background:#fff; border-radius:15px; padding:20px; margin-bottom:20px; border:2px solid #e9ecef; transition: all .3s; }
        .stats-card:hover { border-color:#667eea; transform: translateY(-3px); box-shadow:0 8px 25px rgba(102,126,234,.2); }
        .event-card { background:#fff; border-radius:15px; padding:20px; margin-bottom:20px; border:2px solid #e9ecef; transition: all .3s; }
        .event-card:hover { border-color:#667eea; transform: translateY(-3px); box-shadow:0 8px 25px rgba(102,126,234,.2); }
        .event-gallery { display:grid; grid-template-columns: repeat(auto-fit, minmax(150px,1fr)); gap:10px; margin-top:15px; }
        .gallery-item { border-radius:10px; overflow:hidden; transition: transform .3s; }
        .gallery-item:hover { transform: scale(1.05); }
        .gallery-item img { width:100%; height:100px; object-fit:cover; }
        .review-card { background:#f8f9fa; border-radius:15px; padding:20px; margin-bottom:15px; border-left:4px solid #667eea; }
        .service-card { background:#fff; border-radius:15px; padding:25px; margin-bottom:20px; border:2px solid #e9ecef; text-align:center; transition: all .3s; }
        .service-card:hover { border-color:#667eea; transform: translateY(-3px); box-shadow:0 8px 25px rgba(102,126,234,.2); }
        .service-icon { font-size:3rem; color:#667eea; margin-bottom:15px; }
        .specialization-tag { background:#667eea; color:#fff; padding:5px 12px; border-radius:20px; font-size:.8rem; margin:2px; display:inline-block; }
        .planner-image { width:120px; height:120px; border-radius:50%; object-fit:cover; border:4px solid #fff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="portfolio-container">
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
                                <a href="profile_management.php" class="btn btn-outline-light btn-custom"><i class="fas fa-edit me-1"></i>Edit Profile</a>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <!-- Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-3"><div class="stats-card text-center"><h3 class="fw-bold text-primary"><?php echo (int)$stats['total_events']; ?></h3><p class="text-muted mb-0">Total Events</p></div></div>
                            <div class="col-md-3"><div class="stats-card text-center"><h3 class="fw-bold text-success"><?php echo (int)$stats['completed_events']; ?></h3><p class="text-muted mb-0">Completed Events</p></div></div>
                            <div class="col-md-3"><div class="stats-card text-center"><h3 class="fw-bold text-info"><?php echo (int)$stats['happy_clients']; ?>%</h3><p class="text-muted mb-0">Happy Clients</p></div></div>
                            <div class="col-md-3"><div class="stats-card text-center"><h3 class="fw-bold text-warning"><?php echo number_format($stats['average_rating'],1); ?></h3><p class="text-muted mb-0">Average Rating</p></div></div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/js/lightbox.min.js"></script>
</body>
</html> 