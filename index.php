<?php
session_start();


if (empty($_SESSION['user_id']) && empty($_SESSION['admin_logged_in'])) {
    header("Location: user_login.php");
    exit;
}

$conn = new mysqli("localhost", "root", "", "movies");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Movie categories
$categories = ["released", "upcoming", "classic", "classic Bangla", "classic Korean", "horror"];
$movies = [];

foreach ($categories as $cat) {
    $result = $conn->query("SELECT * FROM movies WHERE category='$cat' ORDER BY year DESC");
    while ($row = $result->fetch_assoc()) {
        $movies[$cat][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Movie Showcase</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #121212;
            color: white;
            margin: 0;
            padding: 20px;
            text-align: center;
        }
        h1 { margin-bottom: 50px; }
        h2 { border-bottom: 2px solid #ff4500; padding-bottom: 5px; }

        .movie-container {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            margin-top: 15px;
            justify-content: center;
        }
        .movie {
            background: #000;
            border-radius: 10px;
            overflow: hidden;
            width: 150px;
            text-align: center;
            transition: transform 0.2s;
        }
        .movie:hover { transform: scale(1.2); }
        .movie img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .movie-title { padding: 10px 5px; }

        .tab-buttons { margin-bottom: 30px; }
        .tab-buttons button {
            padding: 10px 15px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background: red;
            color: white;
        }
        .tab-buttons button.active,
        .tab-buttons button:hover {
            background: orange;
        }

        
        .back-home {
            position: absolute;
            top: 15px;
            right: 20px;
            padding: 6px 12px;
            font-size: 14px;
            background: #ffcc00;
            color: #000;
            border-radius: 4px;
            text-decoration: none;
            transition: 0.3s;
        }
        .back-home:hover {
            background: #ffaa00;
            color: #111;
        }
    </style>
</head>
<body>
    <!--  Back to Home always visible -->
    <a href="main_login.php" class="back-home"> Back</a>

    <h1> NETFLIX</h1>

    <div class="tab-buttons">
        <?php foreach ($categories as $i => $cat): ?>
            <button onclick="showTab('<?= $cat ?>', this)" class="<?= $i==0 ? 'active' : '' ?>">
                <?= ucfirst($cat) ?>
            </button>
        <?php endforeach; ?>
    </div>

    <?php foreach ($categories as $i => $cat): ?>
        <div id="<?= $cat ?>" class="movie-container" style="display:<?= $i==0 ? 'flex':'none' ?>;">
            <?php if (!empty($movies[$cat])): ?>
                <?php foreach ($movies[$cat] as $movie): ?>
                    <div class="movie">
                        <img src="<?= htmlspecialchars($movie['poster']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                        <div class="movie-title"><?= htmlspecialchars($movie['title']) ?> (<?= $movie['year'] ?>)</div>
                        <?php if (!empty($movie['url'])): ?>
                            <button onclick="playTrailer('<?= $movie['url'] ?>')">▶ Watch movie</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No movies in this category.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <!-- Modal for trailers -->
    <div id="trailerModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); align-items:center; justify-content:center;">
        <div style="position:relative; width:80%; max-width:800px; background:#000; border-radius:10px; overflow:hidden;">
            <span onclick="closeTrailer()" style="position:absolute; right:15px; top:10px; color:#fff; font-size:22px; cursor:pointer;">✖</span>
            <iframe id="trailerFrame" width="100%" height="450" src="" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
        </div>
    </div>

    <script>
        function showTab(tab, button) {
            document.querySelectorAll('.movie-container').forEach(div => div.style.display = 'none');
            document.getElementById(tab).style.display = 'flex';
            document.querySelectorAll('.tab-buttons button').forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
        }

        function playTrailer(url) {
            if (url.includes("youtu.be/")) {
                let videoId = url.split("/").pop().split("?")[0];
                url = "https://www.youtube.com/embed/" + videoId;
            } else if (url.includes("watch?v=")) {
                let videoId = url.split("v=")[1].split("&")[0];
                url = "https://www.youtube.com/embed/" + videoId;
            }
            document.getElementById('trailerFrame').src = url + "?autoplay=1";
            document.getElementById('trailerModal').style.display = "flex";
        }

        function closeTrailer() {
            document.getElementById('trailerFrame').src = "";
            document.getElementById('trailerModal').style.display = "none";
        }
    </script>
</body>
</html>
