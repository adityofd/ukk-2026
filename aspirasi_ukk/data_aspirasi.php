<?php
// file: data aspirasi
// deskripsi: halaman untuk admin melihat, memfilter, dan mengelola data aspirasi

// inisialisasi session
session_start();

// include file koneksi database
include 'db.php';

// include file helper untuk menampilkan notif
include 'notif.php';

// cek login
// jika tidak login, arahkan ke halaman login
if($_SESSION['status_login'] != true) {
    header('Location: login.php');
}

// cek apakah ada form post
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ambil action dari form
    $action = $_POST['action'];
    
    // exit dari proses post
    exit;
}

// ambil filter tipe dari url, default adalah semua
$filter_tipe = $_GET['tipe'] ?? 'semua';

// ambil filter kategori dari url, default kosong
$filter_kategori = $_GET['value'] ?? '';

// ambil filter tanggal dari url, default kosong
$filter_tanggal = $_GET['tgl_awal'] ?? '';

// ambil filter bulan dari url, default kosong
$filter_bulan = $_GET['bulan_tahun'] ?? '';

// ambil filter nis dari url, default kosong
$filter_nis = $_GET['nis'] ?? '';

// query dasar untuk mengambil data aspirasi dengan berbagai join
$sql = "SELECT i.*, s.kelas, k.ket_kategori, a.feedback, a.status AS status FROM input_aspirasi i 
        LEFT JOIN tb_siswa s ON i.nis = s.nis 
        LEFT JOIN tb_kategori k ON i.id_kategori = k.id_kategori 
        LEFT JOIN tb_aspirasi a ON i.id_pelaporan = a.id_pelaporan
        WHERE 1=1";

// tambahkan filter berdasarkan tanggal jika dipilih
if($filter_tipe === 'tanggal' && $filter_tanggal) {
    $sql .= " AND DATE(i.tanggal_input) = '$filter_tanggal'";
}

// tambahkan filter berdasarkan kategori jika dipilih
if($filter_tipe === 'kategori' && $filter_kategori) {
    $sql .= " AND i.id_kategori = '$filter_kategori'";
}

// tambahkan filter berdasarkan bulan jika dipilih
if($filter_tipe === 'bulan' && $filter_bulan) {
    $sql .= " AND DATE_FORMAT(i.tanggal_input, '%Y-%m') = '$filter_bulan'";
}

// tambahkan filter berdasarkan nis jika dipilih
if($filter_tipe === 'nis' && $filter_nis) {
    $sql .= " AND i.nis = '$filter_nis'";
}

// tambahkan urutan hasil query, dari tanggal terbaru
$sql .= " ORDER BY i.tanggal_input DESC";

// eksekusi query untuk data aspirasi dengan filter
$data_aspirasi = mysqli_query($conn, $sql);

