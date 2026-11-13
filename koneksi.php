<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "203_test"; // Nama database baru kamu

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>