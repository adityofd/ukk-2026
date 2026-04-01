<?php
// file: input data siswa
// deskripsi: halaman untuk admin menambah atau menghapus data siswa

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

// cek apakah form tambah/edit data siswa disubmit
if(isset($_POST['submit'])) {
    // ambil data nis dari form
    $nis = mysqli_real_escape_string($conn, $_POST['nis']);
    
    // ambil data kelas dari form
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    
    // query untuk mengecek apakah nis sudah ada di database
    $cek = mysqli_query($conn, "SELECT * FROM tb_siswa WHERE nis = '$nis'");
    
    // jika nis sudah ada, lakukan update
    if(mysqli_num_rows($cek) > 0) {
        // update data kelas siswa
        if(mysqli_query($conn, "UPDATE tb_siswa SET kelas = '$kelas' WHERE nis = '$nis'")) {
            // buat notifikasi sukses
            $_SESSION['notif_sukses'] = "data berhasil diperbarui";
        } else {
            // buat notifikasi error
            $_SESSION['notif_error'] = "gagal memperbarui data";
        }
    } else {
        // jika nis belum ada, lakukan insert data baru
        if(mysqli_query($conn, "INSERT INTO tb_siswa (nis, kelas) VALUES ('$nis', '$kelas')")) {
            // buat notifikasi sukses
            $_SESSION['notif_sukses'] = "data berhasil ditambahkan";
        } else {
            // buat notifikasi error
            $_SESSION['notif_error'] = "gagal menambahkan data";
        }
    }
}

// cek apakah tombol delete di klik
if(isset($_POST['delete'])) {
    // ambil data nis dari form
    $nis = mysqli_real_escape_string($conn, $_POST['nis']);
    
    // query untuk mengecek apakah siswa memiliki aspirasi terkait
    $cek_aspirasi = mysqli_query($conn, "SELECT COUNT(*) as total FROM input_aspirasi WHERE nis = '$nis'");
    
    // ambil hasil query
    $hasil = mysqli_fetch_array($cek_aspirasi);
    
    // simpan total aspirasi ke variabel
    $total_aspirasi = $hasil['total'];
    
    // cek apakah siswa memiliki aspirasi terkait
    if($total_aspirasi > 0) {
        // jika ada aspirasi terkait, tampilkan pesan error
        $_SESSION['notif_error'] = "⚠️ tidak bisa menghapus siswa!\nsiswa ini memiliki " . $total_aspirasi . " aspirasi yang masih terkait.\nhapus semua aspirasi terlebih dahulu di menu data aspirasi.";
    } else {
        // jika tidak ada aspirasi terkait, hapus siswa
        if(mysqli_query($conn, "DELETE FROM tb_siswa WHERE nis = '$nis'")) {
            // buat notifikasi sukses
            $_SESSION['notif_sukses'] = "data berhasil dihapus";
        } else {
            // buat notifikasi error
            $_SESSION['notif_error'] = "gagal menghapus data";
        }
    }
}

// query untuk mengambil semua data siswa dari database
$data_siswa = mysqli_query($conn, "SELECT * FROM tb_siswa ORDER BY nis ASC");
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
    <!-- bagian kepala input data siswa -->
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
    <!-- bagian utama input data siswa -->
    <div class="section">
        <div class="container">
            <h3>Input Data Siswa</h3>
            
            <?php echo displayNotif(); ?>
            
            <div class="box">
                <form action="" method="POST">
                    <input type="text" name="nis" placeholder="NIS" class="input-control" required>
                    <input type="text" name="kelas" placeholder="Kelas" class="input-control" required>
                    <input type="submit" name="submit" value="Upload" class="btnn">
                </form>
            </div>

            <h3 style="margin-top: 30px;">Data Siswa yang Sudah Diinput</h3>
            <div class="box">
                <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse; text-align: center;">
                    <thead>
                        <tr style="background-color: #f0f0f0;">
                            <th>No</th>
                            <th>NIS</th>
                            <th>Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if(mysqli_num_rows($data_siswa) > 0) {
                            while($row = mysqli_fetch_object($data_siswa)) {
                                echo '
                                <tr>
                                    <td>'.$no.'</td>
                                    <td>'.$row->nis.'</td>
                                    <td>'.$row->kelas.'</td>
                                    <td>
                                        <form action="" method="POST" style="display:inline;">
                                            <input type="hidden" name="nis" value="'.$row->nis.'">
                                            <button type="submit" name="delete" class="btn-delete">Hapus</button>
                                        </form>
                                    </td>
                                    
                                </tr>
                                ';
                                $no++;
                            }
                        } else {
                            echo '<tr><td colspan="4" style="text-align: center;">Belum ada data siswa</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>3
    </div>

    <!--footer-->
    <!-- bagian kaki input data siswa -->
    <footer>
        <div class="container">
            <small>Copyright &copy; 2026 - Ditt Aspirasi.</small>
        </div>
    </footer>
</body> 
</html>