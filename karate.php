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

// 2. AMBIL STATUS PENDAFTARAN TERBARU (Untuk Notifikasi)
$query_notif = "SELECT p.status, u.nama_ukm 
                FROM pendaftaran p 
                JOIN ukm u ON p.id_ukm = u.id_ukm 
                WHERE p.id_user = '$id_user' 
                ORDER BY p.id_daftar DESC LIMIT 1";
$result_notif = mysqli_query($conn, $query_notif);
$notif = mysqli_fetch_assoc($result_notif);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail UKM - Karate UAD</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

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

        /* --- HEADER DARI DASHBOARD (SAMAIN PERSIS) --- */
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

        .header-left, .header-right {
            flex: 1; 
            display: flex;
            align-items: center;
        }

        .header-left { gap: 20px; }
        .header-right { justify-content: flex-end; gap: 10px; }

        .header-center {
            flex: 2; 
            text-align: center;
        }

        .avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: rgba(255,255,255,0.2); overflow: hidden; 
            border: 1px solid rgba(255,255,255,0.3); cursor: pointer;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.3s;
        }
        .avatar:hover { transform: scale(1.1); border-color: #007aff; }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }

        .profile-menu { position: relative; }
        .dropdown-content {
            display: block; 
            position: absolute; top: 60px; left: 0; 
            background: rgba(20, 20, 20, 0.9); backdrop-filter: blur(30px);
            min-width: 220px; border-radius: 16px; overflow: hidden;
            border: 0.5px solid rgba(255,255,255,0.15);
            opacity: 0; visibility: hidden;
            transform: translateY(10px);
            transition: all 0.4s cubic-bezier(0.2, 1, 0.2, 1);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .profile-menu:hover .dropdown-content { opacity: 1; visibility: visible; transform: translateY(0); }

        .dropdown-item { padding: 14px 20px; font-size: 14px; color: #fff; text-decoration: none; display: block; transition: 0.3s; }
        .dropdown-item:hover { background: rgba(255,255,255,0.1); padding-left: 25px; color: #007aff; }

        .search-container { position: relative; }
        .search-box {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px; padding: 10px 18px;
            display: flex; align-items: center; width: 220px;
            border: 0.5px solid rgba(255,255,255,0.1);
            transition: 0.4s cubic-bezier(0.2, 1, 0.2, 1);
        }
        .search-box:focus-within { width: 280px; background: rgba(255, 255, 255, 0.2); border-color: #007aff; }
        .search-box input { border: none; background: transparent; outline: none; font-size: 14px; width: 100%; color: #fff; }

        .suggestion-box {
            position: absolute; top: 55px; left: 0; width: 100%;
            background: rgba(30, 30, 30, 0.95); backdrop-filter: blur(20px);
            border-radius: 14px; border: 0.5px solid rgba(255,255,255,0.1);
            display: none; overflow: hidden; z-index: 1100;
        }
        .suggestion-item { padding: 12px 18px; font-size: 14px; color: #fff; cursor: pointer; border-bottom: 0.5px solid rgba(255,255,255,0.05); }
        .suggestion-item:hover { background: #007aff; }

        .header-center h2 { 
            color: #ffffff; 
            font-size: 22px; 
            font-weight: 700; 
            letter-spacing: -0.5px; 
            white-space: nowrap;
        }
        
        .header-right { color: rgba(255,255,255,0.8); font-weight: 700; font-size: 13px; text-align: right; }
        .header-right img { width: 45px; }

        /* --- END OF DASHBOARD HEADER --- */

        /* ===== BACK BUTTON iOS STYLE ===== */
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 100px; /* Ditambah agar tidak tertutup fixed header */
            margin-left: 60px;
            padding: 10px 22px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 0.5px solid rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
            position: relative;
            z-index: 10;
        }
        .btn-back:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-5px);
        }
        .btn-back svg {
            width: 16px;
            height: 16px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2.5;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* ===== MAIN CONTENT ===== */
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
            text-align: center; font-weight: 700; display: inline-block;
            transition: 0.3s;
        }
        .btn-daftar:hover { background-color: #0056b3; transform: scale(1.05); }

        /* ===== MODAL iOS ===== */
        .notif-modal {
            display: none; position: fixed; z-index: 2000;
            left: 0; top: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); backdrop-filter: blur(10px);
            justify-content: center; align-items: center;
        }
        .notif-box { background: rgba(30,30,30,0.9); padding: 30px; border-radius: 20px; text-align: center; width: 350px; border: 0.5px solid rgba(255,255,255,0.1); color: #fff; }
        .close-btn { margin-top: 20px; padding: 10px 30px; background: #007aff; color: white; border: none; border-radius: 12px; cursor: pointer; font-weight: 600; }
    </style>
</head>
<body>

    <div class="video-container">
        <video autoplay muted loop playsinline>
            <source src="gambar/karate.mp4" type="video/mp4">
        </video>
    </div>

    <header>
        <div class="header-left">
            <div class="profile-menu">
                <div class="avatar"><img src="gambar/jo.jpg" alt="User"></div>
                <div class="dropdown-content">
                    <div style="padding: 15px 20px; font-weight: bold; border-bottom: 0.5px solid rgba(255,255,255,0.1); font-size: 12px; color: #888;">Halo, <?php echo $nama_user; ?></div>
                    <div class="dropdown-item" onclick="showNotif()" style="cursor:pointer;">Status Notifikasi</div>
                    <a href="logout.php" class="dropdown-item" style="color:#ff453a; border-top:0.5px solid rgba(255,255,255,0.1)">Logout Sesi</a>
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
        <svg viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
        Kembali
    </a>

    <div id="notifModal" class="notif-modal">
        <div class="notif-box">
            <h3>Pemberitahuan</h3>
            <p style="margin: 15px 0; color: #ddd;">
                <?php 
                if ($notif) {
                    echo "Pendaftaran di <b>" . $notif['nama_ukm'] . "</b> berstatus: <br><span style='color: #007aff; font-size: 20px; font-weight: 800; display: block; margin-top: 5px;'>" . strtoupper($notif['status']) . "</span>";
                } else {
                    echo "Belum ada pendaftaran.";
                }
                ?>
            </p>
            <button class="close-btn" onclick="closeNotif()">Selesai</button>
        </div>
    </div>

     <main class="main-content">
        <div class="content-wrapper">
            <h1>UKM Karate</h1>
            <p>
                UKM karate “KKI” UAD merupakan unit kegiatan mahasiswa dibidang olahraga seni beladiri karate dengan perguruan KKI (kushin Ryu M karate Do Indonesia).
            </p>
            <a href="form.php" class="btn-daftar">Daftar Sekarang</a>
        </div>
    </main>

    <script>
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
                        div.onclick = function() {
                            window.location.href = item.link;
                        };
                        box.appendChild(div);
                    });
                } else { box.style.display = "none"; }
            } else { box.style.display = "none"; }
        }

        function showNotif() { document.getElementById('notifModal').style.display = 'flex'; }
        function closeNotif() { document.getElementById('notifModal').style.display = 'none'; }
        
        window.onclick = function(event) {
            if (event.target == document.getElementById('notifModal')) { closeNotif(); }
            if (!document.getElementById("inputUKM").contains(event.target)) {
                document.getElementById("suggestionBox").style.display = "none";
            }
        }
    </script>
</body>
</html>