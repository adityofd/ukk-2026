<?php
// ============ BAGIAN LOGIN ============
session_start();
include 'db.php';

// Proses login jika form disubmit
if(isset($_POST['submit'])) {
    $user = mysqli_real_escape_string($conn, $_POST['user']);
    $pass = mysqli_real_escape_string($conn, $_POST['pass']);
    $cek = mysqli_query($conn, "SELECT * FROM tb_admin WHERE username = '$user' AND password = '".MD5($pass)."'");
    
    if(mysqli_num_rows($cek) > 0) {
        $data = mysqli_fetch_object($cek);
        $_SESSION['status_login'] = true;
        $_SESSION['a_global'] = $data;
        $_SESSION['id'] = $data->id_admin;
        echo '<script>alert("Login Berhasil"); window.location="dashboard.php"</script>';
    } else {
        echo '<script>alert("Username atau Password salah!")</script>';
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

        <!-- Login Form -->
        <form action="" method="POST">
            <input type="text" name="user" placeholder="Username" class="input-control" required>
            <input type="password" name="pass" placeholder="Password" class="input-control" required>
            <input type="submit" name="submit" value="Login" class="btn">
        </form>
    </div>
</body>
</html>