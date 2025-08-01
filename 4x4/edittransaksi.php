<?php 
include '../layout/header.php';
include '../konkon.php';

// Validasi ID transaksi
if (!isset($_GET['id_transaksi'])) {
    die("ID Transaksi tidak valid");
}

$id = $_GET['id_transaksi'];

// Ambil data transaksi
$stmt = $kon->prepare("
    SELECT t.*, u.nama_user 
    FROM transaksi t
    LEFT JOIN user u ON t.id_user = u.id_user
    WHERE t.id_transaksi = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    die("Transaksi tidak ditemukan");
}

// Ambil detail items
$detailQuery = "
    SELECT td.*, m.nama_masakan, m.harga 
    FROM transaksi_detail td
    LEFT JOIN masakan m ON td.id_menu = m.id_menu
    WHERE td.id_transaksi = $id
";
$detailResult = mysqli_query($kon, $detailQuery);
$items = [];
while ($detail = mysqli_fetch_assoc($detailResult)) {
    $items[] = $detail;
}

// Ambil data untuk dropdown
$userQuery = mysqli_query($kon, "SELECT * FROM user ORDER BY nama_user ASC");
$menuQuery = mysqli_query($kon, "SELECT * FROM masakan ORDER BY nama_masakan ASC");

if (isset($_POST['update'])) {
    $id_user = $_POST['id_user'];
    $tanggal = $_POST['tanggal'];
    $newItems = $_POST['items'];

    // Validasi
    if (empty($id_user) || empty($tanggal) || empty($newItems)) {
        echo "<script>alert('Semua field harus diisi!');</script>";
    } else {
        // Mulai transaksi database
        mysqli_begin_transaction($kon);

        try {
            // Update tabel transaksi
            $stmt = $kon->prepare("UPDATE transaksi SET id_user = ?, tanggal = ? WHERE id_transaksi = ?");
            $stmt->bind_param("isi", $id_user, $tanggal, $id);
            $stmt->execute();
            $stmt->close();

            // Hapus detail lama
            mysqli_query($kon, "DELETE FROM transaksi_detail WHERE id_transaksi = $id");

            // Insert detail baru
            foreach ($newItems as $item) {
                $id_menu = $item['id_menu'];
                $jumlah = $item['jumlah'];
                if (!empty($id_menu) && !empty($jumlah)) {
                    $insertDetail = mysqli_query($kon, "INSERT INTO transaksi_detail (id_transaksi, id_menu, jumlah) 
                                                        VALUES ('$id', '$id_menu', '$jumlah')");
                    if (!$insertDetail) {
                        throw new Exception("Gagal menambahkan detail transaksi!");
                    }
                }
            }

            // Commit transaksi
            mysqli_commit($kon);
            echo "<script>alert('Data transaksi berhasil diupdate!'); window.location='../pages/transaksi.php';</script>";
        } catch (Exception $e) {
            // Rollback jika ada error
            mysqli_rollback($kon);
            echo "<script>alert('Gagal update data transaksi: " . $e->getMessage() . "');</script>";
        }
    }
}
?>

<div style="
    background: url('../SSC/img/bg-edit.jpg') no-repeat center center; 
    background-size: cover; 
    min-height: 100vh; 
    padding-top: 80px;">
    <div class="container">
        <div class="card p-4" style="background-color: rgba(255, 255, 255, 0.9);">
            <h2 class="mb-4">Edit Data Transaksi</h2>
            
            <form action="" method="post">
                <div class="mb-3">
                    <label class="form-label">Nama User</label>
                    <select name="id_user" class="form-select" required>
                        <option value="">-- Pilih User --</option>
                        <?php while ($user = mysqli_fetch_assoc($userQuery)) { ?>
                            <option value="<?= $user['id_user'] ?>" <?= $data['id_user'] == $user['id_user'] ? 'selected' : '' ?>>
                                <?= $user['nama_user'] ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($data['tanggal']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Items</label>
                    <div id="item-list">
                        <?php foreach ($items as $index => $item) { ?>
                            <div class="item-row mb-3 d-flex gap-3 align-items-end">
                                <div class="flex-grow-1">
                                    <label class="form-label">Nama Menu</label>
                                    <select name="items[<?= $index ?>][id_menu]" class="form-select" required>
                                        <option value="">-- Pilih Menu --</option>
                                        <?php 
                                        mysqli_data_seek($menuQuery, 0);
                                        while ($menu = mysqli_fetch_assoc($menuQuery)) { ?>
                                            <option value="<?= $menu['id_menu'] ?>" <?= $item['id_menu'] == $menu['id_menu'] ? 'selected' : '' ?>>
                                                <?= $menu['nama_masakan'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div style="width: 120px;">
                                    <label class="form-label">Jumlah</label>
                                    <input type="number" name="items[<?= $index ?>][jumlah]" class="form-control" value="<?= $item['jumlah'] ?>" min="1" required>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm remove-item" style="display: none;">Hapus</button>
                            </div>
                        <?php } ?>
                    </div>
                    <button type="button" id="add-item" class="btn btn-primary btn-sm">Tambah Item</button>
                </div>

                <button type="submit" name="update" class="btn btn-primary">Update Transaksi</button>
                <a href="../pages/transaksi.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<script>
let itemCount = <?= count($items) ?>;

document.getElementById('add-item').addEventListener('click', function() {
    const itemList = document.getElementById('item-list');
    const newItem = document.createElement('div');
    newItem.classList.add('item-row', 'mb-3', 'd-flex', 'gap-3', 'align-items-end');
    newItem.innerHTML = `
        <div class="flex-grow-1">
            <label class="form-label">Nama Menu</label>
            <select name="items[${itemCount}][id_menu]" class="form-select" required>
                <option value="">-- Pilih Menu --</option>
                <?php 
                mysqli_data_seek($menuQuery, 0);
                while ($menu = mysqli_fetch_assoc($menuQuery)) { ?>
                    <option value="<?= $menu['id_menu'] ?>"><?= $menu['nama_masakan'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div style="width: 120px;">
            <label class="form-label">Jumlah</label>
            <input type="number" name="items[${itemCount}][jumlah]" class="form-control" min="1" required>
        </div>
        <button type="button" class="btn btn-danger btn-sm remove-item">Hapus</button>
    `;
    itemList.appendChild(newItem);
    itemCount++;

    updateRemoveButtons();
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item')) {
        e.target.parentElement.remove();
        itemCount--;
        updateRemoveButtons();
    }
});

function updateRemoveButtons() {
    const removeButtons = document.querySelectorAll('.remove-item');
    if (removeButtons.length <= 1) {
        removeButtons.forEach(btn => btn.style.display = 'none');
    } else {
        removeButtons.forEach(btn => btn.style.display = 'inline-block');
    }
}

updateRemoveButtons();
</script>

<?php include '../layout/footer.php'; ?>