<?php
require_once 'classes/AdminSessionManager.php';
require_once 'classes/Admin.php';
require_once 'classes/Product.php';

AdminSessionManager::start();
AdminSessionManager::requireLogin();

$admin = new Admin();
$product = new Product();

$message = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'add' || $action == 'edit') {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $image = trim($_POST['image'] ?? '');
        $stock = intval($_POST['stock'] ?? 0);
        $category = trim($_POST['category'] ?? 'general');
        $status = $_POST['status'] ?? 'active';
        
        // Validation
        if (empty($name) || empty($slug) || $price <= 0) {
            $error = 'Please fill in all required fields with valid values.';
        } else {
            if ($action == 'add') {
                $result = $product->addProduct($name, $slug, $description, $price, $image, $stock, $category, $status);
                if ($result) {
                    $message = 'Product added successfully!';
                } else {
                    $error = 'Failed to add product. Please try again.';
                }
            } else {
                $id = intval($_POST['product_id']);
                $result = $product->updateProduct($id, $name, $slug, $description, $price, $image, $stock, $category, $status);
                if ($result) {
                    $message = 'Product updated successfully!';
                } else {
                    $error = 'Failed to update product. Please try again.';
                }
            }
        }
    } elseif ($action == 'delete') {
        $id = intval($_POST['product_id']);
        $result = $product->deleteProduct($id);
        if ($result) {
            $message = 'Product deleted successfully!';
        } else {
            $error = 'Failed to delete product. Please try again.';
        }
    }
}

// Get all products
$products = $product->getAllProducts();

// Get product for editing
$editProduct = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $editProduct = $product->getProductById($_GET['id']);
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Product Management — RaceNex Admin</title>
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
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
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
                    <a class="nav-link active" href="admin_products.php">
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
                        <span class="navbar-brand mb-0 h1">Product Management</span>
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
                    
                    <!-- Add/Edit Product Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="action" value="<?php echo $editProduct ? 'edit' : 'add'; ?>">
                                <?php if ($editProduct): ?>
                                    <input type="hidden" name="product_id" value="<?php echo $editProduct['id']; ?>">
                                <?php endif; ?>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Product Name *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?php echo htmlspecialchars($editProduct['name'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="slug" class="form-label">Slug *</label>
                                        <input type="text" class="form-control" id="slug" name="slug" 
                                               value="<?php echo htmlspecialchars($editProduct['slug'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($editProduct['description'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="price" class="form-label">Price *</label>
                                        <input type="number" class="form-control" id="price" name="price" 
                                               step="0.01" min="0" value="<?php echo $editProduct['price'] ?? ''; ?>" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="stock" class="form-label">Stock</label>
                                        <input type="number" class="form-control" id="stock" name="stock" 
                                               min="0" value="<?php echo $editProduct['stock'] ?? '0'; ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-control" id="category" name="category">
                                            <option value="general" <?php echo ($editProduct['category'] ?? '') == 'general' ? 'selected' : ''; ?>>General</option>
                                            <option value="engine" <?php echo ($editProduct['category'] ?? '') == 'engine' ? 'selected' : ''; ?>>Engine</option>
                                            <option value="brakes" <?php echo ($editProduct['category'] ?? '') == 'brakes' ? 'selected' : ''; ?>>Brakes</option>
                                            <option value="wheels" <?php echo ($editProduct['category'] ?? '') == 'wheels' ? 'selected' : ''; ?>>Wheels</option>
                                            <option value="exhaust" <?php echo ($editProduct['category'] ?? '') == 'exhaust' ? 'selected' : ''; ?>>Exhaust</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-control" id="status" name="status">
                                            <option value="active" <?php echo ($editProduct['status'] ?? 'active') == 'active' ? 'selected' : ''; ?>>Active (Visible to customers)</option>
                                            <option value="inactive" <?php echo ($editProduct['status'] ?? '') == 'inactive' ? 'selected' : ''; ?>>Inactive (Hidden from customers)</option>
                                        </select>
                                        <small class="text-muted">Only active products are shown to customers on the main website.</small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image Path</label>
                                    <input type="text" class="form-control" id="image" name="image" 
                                           placeholder="images/product.jpg" value="<?php echo htmlspecialchars($editProduct['image'] ?? ''); ?>">
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-admin">
                                        <i class="bi bi-save me-2"></i><?php echo $editProduct ? 'Update Product' : 'Add Product'; ?>
                                    </button>
                                    <?php if ($editProduct): ?>
                                        <a href="admin_products.php" class="btn btn-outline-secondary">Cancel</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Products List -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">All Products (<?php echo count($products); ?>)</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($products)): ?>
                                <p class="text-muted text-center">No products found. Add your first product above.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover">
                                        <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Name</th>
                                                <th>Price</th>
                                                <th>Stock</th>
                                                <th>Category</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products as $prod): ?>
                                                <tr>
                                                    <td>
                                                        <?php if ($prod['image']): ?>
                                                            <img src="<?php echo htmlspecialchars($prod['image']); ?>" 
                                                                 alt="<?php echo htmlspecialchars($prod['name']); ?>" 
                                                                 class="product-image">
                                                        <?php else: ?>
                                                            <div class="product-image bg-secondary d-flex align-items-center justify-content-center">
                                                                <i class="bi bi-image text-muted"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($prod['name']); ?></div>
                                                        <small class="text-muted"><?php echo htmlspecialchars($prod['slug']); ?></small>
                                                    </td>
                                                    <td>$<?php echo number_format($prod['price'], 2); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $prod['stock'] > 10 ? 'success' : ($prod['stock'] > 0 ? 'warning' : 'danger'); ?>">
                                                            <?php echo $prod['stock']; ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info"><?php echo ucfirst($prod['category'] ?? 'general'); ?></span>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?php echo ($prod['status'] ?? 'active') == 'active' ? 'success' : 'secondary'; ?>">
                                                            <?php echo ucfirst($prod['status'] ?? 'active'); ?>
                                                        </span>
                                                        <?php if (($prod['status'] ?? 'active') == 'active'): ?>
                                                            <br><small class="text-success">✓ Visible to customers</small>
                                                        <?php else: ?>
                                                            <br><small class="text-muted">✗ Hidden from customers</small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo date('M j, Y', strtotime($prod['created_at'])); ?></td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <a href="admin_products.php?action=edit&id=<?php echo $prod['id']; ?>" 
                                                               class="btn btn-outline-primary">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-outline-danger" 
                                                                    onclick="deleteProduct(<?php echo $prod['id']; ?>, '<?php echo htmlspecialchars($prod['name']); ?>')">
                                                                <i class="bi bi-trash"></i>
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
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the product "<span id="productName"></span>"?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="product_id" id="deleteProductId">
                        <button type="submit" class="btn btn-danger">Delete Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteProduct(id, name) {
            document.getElementById('deleteProductId').value = id;
            document.getElementById('productName').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
        
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
    </script>
</body>
</html>
