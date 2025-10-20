<?php
require_once 'classes/SessionManager.php';
require_once 'classes/User.php';

SessionManager::start();
SessionManager::requireLogin();

$user = new User();
$user_id = SessionManager::getUserId();
$orderCount = $user->getOrderCount($user_id);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard — RaceNex</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body{background:#071025;color:#fff}
    .btn-accent{background:#ff3b3b;color:#fff;border:none}
    .btn-accent:hover{background:#e03434;color:#fff}
    .card{background:#1a1a2e;border:none;color:#fff}
  </style>
</head>
<body>
<nav class="navbar navbar-dark" style="background:#071025;">
  <div class="container">
    <a class="navbar-brand" href="index.php"><span style="color:#ff3b3b;font-weight:700">Race</span><span>Nex</span></a>
    <div>
      <a class="btn btn-outline-light me-2" href="cart.php">Cart</a>
      <a class="btn btn-danger" href="logout.php">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-5">
  <h2>Hello, <?php echo htmlspecialchars(SessionManager::getUserName()); ?></h2>
  <p class="text-muted">Welcome to your dashboard. Manage orders, check out new parts, and track your purchases.</p>
  
  <?php if (SessionManager::isAdmin()): ?>
    <div class="alert alert-info">
      <i class="bi bi-shield-check me-2"></i>
      <strong>Admin Access:</strong> You have admin privileges. 
      <a href="admin_dashboard.php" class="btn btn-sm btn-outline-primary ms-2">Go to Admin Panel</a>
    </div>
  <?php endif; ?>

  <div class="row">
    <div class="col-md-4">
      <div class="card p-3 mb-3">
        <h5>My Orders</h5>
        <p class="mb-0">You have <strong><?php echo $orderCount; ?></strong> orders.</p>
        <a class="btn btn-sm btn-accent mt-2" href="orders.php">View Orders</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3 mb-3">
        <h5>Shop Parts</h5>
        <p class="mb-0">Explore the latest racing parts and accessories.</p>
        <a class="btn btn-sm btn-accent mt-2" href="index.php">Browse Parts</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3 mb-3">
        <h5>Cart</h5>
        <p class="mb-0">Check items ready for checkout.</p>
        <a class="btn btn-sm btn-accent mt-2" href="cart.php">Open Cart</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
