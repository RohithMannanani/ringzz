<?php
require_once './../connect.php';
require_once '../classes/User.php';
$user = new User($conn);
$message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $res = $user->register($_POST['phone'], $_POST['username'], $_POST['password'], $_POST['confirm']);
    $message = $res['message'];
    if($res['success']){
        header('Location: login.php?registered=1');
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Register - Ringzz</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f8f9fa; margin: 0; }
    .form-container { display: flex; justify-content: center; align-items: center; min-height: 90vh; }
    form { width: 350px; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    input { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; }
    button { background: #007bff; color: white; padding: 10px; width: 100%; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background: #0056b3; }
    .msg { text-align: center; color: red; }
  </style>
</head>
<body>
  <?php include '../header.php'; ?>
  <div class="form-container">
    <form method="POST">
      <h2 style="text-align:center;">Ringzz Register</h2>
      <input type="text" name="phone" placeholder="Phone number" required>
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="confirm" placeholder="Confirm Password" required>
      <button type="submit">Register</button>
      <p style="text-align:center;">Already have an account? <a href="login.php">Login here</a></p>
      <p class="msg"><?= htmlspecialchars($message) ?></p>
    </form>
  </div>
</body>
</html>
