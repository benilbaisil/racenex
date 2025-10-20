<?php
require_once 'classes/AdminSessionManager.php';
require_once 'classes/Admin.php';
require_once 'classes/Order.php';

AdminSessionManager::start();
AdminSessionManager::requireLogin();

$admin = new Admin();
$order = new Order();

$message = '';
$error = '';

// Handle order status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_status') {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    
    $result = $order->updateOrderStatus($order_id, $status);
    if ($result) {
        $message = 'Order status updated successfully!';
    } else {
        $error = 'Failed to update order status.';
    }
}

// Get all orders with user information
$orders = $admin->getRecentOrders(50); // Get more orders for the orders page
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Management — RaceNex Admin</title>
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
        .status-badge {
            font-size: 0.8rem;
            padding: 4px 8px;
        }
        .order-details {
            background: #2a2a3e;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
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
                    <a class="nav-link active" href="admin_orders.php">
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
                        <span class="navbar-brand mb-0 h1">Order Management</span>
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
                    
                    <!-- Orders List -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">All Orders (<?php echo count($orders); ?>)</h5>
                            <div class="d-flex gap-2">
                                <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($orders)): ?>
                                <p class="text-muted text-center">No orders found.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Customer</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($orders as $order): ?>
                                                <tr data-status="<?php echo strtolower($order['status'] ?? 'pending'); ?>">
                                                    <td>
                                                        <strong>#<?php echo $order['id']; ?></strong>
                                                    </td>
                                                    <td>
                                                        <div><?php echo htmlspecialchars($order['user_name'] ?? 'N/A'); ?></div>
                                                        <small class="text-muted">ID: <?php echo $order['user_id']; ?></small>
                                                    </td>
                                                    <td>
                                                        <strong>$<?php echo number_format($order['total'], 2); ?></strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge status-badge bg-<?php 
                                                            $status = strtolower($order['status'] ?? 'pending');
                                                            echo match($status) {
                                                                'pending' => 'warning',
                                                                'processing' => 'info',
                                                                'shipped' => 'primary',
                                                                'delivered' => 'success',
                                                                'cancelled' => 'danger',
                                                                default => 'secondary'
                                                            };
                                                        ?>">
                                                            <?php echo ucfirst($order['status'] ?? 'Pending'); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div><?php echo date('M j, Y', strtotime($order['created_at'])); ?></div>
                                                        <small class="text-muted"><?php echo date('g:i A', strtotime($order['created_at'])); ?></small>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" class="btn btn-outline-primary" 
                                                                    onclick="viewOrderDetails(<?php echo $order['id']; ?>)">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-success" 
                                                                    data-bs-toggle="modal" data-bs-target="#statusModal"
                                                                    onclick="setOrderId(<?php echo $order['id']; ?>, '<?php echo $order['status'] ?? 'pending'; ?>')">
                                                                <i class="bi bi-pencil"></i>
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
    
    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title">Update Order Status</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="order_id" id="orderId">
                        <div class="mb-3">
                            <label for="status" class="form-label">Order Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-admin">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Order Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Order details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setOrderId(orderId, currentStatus) {
            document.getElementById('orderId').value = orderId;
            document.getElementById('status').value = currentStatus;
        }
        
        function viewOrderDetails(orderId) {
            // For now, just show a placeholder
            document.getElementById('orderDetailsContent').innerHTML = `
                <div class="text-center">
                    <p>Order #${orderId} details would be loaded here.</p>
                    <p class="text-muted">This feature can be expanded to show detailed order information including items, customer details, and shipping information.</p>
                </div>
            `;
            new bootstrap.Modal(document.getElementById('detailsModal')).show();
        }
        
        // Status filter functionality
        document.getElementById('statusFilter').addEventListener('change', function() {
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const status = row.getAttribute('data-status');
                if (filter === '' || status === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
