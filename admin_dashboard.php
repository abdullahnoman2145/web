<?php
session_start();
require_once 'db.php';

//  Only admins can access
if (empty($_SESSION['admin_id']) || empty($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

$db = db_connect();

// Approve Application
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $app = $db->query("SELECT * FROM staff_applications WHERE id=$id")->fetch_assoc();

    if ($app && $app['status'] == 'pending') {
        $check = $db->prepare("SELECT id FROM staff WHERE username=? LIMIT 1");
        $check->bind_param("s", $app['username']);
        $check->execute();
        $check->store_result();

        if ($check->num_rows == 0) {
            $stmt = $db->prepare("INSERT INTO staff (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $app['username'], $app['password']);
            $stmt->execute();
        }

        $db->query("UPDATE staff_applications SET status='approved' WHERE id=$id");
        $message = " Application approved and staff added!";
    }
}

// Reject Application
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    $db->query("UPDATE staff_applications SET status='rejected' WHERE id=$id");
    $message = " Application rejected!";
}

// Remove Application (any status)
if (isset($_GET['delete_app'])) {
    $id = intval($_GET['delete_app']);
    $db->query("DELETE FROM staff_applications WHERE id=$id");
    $message = " Application removed successfully!";
}

// Admin Add Staff Manually
if (isset($_POST['add_staff'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $check = $db->prepare("SELECT id FROM staff WHERE username=? LIMIT 1");
        $check->bind_param("s", $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = " This username is already taken!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO staff (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hash);
            $stmt->execute();
            $message = " Staff created successfully!";
        }
    } else {
        $message = " Both fields are required!";
    }
}

// Delete Staff
if (isset($_GET['delete_staff'])) {
    $id = intval($_GET['delete_staff']);

    $staffRow = $db->query("SELECT username FROM staff WHERE id=$id")->fetch_assoc();
    if ($staffRow) {
        $username = $staffRow['username'];

        $db->query("DELETE FROM staff WHERE id=$id");
        $db->query("DELETE FROM staff_applications WHERE username='$username'");
    }

    $message = " Staff deleted successfully!";
}

// Fetch applications
$apps = $db->query("SELECT * FROM staff_applications ORDER BY created_at DESC");

// Fetch staff
$staffs = $db->query("SELECT * FROM staff ORDER BY id ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Manage Staff</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #121212;
            color: white;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .container {
            width: 90%;
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background: #1e1e1e;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(255,255,255,0.1);
            position: relative;
        }
        h2 { color: #ffcc00; margin-bottom: 15px; }
        h3 { margin-top: 25px; color: #ffa500; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 10px;
            border: 1px solid #444;
            text-align: center;
        }
        th {
            background: #e50914;
            color: white;
        }
        tr:nth-child(even) { background: #222; }

        a { color: #ffcc00; text-decoration: none; }
        a:hover { text-decoration: underline; }

        form input {
            padding: 8px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            width: 200px;
        }
        form button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            background: #e50914;
            color: white;
            cursor: pointer;
        }
        form button:hover { background: orange; }

        p { text-align: center; color: lightgreen; }

        /*  Highlighted Movie Page Button */
        .go-movie {
            display: block;
            width: 240px;
            margin: 20px auto;
            padding: 14px 20px;
            text-align: center;
            background: #ffcc00;
            color: #000;
            font-weight: bold;
            border-radius: 6px;
            text-decoration: none;
            font-size: 18px;
            transition: 0.3s;
        }
        .go-movie:hover {
            background: #ffaa00;
            color: #111;
        }

        /*  Logout Button */
        .logout-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            padding: 6px 12px;
            font-size: 14px;
            background: #e74c3c;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
            transition: 0.3s;
        }
        .logout-btn:hover { background: #c0392b; }
    </style>
</head>
<body>
<div class="container">
    <!--  Logout -->
    <a href="logout.php" class="logout-btn">Logout</a>

    <h2>Admin Dashboard - Manage Staff</h2>

    <!--  Highlighted Movie Page Button -->
    <a href="index.php" class="go-movie"> Go to Movie Page</a>

    <?php if (!empty($message)) echo "<p>$message</p>"; ?>

    <h3>Staff Applications</h3>
    <table>
      <tr><th>ID</th><th>Username</th><th>Status</th><th>Action</th></tr>
      <?php while ($row = $apps->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= ucfirst($row['status']) ?></td>
          <td>
            <?php if ($row['status'] == 'pending'): ?>
              <a href="?approve=<?= $row['id'] ?>"> Approve</a> |
              <a href="?reject=<?= $row['id'] ?>"> Reject</a> |
              <a href="?delete_app=<?= $row['id'] ?>" onclick="return confirm('Remove this application permanently?');"> Remove</a>
            <?php else: ?>
              <a href="?delete_app=<?= $row['id'] ?>" onclick="return confirm('Remove this application permanently?');"> Remove</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>

    <h3> Add Staff Manually</h3>
    <form method="post">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" name="add_staff">Add Staff</button>
    </form>

    <h3> Approved Staff List</h3>
    <table>
      <tr><th>ID</th><th>Username</th><th>Action</th></tr>
      <?php while ($row = $staffs->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td>
            <a href="?delete_staff=<?= $row['id'] ?>" onclick="return confirm('Are you sure to delete this staff?');"> Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
</div>
</body>
</html>
