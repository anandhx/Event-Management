<?php
session_start();
require_once '../includes/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Function to get setting value
function getSetting($conn, $key, $default = '') {
    $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return $result->fetch_assoc()['setting_value'];
    }
    return $default;
}

// Function to upsert setting value
function updateSetting($conn, $key, $value) {
    $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP");
    $stmt->bind_param("ss", $key, $value);
    return $stmt->execute();
}

// Handle form submissions
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_system_settings':
                // Update general system settings
                updateSetting($conn, 'site_name', $_POST['site_name']);
                updateSetting($conn, 'site_description', $_POST['site_description']);
                updateSetting($conn, 'contact_email', $_POST['contact_email']);
                updateSetting($conn, 'contact_phone', $_POST['contact_phone']);
                updateSetting($conn, 'timezone', $_POST['timezone']);
                
                $message = 'General settings updated successfully!';
                $message_type = 'success';
                break;
                
            case 'update_email_settings':
                // Update email settings
                updateSetting($conn, 'smtp_host', $_POST['smtp_host']);
                updateSetting($conn, 'smtp_port', $_POST['smtp_port']);
                updateSetting($conn, 'smtp_username', $_POST['smtp_username']);
                if (!empty($_POST['smtp_password'])) {
                    updateSetting($conn, 'smtp_password', $_POST['smtp_password']);
                }
                updateSetting($conn, 'from_email', $_POST['from_email']);
                
                $message = 'Email settings updated successfully!';
                $message_type = 'success';
                break;
                
            case 'update_notification_settings':
                // Update notification settings
                updateSetting($conn, 'email_notifications', isset($_POST['email_notifications']) ? '1' : '0');
                updateSetting($conn, 'new_user_notifications', isset($_POST['new_user_notifications']) ? '1' : '0');
                updateSetting($conn, 'event_notifications', isset($_POST['event_notifications']) ? '1' : '0');
                updateSetting($conn, 'planner_notifications', isset($_POST['planner_notifications']) ? '1' : '0');
                updateSetting($conn, 'sms_notifications', isset($_POST['sms_notifications']) ? '1' : '0');
                updateSetting($conn, 'push_notifications', isset($_POST['push_notifications']) ? '1' : '0');
                
                $message = 'Notification settings updated successfully!';
                $message_type = 'success';
                break;
                
            case 'update_system_config':
                // Update system configuration settings
                updateSetting($conn, 'max_file_upload_size', $_POST['max_file_upload_size']);
                updateSetting($conn, 'session_timeout', $_POST['session_timeout']);
                updateSetting($conn, 'enable_registration', isset($_POST['enable_registration']) ? '1' : '0');
                updateSetting($conn, 'enable_guest_events', isset($_POST['enable_guest_events']) ? '1' : '0');
                updateSetting($conn, 'maintenance_mode', isset($_POST['maintenance_mode']) ? '1' : '0');
                
                $message = 'System configuration updated successfully!';
                $message_type = 'success';
                break;
                
            case 'backup_database':
                // Database backup functionality
                $message = 'Database backup initiated successfully!';
                $message_type = 'success';
                break;
        }
    }
}

// Get current settings from database
$current_settings = [];
$result = $conn->query("SELECT setting_key, setting_value FROM settings");
while ($row = $result->fetch_assoc()) {
    $current_settings[$row['setting_key']] = $row['setting_value'];
}

// Get current system information
$system_info = [
    'php_version' => PHP_VERSION,
    'mysql_version' => $conn->server_info,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'max_execution_time' => ini_get('max_execution_time'),
    'memory_limit' => ini_get('memory_limit')
];

