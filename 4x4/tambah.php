<?php
include '../layout/header.php';
include '../konkon.php';

// Ambil kategori dari URL
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : 'makanan';

if (isset($_POST['submit'])) {
    $nama = $_POST['nama_masakan'];
    $harga = $_POST['harga'];
    $kategori = $_POST['kategori'];
    $deskripsi = $_POST['deskripsi'] ?? '';

    // Proses unggah gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../SSC/img/';
        $file_name = 'food_' . time() . '.' . pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $file_path)) {
            $gambar = $file_name;
        }
    }

    $query = "INSERT INTO masakan (nama_masakan, harga, kategori, deskripsi, gambar) VALUES ('$nama', '$harga', '$kategori', '$deskripsi', '$gambar')";
    $insert = mysqli_query($kon, $query);

    if ($insert) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location='../pages/menu.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data!');</script>";
    }
}
?>
<div 
    style="background: url('../SSC/img/bg-tambah.jpg') no-repeat center center; background-size: cover; min-height: 100vh; padding-top: 80px;"
>
    <div class="container">
        <div class="card p-4" style="background-color: rgba(255, 255, 255, 0.9);">
            <h2 class="mb-4">Tambah Menu Baru</h2>
            
            <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Kategori Menu</label>
                    <select name="kategori" class="form-select" required>
                        <option value="makanan" <?= $kategori == 'makanan' ? 'selected' : '' ?>>Makanan</option>
                        <option value="minuman" <?= $kategori == 'minuman' ? 'selected' : '' ?>>Minuman</option>
                        <option value="dessert" <?= $kategori == 'dessert' ? 'selected' : '' ?>>Dessert</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Nama Menu</label>
                    <input 
                        type="text" 
                        name="nama_masakan" 
                        class="form-control" 
                        placeholder="Contoh: Wagyu Rendang A5"
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
                            placeholder="Contoh: 25000"
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
                        placeholder="Masukkan deskripsi menu (opsional)"
                    ></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Unggah Gambar</label>
                    <input 
                        type="file" 
                        name="gambar" 
                        class="form-control" 
                        accept="image/*"
                    >
                </div>
                
                <button type="submit" name="submit" class="btn btn-primary">
                    Simpan Menu
                </button>
                <a href="../pages/menu.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>