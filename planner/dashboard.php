<?php
session_start();
include '../includes/db.php';

// Check if user is logged in and is an event planner (simulated)
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'event_planner') {
    header("Location: ../index.php");
    exit();
}

$planner_id = $_SESSION['user_id'];

// Dummy planner data for showcase
// In real implementation, this would fetch from database
$planner = [
    'planner_id' => 1,
    'company_name' => 'Elegant Events Co.',
    'expertise' => 'Weddings, Corporate Events',
    'experience_years' => 8,
    'location' => 'New York',
    'bio' => 'Professional event planner with 8+ years of experience'
];

// Dummy assigned events data
$events_result = [
    [
        'event_id' => 1,
        'event_title' => 'Sarah & Mike Wedding',
        'category_name' => 'Wedding',
        'client_name' => 'Sarah Johnson',
        'event_date' => '2024-06-15',
        'status' => 'in_progress',
        'budget' => 25000,
        'location' => 'Grand Hotel, Downtown'
    ],
    [
        'event_id' => 2,
        'event_title' => 'Tech Conference 2024',
        'category_name' => 'Corporate Event',
        'client_name' => 'John Smith',
        'event_date' => '2024-05-20',
        'status' => 'completed',
        'budget' => 15000,
        'location' => 'Convention Center'
    ],
    [
        'event_id' => 3,
        'event_title' => 'Birthday Party',
        'category_name' => 'Birthday Party',
        'client_name' => 'Mike Wilson',
        'event_date' => '2024-07-10',
        'status' => 'pending',
        'budget' => 5000,
        'location' => 'Community Hall'
    ]
];

// Dummy pending events data
$pending_result = [
    [
        'event_id' => 4,
        'event_title' => 'Corporate Meeting',
        'category_name' => 'Corporate Event',
        'client_name' => 'Alice Brown',
        'event_date' => '2024-08-15',
        'status' => 'pending',
        'budget' => 8000,
        'location' => 'Business Center'
    ],
    [
        'event_id' => 5,
        'event_title' => 'Anniversary Party',
        'category_name' => 'Other',
        'client_name' => 'David Lee',
        'event_date' => '2024-09-20',
        'status' => 'pending',
        'budget' => 3000,
        'location' => 'Private Venue'
    ]
];

// Dummy statistics
$total_events = count($events_result);
$completed_events = 1; // Count of completed events
$pending_events = 2; // Count of pending events
$upcoming_events = 3; // Count of upcoming events
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planner Dashboard - EMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .dashboard-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        .event-card {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        .event-card:hover {
            border-color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.2);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 5px 12px;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="dashboard-card p-5">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="fw-bold text-primary">
                                <i class="fas fa-tachometer-alt me-2"></i>Planner Dashboard
                            </h2>
                            <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($planner['company_name'] ?: 'Event Planner'); ?>!</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="profile.php" class="btn btn-outline-primary">
                                <i class="fas fa-user me-2"></i>Profile
                            </a>
                            <a href="../logout.php" class="btn btn-outline-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <i class="fas fa-calendar-check fa-3x mb-3"></i>
                                <h3 class="fw-bold"><?php echo $total_events; ?></h3>
                                <p class="mb-0">Total Events</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <i class="fas fa-clock fa-3x mb-3"></i>
                                <h3 class="fw-bold"><?php echo $pending_events; ?></h3>
                                <p class="mb-0">Pending Events</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <i class="fas fa-calendar-day fa-3x mb-3"></i>
                                <h3 class="fw-bold"><?php echo $upcoming_events; ?></h3>
                                <p class="mb-0">Upcoming Events</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                <h3 class="fw-bold"><?php echo $completed_events; ?></h3>
                                <p class="mb-0">Completed Events</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Assigned Events -->
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-calendar-alt me-2"></i>My Assigned Events
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($events_result->num_rows > 0): ?>
                                        <div class="row">
                                            <?php foreach ($events_result as $event): ?>
                                                <div class="col-md-6 mb-3">
                                                    <div class="event-card p-3">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($event['event_title']); ?></h6>
                                                            <span class="status-badge badge bg-<?php 
                                                                echo $event['status'] == 'pending' ? 'warning' : 
                                                                    ($event['status'] == 'confirmed' ? 'success' : 
                                                                    ($event['status'] == 'completed' ? 'info' : 'secondary')); 
                                                            ?>">
                                                                <?php echo ucfirst(str_replace('_', ' ', $event['status'])); ?>
                                                            </span>
                                                        </div>
                                                        
                                                        <p class="mb-2">
                                                            <i class="fas fa-user me-1 text-primary"></i>
                                                            <?php echo htmlspecialchars($event['client_name']); ?>
                                                        </p>
                                                        <p class="mb-2">
                                                            <i class="fas fa-calendar me-1 text-primary"></i>
                                                            <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                                        </p>
                                                        <p class="mb-2">
                                                            <i class="fas fa-map-marker-alt me-1 text-primary"></i>
                                                            <?php echo htmlspecialchars($event['location']); ?>
                                                        </p>
                                                        
                                                        <div class="d-flex gap-2">
                                                            <a href="event_details.php?id=<?php echo $event['event_id']; ?>" class="btn btn-outline-primary btn-sm">
                                                                <i class="fas fa-eye me-1"></i>View
                                                            </a>
                                                            <a href="manage_event.php?id=<?php echo $event['event_id']; ?>" class="btn btn-outline-success btn-sm">
                                                                <i class="fas fa-cog me-1"></i>Manage
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                            <h5>No Assigned Events</h5>
                                            <p class="text-muted">You don't have any assigned events yet.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Available Events -->
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-plus-circle me-2"></i>Available Events
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if ($pending_result->num_rows > 0): ?>
                                        <?php foreach ($pending_result as $event): ?>
                                            <div class="event-card p-3 mb-3">
                                                <h6 class="fw-bold mb-2"><?php echo htmlspecialchars($event['event_title']); ?></h6>
                                                <p class="mb-2">
                                                    <i class="fas fa-user me-1 text-primary"></i>
                                                    <?php echo htmlspecialchars($event['client_name']); ?>
                                                </p>
                                                <p class="mb-2">
                                                    <i class="fas fa-calendar me-1 text-primary"></i>
                                                    <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                                </p>
                                                <?php if ($event['budget']): ?>
                                                    <p class="mb-2">
                                                        <i class="fas fa-dollar-sign me-1 text-primary"></i>
                                                        $<?php echo number_format($event['budget'], 2); ?>
                                                    </p>
                                                <?php endif; ?>
                                                
                                                <a href="view_event.php?id=<?php echo $event['event_id']; ?>" class="btn btn-success btn-sm w-100">
                                                    <i class="fas fa-eye me-1"></i>View Details
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center py-3">
                                            <i class="fas fa-calendar-check fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No available events at the moment.</p>
                                        </div>
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
</body>
</html> 