<?php
require_once 'classes/SessionManager.php';
require_once 'classes/Order.php';

SessionManager::start();
SessionManager::requireLogin();

$order = new Order();

$payment_id = $_GET['payment_id'] ?? '';
$amount = $_GET['amount'] ?? 0;
$cart_data = $_GET['cart_data'] ?? '';
$address = $_GET['address'] ?? '';

// Decode cart data
$cart_items = [];
if ($cart_data) {
    $cart_items = json_decode(urldecode($cart_data), true);
}

$errors = [];
$order_id = null;

// If we have payment_id and address, create order immediately
if ($payment_id && $address && !empty($cart_items)) {
    $result = $order->createOrderFromCartData(SessionManager::getUserId(), $cart_items, $address, 'Razorpay', $payment_id);
    
    if ($result['success']) {
        $order_id = $result['order_id'];
        
        // Clear cart
        $_SESSION['cart'] = [];
        
        // Redirect to orders page with success message
        header("Location: orders.php?msg=order_created&id={$order_id}&payment_id={$payment_id}");
        exit;
    } else {
        $errors[] = $result['message'];
    }
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Success — RaceNex</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#071025;color:#fff}
        .btn-accent{background:#ff3b3b;color:#fff;border:none}
        .btn-accent:hover{background:#e03434;color:#fff}
        .success-card{background:#1a1a2e;border-radius:15px;padding:30px;margin:20px 0}
        .payment-success{color:#28a745;font-size:24px;font-weight:bold}
        .order-summary{background:#16213e;border-radius:10px;padding:20px;margin:20px 0}
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background:#071025;">
    <div class="container">
        <a class="navbar-brand" href="index.php"><span style="color:#ff3b3b;font-weight:700">Race</span><span>Nex</span></a>
        <div>
            <span class="text-light me-3">Welcome, <?php echo htmlspecialchars(SessionManager::getUserName()); ?>!</span>
            <a class="btn btn-outline-light me-2" href="index.php">Shop</a>
            <a class="btn btn-outline-light me-2" href="dashboard.php">Dashboard</a>
            <a class="btn btn-outline-light me-2" href="orders.php">Orders</a>
            <a class="btn btn-danger" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="success-card">
        <div class="text-center mb-4">
            <div class="payment-success">✅ Payment Successful!</div>
            <p class="mt-2">Your payment has been processed successfully.</p>
            <p><strong>Payment ID:</strong> <?php echo htmlspecialchars($payment_id); ?></p>
            <p><strong>Amount:</strong> ₹<?php echo number_format($amount, 2); ?></p>
        </div>
        
        <?php if ($errors): ?>
            <div class="alert alert-danger"><?php echo implode('<br>', $errors); ?></div>
        <?php endif; ?>
        
        <?php if ($order_id): ?>
            <div class="alert alert-success text-center">
                <h4>Order Placed Successfully!</h4>
                <p>Your order has been confirmed and will be processed soon.</p>
                <p><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
                <a href="orders.php" class="btn btn-accent">View My Orders</a>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                <h4>Payment Successful!</h4>
                <p>Your payment has been processed, but there was an issue creating the order.</p>
                <p>Please contact support with Payment ID: <?php echo htmlspecialchars($payment_id); ?></p>
                <a href="orders.php" class="btn btn-accent">View My Orders</a>
            </div>
        <?php endif; ?>
    </div>
    
</div>

</body>
</html>
