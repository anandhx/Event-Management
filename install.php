<?php
// Event Management System Installation Script
// Run this file once to set up your database

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";

echo "<h2>Event Management System Installation</h2>";

try {
    // Create connection without database
    $conn = new mysqli($servername, $username, $password);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "<p>✓ Connected to MySQL server successfully</p>";
    
    // Read and execute the database setup SQL
    $sql_file = file_get_contents('database_setup.sql');
    
    if ($sql_file === false) {
        die("Error: Could not read database_setup.sql file");
    }
    
    // Split SQL into individual statements
    $statements = explode(';', $sql_file);
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                if ($conn->query($statement)) {
                    $success_count++;
                } else {
                    $error_count++;
                    echo "<p style='color: red;'>Error executing: " . substr($statement, 0, 50) . "...</p>";
                }
            } catch (Exception $e) {
                $error_count++;
                echo "<p style='color: red;'>Exception: " . $e->getMessage() . "</p>";
            }
        }
    }
    
    echo "<p>✓ Database setup completed!</p>";
    echo "<p>Successfully executed: $success_count statements</p>";
    if ($error_count > 0) {
        echo "<p style='color: orange;'>Errors encountered: $error_count statements</p>";
    }
    
    // Test the new database connection
    $conn->select_db('event_management_system');
    
    // Test some basic queries
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>✓ Database test successful! Found " . $row['count'] . " users</p>";
    }
    
    $result = $conn->query("SELECT COUNT(*) as count FROM event_categories");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>✓ Found " . $row['count'] . " event categories</p>";
    }
    
    echo "<h3>Default Login Credentials:</h3>";
    echo "<p><strong>Admin:</strong> username: admin, password: password</p>";
    echo "<p><strong>Planner:</strong> username: planner1, password: password</p>";
    echo "<p><strong>Client:</strong> username: client1, password: password</p>";
    
    echo "<h3>Next Steps:</h3>";
    echo "<p>1. Delete this install.php file for security</p>";
    echo "<p>2. Access your system at: <a href='index.php'>index.php</a></p>";
    echo "<p>3. Login with the default credentials above</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Installation failed: " . $e->getMessage() . "</p>";
}

$conn->close();
?> 