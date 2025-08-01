<?php
session_start();
include '../layout/header.php';
include '../konkon.php';

// Cek autentikasi
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='/TugasAkhir/login/login.php';</script>";
    exit();
}

// Cek apakah user atau karyawan
$is_user = isset($_SESSION['login_type']) && $_SESSION['login_type'] === 'user';
$is_karyawan = isset($_SESSION['login_type']) && $_SESSION['login_type'] === 'karyawan';

if (!$is_user && !$is_karyawan) {
    echo "<script>alert('Halaman ini hanya untuk pelanggan atau karyawan!'); window.location='/TugasAkhir/pages/indegs.php';</script>";
    exit();
}

$id_transaksi = isset($_GET['id_transaksi']) ? (int)$_GET['id_transaksi'] : 0;

// Jika user, pastikan transaksi milik mereka
if ($is_user) {
    $id_user = isset($_SESSION['user']['id_user']) ? $_SESSION['user']['id_user'] : $_SESSION['id_user'];
    $query = "SELECT t.*, u.nama_user, u.id_level 
              FROM transaksi t
              JOIN user u ON t.id_user = u.id_user
              WHERE t.id_transaksi = ? AND t.id_user = ?";
    $stmt = mysqli_prepare($kon, $query);
    mysqli_stmt_bind_param($stmt, "ii", $id_transaksi, $id_user);
} else {
    $query = "SELECT t.*, u.nama_user, u.id_level 
              FROM transaksi t
              JOIN user u ON t.id_user = u.id_user
              WHERE t.id_transaksi = ?";
    $stmt = mysqli_prepare($kon, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_transaksi);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    die("Transaksi tidak ditemukan atau Anda tidak memiliki akses.");
}

// Definisikan perks berdasarkan level
$perks = [
    3 => ['name' => 'Blessing of Light', 'discount' => 5, 'items' => []],
    5 => ['name' => 'Gift from the Moon', 'discount' => 7, 'items' => ['Coupons']],
    10 => ['name' => 'Twilight Perk', 'discount' => 12, 'items' => ['Free Castorice Merch']],
    15 => ['name' => 'Eternal Reverie Perks', 'discount' => 20, 'items' => ['Free Castorice Plushie', 'Free Castorice Merch', 'Starlight Hall Engraving']]
];

$user_level = $data['id_level'];
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

// Ambil detail items
$detailQuery = "
    SELECT td.*, m.nama_masakan, m.harga 
    FROM transaksi_detail td
    LEFT JOIN masakan m ON td.id_menu = m.id_menu
    WHERE td.id_transaksi = ?
";
$detailStmt = mysqli_prepare($kon, $detailQuery);
mysqli_stmt_bind_param($detailStmt, "i", $id_transaksi);
mysqli_stmt_execute($detailStmt);
$detailResult = mysqli_stmt_get_result($detailStmt);

$items = [];
$total_before_discount = 0;
while ($detail = mysqli_fetch_assoc($detailResult)) {
    $subtotal = $detail['jumlah'] * $detail['harga'];
    $total_before_discount += $subtotal;
    $items[] = [
        'nama_masakan' => $detail['nama_masakan'],
        'harga' => $detail['harga'],
        'jumlah' => $detail['jumlah'],
        'subtotal' => $subtotal
    ];
}

$discount_amount = $total_before_discount * ($discount / 100);
$total_after_discount = $total_before_discount - $discount_amount;

mysqli_stmt_close($stmt);
mysqli_stmt_close($detailStmt);
?>

<div class="container mt-5">
    <div class="card p-4 shadow" style="max-width: 500px; margin: auto; background-color: #fdfdfd; border-radius: 16px;">
        <h4 class="text-center mb-4">🧾 Receipt Transaksi</h4>

        <table class="table table-borderless">
            <tr>
                <th>Tanggal</th>
                <td>: <?= date('d M Y', strtotime($data['tanggal'])) ?></td>
            </tr>
            <tr>
                <th>Nama Pelanggan</th>
                <td>: <?= htmlspecialchars($data['nama_user']) ?></td>
            </tr>
            <?php if ($discount > 0): ?>
                <tr>
                    <th>Perk Diskon</th>
                    <td>: <?= htmlspecialchars($user_perks['name']) ?> (<?= $discount ?>%)</td>
                </tr>
            <?php endif; ?>
        </table>

        <table class="table table-borderless mt-3">
            <thead>
                <tr>
                    <th>Menu</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item) { ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nama_masakan']) ?></td>
                        <td>Rp<?= number_format($item['harga'], 0, ',', '.') ?></td>
                        <td><?= $item['jumlah'] ?> porsi</td>
                        <td>Rp<?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr class="fw-bold border-top">
                    <td colspan="3">Total Sebelum Diskon</td>
                    <td>Rp<?= number_format($total_before_discount, 0, ',', '.') ?></td>
                </tr>
                <?php if ($discount > 0): ?>
                    <tr class="fw-bold">
                        <td colspan="3">Diskon (<?= $discount ?>%)</td>
                        <td>- Rp<?= number_format($discount_amount, 0, ',', '.') ?></td>
                    </tr>
                <?php endif; ?>
                <tr class="fw-bold border-top">
                    <td colspan="3">Total Setelah Diskon</td>
                    <td>Rp<?= number_format($total_after_discount, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="text-center mt-4">
            <?php if ($is_user): ?>
                <a href="../pages/history.php" class="btn btn-primary btn-sm">Kembali ke History</a>
            <?php else: ?>
                <a href="../pages/transaksi.php" class="btn btn-primary btn-sm">Kembali ke Transaksi</a>
            <?php endif; ?>
            <button onclick="window.print()" class="btn btn-success btn-sm">🖨️ Cetak</button>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>