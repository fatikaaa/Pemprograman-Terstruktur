<?php
// FILE: user/booking_history.php
require_once '../config.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id_user = $_SESSION['user_id'];

$sql = "SELECT b.*, f.nama AS nama_film, s.nama_studio, 
        jt.tanggal_tayang, jt.jam_tayang, k.kode_kursi
        FROM booking b
        JOIN jadwal_tayang jt ON b.id_jadwal = jt.id_jadwal
        JOIN film f ON jt.id_film = f.id_film
        JOIN studio s ON jt.id_studio = s.id_studio
        JOIN kursi k ON b.id_kursi = k.id_kursi
        WHERE b.id_user = ?
        ORDER BY b.tanggal_pesan DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_user]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Histori Booking</title>
</head>
<body>
    <p><a href="index.php">← Kembali ke Daftar Film</a></p>
    <hr>
    
    <h1>Histori Booking Saya</h1>
    
    <?php if (count($bookings) > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Film</th>
                    <th>Studio</th>
                    <th>Tanggal Tayang</th>
                    <th>Jam Tayang</th>
                    <th>Kursi</th>
                    <th>Total Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($bookings as $b): ?>
                <tr>
                    <td><?= $b['id_booking'] ?></td>
                    <td><?= htmlspecialchars($b['nama_film']) ?></td>
                    <td><?= $b['nama_studio'] ?></td>
                    <td><?= date('d/m/Y', strtotime($b['tanggal_tayang'])) ?></td>
                    <td><?= date('H:i', strtotime($b['jam_tayang'])) ?></td>
                    <td><?= $b['kode_kursi'] ?></td>
                    <td>Rp <?= number_format($b['total_harga'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Anda belum memiliki histori booking.</p>
    <?php endif; ?>
</body>
</html>