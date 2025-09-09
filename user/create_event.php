<?php
session_start();
require_once '../includes/db.php';

// Check if user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'client') {
    header('Location: ../login.php');
    exit();
}

$error = '';
$success = '';

// Get available planners (approved and available)
$available_planners = [];
$stmt = $conn->prepare(
    "SELECT u.id, u.full_name, u.username, p.company_name, p.rating, p.total_reviews, p.specialization, p.hourly_rate
    FROM users u 
    JOIN planners p ON u.id = p.user_id 
    WHERE u.status = 'active' AND u.user_type = 'planner' AND p.approval_status = 'approved' AND p.availability = 1
    ORDER BY p.rating DESC, p.total_reviews DESC"
);
if ($stmt && $stmt->execute()) {
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $available_planners[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $venue = trim($_POST['venue']);
    $budget = (float)$_POST['budget'];
    $guest_count = (int)$_POST['guest_count'];
    $event_type = $_POST['event_type'];
    $planner_id = !empty($_POST['planner_id']) ? (int)$_POST['planner_id'] : null;
    
    // Validation
    if (empty($title) || empty($event_date) || empty($event_time)) {
        $error = 'Please fill in all required fields.';
    } elseif (strtotime($event_date) < strtotime(date('Y-m-d'))) {
        $error = 'Event date cannot be in the past.';
    } elseif ($budget <= 0) {
        $error = 'Budget must be greater than 0.';
    } elseif ($guest_count <= 0) {
        $error = 'Guest count must be greater than 0.';
    } else if ($planner_id) {
        $chk = $conn->prepare("SELECT 1 FROM users u JOIN planners p ON u.id = p.user_id WHERE u.id = ? AND u.status='active' AND u.user_type='planner' AND p.approval_status='approved' AND p.availability=1");
        if ($chk) {
            $chk->bind_param('i', $planner_id);
            $chk->execute();
            if ($chk->get_result()->num_rows === 0) {
                $error = 'Selected planner is not available. Please choose another.';
            }
        }
    }
    
    if (empty($error)) {
        try {
            $conn->begin_transaction();
            $stmt = $conn->prepare(
                "INSERT INTO events (title, description, event_date, event_time, venue, budget, guest_count, event_type, client_id, planner_id, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')"
            );
            $stmt->bind_param("sssssdissi", $title, $description, $event_date, $event_time, $venue, $budget, $guest_count, $event_type, $_SESSION['user_id'], $planner_id);
            
            if ($stmt->execute()) {
                $event_id = $conn->insert_id;
                if ($planner_id) {
                    $stmt = $conn->prepare(
                        "INSERT INTO bookings (event_id, client_id, planner_id, booking_date, amount, status) 
                        VALUES (?, ?, ?, CURDATE(), ?, 'pending')"
                    );
                    $stmt->bind_param("iiid", $event_id, $_SESSION['user_id'], $planner_id, $budget);
                    $stmt->execute();
                    $notification_message = "New event request: " . $title . " from " . $_SESSION['full_name'];
                    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message, is_read, event_id) VALUES (?, ?, 0, ?)");
                    $stmt->bind_param("isi", $planner_id, $notification_message, $event_id);
                    $stmt->execute();
                }
                $conn->commit();
                $success = 'Event created successfully!';
                $title = $description = $venue = '';
                $event_date = $event_time = '';
                $budget = $guest_count = 0;
                $event_type = '';
                $planner_id = null;
            } else {
                throw new Exception('Failed to create event');
            }
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Error creating event: ' . $e->getMessage();
        }
    }
}

