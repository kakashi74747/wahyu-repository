<?php
include '../layout/header.php';
include '../konkon.php';

if (isset($_POST['submit'])) {
    $nama = $_POST['nama_karyawan'];
    $alamat = $_POST['alamat'];
    $gender = $_POST['gender'];
    $agama = $_POST['agama'];
    $bagian = $_POST['bagian'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tipe_karyawan = $_POST['tipe_karyawan'];

    // Exclude the primary key column (e.g., id) from the query
    $query = "INSERT INTO data_karyawan (nama, alamat, gender, agama, bagian, tanggal_mulai, tipe_karyawan) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $kon->prepare($query);
    $stmt->bind_param("sssssss", $nama, $alamat, $gender, $agama, $bagian, $tanggal_mulai, $tipe_karyawan);
    $insert = $stmt->execute();

    if ($insert) {
        echo "<script>alert('Data karyawan berhasil ditambahkan!'); window.location='../pages/kasir.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data karyawan!');</script>";
    }
}
?>

<div 
    style="
        background: url('../SSC/img/bg-tambah.jpg') no-repeat center center; 
        background-size: cover; 
        min-height: 100vh; 
        padding-top: 80px;"
>
    <div class="container">
        <div class="card p-4" style="background-color: rgba(255, 255, 255, 0.9);">
            <h2 class="mb-4">Tambah Karyawan Baru</h2>
            
            <form action="" method="post">
                <div class="mb-3">
                    <label class="form-label">Nama Karyawan</label>
                    <input 
                        type="text" 
                        name="nama_karyawan" 
                        class="form-control" 
                        placeholder="Contoh: Koseki Bijou"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <input 
                        type="text" 
                        name="alamat" 
                        class="form-control" 
                        placeholder="Contoh: Jl. Welirang No.19"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <select name="gender" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Gender --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Agama</label>
                    <select name="agama" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Agama --</option>
                        <option value="1">Islam</option>
                        <option value="2">Kristen</option>
                        <option value="3">Katolik</option>
                        <option value="4">Hindu</option>
                        <option value="5">Buddha</option>
                        <option value="6">Lainnya</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input 
                        type="date" 
                        name="tanggal_mulai" 
                        class="form-control" 
                        required
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipe Karyawan</label>
                    <select name="tipe_karyawan" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Tipe --</option>
                        <option value="full-time">Full-Time</option>
                        <option value="part-time">Part-Time</option>
                        <option value="freelance">Freelance</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Bagian</label>
                    <select name="bagian" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Bagian --</option>
                        <option value="A">Admin</option>
                        <option value="B">Kasir</option>
                        <option value="C">Pelayan</option>
                        <option value="D">Dapur</option>
                        <option value="E">Manager</option>
                    </select>
                </div>

                <button type="submit" name="submit" class="btn btn-primary">
                    Simpan Karyawan
                </button>
                <a href="../pages/kasir.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>