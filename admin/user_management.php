<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$success_message = '';
$error_message = '';

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    
    switch($action) {
        case 'approve_planner':
            if (!empty($user_id)) {
                // Update user status to active
                $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ? AND user_type = 'planner'");
                $stmt->bind_param("i", $user_id);
                
                if ($stmt->execute()) {
                    // Update planner approval status
                    $stmt = $conn->prepare("UPDATE planners SET approval_status = 'approved' WHERE user_id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    
                    $success_message = 'Planner approved successfully!';
                } else {
                    $error_message = 'Failed to approve planner.';
                }
            }
            break;
            
        case 'reject_planner':
            if (!empty($user_id)) {
                // Update user status to rejected
                $stmt = $conn->prepare("UPDATE users SET status = 'rejected' WHERE id = ? AND user_type = 'planner'");
                $stmt->bind_param("i", $user_id);
                
                if ($stmt->execute()) {
                    // Update planner approval status
                    $stmt = $conn->prepare("UPDATE planners SET approval_status = 'rejected' WHERE user_id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    
                    $success_message = 'Planner rejected successfully!';
                } else {
                    $error_message = 'Failed to reject planner.';
                }
            }
            break;
            
        case 'block_user':
            if (!empty($user_id)) {
                $stmt = $conn->prepare("UPDATE users SET status = 'blocked' WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                
                if ($stmt->execute()) {
                    $success_message = 'User blocked successfully!';
                } else {
                    $error_message = 'Failed to block user.';
                }
            }
            break;
            
        case 'activate_user':
            if (!empty($user_id)) {
                $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                
                if ($stmt->execute()) {
                    $success_message = 'User activated successfully!';
                } else {
                    $error_message = 'Failed to activate user.';
                }
            }
            break;
            
        case 'delete_user':
            if (!empty($user_id)) {
                // Delete planner record first if exists
                $stmt = $conn->prepare("DELETE FROM planners WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                
                // Delete user
                $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                
                if ($stmt->execute()) {
                    $success_message = 'User deleted successfully!';
                } else {
                    $error_message = 'Failed to delete user.';
                }
            }
            break;
    }
}

// Get search and filter parameters
$search = $_GET['search'] ?? '';
$user_type_filter = $_GET['user_type'] ?? '';
$status_filter = $_GET['status'] ?? '';
$sort_by = $_GET['sort'] ?? 'created_at';
$sort_order = $_GET['order'] ?? 'DESC';

// Build query with filters
$where_conditions = [];
$params = [];
$param_types = '';

if (!empty($search)) {
    $where_conditions[] = "(u.full_name LIKE ? OR u.email LIKE ? OR u.username LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'sss';
}

if (!empty($user_type_filter)) {
    $where_conditions[] = "u.user_type = ?";
    $params[] = $user_type_filter;
    $param_types .= 's';
}

if (!empty($status_filter)) {
    $where_conditions[] = "u.status = ?";
    $params[] = $status_filter;
    $param_types .= 's';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total count for pagination
$count_query = "SELECT COUNT(*) as total FROM users u $where_clause";
if (!empty($params)) {
    $count_stmt = $conn->prepare($count_query);
    $count_stmt->bind_param($param_types, ...$params);
    $count_stmt->execute();
    $total_users = $count_stmt->get_result()->fetch_assoc()['total'];
} else {
    $total_users = $conn->query($count_query)->fetch_assoc()['total'];
}

// Pagination
$per_page = 20;
$page = max(1, $_GET['page'] ?? 1);
$total_pages = ceil($total_users / $per_page);
$offset = ($page - 1) * $per_page;

// Build main query
$query = "SELECT u.*, p.company_name, p.specialization, p.experience_years, p.approval_status 
          FROM users u 
          LEFT JOIN planners p ON u.id = p.user_id 
          $where_clause 
          ORDER BY u.$sort_by $sort_order 
          LIMIT $per_page OFFSET $offset";

// Execute query
if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($param_types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

// Get user type counts for quick stats
$user_type_counts = [];
$type_result = $conn->query("SELECT user_type, COUNT(*) as count FROM users GROUP BY user_type");
while ($row = $type_result->fetch_assoc()) {
    $user_type_counts[$row['user_type']] = $row['count'];
}

// Get status counts
$status_counts = [];
$status_result = $conn->query("SELECT status, COUNT(*) as count FROM users GROUP BY status");
while ($row = $status_result->fetch_assoc()) {
    $status_counts[$row['status']] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Dashboard</title>
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
            <div class="col-md-3 col-lg-2 px-0">
                <div class="admin-sidebar p-4" id="adminSidebar">
                    <div class="sidebar-header">
                        <div class="mb-3">
                            <i class="fas fa-calendar-check fa-3x text-white-50"></i>
                        </div>
                        <h4>EMS Admin</h4>
                        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Admin'); ?></p>
                    </div>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link" href="admin_index.php">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                        <a class="nav-link active" href="user_management.php">
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
                        <a class="nav-link" href="settings.php">
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
            <div class="col-md-9 col-lg-10 px-0">
                <div class="main-content">
                    <!-- Page Header -->
                    <div class="page-header">
                        <h1><i class="fas fa-users me-3"></i>User Management</h1>
                        <p class="mb-0">Manage all users in the system including clients, planners, and administrators</p>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="stats-cards">
                        <div class="stat-card">
                            <div class="stat-icon bg-primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-value"><?php echo array_sum($user_type_counts); ?></div>
                            <div class="stat-label">Total Users</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon bg-success">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="stat-value"><?php echo $user_type_counts['client'] ?? 0; ?></div>
                            <div class="stat-label">Clients</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon bg-info">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div class="stat-value"><?php echo $user_type_counts['planner'] ?? 0; ?></div>
                            <div class="stat-label">Planners</div>
                        </div>
                        
                        <div class="stat-card">
                            <div class="stat-icon bg-warning">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="stat-value"><?php echo $user_type_counts['admin'] ?? 0; ?></div>
                            <div class="stat-label">Admins</div>
                        </div>
                    </div>

                    <!-- Filters Section -->
                    <div class="filters-section">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <div class="search-box">
                                    <i class="fas fa-search search-icon"></i>
                                    <input type="text" class="form-control" name="search" placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <select name="user_type" class="form-select filter-select">
                                    <option value="">All Types</option>
                                    <option value="client" <?php echo $user_type_filter === 'client' ? 'selected' : ''; ?>>Clients</option>
                                    <option value="planner" <?php echo $user_type_filter === 'planner' ? 'selected' : ''; ?>>Planners</option>
                                    <option value="admin" <?php echo $user_type_filter === 'admin' ? 'selected' : ''; ?>>Admins</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <select name="status" class="form-select filter-select">
                                    <option value="">All Status</option>
                                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="blocked" <?php echo $status_filter === 'blocked' ? 'selected' : ''; ?>>Blocked</option>
                                    <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <select name="sort" class="form-select filter-select">
                                    <option value="created_at" <?php echo $sort_by === 'created_at' ? 'selected' : ''; ?>>Date Created</option>
                                    <option value="full_name" <?php echo $sort_by === 'full_name' ? 'selected' : ''; ?>>Name</option>
                                    <option value="email" <?php echo $sort_by === 'email' ? 'selected' : ''; ?>>Email</option>
                                    <option value="status" <?php echo $sort_by === 'status' ? 'selected' : ''; ?>>Status</option>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-2"></i>Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Alerts -->
                    <?php if ($success_message): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error_message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Users Table -->
                    <div class="table-container">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5><i class="fas fa-list me-2"></i>Users List</h5>
                            <span class="text-muted"><?php echo $total_users; ?> users found</span>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Type</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($users)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">No users found matching your criteria</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar me-3">
                                                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($user['full_name']); ?></strong>
                                                        <br><small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="user-type-badge user-type-<?php echo $user['user_type']; ?>">
                                                    <?php echo ucfirst($user['user_type']); ?>
                                                </span>
                                                <?php if ($user['user_type'] === 'planner' && isset($user['company_name'])): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($user['company_name']); ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div><i class="fas fa-envelope me-2 text-muted"></i><?php echo htmlspecialchars($user['email']); ?></div>
                                                <?php if ($user['phone']): ?>
                                                    <div><i class="fas fa-phone me-2 text-muted"></i><?php echo htmlspecialchars($user['phone']); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo $user['status']; ?>">
                                                    <?php echo ucfirst($user['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <?php if ($user['user_type'] === 'planner' && $user['status'] === 'pending'): ?>
                                                        <button class="btn btn-success btn-action" onclick="approveUser(<?php echo $user['id']; ?>)">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-danger btn-action" onclick="rejectUser(<?php echo $user['id']; ?>)">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($user['status'] === 'active'): ?>
                                                        <button class="btn btn-warning btn-action" onclick="blockUser(<?php echo $user['id']; ?>)">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    <?php elseif ($user['status'] === 'blocked'): ?>
                                                        <button class="btn btn-success btn-action" onclick="activateUser(<?php echo $user['id']; ?>)">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    
                                                    <button class="btn btn-info btn-action" onclick="viewUser(<?php echo $user['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    
                                                    <?php if ($user['user_type'] !== 'admin'): ?>
                                                        <button class="btn btn-danger btn-action" onclick="deleteUser(<?php echo $user['id']; ?>)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <div class="pagination-container">
                                <div class="page-info">
                                    Page <?php echo $page; ?> of <?php echo $total_pages; ?>
                                </div>
                                
                                <nav aria-label="Page navigation">
                                    <ul class="pagination mb-0">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                                                    <i class="fas fa-chevron-left"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
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

        function approveUser(userId) {
            if (confirm('Are you sure you want to approve this user?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="approve_planner">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function rejectUser(userId) {
            if (confirm('Are you sure you want to reject this user?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="reject_planner">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function blockUser(userId) {
            if (confirm('Are you sure you want to block this user?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="block_user">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function activateUser(userId) {
            if (confirm('Are you sure you want to activate this user?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="activate_user">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function viewUser(userId) {
            // Implement user view functionality
            alert('View user details - User ID: ' + userId);
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

        // Auto-submit form when filters change
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                this.closest('form').submit();
            });
        });
    </script>
</body>
</html> 