<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $foto = $_FILES['foto_profil'];
    $nama_file = $foto['name'];
    $tmp_file = $foto['tmp_name'];
    $ukuran_file = $foto['size'];
    $error = $foto['error'];

    // Cek apakah ada file yang diupload
    if ($error === 0) {
        // Validasi ekstensi
        $ekstensi_valid = ['jpg', 'jpeg', 'png'];
        $ekstensi_file = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        if (in_array($ekstensi_file, $ekstensi_valid)) {
            // Batasi ukuran (contoh: maks 2MB)
            if ($ukuran_file < 2000000) {
                // Beri nama unik agar tidak bentrok
                $nama_file_baru = "profile_" . $id_user . "_" . time() . "." . $ekstensi_file;
                
                // Pastikan folder 'uploads' sudah ada
                if (!is_dir('uploads')) {
                    mkdir('uploads');
                }

                if (move_uploaded_file($tmp_file, 'uploads/' . $nama_file_baru)) {
                    // Update ke database tabel 'users'
                    $query = "UPDATE users SET foto = '$nama_file_baru' WHERE id_user = '$id_user'";
                    if (mysqli_query($conn, $query)) {
                        echo "<script>alert('Foto profil berhasil diperbarui!'); window.location='personalize_akun.php';</script>";
                    } else {
                        echo "Gagal update database.";
                    }
                } else {
                    echo "Gagal upload file ke server.";
                }
            } else {
                echo "<script>alert('Ukuran file terlalu besar! Maks 2MB.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Format file harus JPG, JPEG, atau PNG!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Silahkan pilih file terlebih dahulu.'); window.history.back();</script>";
    }
}
?>