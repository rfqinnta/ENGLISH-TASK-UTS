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

// 2. AMBIL STATUS PENDAFTARAN
$query_notif = "SELECT p.status, u.nama_ukm 
                FROM pendaftaran p 
                JOIN ukm u ON p.id_ukm = u.id_ukm 
                WHERE p.id_user = '$id_user' 
                AND (UPPER(p.status) = 'DITERIMA' OR UPPER(p.status) = 'DITOLAK') 
                ORDER BY p.id_daftar DESC";
$result_notif = mysqli_query($conn, $query_notif);
$notif_data = [];
while($rn = mysqli_fetch_assoc($result_notif)) {
    $notif_data[] = $rn;
}

// 3. DATA SLIDESHOW (Original)
$slider_data = [
    ['nama' => 'UKM Seni Musik', 'desc' => 'Tempatnya orang keren #DamaiKamiSepanjangHari.', 'link' => 'musik.php', 'img' => 'gambar/musik1.jpeg'],
    ['nama' => 'UKM Bola Basket', 'desc' => 'Asah skill dribble dan shooting untuk prestasi maksimal.', 'link' => 'basket.php', 'img' => 'gambar/basket3.jpeg'],
    ['nama' => 'UKM Seni Tari', 'desc' => 'Lestarikan budaya lewat gerak tari yang indah dan memukau.', 'link' => 'tari.php', 'img' => 'gambar/TARI3.jpg'],
    ['nama' => 'UKM Fotografi Lensa', 'desc' => 'Tangkap momen berharga melalui sudut pandang lensa kreatif.', 'link' => 'lensa.php', 'img' => 'gambar/lensa2.jpg'],
    ['nama' => 'UKM Taekwondo', 'desc' => 'Bentuk mental baja dan disiplin tinggi melalui bela diri.', 'link' => 'taekwo.php', 'img' => 'gambar/taekwondo1.jpg'],
    ['nama' => 'UKM Karate', 'desc' => 'Asah kekuatan fisik dan jiwa ksatria bersama kami.', 'link' => 'karate.php', 'img' => 'gambar/karate5.jpg'],
    ['nama' => 'UKM Kopma', 'desc' => 'Belajar wirausaha dan kelola ekonomi kreatif bersama.', 'link' => 'kopma.php', 'img' => 'gambar/kopma1.jpg'],
    ['nama' => 'UKM Poros', 'desc' => 'Suarakan aspirasimu melalui jurnalisme kampus yang tajam.', 'link' => 'poros.php', 'img' => 'gambar/poros3.jpg'],
    ['nama' => 'UKM Badminton', 'desc' => 'Tunjukkan smash terbaikmu dan raih prestasi di lapangan.', 'link' => 'badminton.php', 'img' => 'gambar/badminton1.jpg'],
    ['nama' => 'UKM Sepakbola', 'desc' => 'Satukan tekad dan kerja sama tim di lapangan hijau.', 'link' => 'bola.php', 'img' => 'gambar/bola4.png']
];

