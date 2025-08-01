<?php
include '../konkon.php';
include '../layout/header.php';
include '../login/auth_check_karyawan.php';

// Tab aktif (default ke transaksi)
$tab = $_GET['tab'] ?? 'transaksi';

// --- Laporan Transaksi (periode: harian, mingguan, bulanan, tahunan) ---
$periode = $_GET['periode'] ?? 'harian'; // default ke 'harian'
$tahun = $_GET['tahun'] ?? date('Y'); // default ke tahun ini

$whereTransaksi = '';
switch ($periode) {
    case 'harian':
        $whereTransaksi = "WHERE DATE(t.tanggal) = CURDATE()";
        break;
    case 'mingguan':
        $whereTransaksi = "WHERE YEARWEEK(t.tanggal, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'bulanan':
        $whereTransaksi = "WHERE MONTH(t.tanggal) = MONTH(CURDATE()) AND YEAR(t.tanggal) = YEAR(CURDATE())";
        break;
    case 'tahunan':
        $whereTransaksi = "WHERE YEAR(t.tanggal) = '$tahun'";
        break;
}

// --- Laporan Pesanan (menggunakan tabel transaksi, filter tanggal spesifik atau rentang) ---
$tanggalPesanan = $_GET['tanggal_pesanan'] ?? '';
$tanggalMulai = $_GET['tanggal_mulai'] ?? '';
$tanggalSelesai = $_GET['tanggal_selesai'] ?? '';

// Pastikan $wherePesanan selalu didefinisikan
$wherePesanan = '';
if (!empty($tanggalPesanan)) {
    // Filter tanggal spesifik
    $wherePesanan = "WHERE DATE(t.tanggal) = '$tanggalPesanan'";
} elseif (!empty($tanggalMulai) && !empty($tanggalSelesai)) {
    // Filter rentang tanggal
    $wherePesanan = "WHERE DATE(t.tanggal) BETWEEN '$tanggalMulai' AND '$tanggalSelesai'";
}

// Query untuk Laporan Transaksi
$queryTransaksi = "
    SELECT 
        t.id_transaksi,
        t.tanggal,
        u.nama_user,
        GROUP_CONCAT(CONCAT(m.nama_masakan, ' (x', td.jumlah, ')') SEPARATOR ', ') AS items,
        SUM(td.jumlah * m.harga) AS total_before_discount,
        u.id_level
    FROM transaksi t
    JOIN transaksi_detail td ON t.id_transaksi = td.id_transaksi
    JOIN masakan m ON td.id_menu = m.id_menu
    LEFT JOIN user u ON t.id_user = u.id_user
    $whereTransaksi
    GROUP BY t.id_transaksi, t.tanggal, u.nama_user, u.id_level
    ORDER BY t.tanggal DESC
";
$resultTransaksi = mysqli_query($kon, $queryTransaksi);

// Query untuk Laporan Pesanan
$queryPesanan = "
    SELECT 
        t.id_transaksi,
        t.tanggal,
        u.nama_user,
        GROUP_CONCAT(CONCAT(m.nama_masakan, ' (x', td.jumlah, ')') SEPARATOR ', ') AS items,
        t.id_level
    FROM transaksi t
    LEFT JOIN user u ON t.id_user = u.id_user
    JOIN transaksi_detail td ON t.id_transaksi = td.id_transaksi
    JOIN masakan m ON td.id_menu = m.id_menu
    $wherePesanan
    GROUP BY t.id_transaksi
    ORDER BY t.tanggal DESC
";
$resultPesanan = mysqli_query($kon, $queryPesanan);
?>

<style>
    .table {
        table-layout: fixed;
        width: 100%;
    }
    .table th, .table td {
        vertical-align: middle;
        padding: 8px;
    }
    /* Styling untuk Laporan Transaksi */
    .table-transaksi th:nth-child(1), .table-transaksi td:nth-child(1) { /* ID Transaksi */
        width: 15%;
    }
    .table-transaksi th:nth-child(2), .table-transaksi td:nth-child(2) { /* Tanggal */
        width: 20%;
    }
    .table-transaksi th:nth-child(3), .table-transaksi td:nth-child(3) { /* Nama User */
        width: 20%;
    }
    .table-transaksi th:nth-child(4), .table-transaksi td:nth-child(4) { /* Items */
        width: 25%;
        word-break: break-word;
        white-space: normal;
    }
    .table-transaksi th:nth-child(5), .table-transaksi td:nth-child(5) { /* Total Sebelum Diskon */
        width: 10%;
    }
    .table-transaksi th:nth-child(6), .table-transaksi td:nth-child(6) { /* Diskon */
        width: 10%;
    }
    /* Styling untuk Laporan Pesanan */
    .table-pesanan th:nth-child(1), .table-pesanan td:nth-child(1) { /* ID Transaksi */
        width: 15%;
    }
    .table-pesanan th:nth-child(2), .table-pesanan td:nth-child(2) { /* Tanggal */
        width: 25%;
    }
    .table-pesanan th:nth-child(3), .table-pesanan td:nth-child(3) { /* Nama Pelanggan */
        width: 30%;
        word-break: break-word;
        white-space: normal;
    }
    .table-pesanan th:nth-child(4), .table-pesanan td:nth-child(4) { /* Items */
        width: 30%;
        word-break: break-word;
        white-space: normal;
    }
