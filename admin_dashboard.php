<?php
include 'koneksi.php';
session_start();

/** * 1. PROTEKSI KEAMANAN TINGKAT TINGGI */
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin' || $_SESSION['email'] !== 'admin@mail.com') {
    session_destroy(); 
    header("Location: login.php?error=unauthorized");
    exit();
}

// 2. LOGIKA UPDATE STATUS (Terima/Tolak)
if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $aksi = mysqli_real_escape_string($conn, $_GET['aksi']);
    
    if ($aksi === 'terima') {
        $status_baru = 'DITERIMA';
    } elseif ($aksi === 'tolak') {
        $status_baru = 'DITOLAK';
    } else {
        $status_baru = 'PENDING';
    }
    
    $update = mysqli_query($conn, "UPDATE pendaftaran SET status='$status_baru' WHERE id_daftar='$id'");
    if($update) {
        header("Location: admin_dashboard.php?msg=success");
        exit();
    }
}

// 3. AMBIL STATISTIK
$q_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM pendaftaran");
$total_pendaftar = mysqli_fetch_assoc($q_count)['total'];

// 4. QUERY DAFTAR LENGKAP
$query_list = "SELECT p.*, u.nama, u.nim, u.prodi, u.fakultas, u.angkatan, u.email, uk.nama_ukm 
               FROM pendaftaran p
               JOIN users u ON p.id_user = u.id_user
               JOIN ukm uk ON p.id_ukm = uk.id_ukm
               ORDER BY 
                  CASE 
                    WHEN UPPER(TRIM(p.status)) = 'PENDING' THEN 1 
                    WHEN UPPER(TRIM(p.status)) = 'DITERIMA' THEN 2 
                    ELSE 3 
                  END, 
                  p.id_daftar DESC"; 
