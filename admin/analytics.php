<?php
session_start();
require_once '../includes/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get date range for analytics
$date_range = $_GET['date_range'] ?? '30';
$start_date = date('Y-m-d', strtotime("-$date_range days"));
$end_date = date('Y-m-d');

// Revenue Analytics
$revenue_data = [];
$result = $conn->query("
    SELECT DATE(booking_date) as date, SUM(amount) as daily_revenue, COUNT(*) as bookings
    FROM bookings 
    WHERE booking_date BETWEEN '$start_date' AND '$end_date' AND status = 'paid'
    GROUP BY DATE(booking_date)
    ORDER BY date
");
while ($row = $result->fetch_assoc()) {
    $revenue_data[] = $row;
}

// Event Type Distribution
$event_type_stats = [];
$result = $conn->query("
    SELECT event_type, COUNT(*) as count, AVG(budget) as avg_budget
    FROM events 
    WHERE event_date BETWEEN '$start_date' AND '$end_date'
    GROUP BY event_type
    ORDER BY count DESC
");
while ($row = $result->fetch_assoc()) {
    $event_type_stats[] = $row;
}

// Monthly Revenue Trend (Last 12 months)
$monthly_revenue = [];
for ($i = 11; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $result = $conn->query("
        SELECT COALESCE(SUM(amount), 0) as total, COUNT(*) as bookings
        FROM bookings 
        WHERE DATE_FORMAT(booking_date, '%Y-%m') = '$month' AND status = 'paid'
    ");
    $data = $result->fetch_assoc();
    $monthly_revenue[] = [
        'month' => date('M Y', strtotime("-$i months")),
        'revenue' => $data['total'],
        'bookings' => $data['bookings']
    ];
}

// User Growth
$user_growth = [];
$result = $conn->query("
    SELECT DATE(created_at) as date, COUNT(*) as new_users
    FROM users 
    WHERE created_at BETWEEN '$start_date' AND '$end_date'
    GROUP BY DATE(created_at)
    ORDER BY date
");
while ($row = $result->fetch_assoc()) {
    $user_growth[] = $row;
}

// Planner Performance
$planner_performance = [];
$result = $conn->query("
    SELECT u.full_name, p.company_name, 
           COUNT(e.id) as total_events,
           AVG(e.budget) as avg_budget,
           COUNT(CASE WHEN e.status = 'completed' THEN 1 END) as completed_events,
           AVG(r.rating) as avg_rating
    FROM users u
    LEFT JOIN planners p ON u.id = p.user_id
    LEFT JOIN events e ON u.id = e.planner_id
    LEFT JOIN reviews r ON u.id = r.planner_id
    WHERE u.user_type = 'planner' AND u.status = 'active'
    GROUP BY u.id
    ORDER BY total_events DESC
    LIMIT 10
");
while ($row = $result->fetch_assoc()) {
    $planner_performance[] = $row;
}

// Event Status Distribution
$event_status_stats = [];
$result = $conn->query("
    SELECT status, COUNT(*) as count
    FROM events 
    GROUP BY status
    ORDER BY count DESC
");
while ($row = $result->fetch_assoc()) {
    $event_status_stats[] = $row;
}

// Top Venues
$top_venues = [];
$result = $conn->query("
    SELECT venue, COUNT(*) as event_count, AVG(budget) as avg_budget
    FROM events 
    WHERE venue IS NOT NULL AND venue != ''
    GROUP BY venue
    ORDER BY event_count DESC
    LIMIT 10
");
while ($row = $result->fetch_assoc()) {
    $top_venues[] = $row;
}

// Calculate summary statistics
$total_revenue = array_sum(array_column($revenue_data, 'daily_revenue'));
$total_bookings = array_sum(array_column($revenue_data, 'bookings'));
$avg_revenue_per_day = $total_revenue / max(1, count($revenue_data));
$total_events = array_sum(array_column($event_type_stats, 'count'));
$avg_event_budget = array_sum(array_column($event_type_stats, 'avg_budget')) / max(1, count($event_type_stats));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Admin Dashboard</title>
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
                            <i class="fas fa-chart-bar fa-3x text-white-50"></i>
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
                        <a class="nav-link" href="event_management.php">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Event Management</span>
                        </a>
                        <a class="nav-link active" href="analytics.php">
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
                    <!-- Header -->
                    <div class="welcome-section">
                        <h1><i class="fas fa-chart-bar me-3"></i>Analytics Dashboard</h1>
                        <p>Comprehensive insights into your event management system</p>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="filters-section mb-4">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <select class="form-select" name="date_range" onchange="this.form.submit()">
                                    <option value="7" <?php echo $date_range == '7' ? 'selected' : ''; ?>>Last 7 Days</option>
                                    <option value="30" <?php echo $date_range == '30' ? 'selected' : ''; ?>>Last 30 Days</option>
                                    <option value="90" <?php echo $date_range == '90' ? 'selected' : ''; ?>>Last 90 Days</option>
                                    <option value="365" <?php echo $date_range == '365' ? 'selected' : ''; ?>>Last Year</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sync-alt"></i> Update
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Summary Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-primary">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="stat-value">₹<?php echo number_format($total_revenue); ?></div>
                                <div class="stat-label">Total Revenue</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-success">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="stat-value"><?php echo number_format($total_bookings); ?></div>
                                <div class="stat-label">Total Bookings</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-info">
                                    <i class="fas fa-calendar"></i>
                                </div>
                                <div class="stat-value"><?php echo number_format($total_events); ?></div>
                                <div class="stat-label">Total Events</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-warning">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="stat-value">₹<?php echo number_format($avg_revenue_per_day, 0); ?></div>
                                <div class="stat-label">Avg Daily Revenue</div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row 1 -->
                    <div class="row mb-4">
                        <div class="col-md-8 mb-4">
                            <div class="chart-container">
                                <h5><i class="fas fa-chart-line me-2"></i>Monthly Revenue Trend</h5>
                                <canvas id="monthlyRevenueChart" height="100"></canvas>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="chart-container">
                                <h5><i class="fas fa-chart-pie me-2"></i>Event Type Distribution</h5>
                                <canvas id="eventTypeChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row 2 -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-4">
                            <div class="chart-container">
                                <h5><i class="fas fa-chart-area me-2"></i>Daily Revenue</h5>
                                <canvas id="dailyRevenueChart" height="100"></canvas>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="chart-container">
                                <h5><i class="fas fa-users me-2"></i>User Growth</h5>
                                <canvas id="userGrowthChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row 3 -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-4">
                            <div class="chart-container">
                                <h5><i class="fas fa-chart-pie me-2"></i>Event Status Distribution</h5>
                                <canvas id="eventStatusChart" height="100"></canvas>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="chart-container">
                                <h5><i class="fas fa-chart-bar me-2"></i>Top Venues</h5>
                                <canvas id="topVenuesChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Planner Performance Table -->
                    <div class="table-container mb-4">
                        <h5><i class="fas fa-trophy me-2"></i>Top Performing Planners</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Planner</th>
                                        <th>Company</th>
                                        <th>Total Events</th>
                                        <th>Avg Budget</th>
                                        <th>Completed Events</th>
                                        <th>Avg Rating</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($planner_performance as $planner): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($planner['full_name']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($planner['company_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo $planner['total_events']; ?></td>
                                        <td>₹<?php echo number_format($planner['avg_budget'] ?? 0); ?></td>
                                        <td><?php echo $planner['completed_events']; ?></td>
                                        <td>
                                            <?php if ($planner['avg_rating']): ?>
                                                <span class="badge bg-success"><?php echo number_format($planner['avg_rating'], 1); ?>/5.0</span>
                                            <?php else: ?>
                                                <span class="text-muted">No ratings</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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

        // Monthly Revenue Chart
        const monthlyCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($monthly_revenue, 'month')); ?>,
                datasets: [{
                    label: 'Revenue (₹)',
                    data: <?php echo json_encode(array_column($monthly_revenue, 'revenue')); ?>,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Bookings',
                    data: <?php echo json_encode(array_column($monthly_revenue, 'bookings')); ?>,
                    borderColor: '#f093fb',
                    backgroundColor: 'rgba(240, 147, 251, 0.1)',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.4,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Revenue (₹)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Bookings'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });

        // Event Type Chart
        const eventTypeCtx = document.getElementById('eventTypeChart').getContext('2d');
        new Chart(eventTypeCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($event_type_stats, 'event_type')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($event_type_stats, 'count')); ?>,
                    backgroundColor: [
                        '#667eea', '#f093fb', '#4facfe', '#43e97b', '#38f9d7', '#fa709a'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Daily Revenue Chart
        const dailyCtx = document.getElementById('dailyRevenueChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($revenue_data, 'date')); ?>,
                datasets: [{
                    label: 'Daily Revenue (₹)',
                    data: <?php echo json_encode(array_column($revenue_data, 'daily_revenue')); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // User Growth Chart
        const userCtx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(userCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($user_growth, 'date')); ?>,
                datasets: [{
                    label: 'New Users',
                    data: <?php echo json_encode(array_column($user_growth, 'new_users')); ?>,
                    borderColor: '#4facfe',
                    backgroundColor: 'rgba(79, 172, 254, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Event Status Chart
        const statusCtx = document.getElementById('eventStatusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_column($event_status_stats, 'status')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($event_status_stats, 'count')); ?>,
                    backgroundColor: [
                        '#ffc107', '#17a2b8', '#28a745', '#6c757d', '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Top Venues Chart
        const venuesCtx = document.getElementById('topVenuesChart').getContext('2d');
        new Chart(venuesCtx, {
            type: 'horizontalBar',
            data: {
                labels: <?php echo json_encode(array_slice(array_column($top_venues, 'venue'), 0, 8)); ?>,
                datasets: [{
                    label: 'Event Count',
                    data: <?php echo json_encode(array_slice(array_column($top_venues, 'event_count'), 0, 8)); ?>,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });

        
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
