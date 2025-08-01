<?php 
include '../layout/header.php'; 
include '../konkon.php';
include '../login/auth_check_karyawan.php';

$result = mysqli_query($kon, "SELECT * FROM user ORDER BY id_user ASC");
?>

<div class="container">
    <h2 class="mt-4">Data Pelanggan</h2>

    <a href="../4x4/tambahpelanggan.php" class="btn btn-primary mb-3">Tambah Pelanggan</a>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>ID User</th>
                <th>Username</th>
                <th>Password</th>
                <th>Nama User</th>
                <th>Id Level</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($user = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$no}</td>
                        <td>{$user['id_user']}</td>
                        <td>{$user['username']}</td>
                        <td>{$user['password']}</td>
                        <td>{$user['nama_user']}</td>
                        <td>{$user['id_level']}</td>
                        <td>
                            <a href='../4x4/editpelanggan.php?id_user={$user['id_user']}' class='btn btn-sm btn-warning'>Edit</a>
                            <a href='../4x4/hapuspelanggan.php?id_user={$user['id_user']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin ingin menghapus?\")'>Hapus</a>
                        </td>
                      </tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>
</div>

<?php include '../layout/footer.php'; ?>
