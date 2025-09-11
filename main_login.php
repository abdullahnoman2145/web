
<!DOCTYPE html>
<html>
<head>
  <title>Main Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: url('movie-background (3).jpg') no-repeat center center fixed;
      background-size: cover;
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .container {
      text-align: center;
      background: rgba(0, 0, 0, 0.7); /* dark overlay for readability */
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.6);
    }
    .container h2 {
      margin-bottom: 25px;
      color: #ffcc00; /* golden cinema theme */
      font-size: 28px;
    }
    .login-buttons-column {
      display: flex;
      flex-direction: column;   /* vertical buttons */
      gap: 20px;
      margin-top: 20px;
      align-items: center;
    }
    .login-buttons-column button {
      width: 280px;             /* big buttons */
      padding: 18px;
      border: none;
      border-radius: 8px;
      background: #ffcc00;      /* cinema yellow */
      color: #000;
      font-size: 18px;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }
    .login-buttons-column button:hover {
      background: #ffaa00;      /* darker yellow on hover */
      color: #111;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Welcome to Login Portal</h2>

    <div class="login-buttons-column">
      <button onclick="location.href='admin_login.php'">Login as Admin</button>
      <button onclick="location.href='user_login.php'">Login as User</button>
      <button onclick="location.href='staff_login.php'">Login as Staff</button>
    </div>
  </div>
</body>
</html>
