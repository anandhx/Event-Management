<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';
require_once '../includes/mailer.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
	if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
		header('Content-Type: application/json');
		echo json_encode(['success' => false, 'message' => 'Unauthorized']);
		exit();
	}
	header('Location: ../login.php');
	exit();
}

$success_message = '';
$error_message = '';

// Normalize input (supports JSON and form POST)
$input = null;
if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
	$raw = file_get_contents('php://input');
	$input = json_decode($raw, true) ?: [];
}
$action = $input['action'] ?? ($_POST['action'] ?? '');
$user_id = (int)($input['user_id'] ?? ($_POST['user_id'] ?? 0));

function ems_send_planner_status_email($conn, $plannerUserId, $status) {
	$stmt = $conn->prepare("SELECT email, full_name FROM users WHERE id = ? AND user_type = 'planner'");
	$stmt->bind_param("i", $plannerUserId);
	$stmt->execute();
	$res = $stmt->get_result();
	$user = $res->fetch_assoc();
	if (!$user) { return [false, 'Planner user not found']; }

	$toEmail = $user['email'];
	$toName = $user['full_name'];
	if ($status === 'approved') {
		$subject = 'Your Planner Account Has Been Approved';
		$html = '<p>Hi ' . htmlspecialchars($toName) . ',</p>' .
				'<p>Your planner account has been <strong>approved</strong>. You can now log in and start managing events.</p>' .
				'<p>Regards,<br>EMS Admin</p>';
	} elseif ($status === 'rejected') {
		$subject = 'Your Planner Application Status';
		$html = '<p>Hi ' . htmlspecialchars($toName) . ',</p>' .
				'<p>Your planner application has been <strong>rejected</strong>. If you believe this is a mistake or need more information, please contact support.</p>' .
				'<p>Regards,<br>EMS Admin</p>';
	} else {
		return [false, 'Unknown status'];
	}
	return ems_send_email($toEmail, $toName, $subject, $html);
}

// Handle planner approval actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' || ($input !== null && is_array($input))) {
	switch($action) {
		case 'approve_planner':
		case 'approve':
			if (!empty($user_id)) {
				// Update user status to active
				$stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ? AND user_type = 'planner'");
				$stmt->bind_param("i", $user_id);
				if ($stmt->execute()) {
					// Update planner approval status
					$stmt2 = $conn->prepare("UPDATE planners SET approval_status = 'approved' WHERE user_id = ?");
					$stmt2->bind_param("i", $user_id);
					$stmt2->execute();

					list($sent, $err) = ems_send_planner_status_email($conn, $user_id, 'approved');
					$success_message = 'Planner approved successfully!' . ($sent ? '' : ' (email not sent: ' . htmlspecialchars($err) . ')');
				} else {
					$error_message = 'Failed to approve planner.';
				}
			}
			break;
		case 'reject_planner':
		case 'reject':
			if (!empty($user_id)) {
				// Update user status to rejected
				$stmt = $conn->prepare("UPDATE users SET status = 'inactive' WHERE id = ? AND user_type = 'planner'");
				$stmt->bind_param("i", $user_id);
				if ($stmt->execute()) {
					// Update planner approval status
					$stmt2 = $conn->prepare("UPDATE planners SET approval_status = 'rejected' WHERE user_id = ?");
					$stmt2->bind_param("i", $user_id);
					$stmt2->execute();

					list($sent, $err) = ems_send_planner_status_email($conn, $user_id, 'rejected');
					$success_message = 'Planner rejected successfully!' . ($sent ? '' : ' (email not sent: ' . htmlspecialchars($err) . ')');
				} else {
					$error_message = 'Failed to reject planner.';
				}
			}
			break;
		case 'delete_planner':
			if (!empty($user_id)) {
				// Delete planner record first
				$stmt = $conn->prepare("DELETE FROM planners WHERE user_id = ?");
				$stmt->bind_param("i", $user_id);
				$stmt->execute();
				// Delete user
				$stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND user_type = 'planner'");
				$stmt->bind_param("i", $user_id);
				if ($stmt->execute()) {
					$success_message = 'Planner application deleted successfully!';
				} else {
					$error_message = 'Failed to delete planner application.';
				}
			}
			break;
	}

	// JSON response for AJAX
	if ($input !== null && isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
		header('Content-Type: application/json');
		echo json_encode([
			'success' => empty($error_message),
			'message' => empty($error_message) ? $success_message : $error_message
		]);
		exit();
	}
}

