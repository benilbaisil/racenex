<?php
require_once 'classes/SessionManager.php';
require_once 'classes/Product.php';

SessionManager::start();

$product = new Product();
$products = $product->getActiveProducts();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>RaceNex — Vehicle Parts Store</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #0f1724; color: #ffffff; }
    .card { background: linear-gradient(180deg,#1f2937 0%,#111827 100%); border: 1px solid #374151; }
    .brand { color: #ff3b3b; font-weight:700; letter-spacing:1px; }
    .btn-accent { background: #ff3b3b; border: none; color: #fff; }
    .product-img { height:180px; object-fit:cover; }
    .card-title { color: #ffffff !important; }
    .card-text { color: #d1d5db !important; }
    .text-muted { color: #9ca3af !important; }
    .lead { color: #e5e7eb !important; }
    .navbar-brand { color: #ffffff !important; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background:#071025;">
  <div class="container">
    <a class="navbar-brand" href="index.php"><span class="brand">Race</span><span style="color:#fff">Nex</span></a>
    <div>
      <?php if (SessionManager::isLoggedIn()): ?>
        <span class="text-light me-3">Welcome, <?php echo htmlspecialchars(SessionManager::getUserName()); ?>!</span>
        <a class="btn btn-outline-light me-2" href="dashboard.php">Dashboard</a>
        <a class="btn btn-outline-light me-2" href="cart.php">Cart (<?php echo isset($_SESSION['cart'])?array_sum($_SESSION['cart']):0; ?>)</a>
        <a class="btn btn-danger" href="logout.php">Logout</a>
      <?php else: ?>
        <a class="btn btn-outline-light me-2" href="login.php">Login</a>
        <a class="btn btn-danger" href="register.php">Register</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container py-5">
  <div class="text-center mb-4">
    <h1 class="display-5"><span class="brand">Race</span><span style="color:#fff">Nex</span> — Vehicle Parts</h1>
    <p class="lead text-muted">Premium parts for speed lovers — fast delivery, secure checkout.</p>
  </div>

  <div class="row">
    <?php foreach ($products as $p): ?>
      <div class="col-md-3 mb-4">
        <div class="card h-100 shadow-sm">
          <img src="images/<?php echo htmlspecialchars($p['image']); ?>" class="card-img-top product-img" alt="<?php echo htmlspecialchars($p['name']); ?>">
          <div class="card-body">
            <h5 class="card-title text-white"><?php echo htmlspecialchars($p['name']); ?></h5>
            <p class="card-text text-muted small"><?php echo htmlspecialchars(substr($p['description'],0,80)); ?>...</p>
            <div class="d-flex justify-content-between align-items-center">
              <div><strong class="text-white">₹<?php echo number_format($p['price'],2); ?></strong></div>
              <form method="post" action="<?php echo SessionManager::isLoggedIn() ? 'cart.php' : 'login.php'; ?>">
                <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                <input type="hidden" name="action" value="add">
                <button class="btn btn-accent btn-sm">Add to Cart</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
</body>
</html>