// Ambil semua data UKM untuk keperluan search bar (Javascript side)
$all_ukm = [];
$ukm_query = mysqli_query($conn, "SELECT nama_ukm FROM ukm ORDER BY nama_ukm ASC");
while($ru = mysqli_fetch_assoc($ukm_query)) {
    // Tentukan link tujuan berdasarkan nama
    $n = strtolower($ru['nama_ukm']);
    $link = "dashboard.php";
    if (strpos($n, 'musik') !== false) $link = "musik.php";
    else if (strpos($n, 'basket') !== false) $link = "basket.php";
    else if (strpos($n, 'poros') !== false) $link = "poros.php";
    else if (strpos($n, 'tari') !== false) $link = "tari.php";
    else if (strpos($n, 'lensa') !== false) $link = "lensa.php";
    else if (strpos($n, 'kopma') !== false) $link = "kopma.php";
    else if (strpos($n, 'taekwo') !== false) $link = "taekwo.php";
    else if (strpos($n, 'karate') !== false) $link = "karate.php";
    else if (strpos($n, 'badminton') !== false) $link = "badminton.php";
    else if (strpos($n, 'bola') !== false) $link = "bola.php";
    
    $all_ukm[] = ['nama' => $ru['nama_ukm'], 'link' => $link];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - UKM UAD</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }
        
        body, html { 
            height: 100%; 
            background: #F2F2F7; 
            overflow-x: hidden; 
            scroll-behavior: smooth;
            scrollbar-width: none; 
        }
        body::-webkit-scrollbar { display: none; }

        /* HEADER */
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

        .avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: rgba(255,255,255,0.2); overflow: visible;
            border: 1px solid rgba(255,255,255,0.3); cursor: pointer;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .avatar:hover { transform: scale(1.1); }
        .avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }

        .notif-dot {
            position: absolute; top: -2px; right: -2px;
            width: 12px; height: 12px; background: #007aff;
            border: 2px solid #1c1c1e; border-radius: 50%;
            z-index: 10;
        }

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
            font-weight: 500;
        }
        .dropdown-item:hover { background: rgba(255,255,255,0.12); }

        #notifView { display: none; padding: 4px; animation: fadeIn 0.3s ease; }
        
        .notif-scroll-area {
            max-height: 350px; 
            overflow-y: auto; 
            padding: 0 4px 8px;
            scrollbar-width: none;
        }
        .notif-scroll-area::-webkit-scrollbar { display: none; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .notif-header-mini { 
            display: flex; align-items: center; justify-content: space-between;
            padding: 8px 10px 14px; border-bottom: 0.5px solid rgba(255,255,255,0.1); margin-bottom: 12px; 
        }

        .back-pill { 
            background: rgba(255, 255, 255, 0.1); color: #fff; 
            padding: 6px 14px; border-radius: 100px; font-size: 11px; 
            font-weight: 600; cursor: pointer; transition: 0.2s;
            border: none; display: flex; align-items: center; gap: 5px;
        }
        .back-pill:hover { background: rgba(255, 255, 255, 0.2); }

        .modern-card {
            background: rgba(255,255,255,0.06); 
            border-radius: 18px; padding: 14px; margin-bottom: 8px;
            transition: 0.2s; cursor: default;
        }
        .modern-card:hover { background: rgba(255,255,255,0.09); }
        .modern-card h5 { color: #fff; font-size: 13px; font-weight: 600; margin-bottom: 4px; letter-spacing: -0.2px; }
        .modern-card p { color: rgba(255,255,255,0.6); font-size: 11.5px; line-height: 1.4; margin-bottom: 10px; }

        .status-badge {
            display: inline-flex; align-items: center;
            padding: 4px 10px; border-radius: 8px; font-size: 9px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .badge-acc { background: #32D74B; color: #000; }
        .badge-rej { background: #FF453A; color: #fff; }

        /* SEARCH & SUGGESTION STYLES */
        .search-container { position: relative; }
        .search-box {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px; padding: 10px 18px;
            display: flex; align-items: center; width: 220px;
            border: 0.5px solid rgba(255,255,255,0.1);
            transition: 0.4s cubic-bezier(0.2, 1, 0.2, 1);
        }
        .search-box:focus-within { width: 280px; background: rgba(255, 255, 255, 0.15); border-color: #007aff; }
        .search-box input { border: none; background: transparent; outline: none; font-size: 14px; width: 100%; color: #fff; }

        .suggestion-box {
            position: absolute; top: 50px; left: 0; width: 280px;
            background: rgba(28, 28, 30, 0.9); backdrop-filter: blur(20px);
            border-radius: 15px; border: 1px solid rgba(255,255,255,0.1);
            display: none; z-index: 2000; overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }
        .suggestion-item {
            padding: 12px 18px; color: #fff; font-size: 13px;
            cursor: pointer; transition: 0.2s; text-decoration: none; display: block;
        }
        .suggestion-item:hover { background: rgba(255,255,255,0.1); }

        .header-center h2 { color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
        .header-right { color: rgba(255,255,255,0.8); font-weight: 700; font-size: 13px; text-align: right; }
        .header-right img { width: 45px; }

        /* HERO & GRID */
        .hero-slider { height: 100vh; width: 100%; position: relative; background: #000; overflow: hidden; }
        .slide { position: absolute; inset: 0; display: flex; align-items: flex-end; justify-content: center; text-align: center; opacity: 0; transition: 1.2s cubic-bezier(0.4, 0, 0.2, 1); background-size: cover !important; background-position: center !important; }
        .slide.active { opacity: 1; z-index: 1; }
        .slide::after { content: ''; position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 60%); }
        .slide-content { position: relative; z-index: 10; color: white; max-width: 800px; padding: 0 20px; padding-bottom: 80px; transform: translateY(20px); transition: 1.2s; }
        .slide.active .slide-content { transform: translateY(0); }
        .slide-content h1 { font-size: clamp(40px, 8vw, 80px); font-weight: 800; margin-bottom: 15px; letter-spacing: -2px; }
        .slide-content p { font-size: clamp(16px, 2vw, 22px); margin-bottom: 35px; color: rgba(255,255,255,0.9); }
        .btn-explore { background: #fff; color: #000; text-decoration: none; padding: 16px 45px; border-radius: 100px; font-weight: 700; display: inline-block; transition: 0.3s; }

        .grid-section { padding: 80px 60px; background: #fff; }
        .grid-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 25px; max-width: 1400px; margin: 0 auto; }
        .card-ukm { background: #F2F2F7; border-radius: 28px; padding: 30px; display: flex; flex-direction: column; align-items: center; text-decoration: none; transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .card-ukm:hover { transform: scale(1.05); background: #E5E5EA; }
        .card-ukm img { width: 100px; height: 100px; object-fit: contain; margin-bottom: 15px; }
        .card-ukm h3 { font-size: 14px; font-weight: 700; color: #000; text-align: center; }
    </style>
</head>
<body>

    <header>
        <div class="header-left">
            <div class="profile-menu">
                <div class="avatar">
                    <img src="gambar/jo.jpg" alt="User">
                    <?php if(count($notif_data) > 0): ?>
                        <div class="notif-dot" id="dotIcon"></div>
                    <?php endif; ?>
                </div>
                
                <div class="dropdown-content">
                    <div id="mainMenuView">
                        <div style="padding: 14px 16px 10px; font-weight: 700; font-size: 11px; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.5px;">Akun Saya</div>
                        
                        <a href="personalize_akun.php" class="dropdown-item" style="text-decoration: none;">
                            <div style="display:flex; flex-direction:column;">
                                <span style="font-weight:600; font-size:15px; color: #fff;"><?php echo $nama_user; ?></span>
                                <span style="font-size:11px; color:rgba(255,255,255,0.5);">Mahasiswa UAD</span>
                            </div>
                        </a>

                        <div style="height: 0.5px; background: rgba(255,255,255,0.1); margin: 8px 12px;"></div>
                        
                        <div class="dropdown-item" onclick="switchToNotif()">
                            <span style="flex:1">Pemberitahuan</span>
                            <?php if(count($notif_data) > 0): ?>
                                <span id="countBadge" style="background:#007aff; padding:2px 8px; border-radius:10px; font-size:10px; font-weight:800;"><?php echo count($notif_data); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <a href="logout.php" class="dropdown-item" style="color:#FF453A; margin-top:4px;">Keluar Sesi</a>
                    </div>

                    <div id="notifView">
                        <div class="notif-header-mini">
                            <button class="back-pill" onclick="switchToMenu()">Back</button>
                            <?php if (!empty($notif_data)): ?>
                                <span id="clearBtn" onclick="clearNotif()" style="color:#fff; font-size:13px; font-weight:600; margin-right:10px; cursor:pointer;">Clear</span>
                            <?php else: ?>
                                <span style="color:#fff; font-size:13px; font-weight:600; margin-right:10px; opacity: 0.5;">Clear</span>
                            <?php endif; ?>
                        </div>
                        <div class="notif-scroll-area" id="notifArea">
                            <?php if (!empty($notif_data)): ?>
                                <?php foreach($notif_data as $n): 
                                    $status_clean = strtoupper($n['status']); 
                                ?>
                                    <div class="modern-card">
                                        <h5><?php echo $n['nama_ukm']; ?></h5>
                                        <p>Permintaan bergabung Anda telah diproses oleh pengurus UKM.</p>
                                        <div class="status-badge <?php echo ($status_clean == 'DITERIMA') ? 'badge-acc' : 'badge-rej'; ?>">
                                            <?php echo ($status_clean == 'DITERIMA') ? 'Diterima' : 'Ditolak'; ?>
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

    <div id="pageContent">
        <section class="hero-slider">
            <?php foreach ($slider_data as $index => $slide): ?>
                <div class="slide" style="background: url('<?php echo $slide['img']; ?>');">
                    <div class="slide-content">
                        <h1><?php echo $slide['nama']; ?></h1>
                        <p><?php echo $slide['desc']; ?></p>
                        <a href="<?php echo $slide['link']; ?>" class="btn-explore" onclick="saveSlide(<?php echo $index; ?>)">Detail UKM</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <section class="grid-section">
            <h2 style="text-align:center; margin-bottom:40px; font-weight: 800; font-size: 32px; color:#000; letter-spacing: -1px;">Semua Unit Kegiatan Mahasiswa</h2>
            <div class="grid-container">
                <?php
                $sql = "SELECT * FROM ukm ORDER BY nama_ukm ASC";
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($result)) {
                    $nama_low = strtolower($row['nama_ukm']);
                    $logo = "gambar/mabes.png"; $target = "dashboard.php";
                    if (strpos($nama_low, 'musik') !== false) { $logo = "gambar/mabes.png"; $target = "musik.php"; }
                    else if (strpos($nama_low, 'basket') !== false) { $logo = "gambar/baskett.png"; $target = "basket.php"; }
                    else if (strpos($nama_low, 'poros') !== false) { $logo = "gambar/poross.png"; $target = "poros.php"; }
                    else if (strpos($nama_low, 'tari') !== false) { $logo = "gambar/tari.jpg"; $target = "tari.php"; }
                    else if (strpos($nama_low, 'lensa') !== false) { $logo = "gambar/poto.png"; $target = "lensa.php"; }
                    else if (strpos($nama_low, 'kopma') !== false) { $logo = "gambar/kopma.jpeg"; $target = "kopma.php"; }
                    else if (strpos($nama_low, 'taekwo') !== false) { $logo = "gambar/taekwo.png"; $target = "taekwo.php"; }
                    else if (strpos($nama_low, 'karate') !== false) { $logo = "gambar/karate.png"; $target = "karate.php"; }
                    else if (strpos($nama_low, 'badminton') !== false) { $logo = "gambar/badminton.png"; $target = "badminton.php"; }
                    else if (strpos($nama_low, 'bola') !== false) { $logo = "gambar/bola.jpg"; $target = "bola.php"; }
                ?>
                    <a href="<?php echo $target; ?>" class="card-ukm">
                        <img src="<?php echo $logo; ?>" alt="Logo">
                        <h3><?php echo $row['nama_ukm']; ?></h3>
                    </a>
                <?php } ?>
            </div>
        </section>
    </div>

    <script>
        // Data UKM dari PHP ke JS
        const ukmList = <?php echo json_encode($all_ukm); ?>;

        function showSuggestions() {
            const input = document.getElementById('inputUKM').value.toLowerCase();
            const box = document.getElementById('suggestionBox');
            
            box.innerHTML = "";
            
            if (input.length > 0) {
                const filtered = ukmList.filter(ukm => ukm.nama.toLowerCase().includes(input));
                
                if (filtered.length > 0) {
                    box.style.display = "block";
                    filtered.forEach(item => {
                        const div = document.createElement('a');
                        div.href = item.link;
                        div.className = "suggestion-item";
                        div.innerText = item.nama;
                        box.appendChild(div);
                    });
                } else {
                    box.style.display = "none";
                }
            } else {
                box.style.display = "none";
            }
        }

        // Search bar nutup kalo mouse diuluar
        document.addEventListener('click', function(e) {
            if (!document.getElementById('inputUKM').contains(e.target)) {
                document.getElementById('suggestionBox').style.display = 'none';
            }
        });

        function switchToNotif() {
            document.getElementById('mainMenuView').style.display = 'none';
            document.getElementById('notifView').style.display = 'block';
        }

        function switchToMenu() {
            document.getElementById('mainMenuView').style.display = 'block';
            document.getElementById('notifView').style.display = 'none';
        }

        function clearNotif() {
            // Memanggil file PHP untuk menghapus notif di database (nyatu ama clear_notif)
            fetch('clear_notif.php')
                .then(response => response.text())
                .then(data => {
                    // Update tampilan UI secara langsung
                    document.getElementById('notifArea').innerHTML = `
                        <div style="text-align:center; padding: 50px 20px;">
                            <p style="font-size: 12px; color: rgba(255,255,255,0.3);">Tidak ada pemberitahuan</p>
                        </div>`;
                    
                    // Sembunyikan dot notifikasi dan badge angka
                    if(document.getElementById('dotIcon')) document.getElementById('dotIcon').style.display = 'none';
                    if(document.getElementById('countBadge')) document.getElementById('countBadge').style.display = 'none';
                    
                    // Ubah style tombol Clear menjadi pasif
                    if(document.getElementById('clearBtn')) {
                        document.getElementById('clearBtn').style.opacity = '0.5';
                        document.getElementById('clearBtn').style.cursor = 'default';
                        document.getElementById('clearBtn').onclick = null;
                    }
                })
                .catch(err => console.error('Gagal menghapus notifikasi:', err));
        }

        let slides = document.querySelectorAll('.slide');
        let currentSlide = 0;
        let slideInterval;
        function saveSlide(index) { localStorage.setItem('lastSlideIndex', index); }
        function initSlider() {
            const savedIndex = localStorage.getItem('lastSlideIndex');
            if (savedIndex !== null) currentSlide = parseInt(savedIndex);
            if(slides.length > 0) slides[currentSlide].classList.add('active');
            resetTimer();
        }
        function showSlide(index) {
            if (slides.length === 0) return;
            slides[currentSlide].classList.remove('active');
            currentSlide = (index + slides.length) % slides.length;
            slides[currentSlide].classList.add('active');
            saveSlide(currentSlide);
            resetTimer();
        }
        function nextSlide() { showSlide(currentSlide + 1); }
        function resetTimer() { clearInterval(slideInterval); slideInterval = setInterval(nextSlide, 5000); }
        initSlider();
    </script>
</body>
</html>