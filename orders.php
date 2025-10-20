<?php
require_once 'classes/SessionManager.php';
require_once 'classes/Order.php';

SessionManager::start();
SessionManager::requireLogin();

$order = new Order();
$uid = SessionManager::getUserId();

$msg = $_GET['msg'] ?? null;
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$payment_id = $_GET['payment_id'] ?? null;

// Fetch orders
$orders = $order->getUserOrders($uid);

function getItems($order, $oid) {
    return $order->getOrderItems($oid);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - RaceNex</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: #0f1724; 
            color: #e6eef8; 
        }
        .navbar { 
            background: #071025 !important; 
        }
        .brand { 
            color: #ff3b3b; 
            font-weight: 700; 
            letter-spacing: 1px; 
        }
        .btn-accent { 
            background: #ff3b3b; 
            border: none; 
            color: #fff; 
        }
        .btn-accent:hover { 
            background: #e03434; 
            color: #fff; 
        }
        .card { 
            background: linear-gradient(180deg, #111827 0%, #0b1220 100%); 
            border: none; 
            color: #fff; 
        }
        .table-dark {
            background-color: #1a1a2e;
        }
        .table-dark th {
            background-color: #16213e;
            border-color: #374151;
        }
        .table-dark td {
            border-color: #374151;
        }
        .alert-success {
            background-color: #1e3a1e;
            border-color: #2d5a2d;
            color: #d4edda;
        }
        .alert-danger {
            background-color: #3a1e1e;
            border-color: #5a2d2d;
            color: #f8d7da;
        }
        .order-card {
            background: linear-gradient(180deg, #111827 0%, #0b1220 100%);
            border: 1px solid #374151;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .order-header {
            border-bottom: 1px solid #374151;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .order-status {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-completed {
            background: #1e3a1e;
            color: #4ade80;
        }
        .status-pending {
            background: #3a2e1e;
            color: #fbbf24;
        }
        .status-cancelled {
            background: #3a1e1e;
            color: #f87171;
        }
        .order-details {
            background: #16213e;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .detail-label {
            color: #9ca3af;
            font-weight: 500;
        }
        .detail-value {
            color: #fff;
            font-weight: 600;
        }
        .order-items {
            background: #16213e;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .items-title {
            color: #ff3b3b;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #374151;
        }
        .item-row:last-child {
            border-bottom: none;
        }
        .empty-orders {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(180deg, #111827 0%, #0b1220 100%);
            border-radius: 15px;
            border: 1px solid #374151;
        }
        .empty-orders h2 {
            color: #9ca3af;
            margin-bottom: 20px;
        }
        .empty-orders p {
            color: #6b7280;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <span class="brand">Race</span><span style="color:#fff">Nex</span>
            </a>
            <div>
                <span class="text-light me-3">Welcome, <?php echo htmlspecialchars(SessionManager::getUserName()); ?>!</span>
                <a class="btn btn-outline-light me-2" href="index.php">Shop</a>
                <a class="btn btn-outline-light me-2" href="dashboard.php">Dashboard</a>
                <a class="btn btn-outline-light me-2" href="cart.php">Cart (<?php echo isset($_SESSION['cart'])?array_sum($_SESSION['cart']):0; ?>)</a>
                <a class="btn btn-danger" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="text-center mb-4">
            <h1 class="display-5"><span class="brand">My Orders</span></h1>
            <p class="lead text-muted">Track your racing parts orders and delivery status</p>
        </div>

        <?php if ($msg === 'order_created'): ?>
            <div class="alert alert-success text-center">
                <h4>✅ Order Placed Successfully!</h4>
                <p>Order ID: #<?php echo $order_id; ?></p>
                <?php if ($payment_id): ?>
                    <p><small>Payment ID: <?php echo htmlspecialchars($payment_id); ?></small></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (!$orders): ?>
            <div class="empty-orders">
                <h2>📦 No Orders Yet</h2>
                <p>You haven't placed any orders yet. Start by browsing our racing parts!</p>
                <a href="index.php" class="btn btn-accent">🏎️ Browse Parts</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($orders as $o): 
                    $items = getItems($order, $o['id']);
                    $status_class = 'status-' . strtolower($o['status']);
                ?>
                <div class="col-12">
                    <div class="order-card">
                        <div class="order-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-1">Order #<?php echo $o['id']; ?></h3>
                                    <p class="text-muted mb-0"><?php echo date('d M Y, h:i A', strtotime($o['created_at'])); ?></p>
                                </div>
                                <span class="order-status <?php echo $status_class; ?>"><?php echo htmlspecialchars($o['status']); ?></span>
                            </div>
                        </div>
                        
                        <div class="order-details">
                            <div class="detail-row">
                                <span class="detail-label">Total Amount</span>
                                <span class="detail-value">₹<?php echo number_format($o['total'], 2); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Payment Method</span>
                                <span class="detail-value"><?php echo htmlspecialchars($o['payment_method']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Items Count</span>
                                <span class="detail-value"><?php echo count($items); ?> items</span>
                            </div>
                            <?php if (!empty($o['payment_id'])): ?>
                            <div class="detail-row">
                                <span class="detail-label">Payment ID</span>
                                <span class="detail-value"><?php echo substr($o['payment_id'], 0, 20) . '...'; ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="order-items">
                            <h5 class="items-title">🏎️ Ordered Items</h5>
                            <?php foreach ($items as $it): ?>
                                <div class="item-row">
                                    <span><?php echo htmlspecialchars($it['name']); ?></span>
                                    <span><?php echo $it['quantity']; ?> × ₹<?php echo number_format($it['price'], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (!empty($o['address'])): ?>
                        <div class="order-items">
                            <h5 class="items-title">🚚 Delivery Address</h5>
                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($o['address'])); ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="text-end">
                            <a href="index.php" class="btn btn-accent">Reorder</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>