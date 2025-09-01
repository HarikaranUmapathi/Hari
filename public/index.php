<?php
require_once __DIR__ . '/../config.php';
// Simple login form
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $stmt = $mysqli->prepare('SELECT id,password FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    if ($r && password_verify($pass, $r['password'])){
        $_SESSION['user_id'] = $r['id'];
        header('Location: dashboard.php'); exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login - Faculty Leave</title>
  <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
  <div class="container">
    <h2>Login</h2>
    <?php if(!empty($error)) echo '<p class="error">'.esc($error).'</p>';?>
    <form method="POST">
      <label>Email</label>
      <input type="email" name="email" required>
      <label>Password</label>
      <input type="password" name="password" required>
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>

