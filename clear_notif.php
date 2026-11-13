<?php
include 'koneksi.php';
session_start();

if (isset($_SESSION['id_user'])) {
    $id_user = $_SESSION['id_user'];
    // Update status agar tidak muncul di list dashboard user
    mysqli_query($conn, "UPDATE pendaftaran SET status = 'ARCHIVED' WHERE id_user = '$id_user' AND (UPPER(status) = 'DITERIMA' OR UPPER(status) = 'DITOLAK')");
    echo "done";
}
?>