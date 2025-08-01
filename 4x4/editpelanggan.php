<?php 
include '../layout/header.php';
include '../konkon.php';

$id = $_GET['id_user'];

$query = "SELECT * FROM user WHERE id_user = '$id'";
$result = mysqli_query($kon, $query);
$data = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $username   = $_POST['username'];
    $password   = $_POST['password'];
    $nama_user  = $_POST['nama_user'];
    $id_level   = $_POST['id_level'];

    $queryUpdate = "UPDATE user SET 
                    username = '$username', 
                    password = '$password', 
                    nama_user = '$nama_user',
                    id_level = '$id_level'
                    WHERE id_user = '$id'";
    $update = mysqli_query($kon, $queryUpdate);

    if ($update) {
        echo "<script>alert('Data pelanggan berhasil diupdate!'); window.location='../pages/pelanggan.php';</script>";
    } else {
        echo "<script>alert('Gagal update data pelanggan!');</script>";
    }
}
?>

<div 
    style="
        background: url('../SSC/img/bg-edit.jpg') no-repeat center center; 
        background-size: cover; 
        min-height: 100vh; 
        padding-top: 80px;"
>
    <div class="container">
        <div class="card p-4" style="background-color: rgba(255, 255, 255, 0.9);">
            <h2 class="mb-4">Edit Data Pelanggan</h2>
            
            <form action="" method="post">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input 
                        type="text" 
                        name="username" 
                        class="form-control" 
                        value="<?= $data['username'] ?>" 
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input 
                        type="text" 
                        name="password" 
                        class="form-control" 
                        value="<?= $data['password'] ?>" 
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input 
                        type="text" 
                        name="nama_user" 
                        class="form-control" 
                        value="<?= $data['nama_user'] ?>" 
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">ID Level</label>
                    <input 
                        type="number" 
                        name="id_level" 
                        class="form-control" 
                        value="<?= $data['id_level'] ?>" 
                        required
                    >
                </div>

                <button type="submit" name="update" class="btn btn-primary">
                    Update Pelanggan
                </button>
                <a href="../pages/pelanggan.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>
