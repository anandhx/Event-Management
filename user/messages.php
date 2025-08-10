<?php
session_start();

// Dummy user data for showcase
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'john_doe';
    $_SESSION['full_name'] = 'John Doe';
    $_SESSION['user_type'] = 'client';
}

// Dummy conversations
$conversations = [
    [
        'id' => 1,
        'planner' => [
            'name' => 'Jane Smith',
            'company' => 'Elegant Events Co.',
            'image' => '../assets/img/team-1.jpg',
            'online' => true
        ],
        'event' => 'Sarah & Mike Wedding',
        'last_message' => 'Hi! I\'ve confirmed the venue decoration details. Everything looks perfect for your theme.',
        'time' => '2 hours ago',
        'unread' => 1
    ],
    [
        'id' => 2,
        'planner' => [
            'name' => 'Maria Garcia',
            'company' => 'Celebration Masters',
            'image' => '../assets/img/team-3.jpg',
            'online' => false
        ],
        'event' => 'Birthday Celebration',
        'last_message' => 'The birthday party was a huge success! Emma loved every moment.',
        'time' => '1 day ago',
        'unread' => 0
    ],
    [
        'id' => 3,
        'planner' => [
            'name' => 'David Chen',
            'company' => 'Corporate Events Pro',
            'image' => '../assets/img/team-4.jpg',
            'online' => true
        ],
        'event' => 'Corporate Team Building',
        'last_message' => 'I\'ve sent you the updated schedule for the team building event.',
        'time' => '3 hours ago',
        'unread' => 2
    ]
];

