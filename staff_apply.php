<?php
require_once 'db.php';
session_start();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $message = "All fields are required!";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $db = db_connect();

        //  Check if username already exists in staff or applications
        $check = $db->prepare("SELECT id FROM staff WHERE username=? 
                               UNION 
                               SELECT id FROM staff_applications WHERE username=?");
        $check->bind_param("ss", $username, $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = " This username is already taken!";
        } else {
            // Insert new application
            $stmt = $db->prepare("INSERT INTO staff_applications (username, password) VALUES (?, ?)");
            if ($stmt) {
                $stmt->bind_param("ss", $username, $hash);
                if ($stmt->execute()) {
                    $message = " Application submitted! Wait for admin approval.";
                } else {
                    $message = "Error: " . $stmt->error;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Apply for Staff</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h2>Apply for Staff</h2>
    <form method="post">
        <input type="text" name="username" placeholder="Desired Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Apply</button>
    </form>
    <p><?php echo $message; ?></p>
    <p><a href="main_login.php"> Back to Home</a></p>
</div>
</body>
</html>
