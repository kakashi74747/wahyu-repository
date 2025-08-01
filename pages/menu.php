<?php  
include '../konkon.php';
include '../login/auth_check_karyawan.php';

// Cek koneksi
if (!$kon) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Proses toggle Featured
if (isset($_GET['toggle_featured']) && $_SESSION['bagian'] !== 'D') { // Hanya non-pelayan yang bisa toggle
    $id_menu = $_GET['toggle_featured'];
    $current_status = mysqli_fetch_assoc(mysqli_query($kon, "SELECT is_featured FROM masakan WHERE id_menu = '$id_menu'"))['is_featured'];
    $new_status = $current_status ? 0 : 1;
    mysqli_query($kon, "UPDATE masakan SET is_featured = $new_status WHERE id_menu = '$id_menu'") or die(mysqli_error($kon));
    header("Location: /TugasAkhir/pages/menu.php?tab=" . ($_GET['tab'] ?? 'minuman'));
    exit();
}

// Proses toggle Special (hanya untuk admin)
if (isset($_GET['toggle_special']) && $_SESSION['bagian'] === 'A') {
    $id_menu = $_GET['toggle_special'];
    $current_status = mysqli_fetch_assoc(mysqli_query($kon, "SELECT is_special FROM masakan WHERE id_menu = '$id_menu'"))['is_special'];
    $new_status = $current_status ? 0 : 1;
    mysqli_query($kon, "UPDATE masakan SET is_special = 0 WHERE is_special = 1") or die(mysqli_error($kon));
    mysqli_query($kon, "UPDATE masakan SET is_special = $new_status WHERE id_menu = '$id_menu'") or die(mysqli_error($kon));
    header("Location: /TugasAkhir/pages/menu.php?tab=" . ($_GET['tab'] ?? 'minuman'));
    exit();
}

include '../layout/header.php';

// Ambil tab aktif (minuman|makanan|dessert)
$tab_aktif = $_GET['tab'] ?? 'minuman';

// Ambil data
$minuman = mysqli_query($kon, "SELECT * FROM masakan WHERE kategori = 'minuman' ORDER BY id_menu ASC") or die(mysqli_error($kon));
$makanan = mysqli_query($kon, "SELECT * FROM masakan WHERE kategori = 'makanan' ORDER BY id_menu ASC") or die(mysqli_error($kon));
$dessert = mysqli_query($kon, "SELECT * FROM masakan WHERE kategori = 'dessert' ORDER BY id_menu ASC") or die(mysqli_error($kon));

// Pilih data sesuai tab
switch($tab_aktif) {
    case 'makanan': $data_menu = $makanan; break;
    case 'dessert': $data_menu = $dessert; break;
    default:        $data_menu = $minuman; 
}
?>

<link rel="stylesheet" href="../SSC/cssmaxxing.css">

<div class="container">
    <!-- Tab nav -->
    <ul class="nav nav-tabs mt-3">
        <?php 
        $tabs = ['minuman'=>'Minuman','makanan'=>'Makanan','dessert'=>'Dessert'];
        foreach($tabs as $key=>$label): ?>
        <li class="nav-item">
            <a 
              class="nav-link <?= $tab_aktif === $key ? 'active' : '' ?>" 
              href="/TugasAkhir/pages/menu.php?tab=<?= $key ?>"
            >
              <?= $label ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>

    <!-- Judul & tombol tambah -->
    <h2 class="mt-4">Daftar <?= ucfirst($tab_aktif) ?></h2>
    <div class="d-flex justify-content-between mb-3">
        <?php if ($_SESSION['bagian'] === 'A' || $_SESSION['bagian'] === 'C'): // Hanya Admin dan Dapur bisa tambah ?>
            <a href="../4x4/tambah.php?kategori=<?= $tab_aktif ?>" 
               class="btn btn-primary">
               Tambah <?= ucfirst($tab_aktif) ?>
            </a>
        <?php endif; ?>
        <!-- Search Bar -->
        <div class="input-group" style="width: 300px;">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari menu...">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
        </div>
    </div>

    <!-- Tabel -->
    <?php include 'tabel_menu.php'; ?>
</div>

<!-- Tambahkan ikon Bootstrap (jika belum ada) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<script>
// Simpan data asli tabel saat halaman dimuat
const tableBody = document.querySelector('#menuTable tbody');
const originalRows = tableBody.innerHTML;

document.getElementById('searchInput').addEventListener('input', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('#menuTable tbody tr:not(.no-data)');

    // Jika input kosong, kembalikan data asli
    if (searchValue === '') {
        tableBody.innerHTML = originalRows;
        return;
    }

    let hasVisibleRows = false;
    rows.forEach(row => {
        const namaMasakan = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        if (namaMasakan.includes(searchValue)) {
            row.style.display = '';
            hasVisibleRows = true;
        } else {
            row.style.display = 'none';
        }
    });

    // Tampilkan pesan jika tidak ada hasil dan input tidak kosong
    const noDataRow = tableBody.querySelector('tr.no-data');
    if (!hasVisibleRows) {
        if (!noDataRow) {
            tableBody.innerHTML = '<tr class="no-data"><td colspan="7" class="text-center">Tidak ada data yang cocok</td></tr>';
        }
    } else if (noDataRow) {
        noDataRow.remove();
        // Pulihkan baris yang terlihat setelah menghapus pesan "no-data"
        rows.forEach(row => {
            const namaMasakan = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            row.style.display = namaMasakan.includes(searchValue) ? '' : 'none';
        });
    }
});
</script>

<?php include '../layout/footer.php'; ?>