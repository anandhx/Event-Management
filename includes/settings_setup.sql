-- Settings table setup for Event Management System
-- Run this script to create the settings table and insert default values

-- Create settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'number', 'boolean', 'email', 'select') DEFAULT 'text',
    setting_group ENUM('general', 'email', 'notifications', 'system') DEFAULT 'general',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings (only if they don't exist)
INSERT IGNORE INTO settings (setting_key, setting_value, setting_type, setting_group, description) VALUES
-- General Settings
('site_name', 'Event Management System', 'text', 'general', 'Website name displayed throughout the system'),
('site_description', 'Professional event management system for planning and organizing events', 'text', 'general', 'Website description for SEO and display'),
('contact_email', 'admin@ems.com', 'email', 'general', 'Primary contact email address'),
('contact_phone', '+1 (555) 123-4567', 'text', 'general', 'Primary contact phone number'),
('timezone', 'UTC', 'select', 'general', 'Default system timezone'),

-- Email Settings
('smtp_host', 'smtp.gmail.com', 'text', 'email', 'SMTP server hostname'),
('smtp_port', '587', 'number', 'email', 'SMTP server port'),
('smtp_username', 'noreply@ems.com', 'email', 'email', 'SMTP username for authentication'),
('smtp_password', '', 'text', 'email', 'SMTP password for authentication'),
('from_email', 'noreply@ems.com', 'email', 'email', 'Default sender email address'),

-- Notification Settings
('email_notifications', '1', 'boolean', 'notifications', 'Enable email notifications globally'),
('new_user_notifications', '1', 'boolean', 'notifications', 'Notify admins of new user registrations'),
('event_notifications', '1', 'boolean', 'notifications', 'Notify users of event status changes'),
('planner_notifications', '1', 'boolean', 'notifications', 'Notify admins of planner approval requests'),
('sms_notifications', '0', 'boolean', 'notifications', 'Enable SMS notifications'),
('push_notifications', '0', 'boolean', 'notifications', 'Enable push notifications'),

-- System Settings
('maintenance_mode', '0', 'boolean', 'system', 'Enable maintenance mode for all users'),
('max_file_upload_size', '10', 'number', 'system', 'Maximum file upload size in MB'),
('session_timeout', '3600', 'number', 'system', 'User session timeout in seconds'),
('enable_registration', '1', 'boolean', 'system', 'Allow new user registrations'),
('enable_guest_events', '0', 'boolean', 'system', 'Allow events to be viewed without login');

-- Display current settings
SELECT * FROM settings ORDER BY setting_group, setting_key;
