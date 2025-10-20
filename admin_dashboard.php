<?php
require_once 'classes/AdminSessionManager.php';
require_once 'classes/Admin.php';
require_once 'classes/Product.php';

AdminSessionManager::start();
AdminSessionManager::requireLogin();

$admin = new Admin();
$product = new Product();

// Get dashboard statistics
$userCount = $admin->getUserCount();
$orderCount = $admin->getOrderCount();
$productCount = $admin->getProductCount();
$totalRevenue = $admin->getTotalRevenue();
$recentOrders = $admin->getRecentOrders(5);
$productStats = $admin->getProductStats();
$allProducts = $product->getAllProducts();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard — RaceNex</title>
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
        .stat-card {
            background: linear-gradient(135deg, #ff3b3b, #e03434);
            border: none;
            color: #fff;
        }
        .stat-card .card-body {
            padding: 1.5rem;
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
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
                    <a class="nav-link active" href="admin_dashboard.php">
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
                    <a class="nav-link" href="admin_users.php">
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
                        <span class="navbar-brand mb-0 h1">Dashboard Overview</span>
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
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <div class="stat-number"><?php echo $userCount; ?></div>
                                    <div class="text-white-50">Total Users</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <div class="stat-number"><?php echo $productCount; ?></div>
                                    <div class="text-white-50">Total Products</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <div class="stat-number"><?php echo $orderCount; ?></div>
                                    <div class="text-white-50">Total Orders</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <div class="stat-number">$<?php echo number_format($totalRevenue, 2); ?></div>
                                    <div class="text-white-50">Total Revenue</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Product Management Section -->
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Recent Orders</h5>
                                    <a href="admin_orders.php" class="btn btn-outline-admin btn-sm">View All</a>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($recentOrders)): ?>
                                        <p class="text-muted text-center">No orders found.</p>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-dark table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Order ID</th>
                                                        <th>Customer</th>
                                                        <th>Total</th>
                                                        <th>Date</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($recentOrders as $order): ?>
                                                        <tr>
                                                            <td>#<?php echo $order['id']; ?></td>
                                                            <td><?php echo htmlspecialchars($order['user_name'] ?? 'N/A'); ?></td>
                                                            <td>$<?php echo number_format($order['total'], 2); ?></td>
                                                            <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                                                            <td>
                                                                <span class="badge bg-success"><?php echo ucfirst($order['status'] ?? 'pending'); ?></span>
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
                        
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Product Stats</h5>
                                    <a href="admin_products.php" class="btn btn-outline-admin btn-sm">Manage</a>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6 mb-3">
                                            <div class="h4 text-primary"><?php echo $productStats['active_products']; ?></div>
                                            <small class="text-muted">Active Products</small>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="h4 text-warning"><?php echo $productStats['total_stock']; ?></div>
                                            <small class="text-muted">Total Stock</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="h4 text-info">$<?php echo number_format($productStats['avg_price'], 2); ?></div>
                                            <small class="text-muted">Avg Price</small>
                                        </div>
                                        <div class="col-6">
                                            <div class="h4 text-success"><?php echo $productStats['total_products']; ?></div>
                                            <small class="text-muted">Total Products</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="mb-0">Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="admin_add_product.php" class="btn btn-admin">
                                            <i class="bi bi-plus-circle me-2"></i>Add New Product
                                        </a>
                                        <a href="admin_products.php" class="btn btn-outline-admin">
                                            <i class="bi bi-box-seam me-2"></i>Manage Products
                                        </a>
                                        <a href="admin_orders.php" class="btn btn-outline-admin">
                                            <i class="bi bi-list-check me-2"></i>View All Orders
                                        </a>
                                        <a href="admin_users.php" class="btn btn-outline-admin">
                                            <i class="bi bi-people me-2"></i>Manage Users
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
