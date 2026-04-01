<?php
// file: data kategori
// deskripsi: halaman untuk admin mengelola kategori aspirasi

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

// proses tambah kategori baru
if(isset($_POST['submit'])) {
    // ambil data keterangan kategori dari form
    $ket_kategori = mysqli_real_escape_string($conn, $_POST['ket_kategori']);
    
    // validasi: cek apakah keterangan kategori kosong
    if(empty($ket_kategori)) {
        // buat notifikasi error
        $_SESSION['notif_error'] = "keterangan kategori harus diisi!";
    } else {
        // query untuk mengecek apakah kategori dengan nama yang sama sudah ada
        $cek = mysqli_query($conn, "SELECT * FROM tb_kategori WHERE ket_kategori = '$ket_kategori'");
        
        // cek apakah kategori sudah ada
        if(mysqli_num_rows($cek) > 0) {
            // buat notifikasi error jika kategori sudah ada
            $_SESSION['notif_error'] = "kategori sudah ada!";
        } else {
            // query untuk mendapatkan id kategori terbaru
            $max_id = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(id_kategori) as max_id FROM tb_kategori"));
            
            // hitung id kategori baru dengan menambah 1
            $new_id = ($max_id['max_id'] ?? 0) + 1;
            
            // insert kategori baru ke database
            if(mysqli_query($conn, "INSERT INTO tb_kategori (id_kategori, ket_kategori) VALUES ('$new_id', '$ket_kategori')")) {
                // buat notifikasi sukses
                $_SESSION['notif_sukses'] = "kategori berhasil ditambahkan!";
            } else {
                $_SESSION['notif_error'] = "gagal menambahkan kategori!";
            }
        }
    }
}

// bagian update kategori
if(isset($_POST['action']) && $_POST['action'] === 'update') {
    $id_kategori = mysqli_real_escape_string($conn, $_POST['id_kategori']);
    $ket_kategori = mysqli_real_escape_string($conn, $_POST['ket_kategori']);
    
    if(empty($ket_kategori)) {
        $_SESSION['notif_error'] = "Keterangan kategori harus diisi!";
    } else {
        // cek apakah kategori dengan nama yang sama sudah ada (selain yang sedang diedit)
        $cek = mysqli_query($conn, "SELECT * FROM tb_kategori WHERE ket_kategori = '$ket_kategori' AND id_kategori != '$id_kategori'");
        
        if(mysqli_num_rows($cek) > 0) {
            $_SESSION['notif_error'] = "kategori dengan nama yang sama sudah ada!";
        } else {
            if(mysqli_query($conn, "UPDATE tb_kategori SET ket_kategori = '$ket_kategori' WHERE id_kategori = '$id_kategori'")) {
                $_SESSION['notif_sukses'] = "kategori berhasil diperbarui!";
            } else {
                $_SESSION['notif_error'] = "gagal memperbarui kategori!";
            }
        }
    }
}

// bagian hapus kategori
if(isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id_kategori = mysqli_real_escape_string($conn, $_POST['id_kategori']);
    
    // cek apakah kategori masih digunakan
    $cek_aspirasi = mysqli_query($conn, "SELECT COUNT(*) as count FROM input_aspirasi WHERE id_kategori = '$id_kategori'");
    $aspirasi_count = mysqli_fetch_assoc($cek_aspirasi)['count'];
    
    if($aspirasi_count > 0) {
        $_SESSION['notif_error'] = "kategori tidak bisa dihapus! masih digunakan oleh " . $aspirasi_count . " aspirasi.";
    } else {
        // hapus kategori
        if(mysqli_query($conn, "DELETE FROM tb_kategori WHERE id_kategori = '$id_kategori'")) {
            $_SESSION['notif_sukses'] = "kategori berhasil dihapus!";
        } else {
            $_SESSION['notif_error'] = "gagal menghapus kategori!";
        }
    }
}

// bagian ambil data
$data_kategori = mysqli_query($conn, "SELECT * FROM tb_kategori ORDER BY id_kategori ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ditt Aspirasi - Data Kategori</title>
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
            <h3>Data Kategori</h3>

            <?php echo displayNotif(); ?>

            <div class="box">
                <h4>Tambah Kategori Baru</h4>
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="ket_kategori">Keterangan Kategori:</label>
                        <input type="text" id="ket_kategori" name="ket_kategori" placeholder="Masukkan keterangan kategori">
                    </div>
                    <input type="submit" name="submit" value="Tambah Kategori" class="btnn">
                </form>
            </div>

            <h3 style="margin-top: 30px;">Daftar Kategori Yang Sudah Ada</h3>
            <div class="box">
                <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f0f0f0;">
                            <th>No</th>
                            <th>ID Kategori</th>
                            <th>Keterangan Kategori</th>
                            <th>Total Aspirasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if(mysqli_num_rows($data_kategori) > 0) {
                            mysqli_data_seek($data_kategori, 0);
                            while($row = mysqli_fetch_assoc($data_kategori)) {
                                // Hitung jumlah aspirasi per kategori
                                $aspirasi_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM input_aspirasi WHERE id_kategori = '" . $row['id_kategori'] . "'");
                                $aspirasi_count = mysqli_fetch_assoc($aspirasi_query)['count'];
                                
                                echo '
                                <tr>
                                    <td>' . $no . '</td>
                                    <td>' . htmlspecialchars($row['id_kategori']) . '</td>
                                    <td>' . htmlspecialchars($row['ket_kategori']) . '</td>
                                    <td align="center">' . $aspirasi_count . '</td>
                                    <td align="center">
                                        <button type="button" class="btnn" onclick="editKategori(' . $row['id_kategori'] . ', \'' . htmlspecialchars($row['ket_kategori'], ENT_QUOTES) . '\')" style="padding: 6px 12px; margin-right: 5px;">Edit</button>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id_kategori" value="' . $row['id_kategori'] . '">
                                            <button type="submit" class="btn-delete" onclick="return confirm(\'Yakin ingin menghapus kategori ini?\');">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                ';
                                $no++;
                            }
                        } else {
                            echo '<tr><td colspan="5" style="text-align: center;">Belum ada kategori</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kategori -->
    <div id="modalEdit" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Kategori</h3>
                <span class="modal-close" onclick="closeModal()">&times;</span>
            </div>
            <form method="POST" id="formEdit">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id_kategori" id="editIdKategori">
                
                <div class="form-group">
                    <label for="editKetKategori">Keterangan Kategori:</label>
                    <input type="text" id="editKetKategori" name="ket_kategori" class="input-control" required>
                </div>
                
                <div style="text-align: center;">
                    <button type="button" class="btnn" onclick="closeModal()" style="background-color: #999; margin-right: 10px;">Batal</button>
                    <button type="submit" class="btnn">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editKategori(id, ket) {
            document.getElementById('editIdKategori').value = id;
            document.getElementById('editKetKategori').value = ket;
            document.getElementById('modalEdit').classList.add('show');
        }
        
        function closeModal() {
            document.getElementById('modalEdit').classList.remove('show');
        }
        
        // Tutup modal jika klik di luar modal
        window.onclick = function(event) {
            const modal = document.getElementById('modalEdit');
            if (event.target == modal) {
                modal.classList.remove('show');
            }
        }
    </script>

    <!--footer-->
    <footer>
        <div class="container">
            <small>Copyright &copy; 2026 - Ditt Aspirasi.</small>
        </div>
    </footer>
</body> 
</html>
