<?php
include 'koneksi.php';
session_start();

// 1. PROTEKSI HALAMAN
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$nama_user = $_SESSION['nama'];
$id_user = $_SESSION['id_user'];

// 2. AMBIL STATUS PENDAFTARAN (Sesuai Dashboard)
$query_notif = "SELECT p.status, u.nama_ukm 
                FROM pendaftaran p 
                JOIN ukm u ON p.id_ukm = u.id_ukm 
                WHERE p.id_user = '$id_user' AND p.status != 'pending'
                ORDER BY p.id_daftar DESC";
$result_notif = mysqli_query($conn, $query_notif);
$notif_data = [];
while($rn = mysqli_fetch_assoc($result_notif)) {
    $notif_data[] = $rn;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail UKM - Poros UAD</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }
        body, html { height: 100%; overflow: hidden; background: transparent; }

        /* ===== BACKGROUND VIDEO ===== */
        .video-container {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1; overflow: hidden; background-color: #000;
        }
        .video-container video {
            min-width: 100%; min-height: 100%; object-fit: cover;
            filter: brightness(0.5); 
        }

        /* --- HEADER ASLI (UKURAN 22PX TETAP) --- */
        header {
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            padding: 15px 60px; 
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            position: fixed; top: 0; width: 100%; z-index: 1000;
            border-bottom: 0.5px solid rgba(255, 255, 255, 0.1);
        }

        .header-left, .header-right { flex: 1; display: flex; align-items: center; }
        .header-left { gap: 20px; }
        .header-right { justify-content: flex-end; gap: 10px; }
        .header-center { flex: 2; text-align: center; }

        .header-center h2 { 
            color: #ffffff; 
            font-size: 22px; 
            font-weight: 700; 
            letter-spacing: -0.5px; 
            white-space: nowrap;
        }

        .header-right { color: rgba(255,255,255,0.8); font-weight: 700; font-size: 13px; text-align: right; }
        .header-right img { width: 45px; }

        .avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: rgba(255,255,255,0.2); overflow: hidden; 
            border: 1px solid rgba(255,255,255,0.3); cursor: pointer;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .avatar:hover { transform: scale(1.1); }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }

        /* --- DROPDOWN NOTIF DASHBOARD STYLE --- */
        .profile-menu { position: relative; }
        .dropdown-content {
            display: block; 
            position: absolute; top: 55px; left: 0; 
            background: rgba(28, 28, 30, 0.85); 
            backdrop-filter: blur(40px) saturate(150%);
            -webkit-backdrop-filter: blur(40px) saturate(150%);
            min-width: 310px; border-radius: 22px; overflow: hidden;
            border: 1px solid rgba(255,255,255,0.15);
            opacity: 0; visibility: hidden;
            transform: translateY(10px) scale(0.95);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            padding: 8px;
        }
        .profile-menu:hover .dropdown-content { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }

        .dropdown-item { 
            padding: 12px 16px; font-size: 14px; color: #fff; 
            text-decoration: none; display: flex; align-items: center; 
            border-radius: 12px; transition: background 0.2s; cursor: pointer;
        }
        .dropdown-item:hover { background: rgba(255,255,255,0.12); }

        /* Menu Khusus Profil yang bisa diklik */
        .profile-link { text-decoration: none; display: block; }

        #notifView { display: none; padding: 4px; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .notif-header-mini { 
            display: flex; align-items: center; justify-content: space-between;
            padding: 8px 10px 14px; border-bottom: 0.5px solid rgba(255,255,255,0.1); margin-bottom: 12px; 
        }

        .back-pill { 
            background: rgba(255, 255, 255, 0.1); color: #fff; 
            padding: 6px 14px; border-radius: 100px; font-size: 11px; 
            font-weight: 600; cursor: pointer; border: none;
        }

        .modern-card {
            background: rgba(255,255,255,0.06); 
            border-radius: 18px; padding: 14px; margin-bottom: 8px;
            text-align: left;
        }
        .modern-card h5 { color: #fff; font-size: 13px; font-weight: 600; margin-bottom: 4px; }
        .modern-card p { color: rgba(255,255,255,0.6); font-size: 11.5px; line-height: 1.4; }

        .status-badge {
            display: inline-flex; align-items: center;
            padding: 4px 10px; border-radius: 8px; font-size: 9px; font-weight: 700;
            text-transform: uppercase; margin-top: 8px;
        }
        .badge-acc { background: #32D74B; color: #000; }
        .badge-rej { background: #FF453A; color: #fff; }

        /* --- SEARCH BOX --- */
        .search-container { position: relative; }
        .search-box {
            background: rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 10px 18px;
            display: flex; align-items: center; width: 220px; border: 0.5px solid rgba(255,255,255,0.1);
        }
        .search-box input { border: none; background: transparent; outline: none; font-size: 14px; width: 100%; color: #fff; }

        .suggestion-box {
            position: absolute; top: 55px; left: 0; width: 100%;
            background: rgba(30, 30, 30, 0.95); backdrop-filter: blur(20px);
            border-radius: 14px; border: 0.5px solid rgba(255,255,255,0.1);
            display: none; overflow: hidden; z-index: 1100;
        }
        .suggestion-item { padding: 12px 18px; font-size: 14px; color: #fff; cursor: pointer; border-bottom: 0.5px solid rgba(255,255,255,0.05); }
        .suggestion-item:hover { background: #007aff; }

        /* --- CONTENT --- */
        .btn-back {
            display: inline-flex; align-items: center; gap: 10px;
            margin-top: 100px; margin-left: 60px;
            padding: 10px 22px; background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px); border: 0.5px solid rgba(255, 255, 255, 0.2);
            border-radius: 25px; color: white; text-decoration: none;
            font-size: 14px; font-weight: 600; transition: 0.3s; position: relative; z-index: 10;
        }
        .btn-back:hover { background: rgba(255, 255, 255, 0.2); transform: translateX(-5px); }

        .main-content {
            height: calc(100vh - 180px); display: flex;
            flex-direction: column; justify-content: center;
            padding: 0 60px; color: white; position: relative; z-index: 5;
        }
        .content-wrapper { max-width: 600px; }
        .main-content h1 { font-size: 64px; font-weight: 800; margin-bottom: 20px; letter-spacing: -2px; }
        .main-content p { font-size: 16px; line-height: 1.6; margin-bottom: 40px; text-align: justify; color: rgba(255,255,255,0.9); }
        
        .btn-daftar {
            background-color: #007aff; color: white; text-decoration: none;
            padding: 16px 60px; border-radius: 30px;
            text-align: center; font-weight: 700; display: inline-block; transition: 0.3s;
        }
        .btn-daftar:hover { background-color: #0056b3; transform: scale(1.05); }
    </style>
</head>
<body>

    <div class="video-container">
        <video autoplay muted loop playsinline>
            <source src="gambar/badminton.mp4" type="video/mp4">
        </video>
    </div>

    <header>
        <div class="header-left">
            <div class="profile-menu">
                <div class="avatar"><img src="gambar/jo.jpg" alt="User"></div>
                <div class="dropdown-content">
                    <div id="mainMenuView">
                        <div style="padding: 14px 16px 10px; font-weight: 700; font-size: 11px; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.5px;">Akun Saya</div>
                        
                        <a href="personalise.php" class="profile-link">
                            <div class="dropdown-item">
                                <div style="display:flex; flex-direction:column;">
                                    <span style="font-weight:600; font-size:15px; color: #fff;"><?php echo $nama_user; ?></span>
                                    <span style="font-size:11px; color:rgba(255,255,255,0.5);">Mahasiswa UAD</span>
                                </div>
                                <span style="margin-left: auto; color: rgba(255,255,255,0.2); font-size: 18px;">›</span>
                            </div>
                        </a>

                        <div style="height: 0.5px; background: rgba(255,255,255,0.1); margin: 8px 12px;"></div>
                        
                        <div class="dropdown-item" onclick="switchToNotif()">
                            <span style="flex:1">Pemberitahuan</span>
                            <?php if(count($notif_data) > 0): ?>
                                <span style="background:#007aff; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:800;"><?php echo count($notif_data); ?></span>
                            <?php endif; ?>
                        </div>
                        <a href="logout.php" class="dropdown-item" style="color:#FF453A; margin-top:4px;">Keluar Sesi</a>
                    </div>

                    <div id="notifView">
                        <div class="notif-header-mini">
                            <button class="back-pill" onclick="switchToMenu()">Back</button>
                            <span style="color:#fff; font-size:13px; font-weight:600; margin-right:10px;">Updates</span>
                        </div>
                        <div style="max-height: 350px; overflow-y: auto; padding: 0 4px 8px;">
                            <?php if (!empty($notif_data)): ?>
                                <?php foreach($notif_data as $n): ?>
                                    <div class="modern-card">
                                        <h5><?php echo $n['nama_ukm']; ?></h5>
                                        <p>Permintaan bergabung Anda telah diproses.</p>
                                        <div class="status-badge <?php echo ($n['status'] == 'accepted') ? 'badge-acc' : 'badge-rej'; ?>">
                                            <?php echo ($n['status'] == 'accepted') ? 'Diterima' : 'Ditolak'; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="text-align:center; padding: 50px 20px;">
                                    <p style="font-size: 12px; color: rgba(255,255,255,0.3);">Tidak ada pemberitahuan</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="search-container">
                <div class="search-box">
                    <input type="text" id="inputUKM" placeholder="Cari UKM..." oninput="showSuggestions()" autocomplete="off">
                </div>
                <div id="suggestionBox" class="suggestion-box"></div>
            </div>
        </div>
        
        <div class="header-center">
            <h2>Kembangkan Kreatifitasmu!!</h2>
        </div>
        
        <div class="header-right">
            <span>Universitas<br>Ahmad Dahlan</span>
            <img src="gambar/a.png" alt="Logo UAD">
        </div>
    </header>

    <a href="dashboard.php" class="btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
        Kembali
    </a>

    <main class="main-content">
        <div class="content-wrapper">
            <h1>UKM Badminton</h1>
            <p>
                UKM Badminton adalah salah satu Unit Kegiatan Mahasiswa yang ada dilingkungan UAD yang merupakan suatu organisasi pengembangan minat dan bakat mahasiswa tingkat universitas dalam olahraga Badminton yang anggotanya seluruh mahasiswa dari berbagai program studi di UAD. 
            </p>
            <a href="form.php" class="btn-daftar">Daftar Sekarang</a>
        </div>
    </main>

    <script>
        function switchToNotif() {
            document.getElementById('mainMenuView').style.display = 'none';
            document.getElementById('notifView').style.display = 'block';
        }
        function switchToMenu() {
            document.getElementById('mainMenuView').style.display = 'block';
            document.getElementById('notifView').style.display = 'none';
        }

        const daftarUKM = [
            { nama: "UKM Seni Musik UAD", link: "musik.php" },
            { nama: "UKM Bola Basket UAD", link: "basket.php" },
            { nama: "UKM Kopma UAD", link: "kopma.php" },
            { nama: "UKM Seni Tari UAD", link: "tari.php" },
            { nama: "UKM Taekwondo UAD", link: "taekwo.php" },
            { nama: "UKM Karate UAD", link: "karate.php" },
            { nama: "UKM Lensa UAD", link: "lensa.php" },
            { nama: "UKM Poros UAD", link: "poros.php" },
            { nama: "UKM Badminton UAD", link: "badminton.php" },
            { nama: "UKM Sepakbola UAD", link: "bola.php" }
        ];

        function showSuggestions() {
            const input = document.getElementById('inputUKM').value.toLowerCase();
            const box = document.getElementById('suggestionBox');
            box.innerHTML = "";
            if (input.length > 0) {
                const filtered = daftarUKM.filter(ukm => ukm.nama.toLowerCase().includes(input));
                if (filtered.length > 0) {
                    box.style.display = "block";
                    filtered.forEach(item => {
                        const div = document.createElement('div');
                        div.innerHTML = item.nama;
                        div.className = "suggestion-item";
                        div.onclick = function() { window.location.href = item.link; };
                        box.appendChild(div);
                    });
                } else { box.style.display = "none"; }
            } else { box.style.display = "none"; }
        }
    </script>
</body>
</html>