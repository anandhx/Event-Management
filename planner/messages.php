<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'planner') {
    header('Location: ../login.php');
    exit();
}

$plannerId = (int)$_SESSION['user_id'];

// Fetch recent conversations (distinct clients who messaged with planner)
$conversations = [];
$sql = "SELECT u.id AS client_id, u.full_name AS client_name,
               MAX(m.created_at) AS last_time,
               SUBSTRING_INDEX(GROUP_CONCAT(m.message ORDER BY m.created_at DESC SEPARATOR '\n'), '\n', 1) AS last_message
        FROM messages m
        JOIN users u ON (CASE WHEN m.sender_id = ? THEN m.receiver_id ELSE m.sender_id END) = u.id
        WHERE (m.sender_id = ? OR m.receiver_id = ?)
        GROUP BY u.id, u.full_name
        ORDER BY last_time DESC
        LIMIT 20";
$stmt = $conn->prepare($sql);
if ($stmt) { $stmt->bind_param('iii', $plannerId, $plannerId, $plannerId); $stmt->execute(); $res = $stmt->get_result(); while ($row = $res->fetch_assoc()) { $conversations[] = $row; } }

$activeClientId = isset($_GET['client']) ? (int)$_GET['client'] : ( ($conversations[0]['client_id'] ?? 0) );
$messages = [];
if ($activeClientId) {
    $stmt = $conn->prepare("SELECT m.*, u.full_name AS sender_name FROM messages m JOIN users u ON u.id = m.sender_id WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?) ORDER BY m.created_at ASC");
    if ($stmt) { $stmt->bind_param('iiii', $plannerId, $activeClientId, $activeClientId, $plannerId); $stmt->execute(); $res = $stmt->get_result(); while ($row = $res->fetch_assoc()) { $messages[] = $row; } }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Planner</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar { position: sticky; top:0; height:100vh; overflow-y:auto; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding:12px 20px; margin:5px 0; border-radius:10px; transition:all .3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color:#fff; background:rgba(255,255,255,.1); transform: translateX(5px); }
        .main-content { background:#f8f9fa; min-height:100vh; }
        .messages-container { background:#fff; border-radius:20px; box-shadow:0 10px 30px rgba(0,0,0,0.08); height: 75vh; }
        .conversation-list { border-right: 2px solid #e9ecef; height:100%; overflow-y:auto; }
        .chat-area { height:100%; display:flex; flex-direction:column; }
        .chat-header { padding: 16px 20px; border-bottom:2px solid #e9ecef; background:#f8f9fa; }
        .chat-messages { flex:1; padding:20px; overflow-y:auto; background:#f8f9fa; }
        .chat-input { padding:16px 20px; border-top:2px solid #e9ecef; background:#fff; }
        .planner-avatar { width:40px; height:40px; border-radius:50%; object-fit:cover; }
        .message-bubble { margin-bottom:12px; max-width:70%; }
        .message-bubble.self { margin-left:auto; }
        .message-content { padding:12px 16px; border-radius:18px; }
        .message-bubble.self .message-content { background:#667eea; color:#fff; }
        .message-bubble.other .message-content { background:#fff; border:1px solid #e9ecef; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white"><i class="fas fa-user-tie me-2"></i>Planner</h4>
                        <p class="text-white-50 small">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></p>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="planner_index.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                        <a class="nav-link" href="portfolio.php"><i class="fas fa-briefcase me-2"></i>Portfolio</a>
                        <a class="nav-link" href="my_events.php"><i class="fas fa-calendar me-2"></i>My Events</a>
                        <a class="nav-link active" href="messages.php"><i class="fas fa-envelope me-2"></i>Messages</a>
                        <a class="nav-link" href="notifications.php"><i class="fas fa-bell me-2"></i>Notifications</a>
                        <a class="nav-link" href="profile_management.php"><i class="fas fa-user-cog me-2"></i>Profile</a>
                        <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
                    </nav>
                </div>
            </div>
            <div class="col-md-9 col-lg-10 px-0">
                <div class="main-content p-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="messages-container">
                                <div class="row h-100 g-0">
                                    <div class="col-md-4">
                                        <div class="conversation-list">
                                            <div class="p-3 border-bottom"><h5 class="fw-bold mb-0"><i class="fas fa-comments me-2"></i>Conversations</h5></div>
                                            <?php foreach ($conversations as $c): ?>
                                                <a class="d-block p-3 border-bottom text-decoration-none <?php echo ($c['client_id'] == $activeClientId ? 'bg-light' : ''); ?>" href="?client=<?php echo (int)$c['client_id']; ?>">
                                                    <div class="d-flex align-items-center">
                                                        <div class="planner-avatar bg-primary me-3"></div>
                                                        <div class="flex-grow-1">
                                                            <strong class="d-block"><?php echo htmlspecialchars($c['client_name']); ?></strong>
                                                            <small class="text-muted"><?php echo htmlspecialchars(mb_strimwidth($c['last_message'] ?? '', 0, 48, '...')); ?></small>
                                                        </div>
                                                    </div>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="chat-area">
                                            <div class="chat-header">
                                                <h6 class="mb-0">Chat with <?php echo htmlspecialchars(($conversations[array_search($activeClientId, array_column($conversations,'client_id'))]['client_name'] ?? 'Client')); ?></h6>
                                            </div>
                                            <div class="chat-messages">
                                                <?php foreach ($messages as $m): $self = $m['sender_id'] == $plannerId; ?>
                                                    <div class="message-bubble <?php echo $self ? 'self' : 'other'; ?>">
                                                        <div class="message-content">
                                                            <p class="mb-1"><?php echo htmlspecialchars($m['message']); ?></p>
                                                            <small class="text-muted"><?php echo htmlspecialchars($m['created_at']); ?></small>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <div class="chat-input">
                                                <form action="../user/send_message.php" method="POST" class="d-flex gap-2">
                                                    <input type="hidden" name="sender_id" value="<?php echo $plannerId; ?>">
                                                    <input type="hidden" name="receiver_id" value="<?php echo (int)$activeClientId; ?>">
                                                    <input type="text" class="form-control" name="message" placeholder="Type your message..." required>
                                                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i>Send</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
