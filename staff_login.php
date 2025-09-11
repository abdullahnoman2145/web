<?php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); 

    $db = db_connect();
    $stmt = $db->prepare("SELECT id, username, password FROM staff WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($staff_id, $staff_name, $hash);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();

        //  verify hashed password
        if (password_verify($password, $hash)) {
           
            $_SESSION['staff'] = $staff_name;  

            header("Location: staff_add_movie.php");
            exit;
        } else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "Invalid username or password!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Staff Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h2>Staff Login</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>

    <p>Not a staff yet? <a href="staff_apply.php">Apply for Staff</a></p>
    <p><a href="main_login.php"> Back to Home</a></p>
  </div>
</body>
</html>
