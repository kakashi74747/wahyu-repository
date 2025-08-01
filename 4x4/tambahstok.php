<?php
include '../login/auth_check_karyawan.php'; // This must be FIRST
include '../konkon.php';
include '../layout/header.php';

// Cek apakah user sudah login
if (!isset($_SESSION['idKaryawan'])) {
    header("Location: ../login/loginkaryawan.php");
    exit();
}
// Cek akses hanya untuk bagian C (Dapur) dan D (Pelayan)
if ($_SESSION['bagian'] !== 'C' && $_SESSION['bagian'] !== 'D') {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini!');</script>";
    echo "<script>window.location.href='/TugasAkhir/pages/indegs.php';</script>";
    exit();
}

// Proses tambah stok bahan baku
if (isset($_POST['add_stock'])) {
    $id_menu = $_POST['id_menu'];
    $id_bahan = $_POST['id_bahan'];
    $jumlah = $_POST['jumlah'];

    if ($id_bahan && $jumlah > 0) {
        // Cek apakah entri sudah ada
        $check_query = "SELECT * FROM menu_bahan WHERE id_menu = ? AND id_bahan = ?";
        $check_stmt = $kon->prepare($check_query);
        $check_stmt->bind_param("ii", $id_menu, $id_bahan);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            echo "<script>alert('Bahan baku ini sudah ada untuk menu ini! Gunakan edit untuk mengubah jumlah.');</script>";
        } else {
            $insert_query = "INSERT INTO menu_bahan (id_menu, id_bahan, jumlah) VALUES (?, ?, ?)";
            $insert_stmt = $kon->prepare($insert_query);
            $insert_stmt->bind_param("iii", $id_menu, $id_bahan, $jumlah);
            $insert_stmt->execute();
            $insert_stmt->close();
            echo "<script>alert('Bahan baku berhasil ditambahkan!'); window.location.href='/TugasAkhir/pages/stok.php?id_menu=$id_menu';</script>";
            exit();
        }
        $check_stmt->close();
    }
}

// Ambil ID menu dari URL
$id_menu = isset($_GET['id_menu']) ? intval($_GET['id_menu']) : null;
$selected_menu = null;
if ($id_menu) {
    $menu_query = mysqli_query($kon, "SELECT id_menu, nama_masakan FROM masakan WHERE id_menu = $id_menu");
    $selected_menu = mysqli_fetch_assoc($menu_query);
}

if (!$id_menu || !$selected_menu) {
    echo "<script>alert('Menu tidak ditemukan!'); window.location.href='/TugasAkhir/pages/stok.php';</script>";
    exit();
}

// Ambil data bahan baku
$query = mysqli_query($kon, "SELECT * FROM bahan_baku");
if (!$query) {
    die("Query gagal: " . mysqli_error($kon));
}
?>

<div class="container mt-4">
    <h2>Tambah Bahan Baku</h2>
    <h3>Tambah Bahan Baku untuk Menu: <?= htmlspecialchars($selected_menu['nama_masakan']) ?></h3>
    <form method="POST" class="mb-4">
        <input type="hidden" name="id_menu" value="<?= $id_menu ?>">
        <div class="row g-3">
            <div class="col-md-6">
                <select class="form-select" name="id_bahan" required>
                    <option value="">Pilih Bahan Baku</option>
                    <?php
                    while ($bahan = mysqli_fetch_assoc($query)):
                    ?>
                        <option value="<?= $bahan['id_bahan'] ?>"><?= htmlspecialchars($bahan['nama_bahan']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control" name="jumlah" placeholder="Jumlah" min="1" required>
            </div>
            <div class="col-md-2">
                <button type="submit" name="add_stock" class="btn btn-primary">Tambah</button>
            </div>
        </div>
    </form>
    <a href="/TugasAkhir/pages/stok.php?id_menu=<?= $id_menu ?>" class="btn btn-secondary">Kembali</a>
</div>

<?php include '../layout/footer.php'; ?>