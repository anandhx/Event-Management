<?php
session_start();
require_once '../includes/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = ['success' => false, 'message' => ''];
    
    switch ($_POST['action']) {
        case 'update_status':
            $event_id = (int)$_POST['event_id'];
            $new_status = $_POST['status'];
            
            $stmt = $conn->prepare("UPDATE events SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            if ($stmt->bind_param("si", $new_status, $event_id) && $stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Event status updated successfully';
            } else {
                $response['message'] = 'Failed to update event status';
            }
            break;
            
        case 'assign_planner':
            $event_id = (int)$_POST['event_id'];
            $planner_id = (int)$_POST['planner_id'];
            
            $stmt = $conn->prepare("UPDATE events SET planner_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            if ($stmt->bind_param("ii", $planner_id, $event_id) && $stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Planner assigned successfully';
            } else {
                $response['message'] = 'Failed to assign planner';
            }
            break;
            
        case 'delete_event':
            $event_id = (int)$_POST['event_id'];
            
            $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
            if ($stmt->bind_param("i", $event_id) && $stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Event deleted successfully';
            } else {
                $response['message'] = 'Failed to delete event';
            }
            break;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$event_type_filter = $_GET['event_type'] ?? '';
$date_filter = $_GET['date_filter'] ?? '';
$search = $_GET['search'] ?? '';

// Build query with filters
$where_conditions = [];
$params = [];
$param_types = '';

if ($status_filter) {
    $where_conditions[] = "e.status = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

if ($event_type_filter) {
    $where_conditions[] = "e.event_type = ?";
    $params[] = $event_type_filter;
    $param_types .= 's';
}

if ($date_filter) {
    switch ($date_filter) {
        case 'today':
            $where_conditions[] = "DATE(e.event_date) = CURDATE()";
            break;
        case 'week':
            $where_conditions[] = "e.event_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $where_conditions[] = "MONTH(e.event_date) = MONTH(CURRENT_DATE()) AND YEAR(e.event_date) = YEAR(CURRENT_DATE())";
            break;
        case 'upcoming':
            $where_conditions[] = "e.event_date >= CURDATE()";
            break;
    }
}

if ($search) {
    $where_conditions[] = "(e.title LIKE ? OR e.venue LIKE ? OR u1.full_name LIKE ? OR u2.full_name LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'ssss';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get events with filters
$query = "
    SELECT e.*, u1.full_name as client_name, u2.full_name as planner_name, u2.id as planner_id
    FROM events e 
    JOIN users u1 ON e.client_id = u1.id 
    LEFT JOIN users u2 ON e.planner_id = u2.id 
    $where_clause
    ORDER BY e.event_date ASC
";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$events_result = $stmt->get_result();
$events = [];
while ($row = $events_result->fetch_assoc()) {
    $events[] = $row;
}

// Get all planners for assignment
$planners_result = $conn->query("
    SELECT u.id, u.full_name, p.company_name 
    FROM users u 
    LEFT JOIN planners p ON u.id = p.user_id 
    WHERE u.user_type = 'planner' AND u.status = 'active'
    ORDER BY u.full_name
");
$planners = [];
while ($row = $planners_result->fetch_assoc()) {
    $planners[] = $row;
}

// Get event statistics
$stats = [];
$result = $conn->query("SELECT COUNT(*) as count FROM events");
$stats['total_events'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM events WHERE status = 'pending'");
$stats['pending_events'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM events WHERE status = 'confirmed'");
$stats['confirmed_events'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM events WHERE status = 'completed'");
$stats['completed_events'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM events WHERE status = 'cancelled'");
$stats['cancelled_events'] = $result->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management - Admin Dashboard</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/admin-style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="admin-sidebar p-4" id="adminSidebar">
                    <div class="text-center mb-5">
                        <div class="mb-3">
                            <i class="fas fa-calendar-check fa-3x text-white-50"></i>
                        </div>
                        <h4 class="text-white fw-bold">EMS Admin</h4>
                        <p class="text-white-50 mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link" href="admin_index.php">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                        <a class="nav-link" href="user_management.php">
                            <i class="fas fa-users"></i>
                            <span>User Management</span>
                        </a>
                        <a class="nav-link" href="approve_planner.php">
                            <i class="fas fa-user-check"></i>
                            <span>Planner Approvals</span>
                        </a>
                        <a class="nav-link active" href="event_management.php">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Event Management</span>
                        </a>
                        <a class="nav-link" href="analytics.php">
                            <i class="fas fa-chart-bar"></i>
                            <span>Analytics</span>
                        </a>
                        <a class="nav-link" href="settings.php">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                        <a class="nav-link" href="../logout.php">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 px-0">
                <div class="main-content">
                    <!-- Header -->
                    <div class="welcome-section">
                        <h1><i class="fas fa-calendar-alt me-3"></i>Event Management</h1>
                        <p>Manage and monitor all events in the system</p>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-2 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-primary">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="stat-value"><?php echo $stats['total_events']; ?></div>
                                <div class="stat-label">Total Events</div>
                            </div>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-warning">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-value"><?php echo $stats['pending_events']; ?></div>
                                <div class="stat-label">Pending</div>
                            </div>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-info">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stat-value"><?php echo $stats['confirmed_events']; ?></div>
                                <div class="stat-label">Confirmed</div>
                            </div>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-success">
                                    <i class="fas fa-flag-checkered"></i>
                                </div>
                                <div class="stat-value"><?php echo $stats['completed_events']; ?></div>
                                <div class="stat-label">Completed</div>
                            </div>
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-danger">
                                    <i class="fas fa-times-circle"></i>
                                </div>
                                <div class="stat-value"><?php echo $stats['cancelled_events']; ?></div>
                                <div class="stat-label">Cancelled</div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="filters-section mb-4">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="search" placeholder="Search events..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" name="status">
                                    <option value="">All Status</option>
                                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="in_progress" <?php echo $status_filter === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" name="event_type">
                                    <option value="">All Types</option>
                                    <option value="wedding" <?php echo $event_type_filter === 'wedding' ? 'selected' : ''; ?>>Wedding</option>
                                    <option value="birthday" <?php echo $event_type_filter === 'birthday' ? 'selected' : ''; ?>>Birthday</option>
                                    <option value="corporate" <?php echo $event_type_filter === 'corporate' ? 'selected' : ''; ?>>Corporate</option>
                                    <option value="anniversary" <?php echo $event_type_filter === 'anniversary' ? 'selected' : ''; ?>>Anniversary</option>
                                    <option value="conference" <?php echo $event_type_filter === 'conference' ? 'selected' : ''; ?>>Conference</option>
                                    <option value="other" <?php echo $event_type_filter === 'other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" name="date_filter">
                                    <option value="">All Dates</option>
                                    <option value="today" <?php echo $date_filter === 'today' ? 'selected' : ''; ?>>Today</option>
                                    <option value="week" <?php echo $date_filter === 'week' ? 'selected' : ''; ?>>This Week</option>
                                    <option value="month" <?php echo $date_filter === 'month' ? 'selected' : ''; ?>>This Month</option>
                                    <option value="upcoming" <?php echo $date_filter === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="event_management.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Events Table -->
                    <div class="table-container">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5><i class="fas fa-calendar me-2"></i>Events (<?php echo count($events); ?>)</h5>
                            <button class="btn btn-success" onclick="exportEvents()">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Event Details</th>
                                        <th>Client</th>
                                        <th>Planner</th>
                                        <th>Date & Time</th>
                                        <th>Status</th>
                                        <th>Budget</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($events)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No events found matching your criteria</p>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                        <?php foreach ($events as $event): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($event['event_type']); ?></small>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($event['venue']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($event['client_name']); ?></td>
                                            <td>
                                                <?php if ($event['planner_name']): ?>
                                                    <?php echo htmlspecialchars($event['planner_name']); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Unassigned</span>
                                                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="assignPlanner(<?php echo $event['id']; ?>)">
                                                        <i class="fas fa-plus"></i> Assign
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                                <br><small class="text-muted"><?php echo date('g:i A', strtotime($event['event_time'])); ?></small>
                                            </td>
                                            <td>
                                                <select class="form-select form-select-sm status-select" onchange="updateStatus(<?php echo $event['id']; ?>, this.value)">
                                                    <option value="pending" <?php echo $event['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="confirmed" <?php echo $event['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                    <option value="in_progress" <?php echo $event['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                                    <option value="completed" <?php echo $event['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                    <option value="cancelled" <?php echo $event['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                </select>
                                            </td>
                                            <td>$<?php echo number_format($event['budget']); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-info" onclick="viewEventDetails(<?php echo $event['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-warning" onclick="editEvent(<?php echo $event['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" onclick="deleteEvent(<?php echo $event['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Planner Modal -->
    <div class="modal fade" id="assignPlannerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Planner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="assignPlannerForm">
                        <input type="hidden" id="eventId" name="event_id">
                        <div class="mb-3">
                            <label for="plannerId" class="form-label">Select Planner</label>
                            <select class="form-select" id="plannerId" name="planner_id" required>
                                <option value="">Choose a planner...</option>
                                <?php foreach ($planners as $planner): ?>
                                <option value="<?php echo $planner['id']; ?>">
                                    <?php echo htmlspecialchars($planner['full_name']); ?>
                                    <?php if ($planner['company_name']): ?>
                                        (<?php echo htmlspecialchars($planner['company_name']); ?>)
                                    <?php endif; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmAssignPlanner()">Assign Planner</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            sidebar.classList.toggle('show');
        }

        function updateStatus(eventId, newStatus) {
            if (confirm('Are you sure you want to update the event status?')) {
                const formData = new FormData();
                formData.append('action', 'update_status');
                formData.append('event_id', eventId);
                formData.append('status', newStatus);

                fetch('event_management.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                    } else {
                        showAlert('danger', data.message);
                    }
                });
            }
        }

        function assignPlanner(eventId) {
            document.getElementById('eventId').value = eventId;
            new bootstrap.Modal(document.getElementById('assignPlannerModal')).show();
        }

        function confirmAssignPlanner() {
            const formData = new FormData();
            formData.append('action', 'assign_planner');
            formData.append('event_id', document.getElementById('eventId').value);
            formData.append('planner_id', document.getElementById('plannerId').value);

            fetch('event_management.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    bootstrap.Modal.getInstance(document.getElementById('assignPlannerModal')).hide();
                    location.reload();
                } else {
                    showAlert('danger', data.message);
                }
            });
        }

        function deleteEvent(eventId) {
            if (confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
                const formData = new FormData();
                formData.append('action', 'delete_event');
                formData.append('event_id', eventId);

                fetch('event_management.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message);
                        location.reload();
                    } else {
                        showAlert('danger', data.message);
                    }
                });
            }
        }

        function viewEventDetails(eventId) {
            // Implement event details view
            alert('Event details view - Coming soon!');
        }

        function editEvent(eventId) {
            // Implement event editing
            alert('Event editing - Coming soon!');
        }

        function exportEvents() {
            // Implement export functionality
            alert('Export functionality - Coming soon!');
        }

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }

        // Auto-hide sidebar on mobile when clicking outside
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('adminSidebar');
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            
            if (window.innerWidth <= 768 && 
                !sidebar.contains(e.target) && 
                !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    </script>
</body>
</html>
