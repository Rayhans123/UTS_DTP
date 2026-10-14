<?php

$host     = "localhost";
$user     = "root";
$password = "";
$database = "db_laporan_fasilitas";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    error_log("Koneksi gagal: " . $conn->connect_error);
    die("Terjadi kesalahan koneksi. Silakan coba beberapa saat lagi.");
}

$conn->set_charset("utf8mb4");