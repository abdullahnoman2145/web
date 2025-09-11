<?php
require_once 'db.php';
session_start();

// Always define message to avoid warning
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($name) || empty($email) || empty($password)) {
        $message = "All fields are required!";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $db   = db_connect();

        // ✅ Check if email already exists
        $check = $db->prepare("SELECT id FROM admins WHERE email=? LIMIT 1");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "This email is already registered!";
        } else {
            // Insert new admin only if email not found
            $stmt = $db->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("sss", $name, $email, $hash);
                if ($stmt->execute()) {
                    // Redirect after successful signup
                    header("Location: admin_login.php?signup=success");
                    exit();
                } else {
                    $message = "Error: " . $stmt->error;
                }
            } else {
                $message = "SQL Error: " . $db->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Signup</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Admin Signup</h2>
    <form method="post">
        <input type="text" name="name" placeholder="Name" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Signup</button>
    </form>
    <p><?php echo $message; ?></p>
    <p>Already have account? 
        <a href="admin_login.php">Login here</a>
    </p>
</div>
</body>
</html>
