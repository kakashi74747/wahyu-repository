<?php
include '../layout/header.php';
include '../konkon.php';

// Ambil data user untuk dropdown
$userQuery = mysqli_query($kon, "SELECT * FROM user ORDER BY nama_user ASC");

// Ambil data masakan untuk dropdown
$menuQuery = mysqli_query($kon, "SELECT * FROM masakan ORDER BY nama_masakan ASC");

if (isset($_POST['simpan'])) {
    $id_user = $_POST['id_user'];
    $tanggal = $_POST['tanggal'];
    $items = $_POST['items']; // Array berisi id_menu dan jumlah

    // Validasi
    if (empty($id_user) || empty($tanggal) || empty($items)) {
        echo "<script>alert('Semua field harus diisi!');</script>";
    } else {
        // Mulai transaksi database
        mysqli_begin_transaction($kon);

        try {
            // Insert ke tabel transaksi
            $insertTransaksi = mysqli_query($kon, "INSERT INTO transaksi (id_user, tanggal) VALUES ('$id_user', '$tanggal')");
            $id_transaksi = mysqli_insert_id($kon);

            // Insert setiap item ke tabel transaksi_detail
            foreach ($items as $item) {
                $id_menu = $item['id_menu'];
                $jumlah = $item['jumlah'];
                if (!empty($id_menu) && !empty($jumlah)) {
                    $insertDetail = mysqli_query($kon, "INSERT INTO transaksi_detail (id_transaksi, id_menu, jumlah) 
                                                        VALUES ('$id_transaksi', '$id_menu', '$jumlah')");
                    if (!$insertDetail) {
                        throw new Exception("Gagal menambahkan detail transaksi!");
                    }
                }
            }

            // Commit transaksi
            mysqli_commit($kon);
            echo "<script>alert('Transaksi berhasil ditambahkan!'); window.location='../pages/transaksi.php';</script>";
        } catch (Exception $e) {
            // Rollback jika ada error
            mysqli_rollback($kon);
            echo "<script>alert('Gagal menambahkan transaksi: " . $e->getMessage() . "');</script>";
        }
    }
}
?>

<div class="container mt-4">
    <div class="card p-4">
        <h3 class="mb-4">Tambah Transaksi</h3>
        <form action="" method="post">
            <div class="mb-3">
                <label for="id_user" class="form-label">Nama User</label>
                <select name="id_user" class="form-select" required>
                    <option value="">-- Pilih User --</option>
                    <?php while ($user = mysqli_fetch_assoc($userQuery)) { ?>
                        <option value="<?= $user['id_user'] ?>"><?= $user['nama_user'] ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Items</label>
                <div id="item-list">
                    <div class="item-row mb-3 d-flex gap-3 align-items-end">
                        <div class="flex-grow-1">
                            <label class="form-label">Nama Menu</label>
                            <select name="items[0][id_menu]" class="form-select" required>
                                <option value="">-- Pilih Menu --</option>
                                <?php 
                                mysqli_data_seek($menuQuery, 0); // Reset pointer
                                while ($menu = mysqli_fetch_assoc($menuQuery)) { ?>
                                    <option value="<?= $menu['id_menu'] ?>"><?= $menu['nama_masakan'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div style="width: 120px;">
                            <label class="form-label">Jumlah</label>
                            <input type="number" name="items[0][jumlah]" class="form-control" min="1" required>
                        </div>
                        <button type="button" class="btn btn-danger btn-sm remove-item" style="display: none;">Hapus</button>
                    </div>
                </div>
                <button type="button" id="add-item" class="btn btn-primary btn-sm">Tambah Item</button>
            </div>

            <button type="submit" name="simpan" class="btn btn-success">Simpan Transaksi</button>
            <a href="../pages/transaksi.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<script>
let itemCount = 1;

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
                mysqli_data_seek($menuQuery, 0); // Reset pointer
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

    // Update visibility of remove buttons
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
</script>

<?php include '../layout/footer.php'; ?>