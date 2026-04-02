<?php
// file: logout
// deskripsi: file ini menghancurkan session dan mengarahkan ke halaman login

// mulai session
session_start();

// hapus semua data session
session_destroy();

// arahkan pengguna ke halaman login
header('Location: login.php');
?>