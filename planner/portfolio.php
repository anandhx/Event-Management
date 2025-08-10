<?php
session_start();

// Dummy planner data for showcase
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'planner_jane';
    $_SESSION['full_name'] = 'Jane Smith';
    $_SESSION['user_type'] = 'event_planner';
    $_SESSION['company_name'] = 'Elegant Events Co.';
}

// Dummy portfolio data
$portfolio = [
    'planner' => [
        'name' => 'Jane Smith',
        'company' => 'Elegant Events Co.',
        'email' => 'jane@elegantevents.com',
        'phone' => '+1 (555) 123-4567',
        'rating' => 4.9,
        'reviews' => 127,
        'experience' => 8,
        'specializations' => ['Weddings', 'Corporate Events', 'Birthday Parties', 'Anniversaries'],
        'bio' => 'With over 8 years of experience in event planning, I specialize in creating unforgettable moments for my clients. From intimate gatherings to grand celebrations, I bring creativity, attention to detail, and flawless execution to every event.',
        'image' => '../assets/img/team-1.jpg',
        'location' => 'New York, NY',
        'languages' => ['English', 'Spanish'],
        'certifications' => ['Certified Event Planner', 'Wedding Planning Specialist', 'Corporate Event Management']
    ],
    'stats' => [
        'total_events' => 156,
        'completed_events' => 142,
        'happy_clients' => 98,
        'average_rating' => 4.9,
        'years_experience' => 8,
        'specializations' => 4
    ],
    'featured_events' => [
        [
            'id' => 1,
            'title' => 'Sarah & Mike Wedding',
            'category' => 'Wedding',
            'date' => '2024-03-15',
            'venue' => 'Grand Plaza Hotel',
            'budget' => 15000,
            'description' => 'A beautiful garden wedding with 150 guests. Featured elegant decorations, live music, and gourmet catering.',
            'images' => ['../assets/img/wedding-event.jpg', '../assets/img/hero-bg.jpg', '../assets/img/corporate-event.jpg'],
            'client_review' => 'Jane was absolutely amazing! Our wedding was perfect and everything went smoothly.',
            'client_rating' => 5
        ],
        [
            'id' => 2,
            'title' => 'TechCorp Annual Conference',
            'category' => 'Corporate Event',
            'date' => '2024-02-20',
            'venue' => 'Convention Center',
            'budget' => 25000,
            'description' => 'Annual technology conference with 200+ attendees, featuring keynote speakers, workshops, and networking sessions.',
            'images' => ['../assets/img/corporate-event.jpg', '../assets/img/hero-bg.jpg', '../assets/img/wedding-event.jpg'],
            'client_review' => 'Professional and efficient. The conference was a huge success!',
            'client_rating' => 5
        ],
        [
            'id' => 3,
            'title' => 'Emma\'s Sweet 16',
            'category' => 'Birthday Party',
            'date' => '2024-01-15',
            'venue' => 'Community Hall',
            'budget' => 5000,
            'description' => 'A magical sweet 16 celebration with themed decorations, DJ, and photo booth.',
            'images' => ['../assets/img/entertainment.jpg', '../assets/img/hero-bg.jpg', '../assets/img/corporate-event.jpg'],
            'client_review' => 'Emma had the best birthday ever! Thank you for making it so special.',
            'client_rating' => 5
        ]
    ],
    'recent_reviews' => [
        [
            'client' => 'Sarah Johnson',
            'event' => 'Sarah & Mike Wedding',
            'rating' => 5,
            'comment' => 'Jane was absolutely amazing! Our wedding was perfect and everything went smoothly. She handled every detail with professionalism and creativity.',
            'date' => '2024-03-15'
        ],
        [
            'client' => 'TechCorp Inc.',
            'event' => 'Annual Conference',
            'rating' => 5,
            'comment' => 'Professional and efficient. The conference was a huge success! Jane\'s attention to detail and organizational skills are outstanding.',
            'date' => '2024-02-20'
        ],
        [
            'client' => 'Emma Davis',
            'event' => 'Sweet 16 Party',
            'rating' => 5,
            'comment' => 'Emma had the best birthday ever! Thank you for making it so special. The decorations and entertainment were perfect.',
            'date' => '2024-01-15'
        ],
        [
            'client' => 'Robert & Lisa',
            'event' => '25th Anniversary',
            'rating' => 5,
            'comment' => 'Our anniversary celebration was magical. Jane created the perfect romantic atmosphere for our special day.',
            'date' => '2023-12-10'
        ]
    ],
    'services' => [
        [
            'name' => 'Wedding Planning',
            'description' => 'Complete wedding planning from engagement to honeymoon',
            'icon' => 'fas fa-heart',
            'price_range' => '$5,000 - $50,000'
        ],
        [
            'name' => 'Corporate Events',
            'description' => 'Conferences, seminars, team building, and corporate functions',
            'icon' => 'fas fa-briefcase',
            'price_range' => '$2,000 - $100,000'
        ],
        [
            'name' => 'Birthday Celebrations',
            'description' => 'Birthday parties for all ages with themed decorations',
            'icon' => 'fas fa-birthday-cake',
            'price_range' => '$500 - $10,000'
        ],
        [
            'name' => 'Anniversary Parties',
            'description' => 'Romantic anniversary celebrations and vow renewals',
            'icon' => 'fas fa-calendar-heart',
            'price_range' => '$1,000 - $25,000'
        ]
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - <?= $portfolio['planner']['name'] ?> - EventPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/css/lightbox.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .portfolio-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .planner-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 40px;
        }
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .stats-card:hover {
            border-color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
        }
        .event-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .event-card:hover {
            border-color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
        }
        .event-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }
        .gallery-item {
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .gallery-item:hover {
            transform: scale(1.05);
        }
        .gallery-item img {
            width: 100%;
            height: 100px;
            object-fit: cover;
        }
        .review-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        .service-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            text-align: center;
        }
        .service-card:hover {
            border-color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
        }
        .service-icon {
            font-size: 3rem;
            color: #667eea;
            margin-bottom: 15px;
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
        .specialization-tag {
            background: #667eea;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin: 2px;
            display: inline-block;
        }
        .certification-badge {
            background: #28a745;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin: 2px;
            display: inline-block;
        }
        .planner-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="portfolio-container">
                    <!-- Planner Header -->
                    <div class="planner-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?= $portfolio['planner']['image'] ?>" alt="<?= $portfolio['planner']['name'] ?>" class="planner-image me-4">
                                    <div>
                                        <h1 class="fw-bold mb-2"><?= $portfolio['planner']['name'] ?></h1>
                                        <h4 class="mb-2"><?= $portfolio['planner']['company'] ?></h4>
                                        <div class="rating-stars mb-2">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?= $i <= $portfolio['planner']['rating'] ? '' : 'text-muted' ?>"></i>
                                            <?php endfor; ?>
                                            <span class="ms-2"><?= $portfolio['planner']['rating'] ?> (<?= $portfolio['planner']['reviews'] ?> reviews)</span>
                                        </div>
                                        <p class="mb-0 opacity-75">
                                            <i class="fas fa-map-marker-alt me-2"></i><?= $portfolio['planner']['location'] ?> | 
                                            <i class="fas fa-star me-2"></i><?= $portfolio['planner']['experience'] ?> years experience
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-light btn-custom me-2">
                                    <i class="fas fa-envelope me-1"></i>Contact
                                </button>
                                <button class="btn btn-outline-light btn-custom">
                                    <i class="fas fa-heart me-1"></i>Save
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="p-4">
                        <!-- Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="stats-card text-center">
                                    <h3 class="fw-bold text-primary"><?= $portfolio['stats']['total_events'] ?></h3>
                                    <p class="text-muted mb-0">Total Events</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card text-center">
                                    <h3 class="fw-bold text-success"><?= $portfolio['stats']['completed_events'] ?></h3>
                                    <p class="text-muted mb-0">Completed Events</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card text-center">
                                    <h3 class="fw-bold text-info"><?= $portfolio['stats']['happy_clients'] ?>%</h3>
                                    <p class="text-muted mb-0">Happy Clients</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card text-center">
                                    <h3 class="fw-bold text-warning"><?= $portfolio['stats']['average_rating'] ?></h3>
                                    <p class="text-muted mb-0">Average Rating</p>
                                </div>
                            </div>
                        </div>

                        <!-- About & Specializations -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <h4 class="fw-bold mb-3">
                                    <i class="fas fa-user me-2"></i>About Me
                                </h4>
                                <p class="text-muted mb-3"><?= $portfolio['planner']['bio'] ?></p>
                                
                                <h5 class="fw-bold mb-3">Specializations</h5>
                                <div class="mb-3">
                                    <?php foreach ($portfolio['planner']['specializations'] as $spec): ?>
                                        <span class="specialization-tag"><?= $spec ?></span>
                                    <?php endforeach; ?>
                                </div>

                                <h5 class="fw-bold mb-3">Certifications</h5>
                                <div class="mb-3">
                                    <?php foreach ($portfolio['planner']['certifications'] as $cert): ?>
                                        <span class="certification-badge"><?= $cert ?></span>
                                    <?php endforeach; ?>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-2">Contact Information</h6>
                                        <p class="mb-1">
                                            <i class="fas fa-envelope me-2"></i><?= $portfolio['planner']['email'] ?>
                                        </p>
                                        <p class="mb-1">
                                            <i class="fas fa-phone me-2"></i><?= $portfolio['planner']['phone'] ?>
                                        </p>
                                        <p class="mb-0">
                                            <i class="fas fa-map-marker-alt me-2"></i><?= $portfolio['planner']['location'] ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="fw-bold mb-2">Languages</h6>
                                        <p class="mb-0">
                                            <?= implode(', ', $portfolio['planner']['languages']) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <h4 class="fw-bold mb-3">
                                    <i class="fas fa-cogs me-2"></i>Services Offered
                                </h4>
                                <?php foreach ($portfolio['services'] as $service): ?>
                                    <div class="service-card">
                                        <i class="<?= $service['icon'] ?> service-icon"></i>
                                        <h6 class="fw-bold mb-2"><?= $service['name'] ?></h6>
                                        <p class="text-muted mb-2"><?= $service['description'] ?></p>
                                        <small class="text-primary fw-bold"><?= $service['price_range'] ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Featured Events -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="fw-bold mb-3">
                                    <i class="fas fa-star me-2"></i>Featured Events
                                </h4>
                                <?php foreach ($portfolio['featured_events'] as $event): ?>
                                    <div class="event-card">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <h5 class="fw-bold mb-2"><?= $event['title'] ?></h5>
                                                <p class="text-muted mb-2">
                                                    <i class="fas fa-tag me-1"></i><?= $event['category'] ?> | 
                                                    <i class="fas fa-calendar me-1"></i><?= $event['date'] ?> | 
                                                    <i class="fas fa-map-marker-alt me-1"></i><?= $event['venue'] ?> | 
                                                    <i class="fas fa-dollar-sign me-1"></i>$<?= number_format($event['budget']) ?>
                                                </p>
                                                <p class="text-muted mb-3"><?= $event['description'] ?></p>
                                                
                                                <div class="mb-3">
                                                    <h6 class="fw-bold mb-2">Client Review</h6>
                                                    <div class="rating-stars mb-2">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="fas fa-star <?= $i <= $event['client_rating'] ? '' : 'text-muted' ?>"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <p class="text-muted mb-0">"<?= $event['client_review'] ?>"</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <h6 class="fw-bold mb-2">Event Gallery</h6>
                                                <div class="event-gallery">
                                                    <?php foreach ($event['images'] as $index => $image): ?>
                                                        <div class="gallery-item">
                                                            <a href="<?= $image ?>" data-lightbox="event-<?= $event['id'] ?>">
                                                                <img src="<?= $image ?>" alt="Event Image <?= $index + 1 ?>">
                                                            </a>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Recent Reviews -->
                        <div class="row">
                            <div class="col-12">
                                <h4 class="fw-bold mb-3">
                                    <i class="fas fa-comments me-2"></i>Recent Reviews
                                </h4>
                                <div class="row">
                                    <?php foreach ($portfolio['recent_reviews'] as $review): ?>
                                        <div class="col-md-6">
                                            <div class="review-card">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="fw-bold mb-0"><?= $review['client'] ?></h6>
                                                    <div class="rating-stars">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="fas fa-star <?= $i <= $review['rating'] ? '' : 'text-muted' ?>"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                                <p class="text-muted mb-1">
                                                    <i class="fas fa-calendar me-1"></i><?= $review['event'] ?>
                                                </p>
                                                <p class="mb-2">"<?= $review['comment'] ?>"</p>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i><?= $review['date'] ?>
                                                </small>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lightbox2@2.11.3/dist/js/lightbox.min.js"></script>
</body>
</html> 