$result_list = mysqli_query($conn, $query_list);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Dahlan Muda</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --apple-blue: #007AFF;
            --apple-red: #FF3B30;
            --apple-green: #34C759;
            --apple-gray: #8E8E93;
            --bg-color: #F5F5F7;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }
        body { background-color: var(--bg-color); color: #1D1D1F; padding-bottom: 50px; }

        nav {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: saturate(180%) blur(20px); -webkit-backdrop-filter: saturate(180%) blur(20px);
            position: sticky; top: 0; z-index: 1000;
            padding: 15px 40px; display: flex; justify-content: space-between; align-items: center;
            border-bottom: 0.5px solid rgba(0,0,0,0.1);
        }
        nav h1 { font-size: 19px; font-weight: 800; letter-spacing: -0.5px; }
        .btn-logout { background: #000; color: #fff; padding: 8px 18px; border-radius: 100px; text-decoration: none; font-size: 12px; font-weight: 600; transition: 0.3s; }

        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }

        .stats-grid { margin-bottom: 40px; }
        .card-stat {
            background: #fff; padding: 25px; border-radius: 24px; width: 300px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.04);
        }
        .card-stat p { color: var(--apple-gray); font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px; }
        .card-stat h2 { font-size: 42px; font-weight: 800; letter-spacing: -2px; }

        .table-container { background: #fff; border-radius: 28px; padding: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.03); }
        .table-container h3 { margin-bottom: 25px; font-size: 22px; font-weight: 800; letter-spacing: -0.5px; }

        table { width: 100%; border-collapse: collapse; }
        table th { text-align: left; padding: 15px; font-size: 11px; color: var(--apple-gray); text-transform: uppercase; border-bottom: 1px solid rgba(0,0,0,0.05); }
        table td { padding: 20px 15px; border-bottom: 0.5px solid rgba(0,0,0,0.03); font-size: 14px; }

        .name-link { color: var(--apple-blue); cursor: pointer; font-weight: 700; transition: 0.2s; }
        .name-link:hover { opacity: 0.6; }
        
        .badge { padding: 6px 12px; border-radius: 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .status-pending { background: #FFF4E5; color: #FF9500; }
        .status-diterima { background: #EAF9F0; color: var(--apple-green); }
        .status-ditolak { background: #FFF0F0; color: var(--apple-red); }

        /* DROPDOWN STYLE */
        .action-select {
            padding: 8px 12px;
            border-radius: 10px;
            border: 1px solid #E5E5E7;
            background: #F5F5F7;
            font-size: 12px;
            font-weight: 700;
            color: #1D1D1F;
            cursor: pointer;
            outline: none;
            transition: 0.2s;
        }
        .action-select:hover { border-color: var(--apple-blue); }

        /* MODAL DETAIL STYLE */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.3);
            backdrop-filter: blur(15px); -webkit-backdrop-filter: blur(15px);
            display: none; justify-content: center; align-items: center; z-index: 2000;
        }
        .modal-overlay.active { display: flex; }

        .detail-box {
            background: #fff; border-radius: 35px; width: 550px; max-height: 85vh; 
            overflow: hidden; box-shadow: 0 30px 60px rgba(0,0,0,0.2); display: flex; flex-direction: column;
        }
        .detail-header { padding: 40px 40px 25px; background: #fafafa; border-bottom: 1px solid #f0f0f0; }
        .detail-scroll { padding: 30px 40px; overflow-y: auto; flex-grow: 1; }
        
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .info-group { margin-bottom: 20px; }
        .info-group.full { grid-column: span 2; }
        .info-label { font-size: 10px; color: var(--apple-gray); font-weight: 800; text-transform: uppercase; margin-bottom: 5px; }
        .info-value { font-size: 16px; font-weight: 600; color: #1D1D1F; }
        .alasan-bubble { background: #F5F5F7; padding: 20px; border-radius: 20px; font-weight: 400; font-size: 14px; line-height: 1.6; }

        .btn-close { width: 100%; padding: 20px; background: #000; color:#fff; border: none; font-weight: 700; cursor: pointer; font-size: 14px; }
    </style>
</head>
<body>

    <nav>
        <h1>Admin Central</h1>
        <a href="logout.php" class="btn-logout">Sign Out</a>
    </nav>

    <div class="container">
        <div class="stats-grid">
            <div class="card-stat">
                <p>Statistik Pendaftaran</p>
                <h2><?php echo $total_pendaftar; ?></h2>
            </div>
        </div>

        <div class="table-container">
            <h3>Antrean Pendaftaran</h3>
            <table>
                <thead>
                    <tr>
                        <th>Mahasiswa</th>
                        <th>Organisasi</th>
                        <th>Status</th>
                        <th style="text-align: right;">Opsi Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result_list)) { 
                        $curr_status = strtoupper(trim($row['status']));
                        $detail_json = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                    ?>
                    <tr>
                        <td>
                            <div class="name-link" onclick='showDetail(<?php echo $detail_json; ?>)'>
                                <?php echo $row['nama']; ?>
                            </div>
                            <div style="font-size: 11px; color: var(--apple-gray);"><?php echo $row['nim']; ?></div>
                        </td>
                        <td style="font-weight: 600;"><?php echo $row['nama_ukm']; ?></td>
                        <td>
                            <span class="badge status-<?php echo strtolower($curr_status ?: 'pending'); ?>">
                                <?php echo $curr_status ?: 'PENDING'; ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <select class="action-select" onchange="konfirmasi(this.value, <?php echo $row['id_daftar']; ?>, '<?php echo $row['nama']; ?>')">
                                <option value="" disabled selected>Pilih Aksi</option>
                                <option value="terima">Terima Mahasiswa</option>
                                <option value="tolak">Tolak Pendaftaran</option>
                                <option value="pending">Set Pending</option>
                            </select>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="modalDetail" class="modal-overlay">
        <div class="detail-box">
            <div class="detail-header">
                <h2 id="dNama" style="font-size: 28px; font-weight: 800; letter-spacing: -1.2px;">-</h2>
                <p id="dNim" style="color: var(--apple-gray); font-size: 15px; font-weight: 500;"></p>
            </div>
            <div class="detail-scroll">
                <div class="info-grid">
                    <div class="info-group"><div class="info-label">Fakultas</div><div id="dFakultas" class="info-value">-</div></div>
                    <div class="info-group"><div class="info-label">Prodi</div><div id="dProdi" class="info-value">-</div></div>
                    <div class="info-group"><div class="info-label">Angkatan</div><div id="dAngkatan" class="info-value">-</div></div>
                    <div class="info-group"><div class="info-label">WhatsApp</div><div id="dHp" class="info-value" style="color:var(--apple-green)">-</div></div>
                    <div class="info-group full"><div class="info-label">Email</div><div id="dEmail" class="info-value">-</div></div>
                    <div class="info-group full"><div class="info-label">Alasan Bergabung</div><div id="dAlasan" class="alasan-bubble">-</div></div>
                </div>
            </div>
            <button class="btn-close" onclick="closeDetail()">Tutup</button>
        </div>
    </div>

    <div id="modalConfirm" class="modal-overlay">
        <div style="background:#fff; padding:40px; border-radius:30px; width:360px; text-align:center;">
            <h3 style="font-weight:800; font-size:20px;">Konfirmasi Tindakan</h3>
            <p id="mText" style="font-size:14px; color:var(--apple-gray); margin: 20px 0; line-height:1.5;"></p>
            <div style="display:flex; gap:12px;">
                <button onclick="closeModal()" style="flex:1; padding:14px; border-radius:15px; border:none; background:#F2F2F7; font-weight:700; cursor:pointer;">Batal</button>
                <a id="mLink" href="#" style="flex:1; padding:14px; border-radius:15px; text-decoration:none; color:#fff; font-weight:700; display:block; font-size:14px;">Lanjutkan</a>
            </div>
        </div>
    </div>

    <script>
        function showDetail(data) {
            document.getElementById('dNama').innerText = data.nama;
            document.getElementById('dNim').innerText = "Mahasiswa — NIM " + data.nim;
            document.getElementById('dFakultas').innerText = data.fakultas || "-";
            document.getElementById('dProdi').innerText = data.prodi;
            document.getElementById('dAngkatan').innerText = data.angkatan || "-";
            document.getElementById('dHp').innerText = data.no_hp || "-";
            document.getElementById('dEmail').innerText = data.email;
            document.getElementById('dAlasan').innerText = data.alasan || "Tidak ada esai.";
            document.getElementById('modalDetail').classList.add('active');
        }
        function closeDetail() { document.getElementById('modalDetail').classList.remove('active'); }

        function konfirmasi(aksi, id, nama) {
            if(!aksi) return;
            const link = document.getElementById('mLink');
            const label = (aksi === 'terima') ? 'MENERIMA' : (aksi === 'tolak' ? 'MENOLAK' : 'PENDING');
            
            document.getElementById('mText').innerHTML = "Apakah Anda yakin ingin <b>" + label + "</b> pendaftaran dari <b>" + nama + "</b>?";
            link.href = "?aksi=" + aksi + "&id=" + id;
            
            // Warna tombol konfirmasi dinamis
            if(aksi === 'terima') link.style.background = "var(--apple-green)";
            else if(aksi === 'tolak') link.style.background = "var(--apple-red)";
            else link.style.background = "var(--apple-blue)";
            
            document.getElementById('modalConfirm').classList.add('active');
        }

        function closeModal() { 
            document.getElementById('modalConfirm').classList.remove('active');
            // Reset dropdown ke default jika batal
            const selects = document.querySelectorAll('.action-select');
            selects.forEach(s => s.selectedIndex = 0);
        }

        window.onclick = (e) => { if(e.target.classList.contains('modal-overlay')) { closeDetail(); closeModal(); } }
    </script>
</body>
</html>