<?php
require_once 'db.php';
$message = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass = 'admin123'; // fixed password

    if(empty($name) || empty($email)){
        $message = 'All fields required';
    } else {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $db = db_connect();
        // Check if email exists
        $check = $db->prepare("SELECT id FROM admins WHERE email=? LIMIT 1");
        $check->bind_param('s', $email);
        $check->execute();
        $check->store_result();
        if($check->num_rows > 0){
            $message = 'Admin already exists';
        } else {
            $stmt = $db->prepare("INSERT INTO admins (name,email,password) VALUES (?,?,?)");
            $stmt->bind_param('sss', $name, $email, $hash);
            if($stmt->execute()){
                $message = "Admin created!<br>Email: $email<br>Password: $pass<br><b>Delete this file after use!</b>";
            } else {
                $message = 'Error: ' . $stmt->error;
            }
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Create Admin Fixed</title>
</head>
<body>
<h2>Create Admin (Fixed)</h2>
<form method="post">
<label>Name: <input name="name" required></label><br>
<label>Email: <input type="email" name="email" required></label><br>
<button type="submit">Create Admin</button>
</form>

<?php if($message): ?>
<p><?= $message ?></p>
<?php endif; ?>
</body>
</html>
