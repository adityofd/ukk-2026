<?php
// ============ BAGIAN INISIALISASI & AUTENTIKASI ============
session_start();
include 'db.php';
if($_SESSION['status_login'] != true) { 
    echo '<script>window.location="login.php"</script>'; 
    exit; 
}

// ============ BAGIAN PROSES FORM SUBMIT ============
if(isset($_POST['submit'])) {
    $nis = mysqli_real_escape_string($conn, $_POST['nis']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    
    // Cek apakah NIS sudah ada
    $cek = mysqli_query($conn, "SELECT * FROM tb_siswa WHERE nis = '$nis'");
    
    if(mysqli_num_rows($cek) > 0) {
        // Update data jika NIS sudah ada
        if(mysqli_query($conn, "UPDATE tb_siswa SET kelas = '$kelas' WHERE nis = '$nis'")) {
            echo '<script>alert("Data berhasil diperbarui")</script>';
        } else {
            echo '<script>alert("Gagal memperbarui data")</script>';
        }
    } else {
        // Insert data baru jika NIS belum ada
        if(mysqli_query($conn, "INSERT INTO tb_siswa (nis, kelas) VALUES ('$nis', '$kelas')")) {
            echo '<script>alert("Data berhasil ditambahkan")</script>';
        } else {
            echo '<script>alert("Gagal menambahkan data")</script>';
        }
    }
}

// ============ BAGIAN PROSES HAPUS DATA ============
if(isset($_POST['delete'])) {
    $nis = $_POST['nis'];
    
    // Cek apakah ada aspirasi yang terkait dengan NIS ini
    $cek_aspirasi = mysqli_query($conn, "SELECT COUNT(*) as total FROM input_aspirasi WHERE nis = '$nis'");
    $hasil = mysqli_fetch_array($cek_aspirasi);
    $total_aspirasi = $hasil['total'];
    
    // Jika ada aspirasi terkait
    if($total_aspirasi > 0) {
        // Hapus aspirasi terlebih dahulu
        if(mysqli_query($conn, "DELETE FROM input_aspirasi WHERE nis = '$nis'")) {
            // Setelah aspirasi dihapus, hapus siswa
            if(mysqli_query($conn, "DELETE FROM tb_siswa WHERE nis = '$nis'")) {
                echo '<script>alert("Siswa dan ' . $total_aspirasi . ' aspirasi berhasil dihapus")</script>';
            } else {
                echo '<script>alert("Gagal menghapus siswa")</script>';
            }
        } else {
            echo '<script>alert("Gagal menghapus aspirasi")</script>';
        }
    } else {
        // Hapus siswa jika tidak ada aspirasi terkait
        if(mysqli_query($conn, "DELETE FROM tb_siswa WHERE nis = '$nis'")) {
            echo '<script>alert("Data berhasil dihapus")</script>';
        } else {
            echo '<script>alert("Gagal menghapus data")</script>';
        }
    }
}

// ============ BAGIAN AMBIL DATA ============
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
            <h3>Input Data Siswa</h3>
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
                                            <button type="submit" name="delete" onclick="return confirm(\'Yakin ingin menghapus?\');" class="btn-delete">Hapus</button>
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