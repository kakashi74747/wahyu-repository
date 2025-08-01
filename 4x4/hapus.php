<?php
include '../layout/header.php';
include '../konkon.php';

$id = $_GET['id_menu'];

if ($_SESSION['bagian'] === 'D') {
    echo "<script>alert('Anda tidak memiliki izin untuk menghapus menu!'); window.location='../pages/menu.php';</script>";
    exit();
}

if (isset($_POST['hapus'])) {
    $query = "DELETE FROM masakan WHERE id_menu = $id";
    $del = mysqli_query($kon, $query);

    if ($del) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='../pages/menu.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!');</script>";
    }
    exit();
}
?>

<div 
    style="background: url('../SSC/img/bg-hapus.jpg') no-repeat center center; background-size: cover; min-height: 100vh; padding-top: 80px;"
>
    <div class="container">
        <div class="card p-4 text-center" style="background-color: rgba(255, 255, 255, 0.9);">
            <h2 class="mb-3">Konfirmasi Hapus</h2>
            <p>Apakah Anda yakin ingin menghapus data ini?</p>
            
            <form method="post">
                <button type="submit" name="hapus" class="btn btn-danger">Ya, Hapus</button>
                <a href="../pages/menu.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>