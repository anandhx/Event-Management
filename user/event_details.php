<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'client') {
	header('Location: ../login.php');
	exit();
}

$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($eventId <= 0) {
	header('Location: my_events.php');
	exit();
}

$stmt = $conn->prepare("SELECT e.*, 
	u_client.full_name AS client_name,
	u_planner.full_name AS planner_name,
	u_planner.email AS planner_email,
	u_planner.phone AS planner_phone,
	ec.category_name
	FROM events e
	JOIN users u_client ON e.client_id = u_client.id
	LEFT JOIN users u_planner ON e.planner_id = u_planner.id
	LEFT JOIN event_categories ec ON e.category_id = ec.id
	WHERE e.id = ? AND e.client_id = ?");
$stmt->bind_param('ii', $eventId, $_SESSION['user_id']);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
	header('Location: my_events.php');
	exit();
}

// Fetch gallery images
$galleryStmt = $conn->prepare("SELECT image_path, caption FROM event_gallery WHERE event_id = ? ORDER BY uploaded_at DESC");
$galleryStmt->bind_param('i', $eventId);
$galleryStmt->execute();
$gallery = $galleryStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch tasks
$tasksStmt = $conn->prepare("SELECT task_name, description, due_date, status, priority FROM event_tasks WHERE event_id = ? ORDER BY due_date IS NULL, due_date");
$tasksStmt->bind_param('i', $eventId);
$tasksStmt->execute();
$tasks = $tasksStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Event Details</title>
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
						<h4>My Events</h4>
						<p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?></p>
					</div>
					<nav class="nav flex-column">
						<a class="nav-link" href="user_index.php"><i class="fas fa-home"></i><span>Dashboard</span></a>
						<a class="nav-link active" href="my_events.php"><i class="fas fa-calendar"></i><span>My Events</span></a>
						<a class="nav-link" href="notifications.php"><i class="fas fa-bell"></i><span>Notifications</span></a>
						<a class="nav-link" href="messages.php"><i class="fas fa-comments"></i><span>Messages</span></a>
						<a class="nav-link" href="profile_management.php"><i class="fas fa-user"></i><span>Profile</span></a>
						<a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
					</nav>
				</div>
			</div>
			<!-- Main Content -->
			<div class="col-12 px-0">
				<div class="main-content">
					<div class="page-header">
						<h1><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($event['title']); ?></h1>
						<p class="mb-0">Event details and timeline</p>
					</div>

					<div class="row mb-4">
						<div class="col-md-8 mb-4">
							<div class="table-container">
								<h5><i class="fas fa-info-circle me-2"></i>Overview</h5>
								<div class="row">
									<div class="col-md-6">
										<p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($event['event_date'])); ?></p>
										<p><strong>Time:</strong> <?php echo date('g:i A', strtotime($event['event_time'])); ?></p>
										<p><strong>Type:</strong> <?php echo htmlspecialchars($event['event_type']); ?></p>
										<p><strong>Category:</strong> <?php echo htmlspecialchars($event['category_name'] ?? 'N/A'); ?></p>
									</div>
									<div class="col-md-6">
										<p><strong>Status:</strong> <span class="badge bg-secondary"><?php echo ucfirst(str_replace('_',' ',$event['status'])); ?></span></p>
										<p><strong>Budget:</strong> $<?php echo number_format($event['budget'] ?? 0); ?></p>
										<p><strong>Guests:</strong> <?php echo (int)($event['guest_count'] ?? 0); ?></p>
										<p><strong>Duration:</strong> <?php echo (int)($event['duration'] ?? 0); ?> hours</p>
									</div>
								</div>
								<hr>
								<p><strong>Venue:</strong> <?php echo htmlspecialchars($event['venue'] ?: ''); ?></p>
								<p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($event['venue_address'] ?: '')); ?></p>
								<p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($event['description'] ?: '')); ?></p>
								<?php if (!empty($event['special_requirements'])): ?>
									<p><strong>Special Requirements:</strong><br><?php echo nl2br(htmlspecialchars($event['special_requirements'])); ?></p>
								<?php endif; ?>
							</div>
						</div>
						<div class="col-md-4 mb-4">
							<div class="table-container">
								<h5><i class="fas fa-user-tie me-2"></i>Planner</h5>
								<?php if ($event['planner_id']): ?>
									<p><strong><?php echo htmlspecialchars($event['planner_name']); ?></strong></p>
									<p class="mb-1"><i class="fas fa-envelope me-2 text-muted"></i><?php echo htmlspecialchars($event['planner_email'] ?? ''); ?></p>
									<p><i class="fas fa-phone me-2 text-muted"></i><?php echo htmlspecialchars($event['planner_phone'] ?? ''); ?></p>
									<a href="messages.php?with=<?php echo (int)$event['planner_id']; ?>&event_id=<?php echo $eventId; ?>" class="btn btn-primary btn-sm">
										<i class="fas fa-comments me-1"></i> Message Planner
									</a>
								<?php else: ?>
									<p class="text-muted">No planner assigned yet.</p>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="row mb-4">
						<div class="col-md-8 mb-4">
							<div class="table-container">
								<h5><i class="fas fa-list-check me-2"></i>Tasks</h5>
								<?php if (empty($tasks)): ?>
									<p class="text-muted mb-0">No tasks added yet.</p>
								<?php else: ?>
									<div class="table-responsive">
										<table class="table table-sm">
											<thead>
												<tr>
													<th>Task</th>
													<th>Due</th>
													<th>Status</th>
													<th>Priority</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach ($tasks as $t): ?>
												<tr>
													<td>
														<strong><?php echo htmlspecialchars($t['task_name']); ?></strong>
														<?php if (!empty($t['description'])): ?>
															<br><small class="text-muted"><?php echo htmlspecialchars($t['description']); ?></small>
														<?php endif; ?>
													</td>
													<td><?php echo $t['due_date'] ? date('M d, Y', strtotime($t['due_date'])) : '-'; ?></td>
													<td><?php echo ucfirst(str_replace('_',' ',$t['status'])); ?></td>
													<td><?php echo ucfirst($t['priority']); ?></td>
												</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
								<?php endif; ?>
							</div>
						</div>
						<div class="col-md-4 mb-4">
							<div class="table-container">
								<h5><i class="fas fa-image me-2"></i>Gallery</h5>
								<?php if (empty($gallery)): ?>
									<p class="text-muted mb-0">No images uploaded yet.</p>
								<?php else: ?>
									<div class="row g-2">
										<?php foreach ($gallery as $img): ?>
										<div class="col-6">
											<img src="<?php echo htmlspecialchars($img['image_path']); ?>" alt="" class="img-fluid rounded">
											<?php if (!empty($img['caption'])): ?>
												<small class="text-muted d-block mt-1"><?php echo htmlspecialchars($img['caption']); ?></small>
											<?php endif; ?>
										</div>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="../assets/js/bootstrap.bundle.min.js"></script>
	<script>
		function toggleSidebar() {
			const sidebar = document.getElementById('adminSidebar');
			sidebar.classList.toggle('show');
		}
		// Auto-hide sidebar on mobile when clicking outside
		document.addEventListener('click', function(e) {
			const sidebar = document.getElementById('adminSidebar');
			const sidebarToggle = document.querySelector('.sidebar-toggle');
			if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
				sidebar.classList.remove('show');
			}
		});
	</script>
</body>
</html> 