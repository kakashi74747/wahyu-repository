<?php 
include '../layout/header.php'; 
include '../konkon.php';
include '../login/auth_check_karyawan.php';

// Ambil semua transaksi
$query = "
    SELECT t.*, u.nama_user, u.id_level
    FROM transaksi t
    LEFT JOIN user u ON t.id_user = u.id_user
    ORDER BY t.id_transaksi ASC
";
$result = mysqli_query($kon, $query);
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
        width: 10%;
    }
    .table th:nth-child(3), .table td:nth-child(3) { /* Nama User */
        width: 20%;
        word-break: break-word;
        white-space: normal;
        overflow: hidden;
    }
    .table th:nth-child(4), .table td:nth-child(4) { /* Tanggal */
        width: 15%;
    }
    .table th:nth-child(5), .table td:nth-child(5) { /* Items */
        width: 30%;
        word-break: break-word;
        white-space: normal;
    }
    .table th:nth-child(6), .table td:nth-child(6) { /* Total Amount */
        width: 15%;
    }
    .table th:nth-child(7), .table td:nth-child(7) { /* Diskon */
        width: 10%;
    }
    .table th:nth-child(8), .table td:nth-child(8) { /* Aksi */
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
    <h2 class="mt-4">Data Transaksi</h2>
    <a href="../4x4/tambahtransaksi.php" class="btn btn-primary mb-3">Tambah Transaksi</a>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>ID Transaksi</th>
                <th>Nama User</th>
                <th>Tanggal</th>
                <th>Items</th>
                <th>Total Sebelum Diskon</th>
                <th>Diskon</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($result)) {
                $id_transaksi = $row['id_transaksi'];
                $detailQuery = "
                    SELECT td.jumlah, m.nama_masakan, m.harga 
                    FROM transaksi_detail td
                    LEFT JOIN masakan m ON td.id_menu = m.id_menu
                    WHERE td.id_transaksi = ?
                ";
                $detailStmt = $kon->prepare($detailQuery);
                $detailStmt->bind_param("i", $id_transaksi);
                $detailStmt->execute();
                $detailResult = $detailStmt->get_result();

                $items = [];
                $total_before_discount = 0;
                while ($detail = $detailResult->fetch_assoc()) {
                    $subtotal = $detail['harga'] * $detail['jumlah'];
                    $items[] = $detail['nama_masakan'] . " (x" . $detail['jumlah'] . ")";
                    $total_before_discount += $subtotal;
                }
                $itemsList = !empty($items) ? "<ul class='items-list'><li>" . implode("</li><li>", $items) . "</li></ul>" : "-";

                // Gunakan id_level dari transaksi
                $perks = [
                    3 => ['name' => 'Blessing of Light', 'discount' => 5, 'items' => []],
                    5 => ['name' => 'Gift from the Moon', 'discount' => 7, 'items' => ['Coupons']],
                    10 => ['name' => 'Twilight Perk', 'discount' => 12, 'items' => ['Free Castorice Merch']],
                    15 => ['name' => 'Eternal Reverie Perks', 'discount' => 20, 'items' => ['Free Castorice Plushie', 'Free Castorice Merch', 'Starlight Hall Engraving']]
                ];

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
                $discount_amount = $total_before_discount * ($discount / 100);

                echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['id_transaksi']}</td>
                        <td>" . htmlspecialchars($row['nama_user']) . "</td>
                        <td>" . htmlspecialchars($row['tanggal']) . "</td>
                        <td>{$itemsList}</td>
                        <td>Rp " . number_format($total_before_discount, 0, ',', '.') . "</td>
                        <td>" . ($discount > 0 ? $discount . "% (Rp " . number_format($discount_amount, 0, ',', '.') . ")" : "-") . "</td>
                        <td>
                            <div class='action-buttons'>
                                <a href='../4x4/edittransaksi.php?id_transaksi={$row['id_transaksi']}' class='btn btn-sm btn-warning'>Edit</a>
                                <a href='../4x4/hapustransaksi.php?id_transaksi={$row['id_transaksi']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Yakin ingin menghapus transaksi ini?\")'>Hapus</a>
                                <a href='../pages/receipt.php?id_transaksi={$row['id_transaksi']}' class='btn btn-sm btn-primary'>Receipt</a>
                            </div>
                        </td>
                      </tr>";
                $no++;
                $detailStmt->close();
            }
            ?>
        </tbody>
    </table>
</div>

<?php include '../layout/footer.php'; ?>