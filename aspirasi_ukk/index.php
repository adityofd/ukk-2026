<?php
// ============ BAGIAN INISIALISASI ============
include 'db.php';

// ============ BAGIAN FILTER & QUERY ============
$filter_type = $_GET['tipe'] ?? 'semua';
$filter_value = $_GET['value'] ?? '';
$tanggal_awal = $_GET['tgl_awal'] ?? '';

// Buat query dasar
$query = "SELECT i.*, s.kelas, k.ket_kategori FROM input_aspirasi i 
          LEFT JOIN tb_siswa s ON i.nis = s.nis 
          LEFT JOIN tb_kategori k ON i.id_kategori = k.id_kategori 
          WHERE 1=1";

// Terapkan filter sesuai tipe
if($filter_type === 'tanggal' && $tanggal_awal) {
    $tanggal_awal_sql = mysqli_real_escape_string($conn, $tanggal_awal);
    $query .= " AND DATE(i.tanggal_input) = '$tanggal_awal_sql'";
}
elseif($filter_type === 'bulan' && $filter_value) {
    $bulan_tahun = mysqli_real_escape_string($conn, $filter_value);
    $query .= " AND DATE_FORMAT(i.tanggal_input, '%Y-%m') = '$bulan_tahun'";
}
elseif($filter_type === 'siswa' && $filter_value) {
    $nis = mysqli_real_escape_string($conn, $filter_value);
    $query .= " AND i.nis = '$nis'";
}
elseif($filter_type === 'kategori' && $filter_value) {
    $id_kategori = mysqli_real_escape_string($conn, $filter_value);
    $query .= " AND i.id_kategori = '$id_kategori'";
}

$query .= " ORDER BY i.tanggal_input DESC";
$result = mysqli_query($conn, $query);

// ============ BAGIAN AMBIL DATA DROPDOWN ============
$siswa_list = mysqli_query($conn, "SELECT nis, kelas FROM tb_siswa ORDER BY nis ASC");
$kategori_list = mysqli_query($conn, "SELECT id_kategori, ket_kategori FROM tb_kategori ORDER BY id_kategori ASC");
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
    <style>
        .btn-warning {
            background-color: #A7C7E7 !important;
            color: white !important;
        }
        .badge-disposisi {
            background-color: #f44336;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 11px;
            margin-left: 5px;
        }
        .filter-container {
            background-color: #f9f9f9;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .filter-group {
            margin-bottom: 15px;
        }
        .filter-group label {
            display: inline-block;
            width: 150px;
            font-weight: bold;
        }
        .filter-group input,
        .filter-group select {
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 250px;
        }
        .btn-filter {
            padding: 8px 20px;
            background-color: #A7C7E7;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn-filter:hover {
            background-color: #8aadcc;
        }
        .btn-reset {
            padding: 8px 20px;
            background-color: #999;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
            text-decoration: none;
            display: inline-block;
        }
        .stats {
            background-color: #e3f2fd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 4px solid #2196F3;
        }
        .stats p {
            margin: 5px 0;
        }
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
        .btn-tambah-aspirasi {
            display: inline-block;
            padding: 12px 30px;
            background-color: #23ca28;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 20px;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-tambah-aspirasi:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <!--header-->
    <header>
        <div class="container">
            <h1><a href="index.php">Ditt Aspirasi</a></h1>
            <ul>
                <li><a href="login.php">Login Admin</a></li>
            </ul>
        </div>
    </header>

    <!--content-->
    <div class="section">
        <div class="container">
            <h3>Data Aspirasi Siswa</h3>

            <?php if($result && mysqli_num_rows($result) > 0) { ?>
                <div class="stats-container">
                    <div class="stats-card">
                        <h5>Total Aspirasi</h5>
                        <div class="stats-number"><?php echo mysqli_num_rows($result); ?></div>
                    </div>
                    <?php
                    // Calculate status breakdown
                    mysqli_data_seek($result, 0);
                    $menunggu = $proses = $selesai = 0;
                    while($row = mysqli_fetch_assoc($result)) {
                        if($row['status'] === 'menunggu') $menunggu++;
                        elseif($row['status'] === 'proses') $proses++;
                        elseif($row['status'] === 'selesai') $selesai++;
                    }
                    ?>
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
            <?php } ?>
            
            <a href="tambah_data_aspirasi.php" class="btn-tambah-aspirasi">+ Tambah Aspirasi</a>
            
            <div class="box">
                <table border="1" cellspacing="0" class="table">
                    <thead>
                        <tr>
                            <th width="30px">No</th>
                            <th>Tanggal Input</th>
                            <th>NIS</th>
                            <th>Kelas Siswa</th>
                            <th>Kategori Pelaporan</th>
                            <th>Lokasi Terlapor</th>
                            <th>Keterangan Terlapor</th>
                            <th>Status</th>
                            <th>Penyelesaian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if($result && mysqli_num_rows($result) > 0) {
                            mysqli_data_seek($result, 0);
                            while($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td><?php echo $no++ ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal_input'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($row['nis'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['kelas'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['ket_kategori'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['lokasi'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['ket'] ?? ''); ?></td>
                            <td><?php $status = $row['status'] ?? 'menunggu'; $badgeColor = ($status == 'selesai' ? '#4CAF50' : ($status == 'proses' ? '#FFC107' : '#E53935')); ?><span style="background-color: <?php echo $badgeColor; ?>; color: white; padding: 5px 10px; border-radius: 4px; text-transform: capitalize;"><?php echo htmlspecialchars($status); ?></span></td>
                            <td><?php echo htmlspecialchars($row['penyelesaian'] ?? '-'); ?></td>
                        </tr>
                        <?php }
                        } else { ?>
                        <tr>
                            <td colspan="9" align="center"><?php echo ($filter_type !== 'semua' ? 'Tidak ada data untuk filter yang dipilih' : 'Tidak ada data aspirasi'); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!--footer-->
    <footer>
        <div class="container">
            <small>Copyright &copy; 2026 - Ditt Company.</small>
        </div>
    </footer>

    <script>
        function updateFilter(type) {
            // Hide all filter groups
            document.getElementById('filter-tanggal').style.display = 'none';
            document.getElementById('filter-bulan').style.display = 'none';
            document.getElementById('filter-siswa').style.display = 'none';
            document.getElementById('filter-kategori').style.display = 'none';

            // Show selected filter group
            if(type === 'tanggal') {
                document.getElementById('filter-tanggal').style.display = 'block';
            } else if(type === 'bulan') {
                document.getElementById('filter-bulan').style.display = 'block';
            } else if(type === 'siswa') {
                document.getElementById('filter-siswa').style.display = 'block';
            } else if(type === 'kategori') {
                document.getElementById('filter-kategori').style.display = 'block';
            }
        }
    </script>
</body> 
</html>