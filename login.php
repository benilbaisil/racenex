<?php
require_once 'classes/SessionManager.php';
require_once 'classes/User.php';

SessionManager::start();

$user = new User();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = $user->login($email, $password);
    
    if ($result['success']) {
        SessionManager::setUser($result['user_id'], $result['user_name'], $result['role']);
        
        // Redirect based on role
        if ($result['role'] === 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: dashboard.php');
        }
        exit;
    } else {
        $errors = $result['errors'];
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login — RaceNex</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background:#0f1724;color:#e6eef8}
    .card{background:linear-gradient(180deg,#111827 0%,#0b1220 100%);border:none;color:#fff}
    .btn-accent{background:#ff3b3b;border:none;color:#fff}
    .btn-accent:hover{background:#e03434;color:#fff}
  </style>
</head>
<body>
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card p-4">
        <h3 class="mb-3">Welcome back</h3>
        <?php if ($errors): ?><div class="alert alert-danger"><?php echo implode('<br>',$errors); ?></div><?php endif; ?>
        <form method="post">
          <div class="mb-3"><label>Email</label><input class="form-control" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"></div>
          <div class="mb-3"><label>Password</label><input type="password" class="form-control" name="password"></div>
          <button class="btn btn-accent w-100">Login</button>
          <div class="mt-3 small text-muted">Don't have an account? <a href="register.php">Register</a></div>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
