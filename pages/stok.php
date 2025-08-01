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

// Proses tambah atau update stok bahan baku untuk menu
if (isset($_POST['update_stock']) && $_SESSION['bagian'] === 'C') { // Hanya Dapur bisa update
    $id_menu = $_POST['id_menu'];
    $id_bahans = $_POST['id_bahan'];
    $jumlahs = $_POST['jumlah'];

    for ($i = 0; $i < count($id_bahans); $i++) {
        $id_bahan = $id_bahans[$i];
        $jumlah = $jumlahs[$i];

        if ($id_bahan && $jumlah > 0) {
            // Cek apakah sudah ada entri untuk kombinasi id_menu dan id_bahan
            $check_query = "SELECT * FROM menu_bahan WHERE id_menu = ? AND id_bahan = ?";
            $check_stmt = $kon->prepare($check_query);
            $check_stmt->bind_param("ii", $id_menu, $id_bahan);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                // Update existing record
                $update_query = "UPDATE menu_bahan SET jumlah = ? WHERE id_menu = ? AND id_bahan = ?";
                $update_stmt = $kon->prepare($update_query);
                $update_stmt->bind_param("iii", $jumlah, $id_menu, $id_bahan);
            } else {
                // Insert new record
                $insert_query = "INSERT INTO menu_bahan (id_menu, id_bahan, jumlah) VALUES (?, ?, ?)";
                $update_stmt = $kon->prepare($insert_query);
                $update_stmt->bind_param("iii", $id_menu, $id_bahan, $jumlah);
            }
            $update_stmt->execute();
            $update_stmt->close();
            $check_stmt->close();
        }
    }
    echo "<script>alert('Stok bahan baku untuk menu berhasil diperbarui!'); window.location.href='/TugasAkhir/pages/stok.php?id_menu=$id_menu';</script>";
    exit();
}

// Ambil data stok bahan baku
$query = mysqli_query($kon, "SELECT * FROM bahan_baku");
if (!$query) {
    die("Query gagal: " . mysqli_error($kon));
}

// Ambil ID menu dari URL jika ada
$id_menu = isset($_GET['id_menu']) ? intval($_GET['id_menu']) : null;
$selected_menu = null;
if ($id_menu) {
    $menu_query = mysqli_query($kon, "SELECT id_menu, nama_masakan FROM masakan WHERE id_menu = $id_menu");
    $selected_menu = mysqli_fetch_assoc($menu_query);
}

// Ambil existing ingredients for the selected menu
$existing_ingredients = [];
if ($id_menu) {
    $ingredients_query = mysqli_query($kon, "SELECT mb.id_bahan, b.nama_bahan, mb.jumlah FROM menu_bahan mb JOIN bahan_baku b ON mb.id_bahan = b.id_bahan WHERE mb.id_menu = $id_menu");
    while ($ingredient = mysqli_fetch_assoc($ingredients_query)) {
        $existing_ingredients[] = $ingredient;
    }
}
?>

