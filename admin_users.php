<?php
require_once 'classes/AdminSessionManager.php';
require_once 'classes/Admin.php';

AdminSessionManager::start();
AdminSessionManager::requireLogin();

$admin = new Admin();

$message = '';
$error = '';

// Handle user role updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_role') {
    $user_id = intval($_POST['user_id']);
    $role = $_POST['role'];
    
    $stmt = $admin->getConnection()->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param('si', $role, $user_id);
    
    if ($stmt->execute()) {
        $message = 'User role updated successfully!';
    } else {
        $error = 'Failed to update user role.';
    }
}

// Get all users
$users = $admin->getAllUsers();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Management — RaceNex Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #071025;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: #1a1a2e;
            min-height: 100vh;
            border-right: 1px solid #333;
        }
        .sidebar .nav-link {
            color: #ccc;
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: #ff3b3b;
            color: #fff;
        }
        .main-content {
            background: #0f1419;
            min-height: 100vh;
        }
        .card {
            background: #1a1a2e;
            border: 1px solid #333;
            border-radius: 12px;
            color: #fff;
        }
        .card-header {
            background: #2a2a3e;
            border-bottom: 1px solid #333;
            border-radius: 12px 12px 0 0 !important;
        }
        .btn-admin {
            background: #ff3b3b;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-admin:hover {
            background: #e03434;
            color: #fff;
            transform: translateY(-1px);
        }
        .btn-outline-admin {
            color: #ff3b3b;
            border-color: #ff3b3b;
        }
        .btn-outline-admin:hover {
            background: #ff3b3b;
            color: #fff;
        }
        .table-dark {
            --bs-table-bg: #1a1a2e;
            --bs-table-striped-bg: #2a2a3e;
        }
        .navbar-admin {
            background: #1a1a2e;
            border-bottom: 1px solid #333;
        }
        .admin-brand {
            font-size: 1.5rem;
            font-weight: 700;
        }
        .admin-brand .race {
            color: #ff3b3b;
        }
        .admin-brand .nex {
            color: #fff;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            background: #ff3b3b;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3">
                    <h4 class="admin-brand">
                        <span class="race">Race</span><span class="nex">Nex</span>
                        <small class="d-block text-muted">Admin Panel</small>
                    </h4>
                </div>
                <nav class="nav flex-column px-3">
                    <a class="nav-link" href="admin_dashboard.php">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="admin_products.php">
                        <i class="bi bi-box-seam me-2"></i>All Products
                    </a>
                    <a class="nav-link" href="admin_add_product.php">
                        <i class="bi bi-plus-circle me-2"></i>Add Product
                    </a>
                    <a class="nav-link" href="admin_orders.php">
                        <i class="bi bi-receipt me-2"></i>Orders
                    </a>
                    <a class="nav-link active" href="admin_users.php">
                        <i class="bi bi-people me-2"></i>Users
                    </a>
                    <a class="nav-link" href="admin_logout.php">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </nav>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Top Navigation -->
                <nav class="navbar navbar-expand-lg navbar-admin">
                    <div class="container-fluid">
                        <span class="navbar-brand mb-0 h1">User Management</span>
                        <div class="navbar-nav ms-auto">
                            <span class="navbar-text me-3">
                                Welcome, <?php echo htmlspecialchars(AdminSessionManager::getAdminName()); ?>
                            </span>
                            <a class="btn btn-outline-light btn-sm" href="index.php" target="_blank">
                                <i class="bi bi-eye me-1"></i>View Site
                            </a>
                        </div>
                    </div>
                </nav>
                
                <div class="p-4">
                    <?php if ($message): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Users List -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">All Users (<?php echo count($users); ?>)</h5>
                            <div class="d-flex gap-2">
                                <select class="form-select form-select-sm" id="roleFilter" style="width: auto;">
                                    <option value="">All Roles</option>
                                    <option value="user">Users</option>
                                    <option value="admin">Admins</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($users)): ?>
                                <p class="text-muted text-center">No users found.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover">
                                        <thead>
                                            <tr>
                                                <th>User</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Joined</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($users as $user): ?>
                                                <tr data-role="<?php echo $user['role'] ?? 'user'; ?>">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="user-avatar me-3">
                                                                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                                            </div>
                                                            <div>
                                                                <div class="fw-bold"><?php echo htmlspecialchars($user['name']); ?></div>
                                                                <small class="text-muted">ID: <?php echo $user['id']; ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div><?php echo htmlspecialchars($user['email']); ?></div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php echo ($user['role'] ?? 'user') == 'admin' ? 'danger' : 'primary'; ?>">
                                                            <?php echo ucfirst($user['role'] ?? 'User'); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div><?php echo date('M j, Y', strtotime($user['created_at'])); ?></div>
                                                        <small class="text-muted"><?php echo date('g:i A', strtotime($user['created_at'])); ?></small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" class="btn btn-outline-success" 
                                                                    data-bs-toggle="modal" data-bs-target="#roleModal"
                                                                    onclick="setUserId(<?php echo $user['id']; ?>, '<?php echo $user['role'] ?? 'user'; ?>', '<?php echo htmlspecialchars($user['name']); ?>')">
                                                                <i class="bi bi-person-gear"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Role Update Modal -->
    <div class="modal fade" id="roleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title">Update User Role</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_role">
                        <input type="hidden" name="user_id" id="userId">
                        <div class="mb-3">
                            <label class="form-label">User: <span id="userName" class="fw-bold"></span></label>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">User Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> Changing a user to admin will give them full access to the admin panel.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-admin">Update Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setUserId(userId, currentRole, userName) {
            document.getElementById('userId').value = userId;
            document.getElementById('role').value = currentRole;
            document.getElementById('userName').textContent = userName;
        }
        
        // Role filter functionality
        document.getElementById('roleFilter').addEventListener('change', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const role = row.getAttribute('data-role');
                if (filter === '' || role === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
