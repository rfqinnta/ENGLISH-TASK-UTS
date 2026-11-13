<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$user_res = mysqli_query($conn, "SELECT * FROM users WHERE id_user = '$id_user'");
$u = mysqli_fetch_assoc($user_res);

$show_modal = false;
$modal_message = "";
$is_error = false;

if (isset($_POST['submit_pendaftaran'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nim = mysqli_real_escape_string($conn, $_POST['nim']);
    $email = mysqli_real_escape_string($conn, $_POST['email']); // Field Baru
    $prodi = mysqli_real_escape_string($conn, $_POST['prodi']);
    $fakultas = mysqli_real_escape_string($conn, $_POST['fakultas']);
    $angkatan = mysqli_real_escape_string($conn, $_POST['angkatan']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']); 
    $id_ukm = mysqli_real_escape_string($conn, $_POST['id_ukm']);
    $alasan = mysqli_real_escape_string($conn, $_POST['alasan']);

    $cek_duplikat = mysqli_query($conn, "SELECT * FROM pendaftaran WHERE id_user = '$id_user' AND id_ukm = '$id_ukm' AND (status = 'PENDING' OR status = 'DITERIMA')");
    
    if (mysqli_num_rows($cek_duplikat) > 0) {
        $show_modal = true; 
        $is_error = true;
        $modal_message = "Anda sudah memiliki pendaftaran aktif di UKM ini.";
    } else {
        mysqli_query($conn, "DELETE FROM pendaftaran WHERE id_user = '$id_user' AND id_ukm = '$id_ukm' AND status = 'DITOLAK'");
        
        // Update data user termasuk EMAIL
        mysqli_query($conn, "UPDATE users SET nama='$nama', nim='$nim', email='$email', prodi='$prodi', fakultas='$fakultas', angkatan='$angkatan' WHERE id_user='$id_user'");
        
        $query_daftar = "INSERT INTO pendaftaran (id_user, id_ukm, no_hp, alasan, status) 
                         VALUES ('$id_user', '$id_ukm', '$no_hp', '$alasan', 'PENDING')";
        
        if (mysqli_query($conn, $query_daftar)) {
            $show_modal = true; 
            $is_error = false;
            $modal_message = "Pendaftaran Anda berhasil dikirim ke sistem.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration — Dahlan Muda</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }
        body { background: #fff; color: #1d1d1f; line-height: 1.5; -webkit-font-smoothing: antialiased; }

        /* --- HEADER ORIGINAL --- */
        .glass-panel {
            width: 100%;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
            padding: 20px 40px;
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 1000;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .header-box h1 { font-size: 24px; font-weight: 700; letter-spacing: -1px; color: #1c1c1e; }
        .header-box p { font-size: 13px; color: #8e8e93; }
        .brand-logo img { width: 45px; height: auto; }

        /* --- APPLE STYLE CONTENT --- */
        .container { max-width: 980px; margin: 0 auto; padding: 60px 20px 100px; }
        .hero-section { text-align: center; margin-bottom: 60px; }
        .hero-section h2 { font-size: 48px; font-weight: 800; letter-spacing: -2.2px; margin-bottom: 8px; color: #000; }
        .hero-section p { font-size: 22px; color: #86868b; font-weight: 500; letter-spacing: -0.5px; }

        .form-wrapper { max-width: 800px; margin: 0 auto; }
        .form-section { margin-bottom: 60px; }
        .section-title { font-size: 24px; font-weight: 700; letter-spacing: -0.7px; margin-bottom: 25px; color: #1d1d1f; }

        .input-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .input-box { display: flex; flex-direction: column; gap: 10px; }
        .input-box.full { grid-column: span 2; }

        label { font-size: 13px; font-weight: 600; color: #1d1d1f; margin-left: 4px; }
        input, select, textarea {
            background: #f5f5f7; border: 1px solid transparent; border-radius: 14px;
            padding: 18px; font-size: 16px; font-weight: 500; transition: all 0.2s ease;
            color: #1d1d1f;
        }
        input:focus, select:focus, textarea:focus {
            background: #fff; border-color: #0071e3; outline: none;
            box-shadow: 0 0 0 4px rgba(0,113,227,0.1);
        }
        textarea { height: 160px; resize: none; }

        .footer-cta {
            margin-top: 40px; padding-top: 40px; border-top: 1px solid #d2d2d7;
            display: flex; flex-direction: column; align-items: center; gap: 20px;
        }
        .btn-apple {
            background: #0071e3; color: #fff; padding: 16px 40px;
            border-radius: 30px; font-size: 17px; font-weight: 600;
            border: none; cursor: pointer; transition: 0.3s;
        }
        .btn-apple:hover { background: #0077ed; transform: scale(1.02); }
        .btn-back { color: #0066cc; text-decoration: none; font-size: 16px; font-weight: 500; }

        /* --- POPUP MODAL --- */
        .overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.4); backdrop-filter: blur(15px);
            display: <?php echo $show_modal ? 'flex' : 'none'; ?>;
            justify-content: center; align-items: center; z-index: 2000;
        }
        .apple-alert {
            background: #fff; padding: 40px; border-radius: 30px; text-align: center;
            width: 380px; box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            animation: alertShow 0.4s cubic-bezier(0.18, 0.89, 0.32, 1.28);
        }
        @keyframes alertShow { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .alert-title { font-size: 22px; font-weight: 700; margin-bottom: 12px; }
        .alert-msg { font-size: 15px; color: #86868b; margin-bottom: 30px; line-height: 1.5; }
        .btn-home {
            background: #000; color: #fff; padding: 14px 0; width: 100%;
            border-radius: 18px; font-size: 16px; font-weight: 700;
            border: none; cursor: pointer; text-decoration: none; display: block;
        }
    </style>
</head>
<body>

    <div class="glass-panel">
        <div class="header-box">
            <h1>Pendaftaran UKM</h1>
            <p>Join the student organization of your choice.</p>
        </div>
        <div class="brand-logo">
            <img src="gambar/a.png" alt="UAD Logo">
        </div>
    </div>

    <div class="container">
        <div class="hero-section">
            <h2>Pilih masa depanmu.</h2>
            <p>Lengkapi profil mahasiswa dan tentukan pilihan UKM.</p>
        </div>

        <form method="POST" class="form-wrapper">
            <div class="form-section">
                <h3 class="section-title">Informasi Mahasiswa</h3>
                <div class="input-grid">
                    <div class="input-box">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" value="<?php echo $u['nama'] ?? ''; ?>" required>
                    </div>
                    <div class="input-box">
                        <label>NIM</label>
                        <input type="text" name="nim" value="<?php echo $u['nim'] ?? ''; ?>" required maxlength="10">
                    </div>
                    <div class="input-box">
                        <label>Email Kampus</label>
                        <input type="email" name="email" value="<?php echo $u['email'] ?? ''; ?>" required placeholder="user@webmail.uad.ac.id">
                    </div>
                    <div class="input-box">
                        <label>Fakultas</label>
                        <input type="text" name="fakultas" value="<?php echo $u['fakultas'] ?? ''; ?>" required>
                    </div>
                    <div class="input-box">
                        <label>Program Studi</label>
                        <input type="text" name="prodi" value="<?php echo $u['prodi'] ?? ''; ?>" required>
                    </div>
                    <div class="input-box">
                        <label>Angkatan</label>
                        <input type="number" name="angkatan" value="<?php echo $u['angkatan'] ?? ''; ?>" required>
                    </div>
                    <div class="input-box full">
                        <label>Nomor WhatsApp</label>
                        <input type="number" name="no_hp" placeholder="08" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Pilihan Organisasi</h3>
                <div class="input-grid">
                    <div class="input-box full">
                        <label>UKM Yang Dituju</label>
                        <select name="id_ukm" required>
                            <option value="" disabled selected>Pilih Unit Kegiatan Mahasiswa</option>
                            <?php
                            $ukm_list = mysqli_query($conn, "SELECT * FROM ukm ORDER BY nama_ukm ASC");
                            while($row = mysqli_fetch_assoc($ukm_list)) {
                                echo "<option value='".$row['id_ukm']."'>".$row['nama_ukm']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="input-box full">
                        <label>Alasan Ingin Bergabung</label>
                        <textarea name="alasan" placeholder="Apa motivasi terbesar Anda?" required></textarea>
                    </div>
                </div>
            </div>

            <div class="footer-cta">
                <button type="submit" name="submit_pendaftaran" class="btn-apple">Kirim Pendaftaran</button>
                <a href="dashboard.php" class="btn-back">Batal dan Kembali</a>
            </div>
        </form>
    </div>

    <div class="overlay">
        <div class="apple-alert">
            <div class="alert-title" style="color: <?php echo $is_error ? '#ff3b30' : '#1d1d1f'; ?>;">
                <?php echo $is_error ? 'Pendaftaran Gagal' : 'Berhasil Dikirim'; ?>
            </div>
            <div class="alert-msg">
                <?php echo $modal_message; ?>
            </div>
            <a href="dashboard.php" class="btn-home">Kembali ke Home</a>
        </div>
    </div>

</body>
</html>