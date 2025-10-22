<?php
// user/index.php
session_start();
require_once '../config.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil data film dari database
$stmt = $pdo->query("SELECT * FROM film ORDER BY id_film DESC");
$films = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bioskop Online - Pemesanan Tiket</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        h1 {
            color: #667eea;
            font-size: 2.5em;
        }

        .user-menu {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .user-menu a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .user-menu a:hover {
            background: #f0f0f0;
        }

        .logout-btn {
            background: #f44336;
            color: white !important;
            padding: 8px 16px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }

        .logout-btn:hover {
            background: #da190b;
        }

        .subtitle {
            text-align: center;
            color: white;
            font-size: 1.2em;
            margin-bottom: 30px;
        }

        .movies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .movie-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .movie-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .movie-image {
            width: 100%;
            height: 300px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3em;
        }

        .movie-info {
            padding: 20px;
        }

        .movie-title {
            font-size: 1.3em;
            color: #333;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .movie-details {
            font-size: 0.85em;
            color: #666;
            margin-bottom: 12px;
            line-height: 1.6;
        }

        .movie-details span {
            display: block;
            margin-bottom: 4px;
        }

        .rating {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
            margin-bottom: 12px;
        }

        .book-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .book-btn:hover {
            transform: scale(1.02);
        }

        footer {
            text-align: center;
            color: white;
            margin-top: 40px;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .movies-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
            
            h1 {
                font-size: 1.8em;
            }

            header {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🎬 BIOSKOP ONLINE</h1>
            <div class="user-menu">
                <span>Halo, <?= htmlspecialchars($_SESSION['nama']) ?></span>
                <a href="index.php">Film</a>
                <a href="booking_history.php">Riwayat</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </header>

        <p class="subtitle">Pesan tiket favorit Anda sekarang juga!</p>

        <div class="movies-grid">
            <?php foreach ($films as $film): ?>
                <div class="movie-card">
                    <div class="movie-image">🎬</div>
                    <div class="movie-info">
                        <div class="movie-title"><?= htmlspecialchars($film['nama_film']) ?></div>
                        <div class="rating"><?= htmlspecialchars($film['genre']) ?></div>
                        <div class="movie-details">
                            <span><strong>Durasi:</strong> <?= htmlspecialchars($film['durasi_film']) ?></span>
                            <span><strong>Harga:</strong> Rp <?= number_format($film['harga'], 0, ',', '.') ?></span>
                        </div>
                        <p style="font-size: 0.9em; color: #666; margin-bottom: 15px;">
                            <?= htmlspecialchars(substr($film['deskripsi'], 0, 100)) ?>...
                        </p>
                        <a href="detail_film.php?id=<?= $film['id_film'] ?>" class="book-btn">
                            Lihat Detail & Pesan
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Bioskop Online. Selamat menonton!</p>
    </footer>
</body>
</html>