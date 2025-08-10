-- Event Management System Database Setup
-- College Project - Simplified but Functional

-- Create database
CREATE DATABASE IF NOT EXISTS event_management_system;
USE event_management_system;

-- Users table (for all user types)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    user_type ENUM('admin', 'planner', 'client') NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    profile_image VARCHAR(255),
    status ENUM('active', 'inactive', 'pending') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Event Planners additional info
CREATE TABLE planners (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    company_name VARCHAR(100),
    specialization TEXT,
    experience_years INT,
    portfolio_description TEXT,
    hourly_rate DECIMAL(10,2),
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    is_verified BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Events table
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    venue VARCHAR(200),
    budget DECIMAL(10,2),
    guest_count INT,
    event_type ENUM('wedding', 'birthday', 'corporate', 'anniversary', 'conference', 'other') NOT NULL,
    status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    client_id INT NOT NULL,
    planner_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (planner_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Bookings table
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    client_id INT NOT NULL,
    planner_id INT NOT NULL,
    booking_date DATE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'paid', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (planner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Reviews table
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    client_id INT NOT NULL,
    planner_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (planner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Messages table
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Notifications table
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Settings table for system configuration
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'number', 'boolean', 'email', 'select') DEFAULT 'text',
    setting_group ENUM('general', 'email', 'notifications', 'system') DEFAULT 'general',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, setting_type, setting_group, description) VALUES
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

-- Insert sample admin user
INSERT INTO users (username, email, password, full_name, user_type, status) VALUES
('admin', 'admin@ems.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 'active');

-- Insert sample event planners
INSERT INTO users (username, email, password, full_name, user_type, phone, status) VALUES
('jane_smith', 'jane@elegantevents.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane Smith', 'planner', '+1234567890', 'active'),
('mike_johnson', 'mike@corporateevents.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike Johnson', 'planner', '+1234567891', 'active'),
('sarah_wilson', 'sarah@partyplanners.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah Wilson', 'planner', '+1234567892', 'active');

INSERT INTO planners (user_id, company_name, specialization, experience_years, hourly_rate, rating, total_reviews) VALUES
(2, 'Elegant Events Co.', 'Weddings, Corporate Events', 8, 75.00, 4.8, 45),
(3, 'Corporate Events Pro', 'Corporate Events, Conferences', 12, 85.00, 4.7, 38),
(4, 'Party Planners Plus', 'Birthdays, Anniversaries, Parties', 6, 60.00, 4.6, 29);

-- Insert sample clients
INSERT INTO users (username, email, password, full_name, user_type, phone, status) VALUES
('john_doe', 'john@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'client', '+1234567893', 'active'),
('emma_davis', 'emma@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Emma Davis', 'client', '+1234567894', 'active'),
('robert_smith', 'robert@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Robert Smith', 'client', '+1234567895', 'active');

-- Insert sample events
INSERT INTO events (title, description, event_date, event_time, venue, budget, guest_count, event_type, status, client_id, planner_id) VALUES
('Sarah & Mike Wedding', 'Beautiful wedding ceremony and reception', '2024-04-15', '14:00:00', 'Grand Plaza Hotel', 15000.00, 150, 'wedding', 'confirmed', 5, 2),
('Tech Conference 2024', 'Annual technology conference with workshops', '2024-04-20', '09:00:00', 'Convention Center', 25000.00, 200, 'corporate', 'in_progress', 6, 3),
('Birthday Celebration', 'Emma\'s 25th birthday party', '2024-04-18', '18:00:00', 'Community Hall', 5000.00, 50, 'birthday', 'completed', 6, 4),
('Anniversary Party', 'Robert & Lisa 10th anniversary', '2024-04-25', '19:00:00', 'Private Garden', 12000.00, 80, 'anniversary', 'pending', 7, 2);

-- Insert sample bookings
INSERT INTO bookings (event_id, client_id, planner_id, booking_date, amount, status) VALUES
(1, 5, 2, '2024-03-01', 15000.00, 'paid'),
(2, 6, 3, '2024-03-05', 25000.00, 'paid'),
(3, 6, 4, '2024-03-10', 5000.00, 'paid'),
(4, 7, 2, '2024-03-15', 12000.00, 'pending');

-- Insert sample reviews
INSERT INTO reviews (event_id, client_id, planner_id, rating, comment) VALUES
(1, 5, 2, 5, 'Excellent service! Jane made our wedding perfect.'),
(2, 6, 3, 4, 'Great organization and professional service.'),
(3, 6, 4, 5, 'Amazing birthday party! Sarah is fantastic.');

-- Insert sample messages
INSERT INTO messages (sender_id, receiver_id, message) VALUES
(5, 2, 'Hi Jane, I have some questions about the wedding timeline.'),
(2, 5, 'Hi John! Sure, I\'d be happy to discuss the timeline.'),
(6, 3, 'Mike, can we schedule a meeting to discuss the conference details?'),
(3, 6, 'Absolutely Emma! When would be convenient for you?');

-- Insert sample notifications
INSERT INTO notifications (user_id, title, message, type) VALUES
(5, 'Event Confirmed', 'Your wedding event has been confirmed!', 'success'),
(6, 'Event Update', 'Your tech conference is now in progress.', 'info'),
(7, 'Payment Required', 'Please complete payment for your anniversary party.', 'warning');

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_events_date ON events(event_date);
CREATE INDEX idx_events_status ON events(status);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_messages_users ON messages(sender_id, receiver_id);
CREATE INDEX idx_notifications_user ON notifications(user_id); 