<?php
// file: koneksi database
// deskripsi: file ini mengatur koneksi ke database menggunakan mysqli

// konfigurasi database
$hostname = 'localhost'; // host database
$username = 'root'; // username database
$password = ''; // password database
$dbname = 'db_aspirasi'; // nama database

// membuat koneksi ke database
$conn = mysqli_connect($hostname,$username,$password,$dbname)or die('gagal terhubung ke database');
?>          