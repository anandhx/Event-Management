<?php
session_start();
require_once '../includes/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get real statistics from database
$stats = [];

// Total users
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'client'");
$stats['total_users'] = $result->fetch_assoc()['count'];

// Total events
$result = $conn->query("SELECT COUNT(*) as count FROM events");
$stats['total_events'] = $result->fetch_assoc()['count'];

// Total planners
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'planner'");
$stats['total_planners'] = $result->fetch_assoc()['count'];

// Pending approvals (planners with pending status)
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'planner' AND status = 'pending'");
$stats['pending_approvals'] = $result->fetch_assoc()['count'];

// Revenue this month (from bookings)
$result = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM bookings WHERE MONTH(booking_date) = MONTH(CURRENT_DATE()) AND YEAR(booking_date) = YEAR(CURRENT_DATE()) AND status = 'paid'");
$stats['revenue_month'] = $result->fetch_assoc()['total'];

// Events this month
$result = $conn->query("SELECT COUNT(*) as count FROM events WHERE MONTH(event_date) = MONTH(CURRENT_DATE()) AND YEAR(event_date) = YEAR(CURRENT_DATE())");
$stats['events_this_month'] = $result->fetch_assoc()['count'];

// Active planners
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'planner' AND status = 'active'");
$stats['active_planners'] = $result->fetch_assoc()['count'];

// Completed events
$result = $conn->query("SELECT COUNT(*) as count FROM events WHERE status = 'completed'");
$stats['completed_events'] = $result->fetch_assoc()['count'];

