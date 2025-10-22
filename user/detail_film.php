<?php
// FILE: user/detail_film.php
require_once '../config.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_film = $_GET['id'];
$is_logged_in = isset($_SESSION['user_id']);

// Ambil Detail Film
$stmt_film = $pdo->prepare("SELECT * FROM film WHERE id_film = ?");
$stmt_film->execute([$id_film]);
$film = $stmt_film->fetch(PDO::FETCH_ASSOC);

if (!$film) {
    die("Film tidak ditemukan.");
}

// Ambil Jadwal Tayang
$date_today = date('Y-m-d');
$sql_jadwal = "SELECT jt.id_jadwal, jt.tanggal_tayang, jt.jam_tayang, s.nama_studio
               FROM jadwal_tayang jt
               JOIN studio s ON jt.id_studio = s.id_studio
               WHERE jt.id_film = ? AND jt.tanggal_tayang >= ?
               ORDER BY jt.tanggal_tayang ASC, jt.jam_tayang ASC";

$stmt_jadwal = $pdo->prepare($sql_jadwal);
$stmt_jadwal->execute([$id_film, $date_today]);
$jadwal_tayang = $stmt_jadwal->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Film</title>
</head>
<body>
    <p><a href="index.php">← Kembali ke Daftar Film</a></p>
    <hr>
    
    <h1>Detail Film: <?= htmlspecialchars($film['nama']) ?></h1>
    <p><strong>Genre:</strong> <?= $film['genre'] ?></p>
    <p><strong>Durasi:</strong> <?= $film['durasi'] ?> menit</p>
    <p><strong>Harga Tiket:</strong> Rp <?= number_format($film['harga'], 0, ',', '.') ?></p>
    <p><strong>Deskripsi:</strong> <?= htmlspecialchars($film['deskripsi']) ?></p>
    
    <hr>
    <h2>Jadwal Tayang Tersedia</h2>
    <?php if (count($jadwal_tayang) > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Studio</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($jadwal_tayang as $jadwal): ?>
                <tr>
                    <td><?= date('d M Y', strtotime($jadwal['tanggal_tayang'])) ?></td>
                    <td><?= date('H:i', strtotime($jadwal['jam_tayang'])) ?></td>
                    <td><?= $jadwal['nama_studio'] ?></td>
                    <td>
                        <?php if ($is_logged_in): ?>
                            <a href="booking.php?jadwal=<?= $jadwal['id_jadwal'] ?>">Pesan Kursi</a>
                        <?php else: ?>
                            (Login untuk pesan)
                        <?php endif; ?>
                    </td>
                <?php
// FILE: user/detail_film.php
require_once '../config.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_film = $_GET['id'];
$is_logged_in = isset($_SESSION['user_id']);

// Ambil Detail Film
$stmt_film = $pdo->prepare("SELECT * FROM film WHERE id_film = ?");
$stmt_film->execute([$id_film]);
$film = $stmt_film->fetch(PDO::FETCH_ASSOC);

if (!$film) {
    die("Film tidak ditemukan.");
}

// Ambil Jadwal Tayang
$date_today = date('Y-m-d');
$sql_jadwal = "SELECT jt.id_jadwal, jt.tanggal_tayang, jt.jam_tayang, s.nama_studio
               FROM jadwal_tayang jt
               JOIN studio s ON jt.id_studio = s.id_studio
               WHERE jt.id_film = ? AND jt.tanggal_tayang >= ?
               ORDER BY jt.tanggal_tayang ASC, jt.jam_tayang ASC";

$stmt_jadwal = $pdo->prepare($sql_jadwal);
$stmt_jadwal->execute([$id_film, $date_today]);
$jadwal_tayang = $stmt_jadwal->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Film</title>
</head>
<body>
    <p><a href="index.php">← Kembali ke Daftar Film</a></p>
    <hr>
    
    <h1>Detail Film: <?= htmlspecialchars($film['nama']) ?></h1>
    <p><strong>Genre:</strong> <?= $film['genre'] ?></p>
    <p><strong>Durasi:</strong> <?= $film['durasi'] ?> menit</p>
    <p><strong>Harga Tiket:</strong> Rp <?= number_format($film['harga'], 0, ',', '.') ?></p>
    <p><strong>Deskripsi:</strong> <?= htmlspecialchars($film['deskripsi']) ?></p>
    
    <hr>
    <h2>Jadwal Tayang Tersedia</h2>
    <?php if (count($jadwal_tayang) > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Studio</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($jadwal_tayang as $jadwal): ?>
                <tr>
                    <td><?= date('d M Y', strtotime($jadwal['tanggal_tayang'])) ?></td>
                    <td><?= date('H:i', strtotime($jadwal['jam_tayang'])) ?></td>
                    <td><?= $jadwal['nama_studio'] ?></td>
                    <td>
                        <?php if ($is_logged_in): ?>
                            <a href="booking.php?jadwal=<?= $jadwal['id_jadwal'] ?>">Pesan Kursi</a>
                        <?php else: ?>
                            (Login untuk pesan)
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Belum ada jadwal tayang untuk film ini.</p>
    <?php endif; ?>
</body>
</html></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Belum ada jadwal tayang untuk film ini.</p>
    <?php endif; ?>
</body>
</html>