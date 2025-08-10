<?php
// Quick Start Page for Event Management System
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS - Quick Start</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .start-card { 
            background: white; 
            border-radius: 15px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .start-card:hover { transform: translateY(-5px); }
        .step-number { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-4 text-primary">
                <i class="fas fa-calendar-check me-3"></i>
                Event Management System
            </h1>
            <p class="lead text-muted">Quick Start Guide</p>
        </div>

        <div class="row g-4">
            <!-- Step 1: Database Setup -->
            <div class="col-md-4">
                <div class="start-card p-4 h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="step-number me-3">1</div>
                        <h4 class="mb-0">Database Setup</h4>
                    </div>
                    <p class="text-muted">Set up your database and create all necessary tables.</p>
                    <a href="install.php" class="btn btn-primary w-100">
                        <i class="fas fa-database me-2"></i>Run Installation
                    </a>
                </div>
            </div>

            <!-- Step 2: Test Connection -->
            <div class="col-md-4">
                <div class="start-card p-4 h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="step-number me-3">2</div>
                        <h4 class="mb-0">Test Database</h4>
                    </div>
                    <p class="text-muted">Verify that your database connection is working properly.</p>
                    <a href="test_db.php" class="btn btn-info w-100">
                        <i class="fas fa-check-circle me-2"></i>Test Connection
                    </a>
                </div>
            </div>

            <!-- Step 3: Access System -->
            <div class="col-md-4">
                <div class="start-card p-4 h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="step-number me-3">3</div>
                        <h4 class="mb-0">Access System</h4>
                    </div>
                    <p class="text-muted">Login and start using your Event Management System.</p>
                    <a href="login.php" class="btn btn-success w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Go to Login
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="start-card p-4">
                    <h4 class="mb-3">
                        <i class="fas fa-link me-2"></i>Quick Links
                    </h4>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="index.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-home me-2"></i>Main Page
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="user/register.php" class="btn btn-outline-success w-100">
                                <i class="fas fa-user-plus me-2"></i>Register
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="admin/admin_index.php" class="btn btn-outline-warning w-100">
                                <i class="fas fa-cog me-2"></i>Admin Panel
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="planner/planner_index.php" class="btn btn-outline-info w-100">
                                <i class="fas fa-calendar me-2"></i>Planner Panel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Default Credentials -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="start-card p-4">
                    <h4 class="mb-3">
                        <i class="fas fa-key me-2"></i>Default Login Credentials
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>User Type</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-danger">Admin</span></td>
                                    <td><code>admin</code></td>
                                    <td><code>admin123</code></td>
                                    <td>Full system access</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-info">Planner</span></td>
                                    <td><code>planner1</code></td>
                                    <td><code>admin123</code></td>
                                    <td>Event planning access</td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success">Client</span></td>
                                    <td><code>client1</code></td>
                                    <td><code>admin123</code></td>
                                    <td>Event creation access</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Important Notes -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Important Notes:</h5>
                    <ul class="mb-0">
                        <li>Make sure XAMPP (Apache + MySQL) is running before starting</li>
                        <li>Delete <code>install.php</code> and <code>test_db.php</code> after successful setup</li>
                        <li>Change default passwords in production environment</li>
                        <li>Check <code>SETUP.md</code> for detailed instructions</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html> 