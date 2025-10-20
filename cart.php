<?php
require_once 'classes/SessionManager.php';
require_once 'classes/Cart.php';

SessionManager::start();
SessionManager::requireLogin();

$cart = new Cart();
$message = '';

// Handle cart actions
$action = $_POST['action'] ?? $_GET['action'] ?? null;
$product_id = (int)($_POST['product_id'] ?? $_GET['product_id'] ?? 0);

if ($action === 'add' && $product_id) {
    $result = $cart->addToCart($product_id, 1);
    $message = $result['message'];
    header('Location: cart.php');
    exit;
}

if ($action === 'remove' && $product_id) {
    $result = $cart->removeFromCart($product_id);
    $message = $result['message'];
    header('Location: cart.php');
    exit;
}

if ($action === 'update') {
    $quantities = [];
    foreach ($_POST['qty'] as $pid => $q) {
        $quantities[(int)$pid] = (int)$q;
    }
    $result = $cart->updateCart($quantities);
    $message = $result['message'];
    header('Location: cart.php');
    exit;
}

// Get cart data
$cart_data = $cart->getCartItems();
$cartItems = $cart_data['items'];
$total = $cart_data['total'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Cart — RaceNex</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background:#071025;color:#fff}
    .btn-accent{background:#ff3b3b;color:#fff;border:none}
    .btn-accent:hover{background:#e03434;color:#fff}
    .subtotal-cell{font-weight:bold;color:#ff3b3b}
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
  <h2>Your Cart</h2>
  <?php if ($message): ?>
    <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>
  <?php if (!$cartItems): ?>
    <div class="alert alert-info">Your cart is empty. <a href="index.php">Shop now</a></div>
  <?php else: ?>
    <form method="post" action="cart.php">
      <input type="hidden" name="action" value="update">
      <table class="table table-dark table-striped">
        <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($cartItems as $it): 
          $item_subtotal = $it['quantity'] * $it['price'];
        ?>
          <tr>
            <td><?php echo htmlspecialchars($it['name']); ?></td>
            <td class="price-cell" data-price="<?php echo $it['price']; ?>">₹<?php echo number_format($it['price'],2); ?></td>
            <td style="width:120px">
              <input type="number" 
                     name="qty[<?php echo $it['id']; ?>]" 
                     value="<?php echo $it['quantity']; ?>" 
                     min="1" 
                     class="form-control qty-input" 
                     data-product-id="<?php echo $it['id']; ?>"
                     data-price="<?php echo $it['price']; ?>"
                     onchange="updateSubtotal(this)">
            </td>
            <td class="subtotal-cell" id="subtotal-<?php echo $it['id']; ?>">₹<?php echo number_format($item_subtotal,2); ?></td>
            <td><a href="cart.php?action=remove&product_id=<?php echo $it['id']; ?>" class="btn btn-sm btn-outline-light">Remove</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>

      <div class="d-flex justify-content-between align-items-center">
        <div><button class="btn btn-outline-light">Update Cart</button></div>
        <div>
          <strong>Total: ₹<span id="cart-total"><?php echo number_format($total,2); ?></span></strong>
          <button type="button" class="btn btn-accent ms-3" id="checkout-btn">Proceed to Checkout</button>
        </div>
      </div>
    </form>
  <?php endif; ?>
</div>

<script>
function updateSubtotal(input) {
    const productId = input.getAttribute('data-product-id');
    const price = parseFloat(input.getAttribute('data-price'));
    const quantity = parseInt(input.value);
    
    // Calculate subtotal
    const subtotal = price * quantity;
    
    // Update subtotal display
    const subtotalElement = document.getElementById('subtotal-' + productId);
    if (subtotalElement) {
        subtotalElement.textContent = '₹' + subtotal.toFixed(2);
    }
    
    // Update total
    updateCartTotal();
}

function updateCartTotal() {
    let total = 0;
    const subtotalElements = document.querySelectorAll('.subtotal-cell');
    
    subtotalElements.forEach(function(element) {
        const subtotalText = element.textContent.replace('₹', '').replace(',', '');
        const subtotal = parseFloat(subtotalText);
        if (!isNaN(subtotal)) {
            total += subtotal;
        }
    });
    
    // Update total display
    const totalElement = document.getElementById('cart-total');
    if (totalElement) {
        totalElement.textContent = total.toFixed(2);
    }
}

// Initialize cart total on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartTotal();
    
    // Handle checkout button click
    document.getElementById('checkout-btn').addEventListener('click', function() {
        // Collect current quantities from all inputs
        const quantities = {};
        const qtyInputs = document.querySelectorAll('.qty-input');
        
        qtyInputs.forEach(function(input) {
            const productId = input.getAttribute('data-product-id');
            const quantity = parseInt(input.value);
            if (productId && quantity > 0) {
                quantities[productId] = quantity;
            }
        });
        
        // Redirect to checkout with updated quantities
        const quantitiesParam = encodeURIComponent(JSON.stringify(quantities));
        window.location.href = 'checkout.php?updated_quantities=' + quantitiesParam;
    });
});
</script>

</body>
</html>