// query untuk mengambil data kategori
$data_kategori = mysqli_query($conn, "SELECT id_kategori, ket_kategori FROM tb_kategori ORDER BY id_kategori");
$data_nis = mysqli_query($conn, "SELECT DISTINCT nis FROM tb_siswa WHERE nis != '' ORDER BY nis");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ditt Aspirasi - Data Aspirasi</title>
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
            <h3>Data Aspirasi Siswa</h3>
            
            <?php echo displayNotif(); ?>
            
            <div class="filter-container">
                <h4>🔍 Filter Data</h4>
                <form method="GET" action="">
                    <div class="filter-group">
                        <label>Jenis Filter:</label>
                        <select name="tipe">
                            <option value="semua" <?php echo ($filter_tipe === 'semua' ? 'selected' : ''); ?>>📋 Semua Aspirasi</option>
                            <option value="tanggal" <?php echo ($filter_tipe === 'tanggal' ? 'selected' : ''); ?>>📅 Per Tanggal</option>
                            <option value="bulan" <?php echo ($filter_tipe === 'bulan' ? 'selected' : ''); ?>>📆 Per Bulan</option>
                            <option value="kategori" <?php echo ($filter_tipe === 'kategori' ? 'selected' : ''); ?>>🏷️ Per Kategori</option>
                            <option value="nis" <?php echo ($filter_tipe === 'nis' ? 'selected' : ''); ?>>👤 Per NIS Siswa</option>
                        </select>
                        <button type="submit" name="action" value="change_filter" class="btnn" style="margin-top: 10px;">Tampilkan Filter Input</button>
                    </div>

                    <?php if($filter_tipe === 'tanggal'): ?>
                    <div class="filter-group">
                        <label>Pilih Tanggal:</label>
                        <input type="date" name="tgl_awal" value="<?php echo htmlspecialchars($filter_tanggal); ?>" required>
                        <button type="submit" class="btnn">🔍 Cari</button>
                    </div>
                    <?php endif; ?>

                    <?php if($filter_tipe === 'bulan'): ?>
                    <div class="filter-group">
                        <label>Pilih Bulan:</label>
                        <input type="month" name="bulan_tahun" value="<?php echo htmlspecialchars($filter_bulan); ?>" required>
                        <button type="submit" class="btnn">🔍 Cari</button>
                    </div>
                    <?php endif; ?>

                    <?php if($filter_tipe === 'kategori'): ?>
                    <div class="filter-group">
                        <label>Pilih Kategori:</label>
                        <select name="value" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php 
                            mysqli_data_seek($data_kategori, 0);
                            while($k = mysqli_fetch_assoc($data_kategori)): ?>
                                <option value="<?php echo $k['id_kategori']; ?>" <?php echo ($filter_kategori == $k['id_kategori'] ? 'selected' : ''); ?>>
                                    <?php echo htmlspecialchars($k['ket_kategori']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <button type="submit" class="btnn">🔍 Cari</button>
                    </div>
                    <?php endif; ?>

                    <?php if($filter_tipe === 'nis'): ?>
                    <div class="filter-group">
                        <label>Pilih NIS Siswa:</label>
                        <select name="nis" required>
                            <option value="">-- Pilih NIS --</option>
                            <?php 
                            mysqli_data_seek($data_nis, 0);
                            while($n = mysqli_fetch_assoc($data_nis)): ?>
                                <option value="<?php echo $n['nis']; ?>" <?php echo ($filter_nis == $n['nis'] ? 'selected' : ''); ?>>
                                    <?php echo htmlspecialchars($n['nis']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <button type="submit" class="btnn">🔍 Cari</button>
                    </div>
                    <?php endif; ?>

                    <div style="margin-top: 10px;">
                        <a href="data_aspirasi.php" class="btn-reset">🔄 Reset Filter</a>
                    </div>
                </form>
            </div>
            
            <?php 
            if($data_aspirasi && mysqli_num_rows($data_aspirasi) > 0) {
                mysqli_data_seek($data_aspirasi, 0);
                $jumlah_menunggu = 0;
                $jumlah_proses = 0;
                $jumlah_selesai = 0;
                
                while($row = mysqli_fetch_assoc($data_aspirasi)) {
                    if($row['status'] === 'menunggu') {
                        $jumlah_menunggu++;
                    } elseif($row['status'] === 'proses') {
                        $jumlah_proses++;
                    } elseif($row['status'] === 'selesai') {
                        $jumlah_selesai++;
                    }
                }
            ?>
                <div class="stats-container">
                    <div class="stats-card"><h5>Total Aspirasi</h5><div class="stats-number"><?php echo mysqli_num_rows($data_aspirasi); ?></div></div>
                    <div class="stats-card menunggu"><h5>Menunggu</h5><div class="stats-number"><?php echo $jumlah_menunggu; ?></div></div>
                    <div class="stats-card proses"><h5>Proses</h5><div class="stats-number"><?php echo $jumlah_proses; ?></div></div>
                    <div class="stats-card selesai"><h5>Selesai</h5><div class="stats-number"><?php echo $jumlah_selesai; ?></div></div>
                </div>
            <?php } ?>
            
            <div class="box">
                <div class="table-container">
                    <table border="1" cellspacing="0" class="table">
                        <thead>
                            <tr>
                                <th width="30px">No</th>
                                <th>Tanggal Input</th>
                                <th>NIS</th>
                                <th>Kelas</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Feedback</th>
                                <th width="100px">Aksi</th>
                            </tr>
                        </thead>
                    <tbody>
                        <?php
                        $nomor = 1;
                        $warna_status = array(
                            'selesai' => '#4CAF50',
                            'proses' => '#FFC107',
                            'menunggu' => '#E53935'
                        );
                        
                        if($data_aspirasi && mysqli_num_rows($data_aspirasi) > 0) {
                            mysqli_data_seek($data_aspirasi, 0);
                            while($aspirasi = mysqli_fetch_assoc($data_aspirasi)):
                                $status = $aspirasi['status'];
                                if(!$status) {
                                    $status = 'menunggu';
                                }
                                $warna = $warna_status[$status];
                        ?>
                        <tr>
                            <td><?php echo $nomor++; ?></td>
                            <td><?php echo date('d-m-Y H:i', strtotime($aspirasi['tanggal_input'])); ?></td>
                            <td><?php echo htmlspecialchars($aspirasi['nis'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($aspirasi['kelas'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($aspirasi['ket_kategori'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($aspirasi['lokasi'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($aspirasi['ket'] ?? ''); ?></td>
                            <td><span style="background-color: <?php echo $warna; ?>; color: white; padding: 5px 10px; border-radius: 4px; display: inline-block; text-transform: capitalize;"><?php echo htmlspecialchars($status); ?></span></td>
                            <td><?php echo htmlspecialchars($aspirasi['feedback'] ?? '') ? htmlspecialchars($aspirasi['feedback']) : '-'; ?></td>
                            <td align="center">
                                <a href="edit_aspirasi.php?id=<?php echo $aspirasi['id_pelaporan']; ?>" class="btnn" style="padding: 6px 18px; display: inline-block; margin-right: 5px; text-decoration: none;">Jawab</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php } else { ?>
                        <tr>
                            <td colspan="10" align="center"><?php echo ($filter_tipe !== 'semua' ? 'Tidak ada data untuk filter yang dipilih' : 'Tidak ada data aspirasi'); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                </div>
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