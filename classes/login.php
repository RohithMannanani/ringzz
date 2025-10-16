<?php
require_once __DIR__ . '/../config.php';
require_once '../connect.php';
require_once '../classes/User.php';
$user = new User($conn);
$message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $res = $user->login($_POST['phone'], $_POST['password']);
    if($res['success']){
        if ($res['username'] === 'admin') {
            header('Location: ' . BASE_URL . 'classes/admin/add_ring.php');
        } else {
            header('Location: ' . BASE_URL . 'index.php');
        }
        exit;
    } else {
        $message = $res['message'];
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login - Ringzz</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; margin: 0; }
    .form-container { display: flex; justify-content: center; align-items: center; min-height: 90vh; }
    form { width: 350px; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    input { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; }
    button { background: #28a745; color: white; padding: 10px; width: 100%; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background: #218838; }
    .msg { text-align: center; color: red; }
  </style>
</head>
<body>
  <?php include '../header.php'; ?>
  <div class="form-container">
    <form method="POST">
      <h2 style="text-align:center;">Ringzz Login</h2>
      <input type="text" name="phone" placeholder="Phone number" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
      <p style="text-align:center;">Don’t have an account? <a href="register.php">Register here</a></p>
      <p class="msg"><?= htmlspecialchars($message) ?></p>
    </form>
  </div>
</body>
</html>
