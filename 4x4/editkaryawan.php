<?php 
include '../layout/header.php';
include '../konkon.php';

$id = $_GET['id_karyawan'];

$query = "SELECT * FROM data_karyawan WHERE idKaryawan = '$id'";
$result = mysqli_query($kon, $query);
$data = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $gender = $_POST['gender'];
    $agama = $_POST['agama'];
    $bagian = $_POST['bagian'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tipe_karyawan = $_POST['tipe_karyawan'];

    $queryUpdate = "UPDATE data_karyawan SET 
                    nama = ?, 
                    alamat = ?,
                    gender = ?,
                    agama = ?,
                    bagian = ?,
                    tanggal_mulai = ?,
                    tipe_karyawan = ?
                    WHERE idKaryawan = ?";
    $stmt = $kon->prepare($queryUpdate);
    $stmt->bind_param("ssssssss", $nama, $alamat, $gender, $agama, $bagian, $tanggal_mulai, $tipe_karyawan, $id);
    $update = $stmt->execute();

    if ($update) {
        echo "<script>alert('Data berhasil diupdate!'); window.location='../pages/kasir.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data!');</script>";
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
            <h2 class="mb-4">Edit Data Karyawan</h2>
            
            <form action="" method="post">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input 
                        type="text" 
                        name="nama" 
                        class="form-control" 
                        value="<?= $data['nama'] ?>" 
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <input 
                        type="text" 
                        name="alamat" 
                        class="form-control" 
                        value="<?= $data['alamat'] ?>" 
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-control" required>
                        <option value="L" <?= ($data['gender'] == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= ($data['gender'] == 'P') ? 'selected' : '' ?>>Perempuan</option>
                        <option value="O" <?= ($data['gender'] == 'O') ? 'selected' : '' ?>>Lainnya</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Agama</label>
                    <select name="agama" class="form-control" required>
                        <option value="1" <?= ($data['agama'] == '1') ? 'selected' : '' ?>>Islam</option>
                        <option value="2" <?= ($data['agama'] == '2') ? 'selected' : '' ?>>Kristen</option>
                        <option value="3" <?= ($data['agama'] == '3') ? 'selected' : '' ?>>Katolik</option>
                        <option value="4" <?= ($data['agama'] == '4') ? 'selected' : '' ?>>Hindu</option>
                        <option value="5" <?= ($data['agama'] == '5') ? 'selected' : '' ?>>Buddha</option>
                        <option value="6" <?= ($data['agama'] == '6') ? 'selected' : '' ?>>Lainnya</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input 
                        type="date" 
                        name="tanggal_mulai" 
                        class="form-control" 
                        value="<?= $data['tanggal_mulai'] ?>" 
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipe Karyawan</label>
                    <select name="tipe_karyawan" class="form-control" required>
                        <option value="full-time" <?= ($data['tipe_karyawan'] == 'full-time') ? 'selected' : '' ?>>Full-Time</option>
                        <option value="part-time" <?= ($data['tipe_karyawan'] == 'part-time') ? 'selected' : '' ?>>Part-Time</option>
                        <option value="freelance" <?= ($data['tipe_karyawan'] == 'freelance') ? 'selected' : '' ?>>Freelance</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Bagian</label>
                    <select name="bagian" class="form-control" required>
                        <option value="A" <?= ($data['bagian'] == 'A') ? 'selected' : '' ?>>Admin</option>
                        <option value="B" <?= ($data['bagian'] == 'B') ? 'selected' : '' ?>>Kasir</option>
                        <option value="C" <?= ($data['bagian'] == 'C') ? 'selected' : '' ?>>Pelayan</option>
                        <option value="D" <?= ($data['bagian'] == 'D') ? 'selected' : '' ?>>Dapur</option>
                        <option value="E" <?= ($data['bagian'] == 'E') ? 'selected' : '' ?>>Manager</option>
                    </select>
                </div>

                <button type="submit" name="update" class="btn btn-primary">
                    Update Karyawan
                </button>
                <a href="../pages/kasir.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>