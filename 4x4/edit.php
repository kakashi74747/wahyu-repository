<?php 
include '../layout/header.php';
include '../konkon.php';

$id = $_GET['id_menu'];

$query = "SELECT * FROM masakan WHERE id_menu = $id";
$result = mysqli_query($kon, $query);
$data = mysqli_fetch_assoc($result);

if ($_SESSION['bagian'] === 'D') {
    echo "<script>alert('Anda tidak memiliki izin untuk mengedit menu!'); window.location='../pages/menu.php';</script>";
    exit();
}

if (isset($_POST['update'])) {
    $nama = $_POST['nama_masakan'];
    $harga = $_POST['harga'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'] ?? '';

    //upload gambar 
    $gambar = $data['gambar']; //gambar lama sebagai default
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../SSC/img/';
        $file_name = 'food_' . time() . '.' . pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $file_path)) {
            $gambar = $file_name;
            //akan menghapus gambar lama jika ada
            if ($data['gambar'] && file_exists($upload_dir . $data['gambar'])) {
                unlink($upload_dir . $data['gambar']);
            }
        }
    }

    $queryUpdate = "UPDATE masakan SET nama_masakan='$nama', harga='$harga', kategori='$kategori', deskripsi='$deskripsi', gambar='$gambar' WHERE id_menu=$id";
    $update = mysqli_query($kon, $queryUpdate);

    if ($update) {
        echo "<script>alert('YAY! Data berhasil diupdate! :>'); window.location='../pages/menu.php';</script>";
    } else {
        echo "<script>alert('Yahh.. Gagal mengupdate data :<');</script>";
    }
}
?>

<div 
    style="background: url('../SSC/img/bg-edit.jpg') no-repeat center center; background-size: cover; min-height: 100vh; padding-top: 80px;"
>
    <div class="container">
        <div class="card p-4" style="background-color: rgba(255, 255, 255, 0.9);">
            <h2 class="mb-4">Edit Menu</h2>
            
            <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Kategori Menu</label>
                    <select name="kategori" class="form-select" required>
                        <option value="makanan" <?= $data['kategori'] == 'makanan' ? 'selected' : '' ?>>Makanan</option>
                        <option value="minuman" <?= $data['kategori'] == 'minuman' ? 'selected' : '' ?>>Minuman</option>
                        <option value="dessert" <?= $data['kategori'] == 'dessert' ? 'selected' : '' ?>>Dessert</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Nama Menu</label>
                    <input 
                        type="text" 
                        name="nama_masakan" 
                        class="form-control" 
                        value="<?= $data['nama_masakan'] ?>" 
                        required
                    >
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input 
                            type="number" 
                            name="harga" 
                            class="form-control" 
                            value="<?= $data['harga'] ?>"
                            required
                        >
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea 
                        name="deskripsi" 
                        class="form-control" 
                        rows="3"
                    ><?= htmlspecialchars($data['deskripsi']) ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Unggah Gambar Baru</label>
                    <input 
                        type="file" 
                        name="gambar" 
                        class="form-control" 
                        accept="image/*"
                    >
                    <?php if ($data['gambar']): ?>
                        <p>Gambar saat ini: <img src="../SSC/img/<?= htmlspecialchars($data['gambar']) ?>" alt="Gambar saat ini" style="max-width: 100px;"></p>
                    <?php endif; ?>
                </div>
                
                <button type="submit" name="update" class="btn btn-primary">
                    Update Menu
                </button>
                <a href="../pages/menu.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>