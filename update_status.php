<?php
include 'koneksi.php';
session_start();

// Cek keamanan
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    // Update status di tabel pendaftaran
    $query = "UPDATE pendaftaran SET status = '$status' WHERE id_daftar = '$id'";
    
    if (mysqli_query($conn, $query)) {
        header("Location: admin_dashboard.php?msg=success");
    } else {
        header("Location: admin_dashboard.php?msg=error");
    }
}
?>