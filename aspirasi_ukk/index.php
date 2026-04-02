<?php
// file: halaman utama (beranda)
// deskripsi: menampilkan data aspirasi siswa secara publik

// include file koneksi database
include 'db.php';

// query untuk menampilkan semua data aspirasi dari tabel input_aspirasi
// melakukan join dengan tabel tb_siswa untuk mendapatkan kelas siswa
// join dengan tabel tb_kategori untuk mendapatkan keterangan kategori
// join dengan tabel tb_aspirasi untuk mendapatkan feedback dan status
$query = "SELECT i.*, s.kelas, k.ket_kategori, a.feedback, a.status FROM input_aspirasi i 
          LEFT JOIN tb_siswa s ON i.nis = s.nis 
          LEFT JOIN tb_kategori k ON i.id_kategori = k.id_kategori 
          LEFT JOIN tb_aspirasi a ON i.id_pelaporan = a.id_pelaporan
          ORDER BY i.tanggal_input DESC";

// eksekusi query dan simpan hasilnya di variabel result
$result = mysqli_query($conn, $query);
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
                    // hitung breakdown status
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
                            <th>Feedback</th>
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
                            <td>
                                <?php 
                                    $status = $row['status'] ?? 'menunggu';
                                    // Color mapping: Selesai=green, Proses=yellow, Menunggu=red
                                    $badgeColor = '#E53935'; // default red for Menunggu
                                    if($status === 'selesai') {
                                        $badgeColor = '#4CAF50'; // green
                                    } elseif($status === 'proses') {
                                        $badgeColor = '#FFC107'; // yellow
                                    }
                                ?>
                                <span style="background-color: <?php echo $badgeColor; ?>; color: white; padding: 5px 10px; border-radius: 4px; text-transform: capitalize;">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($row['feedback'] ?? '-'); ?></td>
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
</body> 
</html>