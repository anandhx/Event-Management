<?php
session_start();
include '../includes/db.php';

// Check if user is logged in (simulated)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Dummy events data for showcase
// In real implementation, this would fetch from database
$events_result = [
    [
        'event_id' => 1,
        'title' => 'Sarah & Mike Wedding',
        'category_name' => 'Wedding',
        'planner_name' => 'Elegant Events Co.',
        'event_date' => '2024-06-15',
        'status' => 'in_progress',
        'budget' => 25000,
        'location' => 'Grand Hotel, Downtown'
    ],
    [
        'event_id' => 2,
        'title' => 'Tech Conference 2024',
        'category_name' => 'Corporate Event',
        'planner_name' => 'Professional Events',
        'event_date' => '2024-05-20',
        'status' => 'completed',
        'budget' => 15000,
        'location' => 'Convention Center'
    ],
    [
        'event_id' => 3,
        'title' => 'Birthday Party',
        'category_name' => 'Birthday Party',
        'planner_name' => 'Party Planners Inc.',
        'event_date' => '2024-07-10',
        'status' => 'pending',
        'budget' => 5000,
        'location' => 'Community Hall'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events - EMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .events-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .event-card {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        .event-card:hover {
            border-color: #667eea;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.2);
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 5px 12px;
            border-radius: 20px;
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
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="events-card p-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="fw-bold text-primary">
                                <i class="fas fa-calendar-alt me-2"></i>My Events
                            </h2>
                            <p class="text-muted mb-0">Manage and track your events</p>
                        </div>
                        <a href="create_event.php" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create New Event
                        </a>
                    </div>

                    <?php if (!empty($events_result)): ?>
                        <div class="row">
                            <?php foreach ($events_result as $event): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="event-card p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($event['title']); ?></h5>
                                            <span class="status-badge badge bg-<?php 
                                                echo $event['status'] == 'pending' ? 'warning' : 
                                                    ($event['status'] == 'confirmed' ? 'success' : 
                                                    ($event['status'] == 'completed' ? 'info' : 'secondary')); 
                                            ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $event['status'])); ?>
                                            </span>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-tag me-1"></i><?php echo $event['category_name']; ?>
                                            </small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <p class="mb-2">
                                                <i class="fas fa-calendar me-2 text-primary"></i>
                                                <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                            </p>
                                            <p class="mb-2">
                                                <i class="fas fa-clock me-2 text-primary"></i>
                                                <?php echo date('g:i A', strtotime($event['event_time'])); ?>
                                            </p>
                                            <p class="mb-2">
                                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                                <?php echo htmlspecialchars($event['venue']); ?>
                                            </p>
                                            <?php if ($event['expected_guests']): ?>
                                                <p class="mb-2">
                                                    <i class="fas fa-users me-2 text-primary"></i>
                                                    <?php echo $event['expected_guests']; ?> guests
                                                </p>
                                            <?php endif; ?>
                                            <?php if ($event['budget']): ?>
                                                <p class="mb-2">
                                                    <i class="fas fa-dollar-sign me-2 text-primary"></i>
                                                    $<?php echo number_format($event['budget'], 2); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if ($event['planner_name']): ?>
                                            <div class="mb-3 p-3 bg-light rounded">
                                                <small class="text-muted">Assigned to:</small>
                                                <p class="mb-0 fw-bold"><?php echo htmlspecialchars($event['planner_name']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="event_details.php?id=<?php echo $event['event_id']; ?>" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                            <?php if ($event['status'] == 'pending'): ?>
                                                <a href="edit_event.php?id=<?php echo $event['event_id']; ?>" class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h4>No Events Yet</h4>
                            <p class="mb-4">You haven't created any events yet. Start by creating your first event!</p>
                            <a href="create_event.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-plus me-2"></i>Create Your First Event
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 