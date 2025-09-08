-- Event Management System Database Setup
-- Complete and Functional Database Schema

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
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    availability BOOLEAN DEFAULT TRUE,
    location VARCHAR(255),
    bio TEXT,
    services_offered TEXT,
    portfolio TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Event Categories
CREATE TABLE event_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(100)
);

-- Insert default event categories
INSERT INTO event_categories (category_name, description, icon) VALUES
('Wedding', 'Wedding ceremonies and receptions', 'fas fa-heart'),
('Birthday', 'Birthday celebrations', 'fas fa-birthday-cake'),
('Corporate', 'Business meetings and corporate events', 'fas fa-briefcase'),
('Anniversary', 'Anniversary celebrations', 'fas fa-calendar-heart'),
('Conference', 'Professional conferences and seminars', 'fas fa-users'),
('Other', 'Other types of events', 'fas fa-calendar');

-- Events table
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    venue VARCHAR(200),
    venue_address TEXT,
    budget DECIMAL(10,2),
    guest_count INT,
    event_type ENUM('wedding', 'birthday', 'corporate', 'anniversary', 'conference', 'other') NOT NULL,
    status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    client_id INT NOT NULL,
    planner_id INT,
    category_id INT,
    duration INT,
    special_requirements TEXT,
    theme VARCHAR(255),
    color_scheme VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (planner_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES event_categories(id)
);

-- Event Services
CREATE TABLE event_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_name VARCHAR(100) NOT NULL,
    description TEXT,
    category_id INT,
    price_range VARCHAR(100),
    FOREIGN KEY (category_id) REFERENCES event_categories(id)
);

-- Insert default event services
INSERT INTO event_services (service_name, description, category_id, price_range) VALUES
('Venue Decoration', 'Complete venue decoration and setup', 1, '500-5000'),
('Catering Services', 'Food and beverage services', 1, '1000-10000'),
('Photography & Videography', 'Professional photo and video coverage', 1, '500-3000'),
('Music & Entertainment', 'DJ, live bands, or other entertainment', 1, '300-2000'),
('Event Coordination', 'Full event planning and coordination', 1, '1000-5000');

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
    event_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Notifications table
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    event_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Payments table
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    transaction_id VARCHAR(255) NOT NULL,
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL,
    payment_method VARCHAR(50),
    notes VARCHAR(255),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Event Tasks table
CREATE TABLE event_tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    task_name VARCHAR(255) NOT NULL,
    description TEXT,
    assigned_to INT,
    due_date DATE,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- Event Gallery table
CREATE TABLE event_gallery (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    caption VARCHAR(255),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Create indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_user_type ON users(user_type);
CREATE INDEX idx_events_date ON events(event_date);
CREATE INDEX idx_events_status ON events(status);
CREATE INDEX idx_events_client ON events(client_id);
CREATE INDEX idx_events_planner ON events(planner_id);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_messages_users ON messages(sender_id, receiver_id);
CREATE INDEX idx_notifications_user ON notifications(user_id);
CREATE INDEX idx_planners_user ON planners(user_id);
CREATE INDEX idx_planners_approval ON planners(approval_status);

-- Insert default admin user (password: password)
INSERT INTO users (username, email, password, full_name, user_type, status) VALUES
('admin', 'admin@ems.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 'active');

-- Kerala-based sample planners (password: password)
INSERT INTO users (username, email, password, full_name, user_type, status, address) VALUES
('planner_kochi', 'kochi.planner@ems.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kochi Events Pro', 'planner', 'active', 'Kochi, Kerala'),
('planner_tvm', 'tvm.planner@ems.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trivandrum Elite Planners', 'planner', 'active', 'Thiruvananthapuram, Kerala'),
('planner_calicut', 'calicut.planner@ems.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Calicut Grand Events', 'planner', 'active', 'Kozhikode, Kerala'),
('planner_kannur', 'kannur.planner@ems.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kannur Celebration Co', 'planner', 'active', 'Kannur, Kerala'),
('planner_kollam', 'kollam.planner@ems.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kollam Royal Events', 'planner', 'active', 'Kollam, Kerala');

-- Create planner profiles for Kerala planners (approved & available)
INSERT INTO planners (user_id, company_name, specialization, experience_years, approval_status, availability, location, hourly_rate, rating, total_reviews) 
SELECT id, 'Kochi Event Studio', 'Weddings, Corporate Events', 7, 'approved', TRUE, 'Kochi, Kerala', 45.00, 4.7, 85 FROM users WHERE username = 'planner_kochi';
INSERT INTO planners (user_id, company_name, specialization, experience_years, approval_status, availability, location, hourly_rate, rating, total_reviews) 
SELECT id, 'Trivandrum Elite Events', 'Weddings, Conferences', 8, 'approved', TRUE, 'Thiruvananthapuram, Kerala', 50.00, 4.8, 102 FROM users WHERE username = 'planner_tvm';
INSERT INTO planners (user_id, company_name, specialization, experience_years, approval_status, availability, location, hourly_rate, rating, total_reviews) 
SELECT id, 'Calicut Grandeur Planners', 'Birthdays, Anniversaries', 5, 'approved', TRUE, 'Kozhikode, Kerala', 35.00, 4.5, 60 FROM users WHERE username = 'planner_calicut';
INSERT INTO planners (user_id, company_name, specialization, experience_years, approval_status, availability, location, hourly_rate, rating, total_reviews) 
SELECT id, 'Kannur Celebrations', 'Corporate, Weddings', 6, 'approved', TRUE, 'Kannur, Kerala', 40.00, 4.6, 74 FROM users WHERE username = 'planner_kannur';
INSERT INTO planners (user_id, company_name, specialization, experience_years, approval_status, availability, location, hourly_rate, rating, total_reviews) 
SELECT id, 'Kollam Royale Events', 'Weddings, Cultural Events', 9, 'approved', TRUE, 'Kollam, Kerala', 55.00, 4.9, 120 FROM users WHERE username = 'planner_kollam';

-- Insert sample client for testing
INSERT INTO users (username, email, password, full_name, user_type, status) VALUES
('client1', 'client@ems.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sample Client', 'client', 'active'); 