<div class="container mt-4">
    <h2>Stok Bahan Baku</h2>
    <?php if ($id_menu && $selected_menu): ?>
        <h3>Kelola Bahan Baku untuk Menu: <?= htmlspecialchars($selected_menu['nama_masakan']) ?></h3>
        <form method="POST" id="stockForm" class="mb-4" <?php if ($_SESSION['bagian'] !== 'C') echo 'disabled'; ?>>
            <input type="hidden" name="id_menu" value="<?= $id_menu ?>">
            <div class="row g-3" id="ingredientRows">
                <?php foreach ($existing_ingredients as $ingredient): ?>
                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <select class="form-select" name="id_bahan[]" required <?php if ($_SESSION['bagian'] !== 'C') echo 'disabled'; ?>>
                                <option value="">Pilih Bahan Baku</option>
                                <?php
                                mysqli_data_seek($query, 0);
                                while ($bahan = mysqli_fetch_assoc($query)):
                                ?>
                                    <option value="<?= $bahan['id_bahan'] ?>" <?= $bahan['id_bahan'] == $ingredient['id_bahan'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($bahan['nama_bahan']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" name="jumlah[]" value="<?= $ingredient['jumlah'] ?>" placeholder="Jumlah" min="1" required <?php if ($_SESSION['bagian'] !== 'C') echo 'disabled'; ?>>
                        </div>
                        <?php if ($_SESSION['bagian'] === 'C'): ?>
                            <div class="col-md-2">
                                <a href="/TugasAkhir/4x4/editstok.php?id_menu=<?= $id_menu ?>&id_bahan=<?= $ingredient['id_bahan'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            </div>
                            <div class="col-md-2">
                                <a href="/TugasAkhir/4x4/hapusstok.php?id_menu=<?= $id_menu ?>&id_bahan=<?= $ingredient['id_bahan'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <select class="form-select" name="id_bahan[]" required <?php if ($_SESSION['bagian'] !== 'C') echo 'disabled'; ?>>
                            <option value="">Pilih Bahan Baku</option>
                            <?php
                            mysqli_data_seek($query, 0);
                            while ($bahan = mysqli_fetch_assoc($query)):
                            ?>
                                <option value="<?= $bahan['id_bahan'] ?>"><?= htmlspecialchars($bahan['nama_bahan']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="jumlah[]" placeholder="Jumlah" min="1" required <?php if ($_SESSION['bagian'] !== 'C') echo 'disabled'; ?>>
                    </div>
                    <?php if ($_SESSION['bagian'] === 'C'): ?>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-secondary" id="addIngredient">Tambah Bahan</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($_SESSION['bagian'] === 'C'): ?>
                <button type="submit" name="update_stock" class="btn btn-primary mt-3">Simpan Semua</button>
            <?php endif; ?>
        </form>
        <?php if ($_SESSION['bagian'] === 'C'): ?>
            <a href="/TugasAkhir/4x4/tambahstok.php?id_menu=<?= $id_menu ?>" class="btn btn-success mb-3">Tambah Bahan Baru</a>
        <?php endif; ?>
    <?php else: ?>
        <h3>Daftar Stok Bahan Baku</h3>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>ID Bahan</th>
                <th>Nama Bahan</th>
                <th>Satuan</th>
                <th>Permintaan</th>
                <th>Bahan Masuk</th>
                <th>Bahan Terpakai</th>
                <th>Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            <?php
            mysqli_data_seek($query, 0); // Reset pointer
            while ($row = mysqli_fetch_assoc($query)):
                // Hitung stok akhir
                $stok_akhir = ($row['bahan_masuk'] + $row['permintaan']) - $row['bahan_terpakai'];
                // Periksa kebutuhan bahan berdasarkan menu
                $usage_query = "SELECT SUM(jumlah) as total_used FROM menu_bahan mb JOIN masakan m ON mb.id_menu = m.id_menu WHERE mb.id_bahan = ? AND m.status_masakan = 'tersedia'";
                $usage_stmt = $kon->prepare($usage_query);
                $usage_stmt->bind_param("i", $row['id_bahan']);
                $usage_stmt->execute();
                $usage_result = $usage_stmt->get_result();
                $total_used = $usage_result->fetch_assoc()['total_used'] ?? 0;
                $usage_stmt->close();
            ?>
            <tr>
                <td><?= $row['id_bahan'] ?></td>
                <td><?= htmlspecialchars($row['nama_bahan']) ?></td>
                <td><?= htmlspecialchars($row['satuan']) ?></td>
                <td><?= $row['permintaan'] ?></td>
                <td><?= $row['bahan_masuk'] ?></td>
                <td><?= $total_used ?></td>
                <td><?= $stok_akhir ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('addIngredient').addEventListener('click', function() {
    const row = document.createElement('div');
    row.className = 'row g-3 mt-2';
    row.innerHTML = `
        <div class="col-md-4">
            <select class="form-select" name="id_bahan[]" required <?php if ($_SESSION['bagian'] !== 'C') echo 'disabled'; ?>>
                <option value="">Pilih Bahan Baku</option>
                <?php
                mysqli_data_seek($query, 0);
                while ($bahan = mysqli_fetch_assoc($query)):
                ?>
                    <option value="<?= $bahan['id_bahan'] ?>"><?= htmlspecialchars($bahan['nama_bahan']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-4">
            <input type="number" class="form-control" name="jumlah[]" placeholder="Jumlah" min="1" required <?php if ($_SESSION['bagian'] !== 'C') echo 'disabled'; ?>>
        </div>
        <?php if ($_SESSION['bagian'] === 'C'): ?>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger removeIngredient">Hapus</button>
            </div>
        <?php endif; ?>
    `;
    document.getElementById('ingredientRows').appendChild(row);

    // Add event listener to remove button
    row.querySelector('.removeIngredient')?.addEventListener('click', function() {
        row.remove();
    });
});
</script>

<?php include '../layout/footer.php'; ?>