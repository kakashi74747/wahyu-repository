<?php
session_start();
include '../konkon.php';

// Cek apakah karyawan sudah login
if (!isset($_SESSION['idKaryawan']) || !isset($_SESSION['login_type']) || $_SESSION['login_type'] !== 'karyawan') {
    header("Location: ../login.php");
    exit();
}

$idKaryawan = $_SESSION['idKaryawan'];

// Ambil data karyawan dari database
$query = "SELECT * FROM data_karyawan WHERE idKaryawan = ?";
$stmt = $kon->prepare($query);
$stmt->bind_param("s", $idKaryawan);
$stmt->execute();
$result = $stmt->get_result();
$karyawan = $result->fetch_assoc();

if (!$karyawan) {
    echo "Data karyawan tidak ditemukan.";
    exit();
}

// Proses update profil karyawan
if (isset($_POST['update_profile'])) {
    $nama = $_POST['nama'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    
    $update_query = "UPDATE data_karyawan SET nama = ?, alamat = ? WHERE idKaryawan = ?";
    $update_stmt = $kon->prepare($update_query);
    $update_stmt->bind_param("sss", $nama, $alamat, $idKaryawan);
    $update_stmt->execute();
    
    $karyawan['nama'] = $nama;
    $karyawan['alamat'] = $alamat;
}

// Proses update avatar
if (isset($_POST['update_avatar'])) {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../SSC/img/';
        $file_name = 'karyawan_avatar_' . $idKaryawan . '_' . time() . '.' . pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $file_path = "{$upload_dir}{$file_name}";

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $file_path)) {
            if ($karyawan['avatar'] && file_exists($upload_dir . $karyawan['avatar'])) {
                unlink($upload_dir . $karyawan['avatar']);
            }
            $update_query = "UPDATE data_karyawan SET avatar = ? WHERE idKaryawan = ?";
            $update_stmt = $kon->prepare($update_query);
            $update_stmt->bind_param("ss", $file_name, $idKaryawan);
            $update_stmt->execute();
            $karyawan['avatar'] = $file_name;
        }
    }
}

// Proses update pengaturan akun (password)
if (isset($_POST['update_settings'])) {
    $password = $_POST['password'] ?? '';

    if ($password) {
        $update_query = "UPDATE data_karyawan SET password = ? WHERE idKaryawan = ?";
        $update_stmt = $kon->prepare($update_query);
        $update_stmt->bind_param("ss", $password, $idKaryawan);
        $update_stmt->execute();
    }
}

// Mapping bagian ke deskripsi
$bagian_map = [
    'A' => 'Admin',
    'B' => 'Kasir',
    'C' => 'Dapur',
    'D' => 'Pelayan'
];
$bagian_label = $bagian_map[$karyawan['bagian']] ?? 'Tidak Diketahui';

// Konversi gender
$gender_text = ($karyawan['gender'] == 'L') ? 'Laki-laki' : 'Perempuan';

// Konversi agama
$agama_text = '';
switch($karyawan['agama']) {
    case 1: $agama_text = 'Islam'; break;
    case 2: $agama_text = 'Kristen'; break;
    case 3: $agama_text = 'Katolik'; break;
    case 4: $agama_text = 'Hindu'; break;
    case 5: $agama_text = 'Buddha'; break;
    default: $agama_text = 'Lainnya';
}

// Hitung hari kerja berdasarkan tipe karyawan
$tanggal_mulai = new DateTime($karyawan['tanggal_mulai']);
$tanggal_sekarang = new DateTime('2025-05-31 12:34:00'); // Sesuaikan dengan tanggal dan waktu saat ini
$interval = $tanggal_mulai->diff($tanggal_sekarang);
$total_hari = $interval->days; // Total hari sejak tanggal mulai

// Hitung jumlah minggu dan sisa hari
$jumlah_minggu = floor($total_hari / 7);
$sisa_hari = $total_hari % 7;

// Ambil tipe karyawan
$tipe_karyawan = isset($karyawan['tipe_karyawan']) ? $karyawan['tipe_karyawan'] : 'full-time';

