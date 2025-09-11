<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    //  Fixed admin credentials
    $admin_username = "admin";
    $admin_password = "12345";  

    if ($username === $admin_username && $password === $admin_password) {
        // Session set
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_id'] = 1; 
        $_SESSION['admin_username'] = $username;

        
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "Invalid Admin Credentials!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h2>Admin Login</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Admin Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>

    <p><a href="main_login.php">⬅ Back to Home</a></p>
  </div>
</body>
</html>
