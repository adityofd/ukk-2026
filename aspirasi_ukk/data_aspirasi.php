<?php
// ============ BAGIAN INISIALISASI & AUTENTIKASI ============
session_start();
include 'db.php';
if($_SESSION['status_login'] != true) echo '<script>window.location="login.php"</script>';

// ============ BAGIAN PROSES UPDATE ASPIRASI ============
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_penyelesaian') {
    $id_pelaporan = mysqli_real_escape_string($conn, $_POST['id_pelaporan'] ?? '');
    $penyelesaian = mysqli_real_escape_string($conn, $_POST['penyelesaian'] ?? '');
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? 'menunggu');
    
    if($id_pelaporan) {
        if(mysqli_query($conn, "UPDATE input_aspirasi SET penyelesaian = '$penyelesaian', status = '$status' WHERE id_pelaporan = '$id_pelaporan'")) {
            echo '<script>alert("Data berhasil diperbarui"); window.location="data_aspirasi.php"</script>';
        } else {
            echo '<script>alert("Gagal memperbarui data")</script>';
        }
    }
}

// ============ BAGIAN PROSES HAPUS ASPIRASI ============
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_aspirasi') {
    $id_pelaporan = mysqli_real_escape_string($conn, $_POST['id_pelaporan'] ?? '');
    
    if($id_pelaporan) {
        if(mysqli_query($conn, "DELETE FROM input_aspirasi WHERE id_pelaporan = '$id_pelaporan'")) {
            echo '<script>alert("Data berhasil dihapus"); window.location="data_aspirasi.php"</script>';
        } else {
            echo '<script>alert("Gagal menghapus data")</script>';
        }
    }
}

// ============ BAGIAN FILTER & QUERY ============
$filter_type = $_GET['tipe'] ?? 'semua';
$filter_value = $_GET['value'] ?? '';
$tanggal_awal = $_GET['tgl_awal'] ?? '';
$bulan_tahun = $_GET['bulan_tahun'] ?? '';
$nis_filter = $_GET['nis'] ?? '';

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
elseif($filter_type === 'kategori' && $filter_value) {
    $id_kategori = mysqli_real_escape_string($conn, $filter_value);
    $query .= " AND i.id_kategori = '$id_kategori'";
}
elseif($filter_type === 'bulan' && $bulan_tahun) {
    $bulan_tahun_sql = mysqli_real_escape_string($conn, $bulan_tahun);
    $query .= " AND DATE_FORMAT(i.tanggal_input, '%Y-%m') = '$bulan_tahun_sql'";
}
elseif($filter_type === 'nis' && $nis_filter) {
    $nis_sql = mysqli_real_escape_string($conn, $nis_filter);
    $query .= " AND i.nis = '$nis_sql'";
}

$query .= " ORDER BY i.tanggal_input DESC";
$res = mysqli_query($conn, $query);

