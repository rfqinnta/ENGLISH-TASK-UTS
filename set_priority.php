<?php
session_start();

if (isset($_GET['id']) && isset($_GET['to'])) {
    $new_id = $_GET['id'];
    $target = $_GET['to'];

    // Jika ID baru berbeda dengan yang sekarang di nomor 1
    if (!isset($_SESSION['last_viewed_id']) || $_SESSION['last_viewed_id'] != $new_id) {
        // Geser nomor 1 jadi nomor 2
        $_SESSION['second_viewed_id'] = isset($_SESSION['last_viewed_id']) ? $_SESSION['last_viewed_id'] : 0;
        // Set nomor 1 yang baru
        $_SESSION['last_viewed_id'] = $new_id;
    }

    header("Location: " . $target);
    exit();
} else {
    header("Location: dashboard.php");
}