<?php
include 'koneksi.php';
$show_success = false;
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nim = mysqli_real_escape_string($conn, $_POST['nim']); // Tambahkan NIM
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role = 'mahasiswa';

    // Cek apakah NIM atau Email sudah ada
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' OR nim='$nim'");
    if (mysqli_num_rows($cek) > 0) {
        $msg = "NIM atau Email sudah terdaftar!";
    } else {
        // Tambahkan kolom nim di query INSERT
        $query = "INSERT INTO users (nim, nama, email, password, role) VALUES ('$nim', '$nama', '$email', '$password', '$role')";
        if (mysqli_query($conn, $query)) {
            $show_success = true; 
        } else {
            $msg = "Terjadi kesalahan database.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun Dahlan Muda</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');
        body { margin: 0; padding: 0; font-family: 'Montserrat', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #ffffff; }
        .container { width: 90%; max-width: 550px; text-align: center; display: flex; flex-direction: column; align-items: center; }
        .logo { width: 220px; height: auto; margin-bottom: 20px; }
        h1 { color: #2245b6; font-size: 28px; font-weight: 700; margin-bottom: 10px; }
        p.subtitle { color: #666; font-size: 14px; margin-bottom: 30px; }
        .login-form { width: 100%; max-width: 320px; }
        .input-group { margin-bottom: 15px; }
        input { width: 100%; padding: 16px 20px; border: none; border-radius: 10px; background-color: #f0f3ff; font-family: 'Montserrat', sans-serif; font-size: 13px; box-sizing: border-box; outline: none; border: 2px solid transparent; transition: 0.3s; }
        input:focus { border-color: #2245b6; background-color: #fff; }
        .btn-login { width: 100%; padding: 15px; background-color: #2245b6; color: white; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; transition: 0.3s; margin-top: 10px; box-shadow: 0 4px 12px rgba(34, 69, 182, 0.2); }
        .btn-login:hover { background-color: #1a3a9e; transform: translateY(-2px); }
        .error-msg { color: #ef4444; font-size: 12px; margin-bottom: 15px; font-weight: 600; }
        .back-link { margin-top: 25px; font-size: 12px; color: #666; }
        .back-link a { color: #2245b6; text-decoration: none; font-weight: 700; }

        /* MODAL STYLE */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.4); backdrop-filter: blur(8px); z-index: 1000; justify-content: center; align-items: center; }
        .modal-box { background: white; padding: 40px; border-radius: 24px; text-align: center; width: 90%; max-width: 400px; transform: scale(0.8); transition: 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .modal-overlay.active { display: flex; }
        .modal-overlay.active .modal-box { transform: scale(1); }
        .success-icon { width: 60px; height: 60px; background: #e8f5e9; color: #2e7d32; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 30px; }
        .btn-modal { background: #2245b6; color: white; border: none; padding: 12px 30px; border-radius: 10px; font-weight: 600; cursor: pointer; width: 100%; }
    </style>
</head>
<body>

    <div class="container">
        <img src="gambar/a.png" alt="Logo UAD" class="logo">
        <h1>Buat Akun Baru</h1>
        <p class="subtitle">Daftarkan dirimu untuk bergabung dengan berbagai UKM di UAD.</p>

        <div class="login-form">
            <?php if ($msg != ""): ?>
                <div class="error-msg"><?php echo $msg; ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="input-group">
                    <input type="text" name="nim" placeholder="NIM (Nomor Induk Mahasiswa)" required>
                </div>
                <div class="input-group">
                    <input type="text" name="nama" placeholder="Nama Lengkap" required>
                </div>
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn-login">Daftar Akun</button>
            </form>

            <div class="back-link">
                Sudah punya akun? <a href="login.php">Masuk sekarang.</a>
            </div>
        </div>
    </div>

    <div id="successModal" class="modal-overlay <?php echo $show_success ? 'active' : ''; ?>">
        <div class="modal-box">
            <div class="success-icon">✓</div>
            <h2>Pendaftaran Berhasil!</h2>
            <p>Akun kamu sudah terdaftar. Silahkan masuk untuk memilih UKM.</p>
            <button class="btn-modal" onclick="window.location='login.php'">Masuk Sekarang</button>
        </div>
    </div>

</body>
</html>