$selected_planner_id = isset($_GET['planner_id']) ? (int)$_GET['planner_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event - Event Management System</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .sidebar { position: sticky; top: 0; height: 100vh; overflow-y: auto; }
        .create-event-container { background:#fff; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow:hidden; }
        .create-event-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:#fff; padding:30px; text-align:center; }
        .create-event-form { padding:40px; }
        .form-control { border-radius:10px; border:2px solid #e9ecef; padding:12px 15px; transition: all .3s ease; }
        .form-control:focus { border-color:#667eea; box-shadow: 0 0 0 0.2rem rgba(102,126,234,.25); }
        .btn-create { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border:none; border-radius:10px; padding:12px 30px; font-weight:600; }
        .planner-selection { background:#f8f9fa; border-radius:15px; padding:20px; margin-bottom:20px; }
        .planner-card { border:2px solid #e9ecef; border-radius:15px; padding:20px; margin-bottom:15px; cursor:pointer; transition: all .3s; }
        .planner-card:hover { border-color:#667eea; transform: translateY(-2px); box-shadow:0 5px 15px rgba(0,0,0,.1); }
        .planner-card.selected { border-color:#667eea; background: rgba(102,126,234,.1); }
        .main-content { background:#f8f9fa; min-height:100vh; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="text-center mb-4">
                        <h4 class="text-white"><i class="fas fa-user me-2"></i>User Dashboard</h4>
                        <p class="text-white-50 small">Welcome, <?php echo $_SESSION['full_name']; ?></p>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link" href="user_index.php" style="color: rgba(255,255,255,0.8); padding: 12px 20px; margin: 5px 0; border-radius: 10px; transition: all 0.3s ease;"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                        <a class="nav-link active" href="create_event.php" style="color: white; background: rgba(255,255,255,0.1); padding: 12px 20px; margin: 5px 0; border-radius: 10px; transition: all 0.3s ease;"><i class="fas fa-plus me-2"></i>Create Event</a>
                        <a class="nav-link" href="my_events.php" style="color: rgba(255,255,255,0.8); padding: 12px 20px; margin: 5px 0; border-radius: 10px; transition: all 0.3s ease;"><i class="fas fa-calendar me-2"></i>My Events</a>
                        <a class="nav-link" href="user_index.php" style="color: rgba(255,255,255,0.8); padding: 12px 20px; margin: 5px 0; border-radius: 10px; transition: all 0.3s ease;"><i class="fas fa-arrow-left me-2"></i>Back to Dashboard</a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-0">
                <div class="main-content p-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="create-event-container">
                                <div class="create-event-header">
                                    <h2><i class="fas fa-calendar-plus me-2"></i>Create New Event</h2>
                                    <p class="mb-0">Plan your perfect event with our professional planners</p>
                                </div>
                                
                                <div class="create-event-form">
                                    <?php if ($error): ?>
                                        <div class="alert alert-danger"><?php echo $error; ?></div>
                                    <?php endif; ?>
                                    
                                    <?php if ($success): ?>
                                        <div class="alert alert-success">
                                            <?php echo $success; ?>
                                            <br><a href="my_events.php" class="alert-link">View your events</a>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form method="POST" action="" id="eventForm">
                                        <!-- Event Details -->
                                        <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Event Details</h5>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label for="title" class="form-label">Event Title *</label>
                                                    <input type="text" class="form-control" id="title" name="title" placeholder="Enter event title" value="<?php echo htmlspecialchars($title ?? ''); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="event_type" class="form-label">Event Type *</label>
                                                    <select class="form-control" id="event_type" name="event_type" required>
                                                        <option value="">Select type</option>
                                                        <option value="wedding" <?php echo ($event_type ?? '') == 'wedding' ? 'selected' : ''; ?>>Wedding</option>
                                                        <option value="birthday" <?php echo ($event_type ?? '') == 'birthday' ? 'selected' : ''; ?>>Birthday</option>
                                                        <option value="corporate" <?php echo ($event_type ?? '') == 'corporate' ? 'selected' : ''; ?>>Corporate Event</option>
                                                        <option value="anniversary" <?php echo ($event_type ?? '') == 'anniversary' ? 'selected' : ''; ?>>Anniversary</option>
                                                        <option value="conference" <?php echo ($event_type ?? '') == 'conference' ? 'selected' : ''; ?>>Conference</option>
                                                        <option value="other" <?php echo ($event_type ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Event Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Describe your event vision and requirements"><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="event_date" class="form-label">Event Date *</label>
                                                    <input type="date" class="form-control" id="event_date" name="event_date" value="<?php echo $event_date ?? ''; ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="event_time" class="form-label">Event Time *</label>
                                                    <input type="time" class="form-control" id="event_time" name="event_time" value="<?php echo $event_time ?? ''; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="venue" class="form-label">Venue</label>
                                                    <input type="text" class="form-control" id="venue" name="venue" placeholder="Enter venue or leave blank for now" value="<?php echo htmlspecialchars($venue ?? ''); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="guest_count" class="form-label">Expected Guest Count *</label>
                                                    <input type="number" class="form-control" id="guest_count" name="guest_count" min="1" placeholder="Number of guests" value="<?php echo $guest_count ?? ''; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="budget" class="form-label">Budget ($) *</label>
                                            <input type="number" class="form-control" id="budget" name="budget" min="0" step="0.01" placeholder="Enter your budget" value="<?php echo $budget ?? ''; ?>" required>
                                        </div>
                                        
                                        <hr class="my-4">
                                        
                                        <!-- Planner Selection -->
                                        <h5 class="text-primary mb-3"><i class="fas fa-user-tie me-2"></i>Choose a Planner (Optional)</h5>
                                        <p class="text-muted">You can select a planner now or leave it unassigned and choose later.</p>
                                        
                                        <div class="planner-selection">
                                    
                                            
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="radio" name="planner_option" id="select_planner" value="yes" <?php echo $selected_planner_id ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="select_planner">
                                                    <strong>Select a planner</strong> - Choose from our recommended professionals
                                                </label>
                                            </div>
                                            
                                            <div id="plannerSelection" class="<?php echo $selected_planner_id ? '' : 'd-none'; ?>">
                                                <h6 class="mb-3">Available Planners:</h6>
                                                <?php foreach ($available_planners as $planner): ?>
                                                <div class="planner-card" onclick="selectPlanner(<?php echo $planner['id']; ?>, this)">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-8">
                                                            <h6 class="mb-1"><?php echo htmlspecialchars($planner['full_name']); ?></h6>
                                                            <p class="text-muted small mb-1"><i class="fas fa-building me-1"></i><?php echo htmlspecialchars($planner['company_name']); ?></p>
                                                            <p class="text-muted small mb-1"><i class="fas fa-tags me-1"></i><?php echo htmlspecialchars($planner['specialization']); ?></p>
                                                            <p class="text-muted small mb-0"><i class="fas fa-dollar-sign me-1"></i>$<?php echo $planner['hourly_rate']; ?>/hour</p>
                                                        </div>
                                                        <div class="col-md-4 text-end">
                                                            <div class="text-warning mb-2">
                                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                    <i class="fas fa-star<?php echo $i <= $planner['rating'] ? '' : '-o'; ?>"></i>
                                                                <?php endfor; ?>
                                                            </div>
                                                            <small class="text-muted">(<?php echo $planner['total_reviews']; ?> reviews)</small>
                                                            <div class="mt-2">
                                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick='showPortfolio(<?php echo (int)$planner['id']; ?>, <?php echo json_encode($planner["full_name"]); ?>)'><i class="fas fa-images me-1"></i>View Work</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        
                                        <input type="hidden" name="planner_id" id="planner_id" value="<?php echo $selected_planner_id; ?>">
                                        
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary btn-create"><i class="fas fa-calendar-plus me-2"></i>Create Event</button>
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
    <script>
        // Ensure Bootstrap JS is available (fallback to CDN if local missing)
        function loadScriptOnce(src, id) {
            return new Promise((resolve, reject) => {
                if (document.getElementById(id)) { resolve(); return; }
                const s = document.createElement('script');
                s.src = src; s.id = id; s.async = true;
                s.onload = () => resolve();
                s.onerror = () => reject(new Error('Failed to load ' + src));
                document.body.appendChild(s);
            });
        }
        async function ensureBootstrapJs() {
            if (window.bootstrap && window.bootstrap.Modal) return;
            try { await loadScriptOnce('https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', 'bootstrapCdn'); } catch (e) {}
        }
        document.querySelectorAll('input[name="planner_option"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const plannerSelection = document.getElementById('plannerSelection');
                if (this.value === 'yes') {
                    plannerSelection.classList.remove('d-none');
                } else {
                    plannerSelection.classList.add('d-none');
                    document.getElementById('planner_id').value = '';
                    document.querySelectorAll('.planner-card').forEach(card => card.classList.remove('selected'));
                }
            });
        });
        function selectPlanner(plannerId, cardElement) {
            document.querySelectorAll('.planner-card').forEach(card => card.classList.remove('selected'));
            cardElement.classList.add('selected');
            document.getElementById('planner_id').value = plannerId;
            document.getElementById('select_planner').checked = true;
            document.getElementById('plannerSelection').classList.remove('d-none');
        }
        document.getElementById('event_date').min = new Date().toISOString().split('T')[0];

        // Portfolio modal logic
        function ensureModal() {
            if (document.getElementById('portfolioModal')) return;
            const modalHtml = `
            <div class="modal fade" id="portfolioModal" tabindex="-1">
              <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title"><i class=\"fas fa-images me-2\"></i>Portfolio</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div id="portfolioGrid" class="row g-3"></div>
                  </div>
                </div>
              </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }
        window.showPortfolio = async function(plannerId, name) {
            ensureModal();
            await ensureBootstrapJs();
            const modalEl = document.getElementById('portfolioModal');
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            const title = modalEl.querySelector('.modal-title');
            if (title) title.innerHTML = `<i class=\"fas fa-images me-2\"></i>${name}'s Portfolio`;
            const grid = modalEl.querySelector('#portfolioGrid');
            grid.innerHTML = '<div class="text-muted">Loading...</div>';
            try {
                const res = await fetch(`../planner/portfolio_fetch.php?planner_id=${plannerId}`);
                const data = await res.json();
                if (!Array.isArray(data) || data.length === 0) {
                    grid.innerHTML = '<p class="text-muted mb-0">No images uploaded yet.</p>';
                } else {
                    const base = '../';
                    grid.innerHTML = data.map(it => {
                        let src = it.image_path || '';
                        if (src.startsWith('http://') || src.startsWith('https://') || src.startsWith('/')) {
                            // absolute
                        } else {
                            src = base + src.replace(/^\.\/?/, '');
                        }
                        const cap = it.caption ? `<div class=\"card-body p-2\"><small class=\"text-muted\">${it.caption}</small></div>` : '';
                        return `
                        <div class=\"col-md-4\">
                            <div class=\"card\">
                                <img src=\"${src}\" class=\"card-img-top\" alt=\"\">
                                ${cap}
                            </div>
                        </div>
                        `;
                    }).join('');
                }
                modal.show();
            } catch (e) {
                grid.innerHTML = '<div class="text-danger">Failed to load portfolio</div>';
                modal.show();
            }
        }
    </script>
</body>
</html> 