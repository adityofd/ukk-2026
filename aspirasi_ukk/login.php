<?php
// file: login admin
// deskripsi: halaman login untuk admin mengakses dashboard

// inisialisasi session
session_start();

// include file koneksi database
include 'db.php';

// cek apakah form login disubmit
if(isset($_POST['submit'])) {
    // ambil data username dari form dan escape untuk keamanan
    $user = mysqli_real_escape_string($conn, $_POST['user']);
    
    // ambil data password dari form dan md5 untuk keamanan
    $pass = mysqli_real_escape_string($conn, $_POST['pass']);
    
    // query untuk mengecek apakah username dan password cocok di database
    $cek = mysqli_query($conn, "SELECT * FROM tb_admin WHERE username = '$user' AND password = '".MD5($pass)."'");
    
    // cek apakah data admin ditemukan
    if(mysqli_num_rows($cek) > 0) {
        // ambil data admin dari query result
        $data = mysqli_fetch_object($cek);
        
        // set status login menjadi true
        $_SESSION['status_login'] = true;
        
        // simpan data admin lengkap ke session
        $_SESSION['a_global'] = $data;
        
        // simpan id admin ke session
        $_SESSION['id'] = $data->id_admin;
        
        // buat notifikasi sukses
        $_SESSION['notif_sukses'] = "login berhasil";
        
        // arahkan ke halaman dashboard
        header("Location: dashboard.php");
        exit;
    } else {
        // jika username atau password salah, buat notifikasi error
        $_SESSION['notif_error'] = "username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Ditt Aspirasi</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>
<body id="bg-login">
    <div class="box-login">
        <h2>Login</h2>
<!-- bagian ini akan menampilkan notifikasi error jika ada -->
        <?php 
        if(isset($_SESSION['notif_error'])) {
            echo '<div class="notif-error">' . $_SESSION['notif_error'] . '</div>';
            unset($_SESSION['notif_error']);
        }
        ?>

        <!-- Login Form -->
        <form action="" method="POST">
            <input type="text" name="user" placeholder="Username" class="input-control" required>
            <input type="password" name="pass" placeholder="Password" class="input-control" required>
            <input type="submit" name="submit" value="Login" class="btn">
        </form>
    </div>
</body>
</html>