// Get recent events
$recent_events = [];
$result = $conn->query("
    SELECT e.*, u1.full_name as client_name, u2.full_name as planner_name 
    FROM events e 
    JOIN users u1 ON e.client_id = u1.id 
    LEFT JOIN users u2 ON e.planner_id = u2.id 
    ORDER BY e.created_at DESC 
    LIMIT 5
");
while ($row = $result->fetch_assoc()) {
    $recent_events[] = $row;
}

// Get pending planner approvals
$pending_approvals = [];
$result = $conn->query("
    SELECT u.*, p.company_name, p.specialization, p.experience_years 
    FROM users u 
    LEFT JOIN planners p ON u.id = p.user_id 
    WHERE u.user_type = 'planner' AND u.status = 'pending' 
    ORDER BY u.created_at DESC
");
while ($row = $result->fetch_assoc()) {
    $pending_approvals[] = $row;
}

// Get unread notifications count
$notifications_count = getUnreadNotificationsCount($conn, $_SESSION['user_id']);

// Get monthly revenue data for chart
$monthly_revenue = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $result = $conn->query("SELECT COALESCE(SUM(amount), 0) as total FROM bookings WHERE DATE_FORMAT(booking_date, '%Y-%m') = '$month' AND status = 'paid'");
    $monthly_revenue[] = [
        'month' => date('M Y', strtotime("-$i months")),
        'revenue' => $result->fetch_assoc()['total']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Event Management System</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/admin-style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <div class="text-center mb-5">
                        <div class="mb-3">
                            <i class="fas fa-calendar-check fa-3x text-white-50"></i>
                        </div>
                        <h4 class="text-white fw-bold">EMS Admin</h4>
                        <p class="text-white-50 mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="admin_index.php">
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
                    <!-- Welcome Section -->
                    <div class="welcome-section">
                        <h1><i class="fas fa-tachometer-alt me-3"></i>Admin Dashboard</h1>
                        <p>Monitor and manage your event management system from one central location</p>
                    </div>

                    <!-- Quick Actions -->
                    <div class="quick-actions">
                        <div class="quick-action-card" onclick="location.href='user_management.php'">
                            <div class="quick-action-icon bg-primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5>Manage Users</h5>
                            <p class="text-muted mb-0">View and manage all users</p>
                        </div>
                        
                        <div class="quick-action-card" onclick="location.href='approve_planner.php'">
                            <div class="quick-action-icon bg-warning">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <h5>Approve Planners</h5>
                            <p class="text-muted mb-0">Review planner applications</p>
                        </div>
                        
                        <div class="quick-action-card" onclick="location.href='event_management.php'">
                            <div class="quick-action-icon bg-success">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h5>Manage Events</h5>
                            <p class="text-muted mb-0">Oversee all events</p>
                        </div>
                        
                        <div class="quick-action-card" onclick="location.href='analytics.php'">
                            <div class="quick-action-icon bg-info">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h5>View Analytics</h5>
                            <p class="text-muted mb-0">System performance insights</p>
                        </div>
                    </div>
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-primary">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-value"><?php echo number_format($stats['total_users']); ?></div>
                                <div class="stat-label">Total Users</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-success">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="stat-value"><?php echo number_format($stats['total_events']); ?></div>
                                <div class="stat-label">Total Events</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-info">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="stat-value"><?php echo number_format($stats['total_planners']); ?></div>
                                <div class="stat-label">Event Planners</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-warning">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="stat-value">$<?php echo number_format($stats['revenue_month']); ?></div>
                                <div class="stat-label">Monthly Revenue</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Stats Row -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-success">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="stat-value"><?php echo $stats['events_this_month']; ?></div>
                                <div class="stat-label">Events This Month</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-info">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div class="stat-value"><?php echo $stats['active_planners']; ?></div>
                                <div class="stat-label">Active Planners</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-primary">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stat-value"><?php echo $stats['completed_events']; ?></div>
                                <div class="stat-label">Completed Events</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-warning">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-value"><?php echo $stats['pending_approvals']; ?></div>
                                <div class="stat-label">Pending Approvals</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Revenue Chart -->
                    <div class="chart-container">
                        <h5><i class="fas fa-chart-line me-2"></i>Monthly Revenue Trend</h5>
                        <canvas id="revenueChart"></canvas>
                    </div>
                    
                    <!-- Recent Events and Pending Approvals -->
                    <div class="row">
                        <div class="col-md-8 mb-4">
                            <div class="table-container">
                                <h5><i class="fas fa-calendar me-2"></i>Recent Events</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Event</th>
                                                <th>Client</th>
                                                <th>Planner</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Budget</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recent_events as $event): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($event['event_type']); ?></small>
                                                </td>
                                                <td><?php echo htmlspecialchars($event['client_name']); ?></td>
                                                <td><?php echo $event['planner_name'] ? htmlspecialchars($event['planner_name']) : '<span class="text-muted">Unassigned</span>'; ?></td>
                                                <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $event['status'] == 'confirmed' ? 'success' : 
                                                            ($event['status'] == 'pending' ? 'warning' : 
                                                            ($event['status'] == 'completed' ? 'info' : 'secondary')); 
                                                    ?>">
                                                        <?php echo ucfirst(str_replace('_', ' ', $event['status'])); ?>
                                                    </span>
                                                </td>
                                                <td>$<?php echo number_format($event['budget']); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="table-container">
                                <h5><i class="fas fa-clock me-2"></i>Pending Approvals</h5>
                                <?php if (empty($pending_approvals)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                        <p class="text-muted">No pending approvals</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($pending_approvals as $planner): ?>
                                    <div class="pending-approval-item">
                                        <h6 class="mb-2 fw-bold"><?php echo htmlspecialchars($planner['full_name']); ?></h6>
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-building me-1"></i>
                                            <?php echo htmlspecialchars($planner['company_name'] ?? 'N/A'); ?>
                                        </p>
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-star me-1"></i>
                                            <?php echo $planner['experience_years']; ?> years experience
                                        </p>
                                        <p class="text-muted small mb-3">
                                            <i class="fas fa-tags me-1"></i>
                                            <?php echo htmlspecialchars($planner['specialization'] ?? 'N/A'); ?>
                                        </p>
                                        <div class="btn-group btn-group-sm w-100">
                                            <button class="btn btn-success" onclick="approvePlanner(<?php echo $planner['id']; ?>)">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button class="btn btn-danger" onclick="rejectPlanner(<?php echo $planner['id']; ?>)">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
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

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($monthly_revenue, 'month')); ?>,
                datasets: [{
                    label: 'Monthly Revenue ($)',
                    data: <?php echo json_encode(array_column($monthly_revenue, 'revenue')); ?>,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            sidebar.classList.toggle('show');
        }

        function approvePlanner(userId) {
            if (confirm('Are you sure you want to approve this planner?')) {
                fetch('approve_planner.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        action: 'approve'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }
        
        function rejectPlanner(userId) {
            if (confirm('Are you sure you want to reject this planner?')) {
                fetch('approve_planner.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        action: 'reject'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
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







            