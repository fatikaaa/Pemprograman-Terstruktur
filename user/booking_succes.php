<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_booking = $_GET['id'];

// Ambil detail booking
$stmt = $pdo->prepare("
    SELECT b.*, f.nama_film, s.nama_studio, k.kode_kursi, jt.tanggal_tayang, jt.jam_tayang
    FROM booking b
    JOIN jadwal_tayang jt ON b.id_jadwal = jt.id_jadwal
    JOIN film f ON jt.id_film = f.id_film
    JOIN studio s ON jt.id_studio = s.id_studio
    JOIN kursi k ON b.id_kursi = k.id_kursi
    WHERE b.id_booking = ?
");
$stmt->execute([$id_booking]);
$booking = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Berhasil</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-card {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            text-align: center;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            font-size: 5em;
            margin-bottom: 20px;
        }

        h2 {
            color: #4caf50;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }

        .ticket {
            background: #f9f9f9;
            padding: 25px;
            border-radius: 10px;
            margin: 30px 0;
            text-align: left;
            border: 2px dashed #667eea;
        }

        .ticket h3 {
            color: #667eea;
            margin-bottom: 15px;
            text-align: center;
        }

        .ticket p {
            margin-bottom: 10px;
            color: #333;
        }

        .ticket strong {
            color: #667eea;
        }

        .total {
            font-size: 1.3em;
            color: #667eea;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #ddd;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: transform 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-secondary {
            background: #999;
            color: white;
        }

        .btn:hover {
            transform: scale(1.02);
        }

        @media (max-width: 768px) {
            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-icon">✅</div>
        <h2>Pemesanan Berhasil!</h2>
        <p class="subtitle">Tiket Anda telah berhasil dipesan</p>
        
        <div class="ticket">
            <h3>🎫 Detail Tiket</h3>
            <p><strong>ID Booking:</strong> #<?= $booking['id_booking'] ?></p>
            <p><strong>Film:</strong> <?= htmlspecialchars($booking['nama_film']) ?></p>
            <p><strong>Studio:</strong> <?= htmlspecialchars($booking['nama_studio']) ?></p>
            <p><strong>Kursi:</strong> <?= htmlspecialchars($booking['kode_kursi']) ?></p>
            <p><strong>Tanggal:</strong> <?= date('d M Y', strtotime($booking['tanggal_tayang'])) ?></p>
            <p><strong>Jam:</strong> <?= date('H:i', strtotime($booking['jam_tayang'])) ?> WIB</p>
            <p class="total"><strong>Total Bayar:</strong> Rp <?= number_format($booking['total_harga'], 0, ',', '.') ?></p>
        </div>
        
        <p style="color: #999; font-size: 0.9em; margin-bottom: 20px;">
            Simpan tiket ini dan tunjukkan saat masuk bioskop
        </p>
        
        <div class="button-group">
            <a href="index.php" class="btn btn-primary">Pesan Lagi</a>
            <a href="booking_history.php" class="btn btn-secondary">Lihat Riwayat</a>
        </div>
    </div>
</body>
</html>