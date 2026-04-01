<?php
// file: dashboard admin
// deskripsi: halaman utama dashboard untuk admin mengelola data aspirasi

// inisialisasi session
session_start();

// cek apakah pengguna sudah login
// jika tidak login, arahkan ke halaman login
if($_SESSION['status_login'] != true) {
    header('Location: login.php');
    exit;
}

// include file koneksi database
include 'db.php';

// include file helper untuk menampilkan notif
include 'notif.php';

// query untuk menghitung jumlah aspirasi per status
$aspirasi_result = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM tb_aspirasi GROUP BY status");

// inisialisasi variabel untuk menyimpan total aspirasi
$total_aspirasi = 0;

// inisialisasi variabel untuk menyimpan aspirasi berdasarkan status
$menunggu = 0;
$proses = 0;
$selesai = 0;

// hitung total aspirasi dan jumlah berdasarkan status dari hasil query
if($aspirasi_result && mysqli_num_rows($aspirasi_result) > 0) {
    // loop melalui setiap baris hasil query
    while($row = mysqli_fetch_assoc($aspirasi_result)) {
        // tambahkan jumlah aspirasi ke total
        $total_aspirasi = $total_aspirasi + $row['count'];
        
        // cek status dan simpan jumlahnya
        if($row['status'] == 'menunggu') {
            $menunggu = $row['count'];
        }
        if($row['status'] == 'proses') {
            $proses = $row['count'];   
        }
        if($row['status'] == 'selesai') {
            $selesai = $row['count'];
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ditt Aspirasi</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <!--header-->
    <!-- bagian kepala dashboard -->
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
    <!-- bagian utama dashboard -->
    <div class="section">
        <div class="container">
            <h3>Dashboard</h3>
            
            <?php echo displayNotif(); ?>
            
            <div class="stats-container">
                <div class="stats-card">
                    <h5>Total Aspirasi</h5>
                    <div class="stats-number"><?php echo $total_aspirasi; ?></div>
                </div>
                <div class="stats-card menunggu">
                    <h5>Menunggu</h5>
                    <div class="stats-number"><?php echo $menunggu; ?></div>
                </div>
                <div class="stats-card proses">
                    <h5>Proses</h5>
                    <div class="stats-number"><?php echo $proses; ?></div>
                </div>
                <div class="stats-card selesai">
                    <h5>Selesai</h5>
                    <div class="stats-number"><?php echo $selesai; ?></div>
                </div>
            </div>

            <div class="box">
                <h4>Selamat Datang <?php echo $_SESSION['a_global']->username ?> di Ditt Aspirasi</h4>
            </div>
        </div>
    </div>


    <!--footer-->
    <!-- bagian kaki dashboard -->
    <footer>
        <div class="container">
            <small>Copyright &copy; 2026 - Ditt Aspirasi.</small>
        </div>
    </footer>
</body> 
</html>