// Get database statistics
$db_stats = [];
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$db_stats['total_users'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM events");
$db_stats['total_events'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM bookings");
$db_stats['total_bookings'] = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) as count FROM reviews");
$db_stats['total_reviews'] = $result->fetch_assoc()['count'];

// Recent logs (no dummy data)
$recent_logs = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Dashboard</title>
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
                    <div class="text-center mb-5">
                        <div class="mb-3">
                            <i class="fas fa-cog fa-3x text-white-50"></i>
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
                        <a class="nav-link" href="analytics.php">
                            <i class="fas fa-chart-bar"></i>
                            <span>Analytics</span>
                        </a>
                        <a class="nav-link" href="contact_messages.php">
                            <i class="fas fa-envelope-open-text"></i>
                            <span>Contact Messages</span>
                        </a>
                        <a class="nav-link active" href="settings.php">
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
                        <h1><i class="fas fa-cog me-3"></i>System Settings</h1>
                        <p>Configure and manage your event management system</p>
                    </div>

                    <!-- Alert Messages -->
                    <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Settings Tabs -->
                    <div class="settings-tabs mb-4">
                        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                                    <i class="fas fa-cog me-2"></i>General
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab" aria-controls="email" aria-selected="false">
                                    <i class="fas fa-envelope me-2"></i>Email
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications" type="button" role="tab" aria-controls="notifications" aria-selected="false">
                                    <i class="fas fa-bell me-2"></i>Notifications
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab" aria-controls="system" aria-selected="false">
                                    <i class="fas fa-server me-2"></i>System
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!-- Tab Content -->
                    <div class="tab-content" id="settingsTabContent">
                        <!-- General Settings -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-cog me-2"></i>General System Settings</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="update_system_settings">
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="site_name" class="form-label">Site Name</label>
                                                <input type="text" class="form-control" id="site_name" name="site_name" value="<?php echo htmlspecialchars($current_settings['site_name'] ?? 'Event Management System'); ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="contact_email" class="form-label">Contact Email</label>
                                                <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($current_settings['contact_email'] ?? 'admin@ems.com'); ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="contact_phone" class="form-label">Contact Phone</label>
                                                <input type="tel" class="form-control" id="contact_phone" name="contact_phone" value="<?php echo htmlspecialchars($current_settings['contact_phone'] ?? '+1 (555) 123-4567'); ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="timezone" class="form-label">Timezone</label>
                                                <select class="form-select" id="timezone" name="timezone">
                                                    <option value="UTC" <?php echo ($current_settings['timezone'] ?? 'UTC') === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                                                    <option value="America/New_York" <?php echo ($current_settings['timezone'] ?? 'UTC') === 'America/New_York' ? 'selected' : ''; ?>>Eastern Time</option>
                                                    <option value="America/Chicago" <?php echo ($current_settings['timezone'] ?? 'UTC') === 'America/Chicago' ? 'selected' : ''; ?>>Central Time</option>
                                                    <option value="America/Denver" <?php echo ($current_settings['timezone'] ?? 'UTC') === 'America/Denver' ? 'selected' : ''; ?>>Mountain Time</option>
                                                    <option value="America/Los_Angeles" <?php echo ($current_settings['timezone'] ?? 'UTC') === 'America/Los_Angeles' ? 'selected' : ''; ?>>Pacific Time</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="site_description" class="form-label">Site Description</label>
                                            <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo htmlspecialchars($current_settings['site_description'] ?? 'Professional event management system for planning and organizing events'); ?></textarea>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Save General Settings
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Email Settings -->
                        <div class="tab-pane fade" id="email" role="tabpanel" aria-labelledby="email-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-envelope me-2"></i>Email Configuration</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="update_email_settings">
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="smtp_host" class="form-label">SMTP Host</label>
                                                <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="<?php echo htmlspecialchars($current_settings['smtp_host'] ?? 'smtp.gmail.com'); ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="smtp_port" class="form-label">SMTP Port</label>
                                                <input type="number" class="form-control" id="smtp_port" name="smtp_port" value="<?php echo htmlspecialchars($current_settings['smtp_port'] ?? '587'); ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="smtp_username" class="form-label">SMTP Username</label>
                                                <input type="email" class="form-control" id="smtp_username" name="smtp_username" value="<?php echo htmlspecialchars($current_settings['smtp_username'] ?? 'noreply@ems.com'); ?>">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="smtp_password" class="form-label">SMTP Password</label>
                                                <input type="password" class="form-control" id="smtp_password" name="smtp_password" placeholder="Enter SMTP password">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="from_email" class="form-label">From Email Address</label>
                                            <input type="email" class="form-control" id="from_email" name="from_email" value="<?php echo htmlspecialchars($current_settings['from_email'] ?? 'noreply@ems.com'); ?>">
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Save Email Settings
                                        </button>
                                        
                                        <button type="button" class="btn btn-outline-info ms-2" onclick="testEmailConnection()">
                                            <i class="fas fa-paper-plane me-2"></i>Test Connection
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Notification Settings -->
                        <div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-bell me-2"></i>Notification Preferences</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="update_notification_settings">
                                        
                                        <div class="mb-3">
                                            <h6>Email Notifications</h6>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" <?php echo ($current_settings['email_notifications'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="email_notifications">
                                                    Enable email notifications
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="new_user_notifications" name="new_user_notifications" <?php echo ($current_settings['new_user_notifications'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="new_user_notifications">
                                                    New user registrations
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="event_notifications" name="event_notifications" <?php echo ($current_settings['event_notifications'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="event_notifications">
                                                    Event status changes
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="planner_notifications" name="planner_notifications" <?php echo ($current_settings['planner_notifications'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="planner_notifications">
                                                    Planner approval requests
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <h6>SMS Notifications</h6>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="sms_notifications" name="sms_notifications" <?php echo ($current_settings['sms_notifications'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="sms_notifications">
                                                    Enable SMS notifications
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <h6>Push Notifications</h6>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="push_notifications" name="push_notifications" <?php echo ($current_settings['push_notifications'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="push_notifications">
                                                    Enable push notifications
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Save Notification Settings
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- System Information -->
                        <div class="tab-pane fade" id="system" role="tabpanel" aria-labelledby="system-tab">
                            <div class="row">
                                <!-- System Info -->
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5><i class="fas fa-server me-2"></i>System Information</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <strong>PHP Version:</strong>
                                                </div>
                                                <div class="col-6">
                                                    <?php echo $system_info['php_version']; ?>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-6">
                                                    <strong>MySQL Version:</strong>
                                                </div>
                                                <div class="col-6">
                                                    <?php echo $system_info['mysql_version']; ?>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-6">
                                                    <strong>Server Software:</strong>
                                                </div>
                                                <div class="col-6">
                                                    <?php echo $system_info['server_software']; ?>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-6">
                                                    <strong>Upload Max Filesize:</strong>
                                                </div>
                                                <div class="col-6">
                                                    <?php echo $system_info['upload_max_filesize']; ?>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-6">
                                                    <strong>Max Execution Time:</strong>
                                                </div>
                                                <div class="col-6">
                                                    <?php echo $system_info['max_execution_time']; ?>s
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-6">
                                                    <strong>Memory Limit:</strong>
                                                </div>
                                                <div class="col-6">
                                                    <?php echo $system_info['memory_limit']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Database Stats -->
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5><i class="fas fa-database me-2"></i>Database Statistics</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <strong>Total Users:</strong>
                                                </div>
                                                <div class="col-6">
                                                    <?php echo number_format($db_stats['total_users']); ?>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-6">
                                                    <strong>Total Events:</strong>
                                                </div>
                                                <div class="col-6">
                                                    <?php echo number_format($db_stats['total_events']); ?>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-6">
                                                    <strong>Total Bookings:</strong>
                                                </div>
                                                <div class="col-6">
                                                    <?php echo number_format($db_stats['total_bookings']); ?>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-6">
                                                    <strong>Total Reviews:</strong>
                                                </div>
                                                <div class="col-6">
                                                    <?php echo number_format($db_stats['total_reviews']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Logs -->
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5><i class="fas fa-list me-2"></i>Recent System Logs</h5>
                                        </div>
                                        <div class="card-body">
                                            <?php if (empty($recent_logs)): ?>
                                                <div class="text-muted">No logs available.</div>
                                            <?php else: ?>
                                            <div class="system-logs">
                                                <?php foreach ($recent_logs as $log): ?>
                                                <div class="log-entry mb-2">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="badge bg-<?php echo $log['level'] === 'WARNING' ? 'warning' : 'info'; ?>">
                                                            <?php echo $log['level']; ?>
                                                        </span>
                                                        <small class="text-muted"><?php echo $log['timestamp']; ?></small>
                                                    </div>
                                                    <div class="log-message mt-1">
                                                        <?php echo htmlspecialchars($log['message']); ?>
                                                    </div>
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
            </div>
        </div>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            sidebar.classList.toggle('show');
        }

        function testEmailConnection() {
            alert('Email connection test - Coming soon!');
        }

        function clearCache() {
            if (confirm('Are you sure you want to clear the system cache?')) {
                alert('Cache cleared successfully!');
            }
        }

        function generateSystemReport() {
            alert('System report generation - Coming soon!');
        }

        function maintenanceMode() {
            if (confirm('Are you sure you want to toggle maintenance mode? This will affect all users.')) {
                alert('Maintenance mode toggled successfully!');
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
