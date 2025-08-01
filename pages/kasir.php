<?php 
include '../layout/header.php'; 
include '../konkon.php';
include '../login/auth_check_karyawan.php';

$result = mysqli_query($kon, "SELECT * FROM data_karyawan ORDER BY idkaryawan DESC");
?>
<style>
    /* buat membatasi lebar kolom nama & alamat di kasir (apa coba) */
    .table td:nth-child(3), /* Nama */
    .table td:nth-child(4)  /* Alamat */ {
        max-width: 180px;
        word-break: break-word;   /* memaksa teks yg panjang buat pindah baris (APA COBA) */
        white-space: pre-line;    /* line break (apa coba)*/
        overflow-wrap: break-word;
    }
</style>
<div class="container">
    <h2 class="mt-4">Data Karyawan</h2>

    <a href="../4x4/tambahkaryawan.php" class="btn btn-primary mb-3">Tambah Karyawan</a>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>ID Karyawan</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Gender</th>
                <th>Agama</th>
                <th>Tanggal Mulai</th>
                <th>Tipe Karyawan</th>
                <th>Bagian</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $tanggal_sekarang = new DateTime('2025-05-31');
            while ($karyawan = mysqli_fetch_assoc($result)) {
                // Convert gender code to text
                $gender = ($karyawan['gender'] == 'L') ? 'Laki-laki' : 'Perempuan';
                
                // Convert agama code to text
                $agama = '';
                switch($karyawan['agama']) {
                    case 1: $agama = 'Islam'; break;
                    case 2: $agama = 'Kristen'; break;
                    case 3: $agama = 'Katolik'; break;
                    case 4: $agama = 'Hindu'; break;
                    case 5: $agama = 'Buddha'; break;
                    default: $agama = 'Lainnya';
                }
                
                // Convert bagian code to text
                $bagian = '';
                switch($karyawan['bagian']) {
                    case 'A': $bagian = 'Admin'; break;
                    case 'B': $bagian = 'Kasir'; break;
                    case 'C': $bagian = 'Pelayan'; break;
                    case 'D': $bagian = 'Dapur'; break;
                    default: $bagian = 'Lainnya';
                }

                // Hitung hari kerja
                $tanggal_mulai = new DateTime($karyawan['tanggal_mulai']);
                $hari_kerja = $tanggal_mulai->diff($tanggal_sekarang)->days;

                // Ganti operator ?? dengan isset()
                $tipe_karyawan = isset($karyawan['tipe_karyawan']) ? $karyawan['tipe_karyawan'] : 'full-time';

                echo "<tr>
                        <td>$no</td>
                        <td>" . htmlspecialchars($karyawan['idKaryawan']) . "</td>
                        <td>" . htmlspecialchars($karyawan['nama']) . "</td>
                        <td>" . htmlspecialchars($karyawan['alamat']) . "</td>
                        <td>" . htmlspecialchars($gender) . "</td>
                        <td>" . htmlspecialchars($agama) . "</td>
                        <td>" . htmlspecialchars($karyawan['tanggal_mulai']) . "</td>
                        <td>" . htmlspecialchars($tipe_karyawan) . "</td>
                        <td>" . htmlspecialchars($bagian) . "</td>
                        <td>
                            <a href='../4x4/editkaryawan.php?id_karyawan=" . htmlspecialchars($karyawan['idKaryawan']) . "' class='btn btn-sm btn-warning'>Edit</a>
                            <a href='../4x4/hapuskaryawan.php?id_karyawan=" . htmlspecialchars($karyawan['idKaryawan']) . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin ingin menghapus?\")'>Hapus</a>
                        </td>
                      </tr>";
                $no++;
            }
            ?>
        </tbody>
    </table>
</div>

<?php include '../layout/footer.php'; ?>