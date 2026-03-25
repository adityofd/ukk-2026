<?php
// ============ BAGIAN INISIALISASI & AUTENTIKASI ============
session_start();
if($_SESSION['status_login'] != true) { 
    echo '<script>window.location="login.php"</script>'; 
    exit; 
}
include 'db.php';

$success = '';
$error = '';

// ============ BAGIAN TAMBAH KATEGORI ============
if(isset($_POST['submit'])) {
    $ket_kategori = mysqli_real_escape_string($conn, $_POST['ket_kategori']);
    
    if(empty($ket_kategori)) {
        $error = "Keterangan kategori harus diisi!";
        echo '<script>alert("Keterangan kategori harus diisi!")</script>';
    } else {
        // Cek apakah kategori sudah ada
        $cek = mysqli_query($conn, "SELECT * FROM tb_kategori WHERE ket_kategori = '$ket_kategori'");
        
        if(mysqli_num_rows($cek) > 0) {
            $error = "Kategori sudah ada!";
            echo '<script>alert("Kategori sudah ada!")</script>';
        } else {
            // Dapatkan ID kategori terbaru dan tambah 1
            $max_id = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(id_kategori) as max_id FROM tb_kategori"));
            $new_id = ($max_id['max_id'] ?? 0) + 1;
            
            // Insert kategori baru
            if(mysqli_query($conn, "INSERT INTO tb_kategori (id_kategori, ket_kategori) VALUES ('$new_id', '$ket_kategori')")) {
                $success = "Kategori berhasil ditambahkan!";
                echo '<script>alert("Kategori berhasil ditambahkan!")</script>';
            } else {
                $error = "Gagal menambahkan kategori!";
                echo '<script>alert("Gagal menambahkan kategori!")</script>';
            }
        }
    }
}

// ============ BAGIAN HAPUS KATEGORI ============
if(isset($_POST['delete'])) {
    $id_kategori = mysqli_real_escape_string($conn, $_POST['id_kategori']);
    
    // Cek apakah kategori masih digunakan
    $cek_aspirasi = mysqli_query($conn, "SELECT COUNT(*) as count FROM input_aspirasi WHERE id_kategori = '$id_kategori'");
    $aspirasi_count = mysqli_fetch_assoc($cek_aspirasi)['count'];
    
    if($aspirasi_count > 0) {
        $error = "Kategori tidak bisa dihapus! Masih digunakan oleh " . $aspirasi_count . " aspirasi.";
        echo '<script>alert("Kategori tidak bisa dihapus! Masih digunakan oleh ' . $aspirasi_count . ' aspirasi.")</script>';
    } else {
        // Hapus kategori
        if(mysqli_query($conn, "DELETE FROM tb_kategori WHERE id_kategori = '$id_kategori'")) {
            $success = "Kategori berhasil dihapus!";
            echo '<script>alert("Kategori berhasil dihapus!")</script>';
        } else {
            $error = "Gagal menghapus kategori!";
            echo '<script>alert("Gagal menghapus kategori!")</script>';
        }
    }
}

// ============ BAGIAN AMBIL DATA ============
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
    <style>
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
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
            <h3>Data Kategori</h3>

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
                                        <form action="" method="POST" style="display:inline;">
                                            <input type="hidden" name="id_kategori" value="' . $row['id_kategori'] . '">
                                            <button type="submit" name="delete" onclick="return confirm(\'Yakin ingin menghapus kategori ini?\');" class="btn-delete">Hapus</button>
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

    <!--footer-->
    <footer>
        <div class="container">
            <small>Copyright &copy; 2026 - Ditt Aspirasi.</small>
        </div>
    </footer>
</body> 
</html>
