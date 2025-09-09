<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
	header('Location: ../login.php');
	exit();
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$id = (int)($_POST['id'] ?? 0);
	$action = $_POST['action'] ?? '';
	if ($id > 0 && in_array($action, ['mark_read','close'], true)) {
		$status = $action === 'mark_read' ? 'read' : 'closed';
		$upd = $conn->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
		$upd->bind_param('si', $status, $id);
		$upd->execute();
		$_SESSION['success_message'] = 'Message updated.';
		header('Location: contact_messages.php');
		exit();
	}
}

// Fetch messages
$filter = $_GET['status'] ?? '';
if ($filter && !in_array($filter, ['new','read','closed'], true)) { $filter = ''; }
if ($filter) {
	$stmt = $conn->prepare("SELECT cm.*, u.username FROM contact_messages cm LEFT JOIN users u ON cm.user_id = u.id WHERE cm.status = ? ORDER BY cm.created_at DESC");
	$stmt->bind_param('s', $filter);
} else {
	$stmt = $conn->prepare("SELECT cm.*, u.username FROM contact_messages cm LEFT JOIN users u ON cm.user_id = u.id ORDER BY cm.created_at DESC");
}
$stmt->execute();
$messages = $stmt->get_result();

$success = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Contact Messages - Admin</title>
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
			<div class="px-0">
				<div class="admin-sidebar p-4" id="adminSidebar">
					<div class="text-center mb-5">
						<div class="mb-3">
							<i class="fas fa-inbox fa-3x text-white-50"></i>
						</div>
						<h4 class="text-white fw-bold">EMS Admin</h4>
						<p class="text-white-50 mb-0">Contact Messages</p>
					</div>
					<nav class="nav flex-column">
						<a class="nav-link" href="admin_index.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
						<a class="nav-link" href="user_management.php"><i class="fas fa-users"></i><span>User Management</span></a>
						<a class="nav-link" href="approve_planner.php"><i class="fas fa-user-check"></i><span>Planner Approvals</span></a>
						<a class="nav-link" href="event_management.php"><i class="fas fa-calendar-alt"></i><span>Event Management</span></a>
						<a class="nav-link" href="analytics.php"><i class="fas fa-chart-bar"></i><span>Analytics</span></a>
						<a class="nav-link" href="settings.php"><i class="fas fa-cog"></i><span>Settings</span></a>
						<a class="nav-link active" href="contact_messages.php"><i class="fas fa-envelope-open-text"></i><span>Contact Messages</span></a>
						<a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
					</nav>
				</div>
			</div>
			<div class="col-12 px-0">
				<div class="main-content">
					<div class="welcome-section">
						<h1><i class="fas fa-envelope-open-text me-2"></i>Contact Messages</h1>
						<p>View and manage user contact submissions</p>
					</div>

					<?php if ($success): ?>
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						<?php echo htmlspecialchars($success); ?>
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
					<?php endif; ?>

					<div class="card">
						<div class="card-header d-flex justify-content-between align-items-center">
							<h5 class="mb-0">Messages</h5>
							<div>
								<a href="?" class="btn btn-sm btn-outline-secondary">All</a>
								<a href="?status=new" class="btn btn-sm btn-outline-primary">New</a>
								<a href="?status=read" class="btn btn-sm btn-outline-info">Read</a>
								<a href="?status=closed" class="btn btn-sm btn-outline-success">Closed</a>
							</div>
						</div>
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-striped align-middle">
									<thead>
										<tr>
											<th>#</th>
											<th>Submitted</th>
											<th>Name / Email</th>
											<th>Subject</th>
											<th>Message</th>
											<th>Status</th>
											<th>Actions</th>
										</tr>
									</thead>
									<tbody>
									<?php if ($messages->num_rows === 0): ?>
										<tr><td colspan="7" class="text-center text-muted">No messages found.</td></tr>
									<?php else: ?>
										<?php while ($row = $messages->fetch_assoc()): ?>
										<tr>
											<td><?php echo (int)$row['id']; ?></td>
											<td><small><?php echo htmlspecialchars($row['created_at']); ?></small></td>
											<td>
												<div><?php echo htmlspecialchars($row['name']); ?><?php echo $row['username'] ? ' ('.$row['username'].')' : ''; ?></div>
												<small class="text-muted"><?php echo htmlspecialchars($row['email']); ?></small>
											</td>
											<td><?php echo htmlspecialchars($row['subject']); ?></td>
											<td style="max-width:380px"><div class="text-truncate" title="<?php echo htmlspecialchars($row['message']); ?>"><?php echo htmlspecialchars($row['message']); ?></div></td>
											<td>
												<span class="badge bg-<?php echo $row['status']==='new'?'primary':($row['status']==='read'?'info':'success'); ?>"><?php echo htmlspecialchars($row['status']); ?></span>
											</td>
											<td>
												<form method="post" class="d-inline">
													<input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
													<button name="action" value="mark_read" class="btn btn-sm btn-outline-info" <?php echo $row['status']!=='new'?'disabled':''; ?>>Mark Read</button>
												</form>
												<form method="post" class="d-inline">
													<input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
													<button name="action" value="close" class="btn btn-sm btn-outline-success" <?php echo $row['status']==='closed'?'disabled':''; ?>>Close</button>
												</form>
											</td>
										</tr>
										<?php endwhile; ?>
									<?php endif; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="../assets/js/bootstrap.bundle.min.js"></script>
	<script>
	function toggleSidebar(){document.getElementById('adminSidebar').classList.toggle('show');}
	</script>
</body>
</html>
