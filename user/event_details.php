<?php
session_start();

// Dummy user data for showcase
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'john_doe';
    $_SESSION['full_name'] = 'John Doe';
    $_SESSION['user_type'] = 'client';
}

// Dummy event details
$event = [
    'id' => 1,
    'title' => 'Sarah & Mike Wedding',
    'category' => 'Wedding',
    'date' => '2024-03-15',
    'time' => '14:00',
    'venue' => 'Grand Plaza Hotel',
    'venue_address' => '123 Main Street, Downtown, City',
    'budget' => 15000,
    'status' => 'confirmed',
    'planner' => [
        'name' => 'Jane Smith',
        'company' => 'Elegant Events Co.',
        'email' => 'jane@elegantevents.com',
        'phone' => '+1 (555) 123-4567',
        'rating' => 4.9,
        'reviews' => 127,
        'experience' => 8,
        'image' => '../assets/img/team-1.jpg'
    ],
    'description' => 'A beautiful wedding celebration for Sarah and Mike. The event will feature elegant decorations, live music, and a gourmet dinner service. The ceremony will be held in the garden area followed by a reception in the grand ballroom.',
    'guest_count' => 150,
    'duration' => 6,
    'theme' => 'Elegant Garden',
    'color_scheme' => ['#667eea', '#764ba2', '#f093fb'],
    'services' => [
        'Venue Decoration',
        'Catering Services',
        'Photography & Videography',
        'Music & Entertainment',
        'Flower Arrangements',
        'Transportation'
    ],
    'timeline' => [
        ['time' => '14:00', 'activity' => 'Ceremony Begins', 'status' => 'completed'],
        ['time' => '15:00', 'activity' => 'Cocktail Hour', 'status' => 'completed'],
        ['time' => '16:00', 'activity' => 'Reception & Dinner', 'status' => 'in_progress'],
        ['time' => '18:00', 'activity' => 'First Dance', 'status' => 'pending'],
        ['time' => '19:00', 'activity' => 'Cake Cutting', 'status' => 'pending'],
        ['time' => '20:00', 'activity' => 'Reception Ends', 'status' => 'pending']
    ],
    'tasks' => [
        ['task' => 'Finalize guest list', 'assigned_to' => 'Client', 'due_date' => '2024-03-10', 'status' => 'completed'],
        ['task' => 'Choose wedding cake', 'assigned_to' => 'Client', 'due_date' => '2024-03-12', 'status' => 'completed'],
        ['task' => 'Arrange transportation', 'assigned_to' => 'Planner', 'due_date' => '2024-03-14', 'status' => 'in_progress'],
        ['task' => 'Final venue walkthrough', 'assigned_to' => 'Both', 'due_date' => '2024-03-14', 'status' => 'pending']
    ],
    'messages' => [
        ['sender' => 'Jane Smith', 'message' => 'Hi! I\'ve confirmed the venue decoration details. Everything looks perfect for your theme.', 'time' => '2 hours ago', 'type' => 'planner'],
        ['sender' => 'John Doe', 'message' => 'Great! Can you also confirm the photographer will be there by 1 PM?', 'time' => '1 hour ago', 'type' => 'client'],
        ['sender' => 'Jane Smith', 'message' => 'Absolutely! The photographer is confirmed for 1 PM. I\'ll send you the final schedule tomorrow.', 'time' => '30 minutes ago', 'type' => 'planner']
    ]
];

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    // Simulate message sending
    $_SESSION['success_message'] = "Message sent successfully!";
    header("Location: event_details.php?id=" . $event['id']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details - EventPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .event-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .event-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 30px;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 8px 16px;
            border-radius: 25px;
        }
        .planner-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .planner-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }
        .timeline-item {
            border-left: 3px solid #e9ecef;
            padding-left: 20px;
            margin-bottom: 15px;
            position: relative;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #e9ecef;
        }
        .timeline-item.completed::before {
            background: #28a745;
        }
        .timeline-item.in_progress::before {
            background: #ffc107;
        }
        .task-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #e9ecef;
        }
        .task-card.completed {
            border-left-color: #28a745;
            background: #d4edda;
        }
        .task-card.in_progress {
            border-left-color: #ffc107;
            background: #fff3cd;
        }
        .message-bubble {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            max-width: 80%;
        }
        .message-bubble.client {
            background: #667eea;
            color: white;
            margin-left: auto;
        }
        .message-bubble.planner {
            background: #e9ecef;
            color: #333;
        }
        .color-scheme {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .color-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .btn-custom {
            border-radius: 10px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
        }
        .rating-stars {
            color: #ffc107;
        }
        .service-tag {
            background: #667eea;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin: 2px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="event-container">
                    <!-- Event Header -->
                    <div class="event-header">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h1 class="fw-bold mb-2"><?= $event['title'] ?></h1>
                                <p class="mb-2 opacity-75">
                                    <i class="fas fa-calendar me-2"></i><?= $event['date'] ?> at <?= $event['time'] ?>
                                </p>
                                <p class="mb-0 opacity-75">
                                    <i class="fas fa-map-marker-alt me-2"></i><?= $event['venue'] ?>
                                </p>
                            </div>
                            <div class="text-end">
                                <?php
                                $status_class = '';
                                switch($event['status']) {
                                    case 'confirmed': $status_class = 'bg-success'; break;
                                    case 'in_progress': $status_class = 'bg-warning'; break;
                                    case 'completed': $status_class = 'bg-info'; break;
                                    case 'pending': $status_class = 'bg-secondary'; break;
                                }
                                ?>
                                <span class="status-badge <?= $status_class ?>">
                                    <?= ucfirst(str_replace('_', ' ', $event['status'])) ?>
                                </span>
                                <div class="mt-3">
                                    <button class="btn btn-light btn-custom me-2">
                                        <i class="fas fa-edit me-1"></i>Edit Event
                                    </button>
                                    <button class="btn btn-outline-light btn-custom">
                                        <i class="fas fa-download me-1"></i>Export Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <!-- Event Overview -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h4 class="fw-bold mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Event Overview
                                </h4>
                                <p class="text-muted mb-3"><?= $event['description'] ?></p>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p class="mb-1">
                                            <i class="fas fa-users me-2"></i><strong>Guest Count:</strong> <?= $event['guest_count'] ?>
                                        </p>
                                        <p class="mb-1">
                                            <i class="fas fa-clock me-2"></i><strong>Duration:</strong> <?= $event['duration'] ?> hours
                                        </p>
                                        <p class="mb-1">
                                            <i class="fas fa-tag me-2"></i><strong>Category:</strong> <?= $event['category'] ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1">
                                            <i class="fas fa-palette me-2"></i><strong>Theme:</strong> <?= $event['theme'] ?>
                                        </p>
                                        <p class="mb-1">
                                            <i class="fas fa-dollar-sign me-2"></i><strong>Budget:</strong> $<?= number_format($event['budget']) ?>
                                        </p>
                                        <p class="mb-1">
                                            <i class="fas fa-palette me-2"></i><strong>Color Scheme:</strong>
                                        </p>
                                        <div class="color-scheme">
                                            <?php foreach ($event['color_scheme'] as $color): ?>
                                                <div class="color-circle" style="background-color: <?= $color ?>;"></div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="fw-bold mb-3">Services Included</h5>
                                <div class="mb-4">
                                    <?php foreach ($event['services'] as $service): ?>
                                        <span class="service-tag"><?= $service ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="planner-card">
                                    <h5 class="fw-bold mb-3">Event Planner</h5>
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="<?= $event['planner']['image'] ?>" alt="<?= $event['planner']['name'] ?>" 
                                             class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                        <div>
                                            <h6 class="fw-bold mb-1"><?= $event['planner']['name'] ?></h6>
                                            <p class="text-muted mb-0"><?= $event['planner']['company'] ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="rating-stars mb-1">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?= $i <= $event['planner']['rating'] ? '' : 'text-muted' ?>"></i>
                                            <?php endfor; ?>
                                            <span class="ms-2"><?= $event['planner']['rating'] ?></span>
                                        </div>
                                        <small class="text-muted"><?= $event['planner']['reviews'] ?> reviews</small>
                                    </div>

                                    <div class="mb-3">
                                        <p class="mb-1">
                                            <i class="fas fa-envelope me-2"></i><?= $event['planner']['email'] ?>
                                        </p>
                                        <p class="mb-1">
                                            <i class="fas fa-phone me-2"></i><?= $event['planner']['phone'] ?>
                                        </p>
                                        <p class="mb-0">
                                            <i class="fas fa-star me-2"></i><?= $event['planner']['experience'] ?> years experience
                                        </p>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary btn-sm btn-custom">
                                            <i class="fas fa-envelope me-1"></i>Message
                                        </button>
                                        <button class="btn btn-outline-primary btn-sm btn-custom">
                                            <i class="fas fa-eye me-1"></i>View Profile
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Event Timeline -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="fw-bold mb-3">
                                    <i class="fas fa-clock me-2"></i>Event Timeline
                                </h4>
                                <div class="timeline-container">
                                    <?php foreach ($event['timeline'] as $item): ?>
                                        <div class="timeline-item <?= $item['status'] ?>">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="fw-bold mb-1"><?= $item['activity'] ?></h6>
                                                    <p class="text-muted mb-0"><?= $item['time'] ?></p>
                                                </div>
                                                <span class="badge bg-<?= $item['status'] == 'completed' ? 'success' : ($item['status'] == 'in_progress' ? 'warning' : 'secondary') ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $item['status'])) ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Tasks -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="fw-bold mb-3">
                                    <i class="fas fa-tasks me-2"></i>Event Tasks
                                </h4>
                                <div class="row">
                                    <?php foreach ($event['tasks'] as $task): ?>
                                        <div class="col-md-6">
                                            <div class="task-card <?= $task['status'] ?>">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="fw-bold mb-1"><?= $task['task'] ?></h6>
                                                        <p class="text-muted mb-1">
                                                            <i class="fas fa-user me-1"></i><?= $task['assigned_to'] ?>
                                                        </p>
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar me-1"></i>Due: <?= $task['due_date'] ?>
                                                        </small>
                                                    </div>
                                                    <span class="badge bg-<?= $task['status'] == 'completed' ? 'success' : ($task['status'] == 'in_progress' ? 'warning' : 'secondary') ?>">
                                                        <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="row">
                            <div class="col-12">
                                <h4 class="fw-bold mb-3">
                                    <i class="fas fa-comments me-2"></i>Event Messages
                                </h4>
                                
                                <div class="messages-container mb-3" style="max-height: 300px; overflow-y: auto;">
                                    <?php foreach ($event['messages'] as $message): ?>
                                        <div class="message-bubble <?= $message['type'] ?>">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <strong><?= $message['sender'] ?></strong>
                                                <small class="text-muted"><?= $message['time'] ?></small>
                                            </div>
                                            <p class="mb-0"><?= $message['message'] ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <form method="POST" class="d-flex gap-2">
                                    <input type="text" class="form-control" name="message" placeholder="Type your message..." required>
                                    <button type="submit" class="btn btn-primary btn-custom">
                                        <i class="fas fa-paper-plane me-1"></i>Send
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 