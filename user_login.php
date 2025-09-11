<?php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $db = db_connect();
    $stmt = $db->prepare("SELECT id, name, password FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $name, $hash);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();

        // verify hashed password
        if (password_verify($password, $hash)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid email or password!";
        }
    } else {
        $error = "Invalid email or password!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>User Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h2>User Login</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="user_signup.php">Signup here</a></p>
    <p><a href="main_login.php">Back to Home</a></p>
  </div>
</body>
</html>
