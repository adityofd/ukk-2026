<?php
// ============ BAGIAN INISIALISASI ============
session_start();
if($_SESSION['status_login'] != true) echo '<script>window.location="login.php"</script>';
include 'db.php';

// ============ BAGIAN STATISTIK ASPIRASI ============
$aspirasi_result = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM input_aspirasi GROUP BY status");

$total_aspirasi = 0;
$menunggu = 0;
$proses = 0;
$selesai = 0;

// Hitung berdasarkan status
if($aspirasi_result && mysqli_num_rows($aspirasi_result) > 0) {
    while($row = mysqli_fetch_assoc($aspirasi_result)) {
        $total_aspirasi = $total_aspirasi + $row['count'];
        
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stats-card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stats-card h5 {
            margin: 0 0 10px 0;
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
        }
        .stats-card .stats-number {
            font-size: 32px;
            font-weight: bold;
            color: #2196F3;
        }
        .stats-card.menunggu .stats-number {
            color: #E53935;
        }
        .stats-card.proses .stats-number {
            color: #FFC107;
        }
        .stats-card.selesai .stats-number {
            color: #4CAF50;
        }
    </style>
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
            <h3>Dashboard</h3>
            
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
    <footer>
        <div class="container">
            <small>Copyright &copy; 2026 - Ditt Aspirasi.</small>
        </div>
    </footer>
</body> 
</html>