// Hitung hari kerja berdasarkan tipe karyawan
if ($tipe_karyawan === 'full-time') {
    // Full-Time: 5 hari kerja per minggu
    $hari_kerja_per_minggu = 5;
    $hari_kerja = ($jumlah_minggu * $hari_kerja_per_minggu) + min($sisa_hari, $hari_kerja_per_minggu);
} else {
    // Part-Time: rata-rata 3.5 hari kerja per minggu
    $hari_kerja_per_minggu = 3.5;
    $hari_kerja = round(($jumlah_minggu * $hari_kerja_per_minggu) + min($sisa_hari * ($hari_kerja_per_minggu / 7), $hari_kerja_per_minggu));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Profil Karyawan | Cafe Cassie</title>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        :root {
            --bg-primary: #C9B6FF;
            --bg-secondary: #8F5BFF;
            --text-main: #1F1F1F;
            --text-light: #666;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            background: linear-gradient(to bottom right, #EADFFF, #F9F4FF);
            margin: 0;
            padding: 20px;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .profile-container {
            max-width: 800px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(140, 100, 255, 0.15);
            backdrop-filter: blur(5px);
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .profile-avatar-container {
            position: relative;
            margin-right: 30px;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .profile-avatar:hover {
            opacity: 0.8;
            transform: scale(1.05);
        }

        .avatar-edit {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: var(--bg-secondary);
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .profile-info {
            flex: 1;
            min-width: 250px;
        }

        .profile-name {
            font-family: 'Pacifico', cursive;
            color: var(--bg-secondary);
            font-size: 2.2rem;
            margin: 0 0 5px;
            display: flex;
            align-items: center;
        }

        .edit-icon {
            margin-left: 10px;
            cursor: pointer;
            color: #aaa;
            transition: all 0.2s ease;
        }

        .edit-icon:hover {
            color: var(--bg-secondary);
        }

        .profile-role {
            color: #666;
            font-size: 1.1rem;
            margin: 0 0 15px;
            display: flex;
            align-items: center;
        }

        .profile-role i {
            margin-right: 8px;
            color: var(--bg-secondary);
        }

        .edit-form {
            display: none;
            margin-top: 10px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: 'Quicksand', sans-serif;
            font-size: 1rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--bg-secondary);
            box-shadow: 0 0 0 2px rgba(143, 91, 255, 0.2);
        }

        .btn {
            padding: 8px 20px;
            border-radius: 8px;
            border: none;
            font-family: 'Quicksand', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: var(--bg-secondary);
            color: white;
        }

        .btn-primary:hover {
            background-color: #7a4be6;
        }

        .btn-outline {
            background: none;
            border: 1px solid #ddd;
            color: #666;
            margin-left: 10px;
        }

        .btn-outline:hover {
            border-color: #aaa;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            display: none;
        }

        .modal-content {
            background: white;
            padding: 25px;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 5px 30px rgba(0,0,0,0.2);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-family: 'Pacifico', cursive;
            color: var(--bg-secondary);
            font-size: 1.5rem;
            margin: 0;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        .avatar-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 20px;
            display: block;
            border: 3px solid #eee;
        }

        .profile-stats {
            display: flex;
            margin: 20px 0;
            gap: 20px;
            justify-content: center; /* Pusatkan stat-item */
        }

        .stat-item {
            background: rgba(201, 182, 255, 0.2);
            padding: 15px 20px;
            border-radius: 12px;
            text-align: center;
            flex: 0 1 auto; /* Sesuaikan lebar agar tidak terlalu lebar */
            min-width: 150px; /* Atur lebar minimum */
        }

        .stat-number {
            font-weight: 700;
            color: var(--bg-secondary);
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        .stat-subtext {
            color: #666;
            font-size: 0.8rem;
            margin-top: 5px;
        }

        .profile-tabs {
            display: flex;
            border-bottom: 2px solid #eee;
            margin-bottom: 20px;
        }

        .tab-button {
            padding: 12px 20px;
            background: none;
            border: none;
            font-family: 'Quicksand', sans-serif;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
        }

        .tab-button.active {
            color: var(--bg-secondary);
        }

        .tab-button.active:after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--bg-secondary);
        }

        .profile-section {
            margin-bottom: 30px;
        }

        .section-title {
            font-family: 'Pacifico', cursive;
            color: var(--bg-secondary);
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .detail-item {
            background: rgba(201, 182, 255, 0.1);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 10px;
        }

        .detail-label {
            font-weight: 600;
            color: var(--bg-secondary);
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .detail-value {
            color: #333;
            font-size: 1rem;
        }

        .logout-btn, .back-btn {
            display: inline-block;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            font-family: 'Quicksand', sans-serif;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .logout-btn {
            background: #d9534f;
            color: #fff;
            margin-right: 10px;
        }

        .logout-btn:hover {
            background: #c9302c;
        }

        .back-btn {
            background: #6c757d;
            color: #fff;
        }

        .back-btn:hover {
            background: #5a6268;
        }

        @media (max-width: 768px) {
            .profile-container {
                padding: 20px;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .profile-avatar-container {
                margin: 0 auto 20px;
            }

            .profile-name {
                justify-content: center;
            }

            .profile-stats {
                flex-wrap: wrap;
            }

            .stat-item {
                min-width: 100%; /* Pada layar kecil, buat stat-item memenuhi lebar */
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar-container">
                <img src="../SSC/img/<?php echo htmlspecialchars($karyawan['avatar'] ?? 'default-karyawan.jpg'); ?>" class="profile-avatar" alt="Karyawan Avatar" id="karyawanAvatar">
                <div class="avatar-edit" id="editAvatarBtn">
                    <i class="fas fa-camera"></i>
                </div>
            </div>
            <div class="profile-info">
                <h1 class="profile-name" id="profileName">
                    <?php echo htmlspecialchars($karyawan['nama']); ?>
                    <i class="fas fa-pencil-alt edit-icon" id="editProfileBtn"></i>
                </h1>
                <p class="profile-role">
                    <i class="fas fa-coffee"></i>
                    <?php echo htmlspecialchars($bagian_label); ?> at Cafe Cassie
                </p>
            </div>
        </div>

        <div class="edit-form" id="editForm">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="namaInput">Nama</label>
                    <input type="text" class="form-control" id="namaInput" name="nama" value="<?php echo htmlspecialchars($karyawan['nama']); ?>">
                </div>
                <div class="form-group">
                    <label for="alamatInput">Alamat</label>
                    <input type="text" class="form-control" id="alamatInput" name="alamat" value="<?php echo htmlspecialchars($karyawan['alamat']); ?>">
                </div>
                <button type="submit" class="btn btn-primary" name="update_profile">Simpan</button>
                <button type="button" class="btn btn-outline" id="cancelEditBtn">Batal</button>
            </form>
        </div>

        <div class="profile-stats">
            <div class="stat-item">
                <div class="stat-number"><?php echo $hari_kerja; ?></div>
                <div class="stat-label">Hari Kerja</div>
                <div class="stat-subtext"><?php echo htmlspecialchars(ucfirst($tipe_karyawan)); ?></div>
            </div>
        </div>

        <div class="profile-tabs">
            <button class="tab-button active">Info Pribadi</button>
            <button class="tab-button">Pengaturan Akun</button>
        </div>

        <div class="profile-section">
            <h2 class="section-title">Info Pribadi</h2>
            <div class="profile-details">
                <div class="detail-item">
                    <div class="detail-label">ID Karyawan</div>
                    <div class="detail-value"><?php echo htmlspecialchars($karyawan['idKaryawan']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Gender</div>
                    <div class="detail-value"><?php echo htmlspecialchars($gender_text); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Agama</div>
                    <div class="detail-value"><?php echo htmlspecialchars($agama_text); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Tanggal Mulai</div>
                    <div class="detail-value"><?php echo htmlspecialchars($karyawan['tanggal_mulai']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Tipe Karyawan</div>
                    <div class="detail-value"><?php echo htmlspecialchars(ucfirst($tipe_karyawan)); ?></div>
                </div>
            </div>
        </div>

        <div class="profile-section" style="display: none;">
            <h2 class="section-title">Pengaturan Akun</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="passwordInput">Ganti Password</label>
                    <input type="password" class="form-control" id="passwordInput" name="password" placeholder="Masukkan password baru">
                </div>
                <div class="form-group">
                    <label for="confirmPasswordInput">Konfirmasi Password</label>
                    <input type="password" class="form-control" id="confirmPasswordInput" placeholder="Konfirmasi password baru">
                </div>
                <button type="submit" class="btn btn-primary" name="update_settings">Simpan Perubahan</button>
            </form>
        </div>

        <div class="button-group">
            <a href="/TugasAkhir/pages/indegs.php" class="back-btn">Kembali</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="modal-overlay" id="avatarModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Ubah Foto Profil</h3>
                <button class="close-modal" id="closeModalBtn">×</button>
            </div>
            <img src="../SSC/img/<?php echo htmlspecialchars($karyawan['avatar'] ?? 'default-karyawan.jpg'); ?>" class="avatar-preview" id="avatarPreview">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="file" id="avatarUpload" name="avatar" accept="image/*" style="display: none;">
                    <button type="button" class="btn btn-primary" id="uploadBtn" style="width: 100%;">Pilih Gambar</button>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" id="saveAvatarBtn" name="update_avatar" style="width: 100%;" disabled>Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Tab Switching
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.tab-button').forEach(btn => {
                    btn.classList.remove('active');
                });
                button.classList.add('active');
                document.querySelectorAll('.profile-section').forEach(section => {
                    section.style.display = 'none';
                });
                const sections = document.querySelectorAll('.profile-section');
                const index = Array.from(button.parentElement.children).indexOf(button);
                if (sections[index]) {
                    sections[index].style.display = 'block';
                }
            });
        });

        // Edit Profile Form
        const editProfileBtn = document.getElementById('editProfileBtn');
        const profileName = document.getElementById('profileName');
        const editForm = document.getElementById('editForm');
        const cancelEditBtn = document.getElementById('cancelEditBtn');

        editProfileBtn.addEventListener('click', () => {
            profileName.style.display = 'none';
            editForm.style.display = 'block';
            editProfileBtn.style.display = 'none';
        });

        cancelEditBtn.addEventListener('click', () => {
            profileName.style.display = 'flex';
            editForm.style.display = 'none';
            editProfileBtn.style.display = 'inline';
        });

        // Avatar Modal
        const editAvatarBtn = document.getElementById('editAvatarBtn');
        const avatarModal = document.getElementById('avatarModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const avatarUpload = document.getElementById('avatarUpload');
        const uploadBtn = document.getElementById('uploadBtn');
        const saveAvatarBtn = document.getElementById('saveAvatarBtn');
        const avatarPreview = document.getElementById('avatarPreview');

        editAvatarBtn.addEventListener('click', () => {
            avatarModal.style.display = 'flex';
        });

        closeModalBtn.addEventListener('click', () => {
            avatarModal.style.display = 'none';
        });

        uploadBtn.addEventListener('click', (e) => {
            e.preventDefault();
            avatarUpload.click();
        });

        avatarUpload.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    avatarPreview.src = event.target.result;
                    saveAvatarBtn.disabled = false;
                };
                reader.readAsDataURL(file);
            }
        });

        // Password Confirmation
        const settingsForm = document.querySelector('form[action=""][method="POST"]');
        const passwordInput = document.getElementById('passwordInput');
        const confirmPasswordInput = document.getElementById('confirmPasswordInput');

        settingsForm.addEventListener('submit', (e) => {
            if (passwordInput.value !== confirmPasswordInput.value) {
                e.preventDefault();
                alert('Password dan konfirmasi password tidak cocok!');
                return;
            }
            if (passwordInput.value) {
                alert('Pengaturan akun berhasil disimpan!');
            }
        });
    </script>
</body>
</html>