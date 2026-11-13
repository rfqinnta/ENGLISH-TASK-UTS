<?php
include 'koneksi.php';
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; 

    // Mencari user berdasarkan email dan password
    $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        $_SESSION['id_user'] = $row['id_user'];
        $_SESSION['nama'] = $row['nama'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['role'] = $row['role'];

        if ($row['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        $error = "Email atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dahlan Muda</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap');
        body { margin: 0; padding: 0; font-family: 'Montserrat', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #ffffff; }
        .container { width: 90%; max-width: 550px; text-align: center; display: flex; flex-direction: column; align-items: center; }
        .logo { width: 220px; height: auto; margin-bottom: 40px; }
        h1 { color: #2245b6; font-size: 28px; font-weight: 700; margin-bottom: 15px; }
        .description { color: #666; font-size: 13px; line-height: 1.6; max-width: 480px; margin-bottom: 40px; }
        .login-form { width: 100%; max-width: 320px; }
        .input-group { margin-bottom: 15px; }
        input { width: 100%; padding: 16px 20px; border: none; border-radius: 10px; background-color: #f0f3ff; font-family: 'Montserrat', sans-serif; font-size: 13px; box-sizing: border-box; outline: none; border: 2px solid transparent; transition: 0.3s; }
        input:focus { border-color: #2245b6; background-color: #fff; }
        .forgot-pass { width: 100%; text-align: center; margin-bottom: 20px; margin-top: 10px; }
        .forgot-pass a { color: #2245b6; text-decoration: none; font-size: 11px; font-weight: 700; }
        .btn-login { width: 100%; padding: 15px; background-color: #2245b6; color: white; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 12px rgba(34, 69, 182, 0.3); transition: 0.3s; }
        .btn-login:hover { background-color: #1a3a9e; transform: translateY(-2px); }
        .error-msg { color: #ef4444; font-size: 12px; margin-bottom: 15px; font-weight: 600; }
        
        /* Tambahan Link Daftar */
        .register-text { margin-top: 30px; font-size: 12px; color: #666; }
        .register-text a { color: #2245b6; text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>
    <div class="container">
        <img src="gambar/a.png" alt="Logo UAD" class="logo">
        <h1>Selamat Datang Dahlan Muda</h1>
        <p class="description">UKM menjadi wadah bagi mahasiswa untuk menyalurkan passion, mengembangkan potensi diri, serta membangun pengalaman dan relasi positif selama masa perkuliahan</p>
        
        <div class="login-form">
            <?php if ($error != ""): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn-login">Sign in</button>
                <div class="forgot-pass"><a href="#">Forgot your password?</a></div>
            </form>

            <div class="register-text">
                Belum memiliki akun? <a href="register.php">Daftar sekarang.</a>
            </div>
        </div>
    </div>
</body>
</html>