</style>

<div class="container mt-4">
    <!-- Nav Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?= $tab == 'transaksi' ? 'active' : '' ?>" href="?tab=transaksi">Laporan Transaksi</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tab == 'pesanan' ? 'active' : '' ?>" href="?tab=pesanan">Laporan Pesanan</a>
        </li>
    </ul>

    <!-- Tab Content -->
    <?php if ($tab == 'transaksi'): ?>
        <!-- Laporan Transaksi -->
        <form method="GET" class="mb-3">
            <input type="hidden" name="tab" value="transaksi">
            <label for="periode">Pilih Periode:</label>
            <select name="periode" id="periode" class="form-select d-inline w-auto mx-2">
                <option value="harian" <?= $periode == 'harian' ? 'selected' : '' ?>>Harian</option>
                <option value="mingguan" <?= $periode == 'mingguan' ? 'selected' : '' ?>>Mingguan</option>
                <option value="bulanan" <?= $periode == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
                <option value="tahunan" <?= $periode == 'tahunan' ? 'selected' : '' ?>>Tahunan</option>
            </select>

            <input type="number" name="tahun" placeholder="Misal: 2024" value="<?= $tahun ?>" 
                class="form-control d-inline w-auto mx-2" 
                <?= $periode == 'tahunan' ? '' : 'disabled' ?>>

            <button type="submit" class="btn btn-primary">Tampilkan</button>
        </form>

        <script>
        document.getElementById('periode').addEventListener('change', function() {
            const tahunInput = document.querySelector('input[name="tahun"]');
            if (this.value === 'tahunan') {
                tahunInput.removeAttribute('disabled');
            } else {
                tahunInput.setAttribute('disabled', 'disabled');
            }
        });
        </script>

        <h2>Laporan Transaksi</h2>
        <table class="table table-bordered mt-3 table-transaksi">
            <thead class="table-light">
                <tr>
                    <th>ID Transaksi</th>
                    <th>Tanggal</th>
                    <th>Nama User</th>
                    <th>Items</th>
                    <th>Total Sebelum Diskon</th>
                    <th>Diskon</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $perks = [
                    3 => ['name' => 'Blessing of Light', 'discount' => 5, 'items' => []],
                    5 => ['name' => 'Gift from the Moon', 'discount' => 7, 'items' => ['Coupons']],
                    10 => ['name' => 'Twilight Perk', 'discount' => 12, 'items' => ['Free Castorice Merch']],
                    15 => ['name' => 'Eternal Reverie Perks', 'discount' => 20, 'items' => ['Free Castorice Plushie', 'Free Castorice Merch', 'Starlight Hall Engraving']]
                ];
                while ($row = mysqli_fetch_assoc($resultTransaksi)) {
                    $user_level = $row['id_level'];
                    $user_perks = ['name' => 'No Perks', 'discount' => 0, 'items' => []];
                    $perk_levels = array_keys($perks);
                    rsort($perk_levels);
                    foreach ($perk_levels as $perk_level) {
                        if ($user_level >= $perk_level) {
                            $user_perks = $perks[$perk_level];
                            break;
                        }
                    }
                    $discount = $user_perks['discount'];
                    $discount_amount = $row['total_before_discount'] * ($discount / 100);
                    $total_after_discount = $row['total_before_discount'] - $discount_amount;
                ?>
                    <tr>
                        <td><?= $row['id_transaksi'] ?></td>
                        <td><?= $row['tanggal'] ?></td>
                        <td><?= htmlspecialchars($row['nama_user'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['items']) ?></td>
                        <td>Rp<?= number_format($row['total_before_discount'], 0, ',', '.') ?></td>
                        <td><?= $discount > 0 ? $discount . "% (Rp " . number_format($discount_amount, 0, ',', '.') . ")" : "-" ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <?php
        // Ambil top seller
        $queryTop = "
            SELECT m.nama_masakan, SUM(td.jumlah) AS total_terjual
            FROM transaksi t
            JOIN transaksi_detail td ON t.id_transaksi = td.id_transaksi
            JOIN masakan m ON td.id_menu = m.id_menu
            $whereTransaksi
            GROUP BY td.id_menu
            ORDER BY total_terjual DESC
            LIMIT 1
        ";
        $topResult = mysqli_query($kon, $queryTop);
        $top = mysqli_fetch_assoc($topResult);
        ?>

        <?php if ($top): ?>
            <div class="alert alert-success mt-3">
                🍱 Menu Terlaris (<?= ucfirst($periode) ?><?= $periode == 'tahunan' ? " $tahun" : '' ?>): 
                <strong><?= $top['nama_masakan'] ?></strong> (<?= $top['total_terjual'] ?> terjual)
            </div>
        <?php endif; ?>

        <?php
        // Hitung total pendapatan
        mysqli_data_seek($resultTransaksi, 0); // reset result pointer
        $total = 0;
        while ($row = mysqli_fetch_assoc($resultTransaksi)) {
            $total += $row['total_before_discount'];
        }
        ?>

        <div class="alert alert-info">
            💰 Total Pendapatan: <strong>Rp<?= number_format($total, 0, ',', '.') ?></strong>
        </div>

    <?php elseif ($tab == 'pesanan'): ?>
        <!-- Laporan Pesanan -->
        <form method="GET" class="mb-3">
            <input type="hidden" name="tab" value="pesanan">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="tanggal_pesanan">Tanggal Spesifik:</label>
                    <input type="date" name="tanggal_pesanan" id="tanggal_pesanan" class="form-control" 
                        value="<?= htmlspecialchars($tanggalPesanan) ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="tanggal_mulai">Dari Tanggal:</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" 
                        value="<?= htmlspecialchars($tanggalMulai) ?>">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="tanggal_selesai">Sampai Tanggal:</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" 
                        value="<?= htmlspecialchars($tanggalSelesai) ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Tampilkan</button>
            <a href="?tab=pesanan" class="btn btn-secondary">Reset</a>
        </form>

        <script>
        // Validasi: Nonaktifkan input lain saat salah satu diisi
        document.getElementById('tanggal_pesanan').addEventListener('input', function() {
            const tanggalMulai = document.getElementById('tanggal_mulai');
            const tanggalSelesai = document.getElementById('tanggal_selesai');
            if (this.value) {
                tanggalMulai.value = '';
                tanggalSelesai.value = '';
                tanggalMulai.setAttribute('disabled', 'disabled');
                tanggalSelesai.setAttribute('disabled', 'disabled');
            } else {
                tanggalMulai.removeAttribute('disabled');
                tanggalSelesai.removeAttribute('disabled');
            }
        });

        document.getElementById('tanggal_mulai').addEventListener('input', function() {
            const tanggalPesanan = document.getElementById('tanggal_pesanan');
            if (this.value) {
                tanggalPesanan.value = '';
                tanggalPesanan.setAttribute('disabled', 'disabled');
            } else {
                if (!document.getElementById('tanggal_selesai').value) {
                    tanggalPesanan.removeAttribute('disabled');
                }
            }
        });

        document.getElementById('tanggal_selesai').addEventListener('input', function() {
            const tanggalPesanan = document.getElementById('tanggal_pesanan');
            if (this.value) {
                tanggalPesanan.value = '';
                tanggalPesanan.setAttribute('disabled', 'disabled');
            } else {
                if (!document.getElementById('tanggal_mulai').value) {
                    tanggalPesanan.removeAttribute('disabled');
                }
            }
        });
        </script>

        <h2>Laporan Pesanan</h2>
        <table class="table table-bordered mt-3 table-pesanan">
            <thead class="table-light">
                <tr>
                    <th>ID Transaksi</th>
                    <th>Tanggal</th>
                    <th>Nama Pelanggan</th>
                    <th>Items</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($resultPesanan)): ?>
                    <tr>
                        <td><?= $row['id_transaksi'] ?></td>
                        <td><?= $row['tanggal'] ?></td>
                        <td><?= htmlspecialchars($row['nama_user'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['items']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php
        // Hitung total pesanan
        mysqli_data_seek($resultPesanan, 0); // reset result pointer
        $totalPesanan = mysqli_num_rows($resultPesanan);
        ?>

        <div class="alert alert-info">
            📋 Total Pesanan: <strong><?= $totalPesanan ?></strong>
        </div>
    <?php endif; ?>
</div>

<?php include '../layout/footer.php'; ?>