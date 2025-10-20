<?php
require_once 'classes/AdminSessionManager.php';
require_once 'classes/Product.php';

AdminSessionManager::start();
AdminSessionManager::requireLogin();

$product = new Product();
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $image = trim($_POST['image'] ?? '');
    $stock = intval($_POST['stock'] ?? 0);
    $category = trim($_POST['category'] ?? 'general');
    
    // Validation
    if (empty($name) || empty($slug) || $price <= 0) {
        $error = 'Please fill in all required fields with valid values.';
    } else {
        // Always set new products as active (visible to customers)
        $result = $product->addProduct($name, $slug, $description, $price, $image, $stock, $category, 'active');
        if ($result) {
            $message = 'Product added successfully! It is now visible to customers on the main website.';
            // Clear form data after successful submission
            $name = $slug = $description = $image = '';
            $price = $stock = 0;
            $category = 'general';
        } else {
            $error = 'Failed to add product. Please try again.';
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Add Product — RaceNex Admin</title>
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
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-admin:hover {
            background: #e03434;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn-outline-admin {
            color: #ff3b3b;
            border-color: #ff3b3b;
        }
        .btn-outline-admin:hover {
            background: #ff3b3b;
            color: #fff;
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
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #444;
            color: #fff;
            border-radius: 8px;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #ff3b3b;
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(255, 59, 59, 0.25);
        }
        .form-control::placeholder {
            color: #aaa;
        }
        .form-label {
            color: #fff;
            font-weight: 500;
        }
        .required {
            color: #ff3b3b;
        }
        .success-alert {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid #28a745;
            color: #28a745;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .error-alert {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #dc3545;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .info-box {
            background: rgba(23, 162, 184, 0.2);
            border: 1px solid #17a2b8;
            color: #17a2b8;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
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
                    <a class="nav-link active" href="admin_add_product.php">
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
                        <span class="navbar-brand mb-0 h1">Add New Product</span>
                        <div class="navbar-nav ms-auto">
                            <span class="navbar-text me-3">
                                Welcome, <?php echo htmlspecialchars(AdminSessionManager::getAdminName()); ?>
                            </span>
                            <a class="btn btn-outline-light btn-sm" href="index.php" target="_blank">
                                <i class="bi bi-eye me-1"></i>View Customer Site
                            </a>
                        </div>
                    </div>
                </nav>
                
                <div class="p-4">
                    <?php if ($message): ?>
                        <div class="success-alert">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="error-alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Info Box -->
                    <div class="info-box">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Product Visibility:</strong> Products added through this page will automatically be visible to customers on the main website. You can manage product status later in the "All Products" section.
                    </div>
                    
                    <!-- Add Product Form -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="bi bi-plus-circle me-2"></i>Add New Product
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="productForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">
                                            Product Name <span class="required">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo htmlspecialchars($name ?? ''); ?>" 
                                               placeholder="Enter product name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="slug" class="form-label">
                                            URL Slug <span class="required">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="slug" name="slug" 
                                               value="<?php echo htmlspecialchars($slug ?? ''); ?>" 
                                               placeholder="product-url-slug" required>
                                        <small class="text-muted">This will be used in the product URL</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Product Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" 
                                              placeholder="Describe the product features and benefits"><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="price" class="form-label">
                                            Price <span class="required">*</span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" id="price" name="price" 
                                                   step="0.01" min="0" value="<?php echo $price ?? ''; ?>" 
                                                   placeholder="0.00" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="stock" class="form-label">Stock Quantity</label>
                                        <input type="number" class="form-control" id="stock" name="stock" 
                                               min="0" value="<?php echo $stock ?? '0'; ?>" placeholder="0">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-control" id="category" name="category">
                                            <option value="general" <?php echo ($category ?? 'general') == 'general' ? 'selected' : ''; ?>>General</option>
                                            <option value="engine" <?php echo ($category ?? '') == 'engine' ? 'selected' : ''; ?>>Engine</option>
                                            <option value="brakes" <?php echo ($category ?? '') == 'brakes' ? 'selected' : ''; ?>>Brakes</option>
                                            <option value="wheels" <?php echo ($category ?? '') == 'wheels' ? 'selected' : ''; ?>>Wheels</option>
                                            <option value="exhaust" <?php echo ($category ?? '') == 'exhaust' ? 'selected' : ''; ?>>Exhaust</option>
                                            <option value="suspension" <?php echo ($category ?? '') == 'suspension' ? 'selected' : ''; ?>>Suspension</option>
                                            <option value="electrical" <?php echo ($category ?? '') == 'electrical' ? 'selected' : ''; ?>>Electrical</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="image" class="form-label">Image Path</label>
                                        <input type="text" class="form-control" id="image" name="image" 
                                               value="<?php echo htmlspecialchars($image ?? ''); ?>" 
                                               placeholder="images/product.jpg">
                                        <small class="text-muted">e.g., images/brakepads.jpg</small>
                                    </div>
                                </div>
                                
                                <div class="d-flex gap-3 mt-4">
                                    <button type="submit" class="btn btn-admin">
                                        <i class="bi bi-plus-circle me-2"></i>Add Product
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary" onclick="resetForm()">
                                        <i class="bi bi-arrow-clockwise me-2"></i>Reset Form
                                    </button>
                                    <a href="admin_products.php" class="btn btn-outline-admin">
                                        <i class="bi bi-list me-2"></i>View All Products
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Quick Tips -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="bi bi-lightbulb me-2"></i>Quick Tips
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="bi bi-check text-success me-2"></i>Use descriptive product names</li>
                                        <li><i class="bi bi-check text-success me-2"></i>Slug will auto-generate from name</li>
                                        <li><i class="bi bi-check text-success me-2"></i>Set realistic stock quantities</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="bi bi-check text-success me-2"></i>Choose appropriate categories</li>
                                        <li><i class="bi bi-check text-success me-2"></i>Use clear, high-quality images</li>
                                        <li><i class="bi bi-check text-success me-2"></i>Write compelling descriptions</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-generate slug from name
        document.getElementById('name').addEventListener('input', function() {
            const slug = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            document.getElementById('slug').value = slug;
        });
        
        // Reset form function
        function resetForm() {
            document.getElementById('productForm').reset();
        }
        
        // Form validation
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const slug = document.getElementById('slug').value.trim();
            const price = parseFloat(document.getElementById('price').value);
            
            if (!name || !slug || price <= 0) {
                e.preventDefault();
                alert('Please fill in all required fields with valid values.');
                return false;
            }
        });
    </script>
</body>
</html>