// Fetch pending planners from database
$pending_planners = [];
$query = "SELECT u.*, p.company_name, p.specialization, p.experience_years, p.hourly_rate, p.approval_status
		  FROM users u 
		  JOIN planners p ON u.id = p.user_id 
		  WHERE p.approval_status = 'pending' AND u.user_type = 'planner'
		  ORDER BY u.created_at ASC";

$result = $conn->query($query);

if ($result) {
	while ($row = $result->fetch_assoc()) {
		$pending_planners[] = $row;
	}
}

// Fetch all planners for overview
$all_planners = [];
$query = "SELECT u.*, p.company_name, p.specialization, p.experience_years, p.hourly_rate, p.approval_status
		  FROM users u 
		  JOIN planners p ON u.id = p.user_id 
		  WHERE u.user_type = 'planner'
		  ORDER BY u.created_at DESC";

$result = $conn->query($query);

if ($result) {
	while ($row = $result->fetch_assoc()) {
		$all_planners[] = $row;
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Planners - Admin Dashboard</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/admin-style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="px-0">
                <div class="admin-sidebar p-4" id="adminSidebar">
                    <div class="sidebar-header">
                        <div class="mb-3">
                            <i class="fas fa-calendar-check fa-3x text-white-50"></i>
                        </div>
                        <h4>EMS Admin</h4>
                        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?></p>
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
                        <a class="nav-link active" href="approve_planner.php">
                            <i class="fas fa-user-check"></i>
                            <span>Planner Approvals</span>
                        </a>
                        <a class="nav-link" href="event_management.php">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Event Management</span>
                        </a>
                        <a class="nav-link" href="analytics.php">
                            <i class="fas fa-chart-bar"></i>
                            <span>Analytics</span>
                        </a>
                        <a class="nav-link" href="contact_messages.php">
                            <i class="fas fa-envelope-open-text"></i>
                            <span>Contact Messages</span>
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
            <div class="col-12 px-0">
                <div class="main-content">
                    <!-- Page Header -->
                    <div class="page-header">
                        <h1>
                            <i class="fas fa-user-check"></i>
                            Planner Approval System
                        </h1>
                        <p>Review and manage planner applications for your event management system</p>
                    </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h4><?php echo count(array_filter($all_planners, function($p) { return $p['approval_status'] === 'pending'; })); ?></h4>
                            <p class="mb-0">Pending Approval</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h4><?php echo count(array_filter($all_planners, function($p) { return $p['approval_status'] === 'approved'; })); ?></h4>
                            <p class="mb-0">Approved Planners</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h4><?php echo count(array_filter($all_planners, function($p) { return $p['approval_status'] === 'rejected'; })); ?></h4>
                            <p class="mb-0">Rejected Planners</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <h4><?php echo count($all_planners); ?></h4>
                            <p class="mb-0">Total Planners</p>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Pending Planners Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h3 class="mb-3">
                            <i class="fas fa-clock me-2 text-warning"></i>Pending Approval
                        </h3>
                        
                        <?php if (empty($pending_planners)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>No pending planner applications at the moment.
                            </div>
                        <?php else: ?>
                            <?php foreach ($pending_planners as $planner): ?>
                                <div class="planner-card pending-card p-4">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center mb-3">
                                                <img src="../assets/img/team-<?php echo ($planner['id'] % 4) + 1; ?>.jpg" 
                                                     alt="Profile" class="rounded-circle me-3" width="60" height="60">
                                                <div>
                                                    <h5 class="mb-1"><?php echo htmlspecialchars($planner['full_name']); ?></h5>
                                                    <p class="mb-1 text-muted">@<?php echo htmlspecialchars($planner['username']); ?></p>
                                                    <span class="badge bg-warning">Pending Approval</span>
                                                </div>
                                            </div>
                                            
                                            <div class="planner-details">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p><strong>Company:</strong> <?php echo htmlspecialchars($planner['company_name']); ?></p>
                                                        <p><strong>Specialization:</strong> <?php echo htmlspecialchars($planner['specialization']); ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p><strong>Experience:</strong> <?php echo $planner['experience_years']; ?> years</p>
                                                        <p><strong>Hourly Rate:</strong> $<?php echo number_format($planner['hourly_rate'], 2); ?></p>
                                                    </div>
                                                </div>
                                                <p><strong>Email:</strong> <?php echo htmlspecialchars($planner['email']); ?></p>
                                                <?php if ($planner['phone']): ?>
                                                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($planner['phone']); ?></p>
                                                <?php endif; ?>
                                                <p><strong>Applied:</strong> <?php echo date('F j, Y', strtotime($planner['created_at'])); ?></p>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="action-buttons">
                                                <form method="POST" class="d-grid">
                                                    <input type="hidden" name="action" value="approve_planner">
                                                    <input type="hidden" name="user_id" value="<?php echo $planner['id']; ?>">
                                                    <button type="submit" class="btn btn-success btn-lg mb-2" onclick="return confirm('Approve this planner? They will be able to login immediately.')">
                                                        <i class="fas fa-check me-2"></i>Approve
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" class="d-grid">
                                                    <input type="hidden" name="action" value="reject_planner">
                                                    <input type="hidden" name="user_id" value="<?php echo $planner['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-lg mb-2" onclick="return confirm('Reject this planner? This will prevent them from accessing the system.')">
                                                        <i class="fas fa-times me-2"></i>Reject
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" class="d-grid" onsubmit="return confirm('Delete this application? This action cannot be undone.')">
                                                    <input type="hidden" name="action" value="delete_planner">
                                                    <input type="hidden" name="user_id" value="<?php echo $planner['id']; ?>">
                                                  
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- All Planners Overview -->
                <div class="row">
                    <div class="col-12">
                        <h3 class="mb-3">
                            <i class="fas fa-list me-2 text-primary"></i>All Planners Overview
                        </h3>
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Company</th>
                                        <th>Specialization</th>
                                        <th>Experience</th>
                                        <th>Rate</th>
                                        <th>Status</th>
                                        <th>Applied</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($all_planners as $planner): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="../assets/img/team-<?php echo ($planner['id'] % 4) + 1; ?>.jpg" 
                                                         alt="Profile" class="rounded-circle me-2" width="30" height="30">
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($planner['full_name']); ?></strong><br>
                                                        <small class="text-muted">@<?php echo htmlspecialchars($planner['username']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($planner['company_name']); ?></td>
                                            <td><?php echo htmlspecialchars($planner['specialization']); ?></td>
                                            <td><?php echo $planner['experience_years']; ?> years</td>
                                            <td>$<?php echo number_format($planner['hourly_rate'], 2); ?></td>
                                            <td>
                                                <?php
                                                $status_class = '';
                                                switch($planner['approval_status']) {
                                                    case 'approved': $status_class = 'bg-success'; break;
                                                    case 'pending': $status_class = 'bg-warning'; break;
                                                    case 'rejected': $status_class = 'bg-danger'; break;
                                                    default: $status_class = 'bg-secondary';
                                                }
                                                ?>
                                                <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($planner['approval_status']); ?></span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($planner['created_at'])); ?></td>
                                            <td>
                                                <?php if ($planner['approval_status'] === 'pending'): ?>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="approve_planner">
                                                        <input type="hidden" name="user_id" value="<?php echo $planner['id']; ?>">
                                                        <button type="submit" class="btn btn-success btn-sm" title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" style="display: inline;">
                                                        <input type="hidden" name="action" value="reject_planner">
                                                        <input type="hidden" name="user_id" value="<?php echo $planner['id']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Reject">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this planner?')">
                                                    <input type="hidden" name="action" value="delete_planner">
                                                    <input type="hidden" name="user_id" value="<?php echo $planner['id']; ?>">
                                                 
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (empty($all_planners)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No planners found</h5>
                                <p class="text-muted">Planner applications will appear here once they are submitted.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            sidebar.classList.toggle('show');
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