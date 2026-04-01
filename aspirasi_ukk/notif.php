<?php
// file: menampilkan notifikasi
// deskripsi: file helper untuk menampilkan notifikasi sukses atau error

// fungsi untuk menampilkan notifikasi dari session
function displayNotif() {
    // inisialisasi variabel untuk menyimpan notifikasi
    $notif = '';
    
    // cek apakah ada notifikasi sukses di session
    if(isset($_SESSION['notif_sukses'])) {
        // tambahkan notifikasi sukses ke variabel
        $notif .= '<div class="notif-success">' . $_SESSION['notif_sukses'] . '</div>';
        // hapus notifikasi sukses dari session agar tidak ditampilkan lagi
        unset($_SESSION['notif_sukses']);
    }
    
    // cek apakah ada notifikasi error di session
    if(isset($_SESSION['notif_error'])) {
        // tambahkan notifikasi error ke variabel
        $notif .= '<div class="notif-error">' . $_SESSION['notif_error'] . '</div>';
        // hapus notifikasi error dari session agar tidak ditampilkan lagi
        unset($_SESSION['notif_error']);
    }
    
    // kembalikan notifikasi yang sudah diformat
    return $notif;
}
