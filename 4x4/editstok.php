<?php
include '../layout/header.php';
include '../konkon.php';
include '../login/auth_check_karyawan.php';

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

// Proses update stok bahan baku
if (isset($_POST['update_stock'])) {
    $id_menu = $_POST['id_menu'];
    $id_bahan = $_POST['id_bahan'];
    $jumlah = $_POST['jumlah'];

    if ($id_bahan && $jumlah > 0) {
        $update_query = "UPDATE menu_bahan SET jumlah = ? WHERE id_menu = ? AND id_bahan = ?";
        $update_stmt = $kon->prepare($update_query);
        $update_stmt->bind_param("iii", $jumlah, $id_menu, $id_bahan);
        $update_stmt->execute();
        $update_stmt->close();

        echo "<script>alert('Bahan baku berhasil diperbarui!'); window.location.href='/TugasAkhir/pages/stok.php?id_menu=$id_menu';</script>";
        exit();
    }
}

// Ambil ID menu dan ID bahan dari URL
$id_menu = isset($_GET['id_menu']) ? intval($_GET['id_menu']) : null;
$id_bahan = isset($_GET['id_bahan']) ? intval($_GET['id_bahan']) : null;

if (!$id_menu || !$id_bahan) {
    echo "<script>alert('Parameter tidak lengkap!'); window.location.href='/TugasAkhir/pages/stok.php';</script>";
    exit();
}

$selected_menu = mysqli_fetch_assoc(mysqli_query($kon, "SELECT id_menu, nama_masakan FROM masakan WHERE id_menu = $id_menu"));
$ingredient = mysqli_fetch_assoc(mysqli_query($kon, "SELECT mb.jumlah, b.nama_bahan FROM menu_bahan mb JOIN bahan_baku b ON mb.id_bahan = b.id_bahan WHERE mb.id_menu = $id_menu AND mb.id_bahan = $id_bahan"));

if (!$selected_menu || !$ingredient) {
    echo "<script>alert('Data tidak ditemukan!'); window.location.href='/TugasAkhir/pages/stok.php';</script>";
    exit();
}
?>

<div class="container mt-4">
    <h2>Edit Bahan Baku</h2>
    <h3>Edit Bahan Baku untuk Menu: <?= htmlspecialchars($selected_menu['nama_masakan']) ?> - <?= htmlspecialchars($ingredient['nama_bahan']) ?></h3>
    <form method="POST" class="mb-4">
        <input type="hidden" name="id_menu" value="<?= $id_menu ?>">
        <input type="hidden" name="id_bahan" value="<?= $id_bahan ?>">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" class="form-control" value="<?= htmlspecialchars($ingredient['nama_bahan']) ?>" readonly>
            </div>
            <div class="col-md-4">
                <input type="number" class="form-control" name="jumlah" value="<?= $ingredient['jumlah'] ?>" placeholder="Jumlah" min="1" required>
            </div>
            <div class="col-md-2">
                <button type="submit" name="update_stock" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </form>
    <a href="/TugasAkhir/pages/stok.php?id_menu=<?= $id_menu ?>" class="btn btn-secondary">Kembali</a>
</div>

<?php include '../layout/footer.php'; ?>