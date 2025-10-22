<?php
// FILE: user/booking.php
require_once '../config.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id_user = $_SESSION['user_id'];
$id_jadwal = $_GET['jadwal'] ?? null;
$error = '';

if (!$id_jadwal) {
    header('Location: index.php');
    exit;
}

// Ambil Detail Jadwal
$sql_detail = "SELECT jt.id_jadwal, jt.tanggal_tayang, jt.jam_tayang, 
               f.id_film, f.nama AS nama_film, f.harga, s.id_studio, s.nama_studio
               FROM jadwal_tayang jt
               JOIN film f ON jt.id_film = f.id_film
               JOIN studio s ON jt.id_studio = s.id_studio
               WHERE jt.id_jadwal = ?";
               
$stmt_detail = $pdo->prepare($sql_detail);
$stmt_detail->execute([$id_jadwal]);
$detail = $stmt_detail->fetch(PDO::FETCH_ASSOC);

if (!$detail) {
    die("Jadwal tayang tidak valid.");
}

$id_studio = $detail['id_studio'];
$harga_tiket = $detail['harga'];


// PROSES BOOKING (POST REQUEST)
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_kursi'])) {
    $id_kursi = $_POST['id_kursi'];
    $tanggal_pesan = date('Y-m-d');
    $total_harga = $harga_tiket;
    
    try {
        $pdo->beginTransaction();

        $stmt_kode_kursi = $pdo->prepare("SELECT kode_kursi FROM kursi WHERE id_kursi = ?");
        $stmt_kode_kursi->execute([$id_kursi]);
        $kursi_kode = $stmt_kode_kursi->fetchColumn();
        
        $stmt_check_kursi = $pdo->prepare("SELECT status FROM kursi WHERE id_kursi = ?");
        $stmt_check_kursi->execute([$id_kursi]);
        $kursi_status = $stmt_check_kursi->fetchColumn();

        if ($kursi_status == 'terisi') {
            $error = "Kursi sudah terisi.";
            $pdo->rollBack();
        } else {
            // Update status kursi dan Insert data booking
            $stmt_update_kursi = $pdo->prepare("UPDATE kursi SET status = 'terisi' WHERE id_kursi = ?");
            $stmt_update_kursi->execute([$id_kursi]);

            $sql_booking = "INSERT INTO booking (id_user, id_jadwal, id_kursi, tanggal_pesan, jumlah_tiket, total_harga) 
                            VALUES (?, ?, ?, ?, 1, ?)";
            $stmt_booking = $pdo->prepare($sql_booking);
            $stmt_booking->execute([$id_user, $id_jadwal, $id_kursi, $tanggal_pesan, $total_harga]);
            
            $pdo->commit();
            
            // Simpan data konfirmasi untuk histori
            $_SESSION['booking_success'] = [
                'nama' => $_SESSION['user_nama'],
                'film' => $detail['nama_film'],
                'jadwal' => date('d M Y', strtotime($detail['tanggal_tayang'])) . ' - ' . date('H:i', strtotime($detail['jam_tayang'])),
                'kursi' => $kursi_kode, 
                'total' => $total_harga
            ];
            
            header("Location: booking_history.php");
            exit;
        }

    } catch(Exception $e) {
        $pdo->rollBack();
        $error = "Terjadi kesalahan saat memproses booking.";
    }
}


// AMBIL DATA KURSI STUDIO
$sql_kursi = "SELECT k.id_kursi, k.kode_kursi, k.status
              FROM kursi k
              WHERE k.id_studio = ?
              ORDER BY k.kode_kursi ASC";

$stmt_kursi = $pdo->prepare($sql_kursi);
$stmt_kursi->execute([$id_studio]);
$kursi_list = $stmt_kursi->fetchAll(PDO::FETCH_ASSOC);

$kursi_per_baris = [];
foreach ($kursi_list as $kursi) {
    $baris = substr($kursi['kode_kursi'], 0, 1);
    $kursi_per_baris[$baris][] = $kursi;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesan Kursi</title>
    <style>
        .kursi-grid { display: block; max-width: 400px; margin: 20px auto; }
        .kursi-baris { display: flex; margin-bottom: 5px; }
        .kursi-baris span { width: 30px; font-weight: bold; }
        .kursi { 
            width: 30px; height: 30px; border: 1px solid #000; margin-right: 5px; 
            text-align: center; line-height: 30px; cursor: pointer; display: inline-block;
        }
        .tersedia { background-color: lightgreen; }
        .terisi { background-color: red; cursor: not-allowed; }
        .dipilih { background-color: blue; color: white; border-color: blue; }
        .screen { background: #333; color: white; text-align: center; padding: 10px; margin: 20px 0; }
    </style>
</head>
<body>
    <p><a href="detail_film.php?id=<?= $detail['id_film'] ?>">← Kembali</a></p>
    <hr>

    <h1>Pesan Kursi untuk: <?= htmlspecialchars($detail['nama_film']) ?></h1>
    <p>Jadwal: <?= date('d M Y', strtotime($detail['tanggal_tayang'])) ?> | <?= date('H:i', strtotime($detail['jam_tayang'])) ?></p>
    <p>Harga: Rp <?= number_format($harga_tiket, 0, ',', '.') ?></p>
    
    <?php if($error): ?><p style="color: red;"><?= $error ?></p><?php endif; ?>

    <div class="screen">LAYAR</div>
    
    <form method="POST" id="form-booking">
        <input type="hidden" name="id_kursi" id="id_kursi_input" required>
        
        <div class="kursi-grid">
            <?php foreach($kursi_per_baris as $baris => $kursi_baris): ?>
                <div class="kursi-baris">
                    <span><?= $baris ?></span>
                    <?php foreach($kursi_baris as $kursi): 
                        $status_class = $kursi['status'];
                        $is_clickable = ($kursi['status'] == 'tersedia') ? 'data-id="'.$kursi['id_kursi'].'"' : 'disabled';
                    ?>
                        <div class="kursi <?= $status_class ?>" <?= $is_clickable ?>>
                            <?= substr($kursi['kode_kursi'], 1) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <button type="submit" id="submit-btn" disabled>Konfirmasi Pesanan (1 Tiket)</button>
    </form>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kursiElements = document.querySelectorAll('.kursi.tersedia');
            const idKursiInput = document.getElementById('id_kursi_input');
            const submitBtn = document.getElementById('submit-btn');

            kursiElements.forEach(kursi => {
                kursi.addEventListener('click', function() {
                    if (this.classList.contains('terisi')) return;

                    kursiElements.forEach(k => k.classList.remove('dipilih'));
                    this.classList.add('dipilih');
                    
                    idKursiInput.value = this.dataset.id;
                    submitBtn.disabled = false;
                });
            });
        });
    </script>
</body>
</html>