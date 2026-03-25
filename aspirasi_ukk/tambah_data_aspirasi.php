<?php
// ============ BAGIAN INISIALISASI ============
session_start();
include 'db.php';

// ============ BAGIAN PROSES FORM SUBMIT ============
if(isset($_POST['submit'])) {
    // Ambil data dari form
    $nis = $_POST['nis'];
    $id_kategori = $_POST['id_kategori'];
    $lokasi = $_POST['lokasi'];
    $ket = $_POST['ket'];
    
    // Validasi: cek apakah semua field diisi
    if($nis == '' || $id_kategori == '' || $lokasi == '' || $ket == '') {
        echo '<script>alert("Semua field harus diisi")</script>';
    } else {
        // Ambil ID pelaporan terbaru
        $ambil_id = mysqli_query($conn, "SELECT MAX(id_pelaporan) as max_id FROM input_aspirasi");
        $hasil = mysqli_fetch_array($ambil_id);
        $id_pelaporan = $hasil['max_id'] + 1;
        
        // Insert data aspirasi ke database
        $sql = "INSERT INTO input_aspirasi (id_pelaporan, nis, id_kategori, lokasi, ket) VALUES ('$id_pelaporan', '$nis', '$id_kategori', '$lokasi', '$ket')";
        $insert = mysqli_query($conn, $sql);
        
        if($insert) {
            echo '<script>alert("Data berhasil ditambahkan"); window.location="index.php"</script>';
        } else {
            echo '<script>alert("Gagal menambahkan data")</script>';
        }
    }
}

// ============ BAGIAN AMBIL DATA ============
$siswa_list = mysqli_query($conn, "SELECT nis, kelas FROM tb_siswa ORDER BY nis ASC");
$kategori_list = mysqli_query($conn, "SELECT id_kategori, ket_kategori FROM tb_kategori ORDER BY id_kategori ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ditt Aspirasi - Tambah Aspirasi</title>
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
    </style>
</head>
<body>
    <!--header-->
    <header>
        <div class="container">
            <h1><a href="index.php">Ditt Aspirasi</a></h1>
        </div>
    </header>

    <!--content-->
    <div class="section">
        <div class="container">
            <h3>Tambah Aspirasi Siswa</h3>
            <div class="box">
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="nis">NIS & Kelas Siswa</label>
                        <select id="nis" name="nis" class="input-control" required>
                            <option value="">-- Pilih Siswa --</option>
                            <?php
                            if($siswa_list && mysqli_num_rows($siswa_list) > 0) {
                                while($siswa = mysqli_fetch_assoc($siswa_list)) {
                                    echo '<option value="' . htmlspecialchars($siswa['nis']) . '">' . 
                                         htmlspecialchars($siswa['nis']) . ' - ' . 
                                         htmlspecialchars($siswa['kelas']) . 
                                         '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="id_kategori">Kategori Pelaporan</label>
                        <select id="id_kategori" name="id_kategori" class="input-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php
                            if($kategori_list && mysqli_num_rows($kategori_list) > 0) {
                                while($kategori = mysqli_fetch_assoc($kategori_list)) {
                                    echo '<option value="' . htmlspecialchars($kategori['id_kategori']) . '">' . 
                                         htmlspecialchars($kategori['ket_kategori']) . 
                                         '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="lokasi">Lokasi Terlapor</label>
                        <input type="text" id="lokasi" name="lokasi" placeholder="Masukkan lokasi terlapor" class="input-control" required>
                    </div>

                    <div class="form-group">
                        <label for="ket">Keterangan Terlapor</label>
                        <textarea id="ket" name="ket" placeholder="Masukkan keterangan atau deskripsi aspirasi" class="input-control" style="height: 100px;" required></textarea>
                    </div>

                    <input type="submit" name="submit" value="Submit" class="btnn">
                    <input type="button" value="Kembali" class="btnn" onclick="window.location='index.php'" style="background-color: #999; margin-left: 10px;">
                </form>
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