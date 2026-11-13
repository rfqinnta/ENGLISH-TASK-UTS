<?php
include 'koneksi.php';
session_start();

// Proteksi halaman
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// Ambil data dari tabel 'users'
$query = "SELECT * FROM users WHERE id_user = '$id_user'"; 
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error Database: " . mysqli_error($conn));
}

$user = mysqli_fetch_assoc($result);

// Logika sederhana untuk foto profil (jika kolom 'foto' kosong, gunakan default)
$foto_profil = (!empty($user['foto'])) ? 'uploads/' . $user['foto'] : 'gambar/jo.jpg';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --apple-bg: #F2F2F7;
            --apple-card: rgba(255, 255, 255, 0.85);
            --apple-blue: #007AFF;
            --apple-gray: #8E8E93;
            --apple-border: rgba(0,0,0,0.05);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }

        body {
            background-color: var(--apple-bg);
            color: #000;
            display: flex;
            justify-content: center;
            padding: 40px 20px;
        }

        .container {
            width: 100%;
            max-width: 500px;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }

        .btn-back {
            color: var(--apple-blue);
            text-decoration: none;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 25px;
            font-weight: 500;
        }

        h1 { font-size: 32px; font-weight: 700; margin-bottom: 25px; letter-spacing: -1px; }

        /* Profile Banner */
        .profile-card {
            background: var(--apple-card);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            padding: 25px;
            border-radius: 22px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }

        .avatar-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
        }

        .profile-card img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(0,0,0,0.1);
        }

        .profile-card h2 { font-size: 20px; font-weight: 600; margin-top: 5px; }
        .profile-card p { font-size: 13px; color: var(--apple-gray); }

        /* Settings List */
        .settings-list {
            background: var(--apple-card);
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 25px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }

        .item {
            padding: 14px 20px;
            border-bottom: 0.5px solid var(--apple-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item:last-child { border-bottom: none; }

        .label { font-size: 15px; font-weight: 500; }
        
        .value-pakem {
            border: none;
            background: transparent;
            text-align: right;
            color: var(--apple-gray);
            font-size: 14px;
            width: 65%;
            outline: none;
            cursor: default;
        }

        /* Custom File Upload Button */
        .file-input-wrapper {
            width: 100%;
            padding: 10px 0;
        }
        
        .file-input-wrapper input[type="file"] {
            font-size: 12px;
            color: var(--apple-gray);
        }

        .btn-save {
            background: var(--apple-blue);
            color: white;
            border: none;
            width: 100%;
            padding: 16px;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 15px;
        }

        .btn-save:active { transform: scale(0.97); opacity: 0.9; }

        .footer {
            text-align: center;
            color: var(--apple-gray);
            font-size: 12px;
            margin-top: 30px;
            line-height: 1.5;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="dashboard.php" class="btn-back"><span>‹</span> Dashboard</a>
    <h1>Profile</h1>

    <form action="proses_update_foto.php" method="POST" enctype="multipart/form-data">
        <div class="profile-card">
            <div class="avatar-wrapper">
                <img src="<?php echo $foto_profil; ?>" alt="User profile">
            </div>
            <div>
                <h2><?php echo htmlspecialchars($user['nama']); ?></h2>
                <p>Mahasiswa Universitas Ahmad Dahlan</p>
            </div>
            
            <div class="file-input-wrapper">
                <input type="file" name="foto_profil" accept="image/*">
            </div>
        </div>

        <div class="settings-list">
            <div class="item">
                <span class="label">Nama Lengkap</span>
                <input type="text" class="value-pakem" value="<?php echo htmlspecialchars($user['nama']); ?>" readonly>
            </div>
            <div class="item">
                <span class="label">NIM</span>
                <input type="text" class="value-pakem" value="<?php echo htmlspecialchars($user['nim']); ?>" readonly>
            </div>
            <div class="item">
                <span class="label">Email</span>
                <input type="text" class="value-pakem" value="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : 'mahasiswa@uad.ac.id'; ?>" readonly>
            </div>
        </div>

        <button type="submit" class="btn-save">Update Foto Profil</button>
    </form>

    <div class="footer">
        TUGAS PWEB DINAMIS <br>
        NANTA MAIL IKHYA
    </div>
</div>

</body>
</html>