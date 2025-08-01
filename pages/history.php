<?php 
session_start();
include '../layout/header.php'; 
include '../konkon.php';

// Cek autentikasi
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='/TugasAkhir/login/login.php';</script>";
    exit();
}

// Pastikan hanya user yang bisa mengakses halaman ini
if (!isset($_SESSION['login_type']) || $_SESSION['login_type'] !== 'user') {
    echo "<script>alert('Halaman ini hanya untuk pelanggan!'); window.location='/TugasAkhir/pages/indegs.php';</script>";
    exit();
}

// Ambil ID user dari sesi
$id_user = isset($_SESSION['user']['id_user']) ? $_SESSION['user']['id_user'] : $_SESSION['id_user'];

// Ambil semua transaksi milik user ini
$query = "
    SELECT t.*, u.nama_user 
    FROM transaksi t
    LEFT JOIN user u ON t.id_user = u.id_user
    WHERE t.id_user = ?
    ORDER BY t.tanggal DESC
";
$stmt = mysqli_prepare($kon, $query);
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
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
    .table th:nth-child(1), .table td:nth-child(1) { /* No */
        width: 5%;
    }
    .table th:nth-child(2), .table td:nth-child(2) { /* ID Transaksi */
        width: 15%;
    }
    .table th:nth-child(3), .table td:nth-child(3) { /* Tanggal */
        width: 20%;
    }
    .table th:nth-child(4), .table td:nth-child(4) { /* Items */
        width: 40%;
        word-break: break-word;
        white-space: normal;
    }
    .table th:nth-child(5), .table td:nth-child(5) { /* Total */
        width: 15%;
    }
    .table th:nth-child(6), .table td:nth-child(6) { /* Aksi */
        width: 15%;
    }
    .items-list {
        padding-left: 20px;
        margin: 0;
    }
    .action-buttons {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
        justify-content: center;
    }
    .action-buttons .btn {
        padding: 4px 8px;
        font-size: 0.85rem;
    }
</style>

<div class="container">
    <h2 class="mt-4">Riwayat Pesanan</h2>

    <?php if (mysqli_num_rows($result) == 0): ?>
        <p class="text-center">Anda belum memiliki riwayat pesanan. <a href="/TugasAkhir/pages/indegs.php">Pesan sekarang!</a></p>
    <?php else: ?>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>ID Transaksi</th>
                    <th>Tanggal</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    // Ambil detail items untuk transaksi ini
                    $id_transaksi = $row['id_transaksi'];
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
                    $total = 0;
                    while ($detail = mysqli_fetch_assoc($detailResult)) {
                        $subtotal = $detail['jumlah'] * $detail['harga'];
                        $total += $subtotal;
                        $items[] = $detail['nama_masakan'] . " (x" . $detail['jumlah'] . ")";
                    }
                    $itemsList = !empty($items) ? "<ul class='items-list'><li>" . implode("</li><li>", $items) . "</li></ul>" : "-";
                    
                    // Perbarui total_amount di tabel transaksi jika berbeda
                    if (!$row['total_amount'] || $row['total_amount'] != $total) {
                        $updateTotalQuery = "UPDATE transaksi SET total_amount = ? WHERE id_transaksi = ?";
                        $updateTotalStmt = mysqli_prepare($kon, $updateTotalQuery);
                        mysqli_stmt_bind_param($updateTotalStmt, "di", $total, $id_transaksi);
                        mysqli_stmt_execute($updateTotalStmt);
                        mysqli_stmt_close($updateTotalStmt);
                    }

                    echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['id_transaksi']}</td>
                            <td>" . htmlspecialchars($row['tanggal']) . "</td>
                            <td>{$itemsList}</td>
                            <td>Rp " . number_format($total, 0, ',', '.') . "</td>
                            <td>
                                <div class='action-buttons'>
                                    <a href='/TugasAkhir/pages/receipt.php?id_transaksi={$row['id_transaksi']}' class='btn btn-sm btn-primary'>Receipt</a>
                                </div>
                            </td>
                          </tr>";
                    $no++;
                    mysqli_stmt_close($detailStmt);
                }
                ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="/TugasAkhir/pages/indegs.php" class="btn btn-secondary mt-3">Kembali ke Menu</a>
</div>

<?php 
mysqli_stmt_close($stmt);
include '../layout/footer.php'; 
?>