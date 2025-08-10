<?php
/**
 * Settings Table Installation Script
 * Run this file once to create the settings table and insert default values
 */

require_once 'includes/db.php';

echo "<h2>Settings Table Installation</h2>";

try {
    // Create settings table
    $sql = "CREATE TABLE IF NOT EXISTS settings (
        id INT PRIMARY KEY AUTO_INCREMENT,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT,
        setting_type ENUM('text', 'number', 'boolean', 'email', 'select') DEFAULT 'text',
        setting_group ENUM('general', 'email', 'notifications', 'system') DEFAULT 'general',
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>✓ Settings table created successfully</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating settings table: " . $conn->error . "</p>";
    }
    
    // Insert default settings
    $default_settings = [
        // General Settings
        ['site_name', 'Event Management System', 'text', 'general', 'Website name displayed throughout the system'],
        ['site_description', 'Professional event management system for planning and organizing events', 'text', 'general', 'Website description for SEO and display'],
        ['contact_email', 'admin@ems.com', 'email', 'general', 'Primary contact email address'],
        ['contact_phone', '+1 (555) 123-4567', 'text', 'general', 'Primary contact phone number'],
        ['timezone', 'UTC', 'select', 'general', 'Default system timezone'],
        
        // Email Settings
        ['smtp_host', 'smtp.gmail.com', 'text', 'email', 'SMTP server hostname'],
        ['smtp_port', '587', 'number', 'email', 'SMTP server port'],
        ['smtp_username', 'noreply@ems.com', 'email', 'email', 'SMTP username for authentication'],
        ['smtp_password', '', 'text', 'email', 'SMTP password for authentication'],
        ['from_email', 'noreply@ems.com', 'email', 'email', 'Default sender email address'],
        
        // Notification Settings
        ['email_notifications', '1', 'boolean', 'notifications', 'Enable email notifications globally'],
        ['new_user_notifications', '1', 'boolean', 'notifications', 'Notify admins of new user registrations'],
        ['event_notifications', '1', 'boolean', 'notifications', 'Notify users of event status changes'],
        ['planner_notifications', '1', 'boolean', 'notifications', 'Notify admins of planner approval requests'],
        ['sms_notifications', '0', 'boolean', 'notifications', 'Enable SMS notifications'],
        ['push_notifications', '0', 'boolean', 'notifications', 'Enable push notifications'],
        
        // System Settings
        ['maintenance_mode', '0', 'boolean', 'system', 'Enable maintenance mode for all users'],
        ['max_file_upload_size', '10', 'number', 'system', 'Maximum file upload size in MB'],
        ['session_timeout', '3600', 'number', 'system', 'User session timeout in seconds'],
        ['enable_registration', '1', 'boolean', 'system', 'Allow new user registrations'],
        ['enable_guest_events', '0', 'boolean', 'system', 'Allow events to be viewed without login']
    ];
    
    $inserted = 0;
    $updated = 0;
    
    foreach ($default_settings as $setting) {
        $key = $setting[0];
        $value = $setting[1];
        $type = $setting[2];
        $group = $setting[3];
        $description = $setting[4];
        
        // Check if setting already exists
        $check_sql = "SELECT id FROM settings WHERE setting_key = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $key);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing setting
            $update_sql = "UPDATE settings SET setting_value = ?, setting_type = ?, setting_group = ?, description = ?, updated_at = CURRENT_TIMESTAMP WHERE setting_key = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("sssss", $value, $type, $group, $description, $key);
            if ($update_stmt->execute()) {
                $updated++;
            }
        } else {
            // Insert new setting
            $insert_sql = "INSERT INTO settings (setting_key, setting_value, setting_type, setting_group, description) VALUES (?, ?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("sssss", $key, $value, $type, $group, $description);
            if ($insert_stmt->execute()) {
                $inserted++;
            }
        }
    }
    
    echo "<p style='color: green;'>✓ Settings configured successfully</p>";
    echo "<p>Inserted: $inserted new settings</p>";
    echo "<p>Updated: $updated existing settings</p>";
    
    // Display current settings
    echo "<h3>Current Settings:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Key</th><th>Value</th><th>Type</th><th>Group</th></tr>";
    
    $result = $conn->query("SELECT setting_key, setting_value, setting_type, setting_group FROM settings ORDER BY setting_group, setting_key");
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['setting_key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['setting_value']) . "</td>";
        echo "<td>" . htmlspecialchars($row['setting_type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['setting_group']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><a href='admin/settings.php'>Go to Admin Settings</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

$conn->close();
?>
