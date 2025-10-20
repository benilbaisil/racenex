<?php
require_once 'classes/Admin.php';
require_once 'classes/AdminSessionManager.php';

AdminSessionManager::start();

// Redirect if already logged in
if (AdminSessionManager::isLoggedIn()) {
    header('Location: admin_dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        $admin = new Admin();
        $result = $admin->login($email, $password);
        
        if ($result['success']) {
            AdminSessionManager::login($result['admin_id'], $result['admin_name'], $result['role']);
            // Ensure we're redirecting to admin dashboard
            header('Location: admin_dashboard.php');
            exit();
        } else {
            $error = implode(', ', $result['errors']);
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Login — RaceNex</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #071025 0%, #1a1a2e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            color: #fff;
        }
        .login-card {
            background: rgba(26, 26, 46, 0.9);
            border: 1px solid #333;
            border-radius: 15px;
            backdrop-filter: blur(10px);
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
        .admin-brand {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 2rem;
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
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card p-5">
                    <div class="text-center mb-4">
                        <h1 class="admin-brand">
                            <span class="race">Race</span><span class="nex">Nex</span>
                        </h1>
                        <h4 class="text-muted">Admin Panel</h4>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="admin@racenex.com" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Enter your password" required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-admin">Login to Admin Panel</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="index.php" class="text-muted text-decoration-none">
                            ← Back to Main Site
                        </a>
                    </div>
                    
                    <div class="mt-4 p-3 bg-dark rounded">
                        <small class="text-muted">
                            <strong>Default Admin Credentials:</strong><br>
                            Email: admin@racenex.com<br>
                            Password: admin123
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
