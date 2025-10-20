<?php
require_once 'classes/SessionManager.php';
require_once 'classes/Cart.php';
require_once 'classes/Order.php';

SessionManager::start();
SessionManager::requireLogin();

$cart = new Cart();
$order = new Order();

// Check if cart is empty
if ($cart->isEmpty()) {
    header('Location: cart.php');
    exit;
}

// Get cart data
$cart_data = $cart->getCartItems();
$items = $cart_data['items'];
$total = $cart_data['total'];

// If there are updated quantities from cart page, use them
if (isset($_GET['updated_quantities'])) {
    $updated_quantities = json_decode($_GET['updated_quantities'], true);
    if ($updated_quantities) {
        // Update cart with new quantities
        $cart->updateCart($updated_quantities);
        // Get fresh cart data
        $cart_data = $cart->getCartItems();
        $items = $cart_data['items'];
        $total = $cart_data['total'];
    }
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');

    if (!$address) $errors[] = "Enter shipping address.";
    // For Razorpay payment, order will be created in success.php after payment
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><title>Checkout — RaceNex</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <style>
    body{background:#071025;color:#fff}
    .btn-accent{background:#ff3b3b;color:#fff;border:none}
    .btn-accent:hover{background:#e03434;color:#fff}
  </style>
</head>
<body>
<div class="container py-5">
  <h2>Checkout</h2>
  <?php if ($errors): ?><div class="alert alert-danger"><?php echo implode('<br>',$errors); ?></div><?php endif; ?>

  <div class="row">
    <div class="col-md-6">
      <form method="post">
        <div class="mb-3">
          <label>Shipping Address</label>
          <textarea name="address" class="form-control" rows="4"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
        </div>
        <div class="mb-3">
          <label>Payment Method</label>
          <div class="form-control" style="background-color: #495057; border: 1px solid #6c757d; color: #fff;">
            Razorpay (Card/UPI/Net Banking)
          </div>
          <input type="hidden" name="payment" value="Razorpay">
        </div>
        <button type="button" class="btn btn-accent" id="place-order-btn">Place Order</button>
      </form>
    </div>

    <div class="col-md-6">
      <h5>Order Summary</h5>
      <ul class="list-group">
        <?php foreach ($items as $it): 
          $item_subtotal = $it['quantity'] * $it['price'];
        ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <?php echo htmlspecialchars($it['name']); ?> x <?php echo $it['quantity']; ?>
            <span>₹<?php echo number_format($item_subtotal,2); ?></span>
          </li>
        <?php endforeach; ?>
        <li class="list-group-item d-flex justify-content-between"><strong>Total</strong><strong>₹<?php echo number_format($total,2); ?></strong></li>
      </ul>
    </div>
  </div>
</div>

<script>
// Place Order button click handler
document.getElementById('place-order-btn').addEventListener('click', function() {
    const address = document.querySelector('textarea[name="address"]').value;
    
    if (!address.trim()) {
        alert('Please enter shipping address first.');
        return;
    }
    
    // Trigger Razorpay payment
    var options = {
        "key": "rzp_test_1TSGXPk46TbXBv", // Your Razorpay test key
        "amount": "<?php echo (int)($total * 100); ?>", // Amount in paise
        "currency": "INR",
        "name": "RaceNex",
        "description": "Racing Parts Order Payment",
        "image": "https://cdn-icons-png.flaticon.com/512/3097/3097132.png",
        "handler": function (response){
            // After successful payment, redirect to success page
            const cartData = <?php echo json_encode($cart->getCartData()); ?>;
            window.location.href = "success.php?payment_id=" + response.razorpay_payment_id + 
                                 "&amount=<?php echo $total; ?>" +
                                 "&cart_data=" + encodeURIComponent(JSON.stringify(cartData)) +
                                 "&address=" + encodeURIComponent(address);
        },
        "prefill": {
            "name": "<?php echo htmlspecialchars(SessionManager::getUserName() ?? 'Customer'); ?>",
            "email": "customer@racenex.com",
            "contact": "9999999999"
        },
        "theme": {
            "color": "#ff3b3b"
        },
        "modal": {
            "ondismiss": function(){
                console.log("Payment cancelled");
            }
        }
    };
    var rzp = new Razorpay(options);
    rzp.open();
});
</script>

</body>
</html>