// Dummy messages for active conversation
$active_conversation = [
    'id' => 1,
    'planner' => [
        'name' => 'Jane Smith',
        'company' => 'Elegant Events Co.',
        'image' => '../assets/img/team-1.jpg',
        'online' => true
    ],
    'event' => 'Sarah & Mike Wedding',
    'messages' => [
        [
            'sender' => 'planner',
            'message' => 'Hi John! I\'ve confirmed the venue decoration details. Everything looks perfect for your theme.',
            'time' => '2 hours ago',
            'status' => 'read'
        ],
        [
            'sender' => 'client',
            'message' => 'Great! Can you also confirm the photographer will be there by 1 PM?',
            'time' => '1 hour ago',
            'status' => 'read'
        ],
        [
            'sender' => 'planner',
            'message' => 'Absolutely! The photographer is confirmed for 1 PM. I\'ll send you the final schedule tomorrow.',
            'time' => '30 minutes ago',
            'status' => 'read'
        ],
        [
            'sender' => 'client',
            'message' => 'Perfect! Also, can we add some extra floral arrangements for the ceremony area?',
            'time' => '15 minutes ago',
            'status' => 'sent'
        ],
        [
            'sender' => 'planner',
            'message' => 'Of course! I\'ll add extra floral arrangements to the ceremony area. I\'ll update the quote and send it to you.',
            'time' => 'Just now',
            'status' => 'sent'
        ]
    ]
];

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    // Simulate message sending
    $_SESSION['success_message'] = "Message sent successfully!";
    header("Location: messages.php?conversation=" . $active_conversation['id']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - EventPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .messages-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            height: 600px;
        }
        .conversation-list {
            border-right: 2px solid #e9ecef;
            height: 100%;
            overflow-y: auto;
        }
        .conversation-item {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .conversation-item:hover {
            background: #f8f9fa;
        }
        .conversation-item.active {
            background: #667eea;
            color: white;
        }
        .conversation-item.active .text-muted {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        .chat-area {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .chat-header {
            padding: 20px;
            border-bottom: 2px solid #e9ecef;
            background: #f8f9fa;
            border-radius: 20px 0 0 0;
        }
        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f8f9fa;
        }
        .message-bubble {
            margin-bottom: 15px;
            max-width: 70%;
        }
        .message-bubble.client {
            margin-left: auto;
        }
        .message-bubble.planner {
            margin-right: auto;
        }
        .message-content {
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
        }
        .message-bubble.client .message-content {
            background: #667eea;
            color: white;
        }
        .message-bubble.planner .message-content {
            background: white;
            color: #333;
            border: 1px solid #e9ecef;
        }
        .message-time {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 5px;
        }
        .message-status {
            font-size: 0.7rem;
            margin-top: 2px;
        }
        .chat-input {
            padding: 20px;
            border-top: 2px solid #e9ecef;
            background: white;
            border-radius: 0 0 20px 0;
        }
        .planner-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        .online-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #28a745;
            border: 2px solid white;
            position: absolute;
            bottom: 0;
            right: 0;
        }
        .unread-badge {
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: bold;
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
        .typing-indicator {
            padding: 10px 15px;
            color: #6c757d;
            font-style: italic;
        }
        .message-input {
            border-radius: 25px;
            border: 2px solid #e9ecef;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }
        .message-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="messages-container">
                    <div class="row h-100">
                        <!-- Conversation List -->
                        <div class="col-md-4 p-0">
                            <div class="conversation-list">
                                <div class="p-3 border-bottom">
                                    <h5 class="fw-bold mb-0">
                                        <i class="fas fa-comments me-2"></i>Messages
                                    </h5>
                                </div>
                                
                                <?php foreach ($conversations as $conversation): ?>
                                    <div class="conversation-item <?= $conversation['id'] == $active_conversation['id'] ? 'active' : '' ?>">
                                        <div class="d-flex align-items-center">
                                            <div class="position-relative me-3">
                                                <img src="<?= $conversation['planner']['image'] ?>" 
                                                     alt="<?= $conversation['planner']['name'] ?>" 
                                                     class="planner-avatar">
                                                <?php if ($conversation['planner']['online']): ?>
                                                    <div class="online-indicator"></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <h6 class="fw-bold mb-1"><?= $conversation['planner']['name'] ?></h6>
                                                        <p class="text-muted mb-1 small"><?= $conversation['planner']['company'] ?></p>
                                                        <p class="text-muted mb-0 small"><?= $conversation['event'] ?></p>
                                                    </div>
                                                    <div class="text-end">
                                                        <small class="text-muted d-block"><?= $conversation['time'] ?></small>
                                                        <?php if ($conversation['unread'] > 0): ?>
                                                            <div class="unread-badge"><?= $conversation['unread'] ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <p class="text-muted mb-0 small mt-2">
                                                    <?= strlen($conversation['last_message']) > 50 ? substr($conversation['last_message'], 0, 50) . '...' : $conversation['last_message'] ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Chat Area -->
                        <div class="col-md-8 p-0">
                            <div class="chat-area">
                                <!-- Chat Header -->
                                <div class="chat-header">
                                    <div class="d-flex align-items-center">
                                        <div class="position-relative me-3">
                                            <img src="<?= $active_conversation['planner']['image'] ?>" 
                                                 alt="<?= $active_conversation['planner']['name'] ?>" 
                                                 class="planner-avatar">
                                            <?php if ($active_conversation['planner']['online']): ?>
                                                <div class="online-indicator"></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1"><?= $active_conversation['planner']['name'] ?></h6>
                                            <p class="text-muted mb-0"><?= $active_conversation['planner']['company'] ?></p>
                                            <small class="text-muted"><?= $active_conversation['event'] ?></small>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-primary btn-sm btn-custom">
                                                <i class="fas fa-phone me-1"></i>Call
                                            </button>
                                            <button class="btn btn-outline-primary btn-sm btn-custom">
                                                <i class="fas fa-video me-1"></i>Video
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chat Messages -->
                                <div class="chat-messages">
                                    <?php foreach ($active_conversation['messages'] as $message): ?>
                                        <div class="message-bubble <?= $message['sender'] ?>">
                                            <div class="message-content">
                                                <p class="mb-0"><?= $message['message'] ?></p>
                                                <div class="message-time">
                                                    <?= $message['time'] ?>
                                                    <?php if ($message['sender'] == 'client'): ?>
                                                        <span class="message-status">
                                                            <i class="fas fa-<?= $message['status'] == 'read' ? 'check-double text-primary' : 'check' ?>"></i>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <!-- Typing Indicator -->
                                    <div class="typing-indicator">
                                        <i class="fas fa-circle me-1"></i>
                                        <i class="fas fa-circle me-1"></i>
                                        <i class="fas fa-circle me-1"></i>
                                        Jane is typing...
                                    </div>
                                </div>

                                <!-- Chat Input -->
                                <div class="chat-input">
                                    <form method="POST" class="d-flex gap-3">
                                        <div class="flex-grow-1">
                                            <input type="text" class="form-control message-input" name="message" 
                                                   placeholder="Type your message..." required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-custom">
                                            <i class="fas fa-paper-plane me-1"></i>Send
                                        </button>
                                        <button type="button" class="btn btn-outline-primary btn-custom">
                                            <i class="fas fa-paperclip"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-scroll to bottom of chat
        function scrollToBottom() {
            const chatMessages = document.querySelector('.chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        // Scroll to bottom on page load
        document.addEventListener('DOMContentLoaded', function() {
            scrollToBottom();
        });

        // Conversation switching
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.addEventListener('click', function() {
                // Remove active class from all items
                document.querySelectorAll('.conversation-item').forEach(i => i.classList.remove('active'));
                // Add active class to clicked item
                this.classList.add('active');
                
                // In a real application, this would load the conversation
                // For now, we'll just show a message
                console.log('Switching conversation...');
            });
        });

        // Auto-resize textarea (if using textarea instead of input)
        const messageInput = document.querySelector('.message-input');
        if (messageInput) {
            messageInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
        }

        // Simulate typing indicator
        setTimeout(() => {
            const typingIndicator = document.querySelector('.typing-indicator');
            if (typingIndicator) {
                typingIndicator.style.display = 'none';
            }
        }, 3000);
    </script>
</body>
</html> 