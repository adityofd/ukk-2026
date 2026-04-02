<?php
session_start();
include 'db.php';

if(isset($_GET['idt'])){
    $id = mysqli_real_escape_string($conn, $_GET['idt']);
    
    // hapus dari tb_aspirasi
    $hapus1 = mysqli_query($conn, "DELETE FROM tb_aspirasi WHERE id_pelaporan = '$id'");
    // hapus dari input_aspirasi
    $hapus2 = mysqli_query($conn, "DELETE FROM input_aspirasi WHERE id_pelaporan = '$id'");
    
    if($hapus1 && $hapus2) {
        $_SESSION['notif_sukses'] = "Aspirasi berhasil dihapus";
    } else {
        $_SESSION['notif_error'] = "Gagal menghapus aspirasi";
    }
    
    header('Location: data_aspirasi.php');
    exit;
} else {
    header('Location: data_aspirasi.php');
    exit;
}
?>