// ============ BAGIAN AMBIL DATA ============
$kategori_list = mysqli_query($conn, "SELECT id_kategori, ket_kategori FROM tb_kategori ORDER BY id_kategori ASC");
$nis_list = mysqli_query($conn, "SELECT DISTINCT nis FROM tb_siswa WHERE nis IS NOT NULL AND nis != '' ORDER BY nis ASC");
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
    <style>
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .read-only {
            background-color: #f0f0f0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .modal {
            display: none !important;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
        }
        .modal.show {
            display: block !important;
        }
        .modal-content {
            background: white;
            margin: 50px auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .modal-header h3 {
            margin: 0;
        }
        .modal-close {
            cursor: pointer;
            font-size: 28px;
            font-weight: bold;
            color: #999;
        }
        .modal-close:hover {
            color: #000;
            
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
            <h3>Data Aspirasi Siswa</h3>
            
            <div class="filter-container">
                <h4>Filter Data</h4>
                <form method="GET" action="">
                    <div class="filter-group">
                        <label>Jenis Filter:</label>
                        <select name="tipe" onchange="updateFilter(this.value)">
                            <option value="semua" <?php echo ($filter_type === 'semua' ? 'selected' : ''); ?>>Semua Aspirasi</option>
                            <option value="tanggal" <?php echo ($filter_type === 'tanggal' ? 'selected' : ''); ?>>Per Tanggal</option>
                            <option value="bulan" <?php echo ($filter_type === 'bulan' ? 'selected' : ''); ?>>Per Bulan</option>
                            <option value="kategori" <?php echo ($filter_type === 'kategori' ? 'selected' : ''); ?>>Per Kategori</option>
                            <option value="nis" <?php echo ($filter_type === 'nis' ? 'selected' : ''); ?>>Per NIS Siswa</option>
                        </select>
                    </div>

                    <div class="filter-group" id="filter-tanggal" style="display: <?php echo ($filter_type === 'tanggal' ? 'block' : 'none'); ?>">
                        <label>Tanggal:</label>
                        <input type="date" name="tgl_awal" value="<?php echo htmlspecialchars($tanggal_awal); ?>">
                    </div>

                    <div class="filter-group" id="filter-bulan" style="display: <?php echo ($filter_type === 'bulan' ? 'block' : 'none'); ?>">
                        <label>Bulan:</label>
                        <input type="month" name="bulan_tahun" value="<?php echo htmlspecialchars($bulan_tahun); ?>">
                    </div>

                    <div class="filter-group" id="filter-kategori" style="display: <?php echo ($filter_type === 'kategori' ? 'block' : 'none'); ?>">
                        <label>Kategori:</label>
                        <select name="value" onchange="submitFilter()">
                            <option value="">-- Pilih Kategori --</option>
                            <?php
                            if($kategori_list && mysqli_num_rows($kategori_list) > 0) {
                                while($kategori = mysqli_fetch_assoc($kategori_list)) {
                                    $selected = ($filter_value == $kategori['id_kategori'] ? 'selected' : '');
                                    echo '<option value="' . htmlspecialchars($kategori['id_kategori']) . '" ' . $selected . '>' . 
                                         htmlspecialchars($kategori['ket_kategori']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="filter-group" id="filter-nis" style="display: <?php echo ($filter_type === 'nis' ? 'block' : 'none'); ?>">
                        <label>NIS Siswa:</label>
                        <select name="nis" onchange="submitFilter()">
                            <option value="">-- Pilih NIS --</option>
                            <?php
                            if($nis_list && mysqli_num_rows($nis_list) > 0) {
                                while($nis_row = mysqli_fetch_assoc($nis_list)) {
                                    $selected = ($nis_filter == $nis_row['nis'] ? 'selected' : '');
                                    echo '<option value="' . htmlspecialchars($nis_row['nis']) . '" ' . $selected . '>' . 
                                         htmlspecialchars($nis_row['nis']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <a href="data_aspirasi.php" class="btn-reset">Reset</a>
                </form>
            </div>
            
            <?php if($res && mysqli_num_rows($res) > 0) { ?>
                <div class="stats-container">
                    <div class="stats-card">
                        <h5>Total Aspirasi</h5>
                        <div class="stats-number"><?php echo mysqli_num_rows($res); ?></div>
                    </div>
                    <?php
                    // Calculate status breakdown
                    mysqli_data_seek($res, 0);
                    $menunggu = $proses = $selesai = 0;
                    while($row = mysqli_fetch_assoc($res)) {
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
            
            <div class="box">
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
                            <th>Penyelesaian</th>
                            <th width="100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        if($res && mysqli_num_rows($res) > 0) {
                            mysqli_data_seek($res, 0);
                            while($row = mysqli_fetch_assoc($res)) {
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
                            <td align="center" style="vertical-align: middle;" data-id="<?php echo $row['id_pelaporan']; ?>" data-ket="<?php echo htmlspecialchars($row['ket'] ?? ''); ?>" data-lokasi="<?php echo htmlspecialchars($row['lokasi'] ?? ''); ?>" data-penyelesaian="<?php echo htmlspecialchars($row['penyelesaian'] ?? ''); ?>" data-status="<?php echo htmlspecialchars($row['status'] ?? 'menunggu'); ?>">
                                <button type="button" class="btnn" onclick="openEditModal(this)" style="padding: 8px 20px; display: inline-block; margin-right: 5px;">
                                    Jawab
                                </button>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="action" value="delete_aspirasi">
                                    <input type="hidden" name="id_pelaporan" value="<?php echo $row['id_pelaporan']; ?>">
                                    <button type="submit" class="btn-delete-red" onclick="return confirm('Apakah Anda yakin ingin menghapus aspirasi ini?')">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php }
                        } else { ?>
                        <tr>
                            <td colspan="10" align="center"><?php echo ($filter_type !== 'semua' ? 'Tidak ada data untuk filter yang dipilih' : 'Tidak ada data aspirasi'); ?></td>
                        </tr>
                        <?php }
                        if(isset($nis_list)) {
                            mysqli_free_result($nis_list);
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal untuk edit penyelesaian -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Berikan Penyelesaian</h3>
                <span class="modal-close" onclick="closeEditModal()">&times;</span>
            </div>
            <form method="POST" onsubmit="return handleSubmit(event)">
                <input type="hidden" name="action" value="update_penyelesaian">
                <input type="hidden" name="id_pelaporan" id="edit_id" value="">
                
                <div class="form-group">
                    <label for="display_lokasi">Lokasi Terlapor</label>
                    <div id="display_lokasi" style="background-color: #f5f5f5; padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px;">-</div>
                </div>

                <div class="form-group">
                    <label for="display_feedback">Keterangan Aspirasi</label>
                    <div id="display_feedback" style="background-color: #f5f5f5; padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px; max-height: 100px; overflow-y: auto; white-space: pre-wrap; word-wrap: break-word;">-</div>
                </div>

                <div class="form-group" id="display_penyelesaian_group" style="display: none;">
                    <label for="display_penyelesaian">Penyelesaian Sebelumnya</label>
                    <div id="display_penyelesaian" style="background-color: #e8f5e9; padding: 10px; border: 1px solid #81c784; border-radius: 4px; margin-bottom: 15px; max-height: 100px; overflow-y: auto; white-space: pre-wrap; word-wrap: break-word;">-</div>
                </div>
                
                <div class="form-group">
                    <label for="edit_status">Status</label>
                    <select id="edit_status" name="status" class="input-control" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;" required>
                        <option value="menunggu">Menunggu</option>
                        <option value="proses">Proses</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit_penyelesaian">Penyelesaian / Tanggapan Anda</label>
                    <textarea id="edit_penyelesaian" name="penyelesaian" class="input-control" style="height: 120px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-family: Arial, sans-serif;" placeholder="Masukkan penyelesaian/tanggapan untuk aspirasi ini"></textarea>
                </div>

                <div style="text-align: center; margin-top: 20px;">
                    <button type="button" class="btnn" onclick="closeEditModal()" style="background-color: #999; margin-right: 10px;">Batal</button>
                    <button type="submit" class="btnn">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!--footer-->
    <footer>
        <div class="container">
            <small>Copyright &copy; 2026 - Ditt Aspirasi.</small>
        </div>
    </footer>

    <script>
        function updateFilter(type) {
            // Hide all filter groups
            document.getElementById('filter-tanggal').style.display = 'none';
            document.getElementById('filter-bulan').style.display = 'none';
            document.getElementById('filter-kategori').style.display = 'none';
            document.getElementById('filter-nis').style.display = 'none';

            // Show selected filter group
            if(type === 'tanggal') {
                document.getElementById('filter-tanggal').style.display = 'block';
            } else if(type === 'bulan') {
                document.getElementById('filter-bulan').style.display = 'block';
            } else if(type === 'kategori') {
                document.getElementById('filter-kategori').style.display = 'block';
            } else if(type === 'nis') {
                document.getElementById('filter-nis').style.display = 'block';
            }
        }

        function submitFilter() {
            document.querySelector('form').submit();
        }

        function openEditModal(button) {
            var td = button.parentElement;
            var id = td.getAttribute('data-id');
            var lokasi = td.getAttribute('data-lokasi') || '-';
            var ket = td.getAttribute('data-ket') || '-';
            var penyelesaian = td.getAttribute('data-penyelesaian') || '';
            var status = td.getAttribute('data-status') || 'menunggu';
            
            console.log("Opening modal for ID:", id, "Status:", status, "Penyelesaian:", penyelesaian);
            document.getElementById('edit_id').value = id;
            document.getElementById('display_lokasi').textContent = lokasi;
            document.getElementById('display_feedback').textContent = ket;
            
            // Tampilkan penyelesaian sebelumnya jika ada
            var penyelesaianGroup = document.getElementById('display_penyelesaian_group');
            if (penyelesaian && penyelesaian.trim() !== '') {
                penyelesaianGroup.style.display = 'block';
                document.getElementById('display_penyelesaian').textContent = penyelesaian;
            } else {
                penyelesaianGroup.style.display = 'none';
            }
            
            document.getElementById('edit_penyelesaian').value = penyelesaian || '';
            document.getElementById('edit_status').value = status;
            
            var modal = document.getElementById('editModal');
            modal.classList.add('show');
            modal.style.display = 'block';
        }

        function closeEditModal() {
            console.log("Closing modal");
            var modal = document.getElementById('editModal');
            modal.classList.remove('show');
            modal.style.display = 'none';
            document.getElementById('edit_id').value = '';
            document.getElementById('display_lokasi').textContent = '-';
            document.getElementById('display_feedback').textContent = '-';
            document.getElementById('display_penyelesaian').textContent = '-';
            document.getElementById('display_penyelesaian_group').style.display = 'none';
            document.getElementById('edit_penyelesaian').value = '';
            document.getElementById('edit_status').value = 'menunggu';
        }

        function handleSubmit(event) {
            var status = document.getElementById('edit_status').value.trim();
            if (!status) {
                alert("Status harus dipilih");
                event.preventDefault();
                return false;
            }
            return true;
        }

        // Close modal if click outside
        window.onclick = function(event) {
            var modal = document.getElementById('editModal');
            if (event.target == modal) {
                closeEditModal();
            }
        }
    </script>
</body> 
</html>