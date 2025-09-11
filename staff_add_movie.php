<?php
session_start();

if (empty($_SESSION['staff'])) {
    header("Location: staff_login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "movies");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//  Delete Movie
if (isset($_GET['delete_movie'])) {
    $id = intval($_GET['delete_movie']);
    $conn->query("DELETE FROM movies WHERE id=$id");
    $msg = "Movie deleted successfully!";
}

//  Add Movie
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $year = intval($_POST['year']);
    $url = $conn->real_escape_string($_POST['url']);
    $category = $conn->real_escape_string($_POST['category']);

    // Handle poster upload
    $posterPath = "";
    if (!empty($_FILES['poster']['name'])) {
        $targetDir = "image/"; 
        if (!is_dir($targetDir)) { mkdir($targetDir, 0777, true); }
        $fileName = time() . "_" . basename($_FILES['poster']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['poster']['tmp_name'], $targetFile)) {
            $posterPath = $targetFile; 
        } else {
            $msg = " Poster upload failed!";
        }
    }

    if ($posterPath != "") {
        $sql = "INSERT INTO movies (title, year, poster, url, category) 
                VALUES ('$title', '$year', '$posterPath', '$url', '$category')";
        if ($conn->query($sql)) {
            $msg = " Movie added successfully!";
        } else {
            $msg = " Error: " . $conn->error;
        }
    }
}

//  Fetch All Movies
$movies = $conn->query("SELECT * FROM movies ORDER BY year DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Staff - Manage Movies</title>
    <style>
        body { font-family: Arial; background:#121212; color:white; text-align:center; margin:0; padding:0; }
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
        h1 { color: #ffcc00; }
        form { margin:auto; width:400px; background:#222; padding:20px; border-radius:10px; }
        input, select { width:100%; padding:10px; margin:10px 0; border:none; border-radius:5px; }
        button { padding:10px 20px; border:none; border-radius:5px; background:red; color:white; cursor:pointer; }
        button:hover { background:orange; }
        .logout { position:absolute; top:15px; right:20px; background:#e74c3c; color:white; padding:6px 12px; border-radius:4px; text-decoration:none; }
        .logout:hover { background:#c0392b; }

        /* Highlighted Movie Page Button */
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
        .go-movie:hover { background: #ffaa00; color: #111; }

        /* Table Style */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #444; text-align: center; }
        th { background: #e50914; color:white; }
        tr:nth-child(even) { background: #222; }
        img { width: 80px; height: 100px; object-fit: cover; }
        a.delete-btn { color: #ff4444; text-decoration: none; }
        a.delete-btn:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="container">
    <!--  Logout button -->
    <a href="logout.php" class="logout">Logout</a>

    <h1> Staff Panel - Manage Movies</h1>

    <!--  Highlighted Go to Movie Page -->
    <a href="index.php" class="go-movie"> Go to Movie Page</a>

    <?php if (!empty($msg)) echo "<p>$msg</p>"; ?>

    <!-- Add Movie Form -->
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Movie Title" required>
        <input type="number" name="year" placeholder="Year" required>
        <label style="float:left;">Poster Image:</label>
        <input type="file" name="poster" accept="image/*" required>
        <input type="text" name="url" placeholder="YouTube Trailer URL (optional)">
        <select name="category" required>
            <option value="">-- Select Category --</option>
            <option value="released">Released</option>
            <option value="upcoming">Upcoming</option>
            <option value="classic">Classic</option>
            <option value="classic Bangla">Classic Bangla</option>
            <option value="classic Korean">Classic Korean</option>
            <option value="horror">Horror</option>
        </select>
        <button type="submit">Add Movie</button>
    </form>

    <!-- Movie List -->
    <h2> All Movies</h2>
    <table>
        <tr>
            <th>ID</th><th>Poster</th><th>Title</th><th>Year</th><th>Category</th><th>Action</th>
        </tr>
        <?php while ($row = $movies->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><img src="<?= htmlspecialchars($row['poster']) ?>" alt=""></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= $row['year'] ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><a href="?delete_movie=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Delete this movie?');"> Remove</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
