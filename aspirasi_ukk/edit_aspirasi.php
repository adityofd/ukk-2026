<?php
// file: edit aspirasi / feedback aspirasi
// deskripsi: halaman untuk admin memberikan feedback dan mengubah status aspirasi

// inisialisasi session
session_start();

// include file koneksi database
include 'db.php';

// include file helper untuk menampilkan notif
include 'notif.php';

// cek apakah pengguna sudah login
// jika tidak login, arahkan ke halaman login
if($_SESSION['status_login'] != true) {
    header('Location: login.php');
    exit;
}

// ambil id pelaporan dari url
$id_pelaporan = $_GET['id'] ?? '';

// cek apakah id pelaporan kosong
if(empty($id_pelaporan)) {
    // buat notifikasi error
    $_SESSION['notif_error'] = "id tidak valid";
    
    // arahkan kembali ke halaman data aspirasi
    header('Location: data_aspirasi.php');
    exit;
}

// escape id pelaporan untuk keamanan
$id_pelaporan = mysqli_real_escape_string($conn, $id_pelaporan);

// query untuk mengambil data aspirasi berdasarkan id
// melakukan join dengan tabel lain untuk mendapatkan keterangan lengkap
$aspirasi = mysqli_query($conn, "SELECT i.*, s.kelas, k.ket_kategori, a.feedback, a.status AS status FROM input_aspirasi i 
                                 LEFT JOIN tb_siswa s ON i.nis = s.nis 
                                 LEFT JOIN tb_kategori k ON i.id_kategori = k.id_kategori 
                                 LEFT JOIN tb_aspirasi a ON i.id_pelaporan = a.id_pelaporan
                                 WHERE i.id_pelaporan = '$id_pelaporan'");

// cek apakah data aspirasi ditemukan
if(!$aspirasi || mysqli_num_rows($aspirasi) == 0) {
    // buat notifikasi error jika data tidak ditemukan
    $_SESSION['notif_error'] = "data tidak ditemukan";
    
    // arahkan kembali ke halaman data aspirasi
    header('Location: data_aspirasi.php');
    exit;
}

// ambil data aspirasi dari query result
$data = mysqli_fetch_assoc($aspirasi);

// cek apakah form update feedback dan status disubmit
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_penyelesaian') {
    // ambil feedback dari form
    $feedback = mysqli_real_escape_string($conn, $_POST['penyelesaian'] ?? '');
    
    // ambil status dari form
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? 'menunggu');
    
    // cek apakah status sudah dipilih
    if($status) {
        // query untuk mengecek apakah data sudah ada di tb_aspirasi
        $cek_data = mysqli_query($conn, "SELECT id_aspirasi FROM tb_aspirasi WHERE id_pelaporan = '$id_pelaporan'");
        
        // cek hasil query
        if($cek_data && mysqli_num_rows($cek_data) > 0) {
            // jika data sudah ada, lakukan update
            $result = mysqli_query($conn, "UPDATE tb_aspirasi SET feedback = '$feedback', status = '$status' WHERE id_pelaporan = '$id_pelaporan'");
        } else {
            // jika data belum ada, lakukan insert
            $result = mysqli_query($conn, "INSERT INTO tb_aspirasi (status, id_pelaporan, feedback) VALUES ('$status', '$id_pelaporan', '$feedback')");
        }
        
        // cek apakah query berhasil dieksekusi
        if($result) {
            // buat notifikasi sukses
            $_SESSION['notif_sukses'] = "data berhasil diperbarui";
            
            // arahkan kembali ke halaman data aspirasi
            header('Location: data_aspirasi.php');
            exit;
        } else {
            // buat notifikasi error jika query gagal
            $_SESSION['notif_error'] = "gagal memperbarui data: " . mysqli_error($conn);
        }
    } else {
        // buat notifikasi error jika status tidak dipilih
        $_SESSION['notif_error'] = "status harus dipilih";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ditt Aspirasi - Feedback Aspirasi</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <!--header-->
    <header>
        <div class="container">
            <h1><a href="dashboard.php">Ditt Aspirasi</a></h1>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="input_data.php">Input Data Siswa</a></li>
                <li><a href="data_aspirasi.php">Data Aspirasi</a></li>
                <li><a href="data_kategori.php">Data Kategori</a></li>
                <li><a href="keluar.php">Keluar</a></li>
            </ul>
        </div>
    </header>

    <!--content-->
    <div class="section">
        <div class="container">
            <h3>Berikan Feedback & Status Aspirasi</h3>
            
            <?php echo displayNotif(); ?>
            
            <div class="box">
                <form method="POST">
                    <input type="hidden" name="action" value="update_penyelesaian">
                    
                    <div class="form-group">
                        <label for="tanggal_input">Tanggal Input</label>
                        <div class="read-only"><?php echo date('d-m-Y H:i', strtotime($data['tanggal_input'] ?? '')); ?></div>
                    </div>

                    <div class="form-group">
                        <label for="nis">NIS Siswa</label>
                        <div class="read-only"><?php echo htmlspecialchars($data['nis'] ?? ''); ?></div>
                    </div>

                    <div class="form-group">
                        <label for="kelas">Kelas</label>
                        <div class="read-only"><?php echo htmlspecialchars($data['kelas'] ?? ''); ?></div>
                    </div>

                    <div class="form-group">
                        <label for="kategori">Kategori Pelaporan</label>
                        <div class="read-only"><?php echo htmlspecialchars($data['ket_kategori'] ?? ''); ?></div>
                    </div>

                    <div class="form-group">
                        <label for="lokasi">Lokasi Terlapor</label>
                        <div class="read-only"><?php echo htmlspecialchars($data['lokasi'] ?? ''); ?></div>
                    </div>

                    <div class="form-group">
                        <label for="keterangan">Keterangan Aspirasi</label>
                        <div class="read-only" style="max-height: 120px; overflow-y: auto; word-wrap: break-word;">
                            <?php echo htmlspecialchars($data['ket'] ?? ''); ?>
                        </div>
                    </div>

                    <?php if(!empty($data['feedback'])) { ?>
                    <div class="form-group">
                        <label for="feedback_lama">Feedback Sebelumnya</label>
                        <div class="read-only" style="background-color: #e8f5e9; border-left: 4px solid #81c784; max-height: 100px; overflow-y: auto;  word-wrap: break-word;">
                            <?php echo htmlspecialchars($data['feedback']); ?>
                        </div>
                    </div>
                    <?php } ?>
                    
                    <div class="form-group">
                        <label for="status">Status Penyelesaian</label>
                        <select name="status" class="input-control" required>
                            <option value="Menunggu" <?php echo ($data['status'] === 'Menunggu' ? 'selected' : ''); ?>>Menunggu</option>
                            <option value="Proses" <?php echo ($data['status'] === 'Proses' ? 'selected' : ''); ?>>Proses</option>
                            <option value="Selesai" <?php echo ($data['status'] === 'Selesai' ? 'selected' : ''); ?>>Selesai</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="penyelesaian">Feedback / Tanggapan Anda</label>
                        <textarea name="penyelesaian" class="input-control" style="height: 150px;" placeholder="Masukkan feedback atau tanggapan untuk aspirasi ini"><?php echo htmlspecialchars($data['feedback'] ?? ''); ?></textarea>
                    </div>

                    <div style="text-align: center; margin-top: 20px;">
                        <a href="data_aspirasi.php" class="btnn" style="background-color: #999; margin-right: 10px; text-decoration: none;">Batal</a>
                        <button type="submit" class="btnn">Simpan Feedback</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--footer-->
    <footer>
        <div class="container">
            <small>Copyright &copy; 2026 - Ditt Aspirasi.</small>
        </div>
    </footer>
</body>
</html>
