<?php
session_start();
require_once 'db.php';

if(empty($_SESSION['admin_id'])){
    header("Location: login.php");
    exit;
}

$db = db_connect();

// Add User
if(isset($_POST['add_user'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $stmt = $db->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
    $stmt->bind_param("sss", $name, $email, $password);
    $stmt->execute();
}

// Delete User
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $db->query("DELETE FROM users WHERE id=$id");
}

// Fetch Users
$users = $db->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #000; /* black background */
      color: white;
      margin: 0;
      padding: 0;
    }
    header {
      background: #111;
      padding: 15px;
      text-align: center;
    }
    header h2 {
      margin: 0;
    }
    a.logout {
      color: #e74c3c;
      text-decoration: none;
      float: right;
      margin-right: 20px;
    }
    a.logout:hover {
      color: #ff4d4d;
    }
    .container {
      width: 80%;
      margin: 20px auto;
    }
    form input, form button {
      padding: 8px;
      margin: 5px 0;
      border-radius: 4px;
      border: none;
    }
    form input {
      width: 200px;
    }
    form button {
      background: #3498db;
      color: white;
      cursor: pointer;
    }
    form button:hover {
      background: #2980b9;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 10px;
      border: 1px solid #555;
      text-align: center;
    }
    th {
      background: #3498db;
    }
    tr:nth-child(even) {
      background: #222;
    }
    a.delete-btn {
      color: #e74c3c;
      text-decoration: none;
    }
    a.delete-btn:hover {
      color: #ff4d4d;
    }
    body {
      text-align: center; /* centers inline elements like buttons */
      margin-top: 50px;
      font-family: Arial, sans-serif;
    }

    button {
      background-color: #007BFF; /* Bootstrap blue */
      color: white;
      border: none;
      padding: 12px 24px;
      margin: 10px;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
    }

    button:hover {
      background-color: #0056b3; /* darker blue on hover */
    }
  </style>
</head>
<body>
  <header>
    <h2>Admin Dashboard</h2>
  </header>

  <div class="container">
    <h3>Add User</h3>
    <form method="post">
      <input type="text" name="name" placeholder="Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="add_user">Add User</button>
    </form>

    <h3>User List</h3>
    <table>
      <tr><th>ID</th><th>Name</th><th>Email</th><th>Action</th></tr>
      <?php while($row = $users->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td>
            <a class="delete-btn" href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this user?');">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
  <button onclick="location.href='index.php'">Back to Home Page</button>
  <button onclick="location.href='main_login.php'">Back to the main Log In page</button>
</body>
</html>
