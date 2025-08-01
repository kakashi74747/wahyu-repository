<?php
session_start();
include '../konkon.php';

if (!$kon) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Cek autentikasi
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    file_put_contents('debug_session.txt', "SESSION not logged in: " . print_r($_SESSION, true) . "\n", FILE_APPEND);
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location='/TugasAkhir/login/login.php';</script>";
    exit();
}

// Cek apakah user adalah pelanggan (berdasarkan tabel user) atau karyawan
$is_user = isset($_SESSION['login_type']) && $_SESSION['login_type'] === 'user' && (isset($_SESSION['user']['id_user']) || isset($_SESSION['id_user']));
$is_karyawan = isset($_SESSION['login_type']) && $_SESSION['login_type'] === 'karyawan';

// Jika bukan user atau karyawan, redirect ke indegs.php
if (!$is_user && !$is_karyawan) {
    file_put_contents('debug_session.txt', "Not user or karyawan: " . print_r($_SESSION, true) . "\n", FILE_APPEND);
    echo "<script>alert('Halaman ini hanya untuk pelanggan atau karyawan!'); window.location='/TugasAkhir/pages/indegs.php';</script>";
    exit();
}

// Validasi user di tabel user
if ($is_user) {
    $id_user = isset($_SESSION['user']['id_user']) ? $_SESSION['user']['id_user'] : $_SESSION['id_user'];
    $query = "SELECT id_user, nama_user, id_level FROM user WHERE id_user = ?";
    $stmt = mysqli_prepare($kon, $query);
    mysqli_stmt_bind_param($stmt, "s", $id_user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($result) == 0) {
        file_put_contents('debug_session.txt', "User not found in DB: " . print_r($_SESSION, true) . "\n", FILE_APPEND);
        echo "<script>alert('Halaman ini hanya untuk pelanggan!'); window.location='/TugasAkhir/pages/indegs.php';</script>";
        exit();
    }
    $db_user = mysqli_fetch_assoc($result);
    $_SESSION['user'] = [
        'id_user' => $db_user['id_user'],
        'nama_user' => $db_user['nama_user'],
        'id_level' => $db_user['id_level'],
        'username' => $_SESSION['username'] ?? ''
    ];
    mysqli_stmt_close($stmt);
}

// Ambil data perk berdasarkan level
if ($is_user) {
    $ranks = [
        1 => 'Moonlit Guest',
        2 => 'Wanderer',
        3 => 'Iris Seeker',
        4 => 'Honey Homie',
        5 => 'Aetherling',
        6 => 'Echo of Dusk',
        7 => 'Nameless Traveler',
        8 => 'Voyager 1',
        9 => 'Voyager 2',
        10 => 'Starseer',
        11 => 'Celestian',
        12 => 'Chronova',
        13 => 'Oracle',
        14 => 'Eidolon',
        15 => 'Aeons'
    ];

    $perks = [
        3 => ['name' => 'Blessing of Light', 'discount' => 5, 'items' => []],
        5 => ['name' => 'Gift from the Moon', 'discount' => 7, 'items' => ['Coupons']],
        10 => ['name' => 'Twilight Perk', 'discount' => 12, 'items' => ['Free Castorice Merch']],
        15 => ['name' => 'Eternal Reverie Perks', 'discount' => 20, 'items' => ['Free Castorice Plushie', 'Free Castorice Merch', 'Starlight Hall Engraving']]
    ];

    $user_level = $_SESSION['user']['id_level'];
    $user_perks = ['name' => 'No Perks', 'discount' => 0, 'items' => []];
    $perk_levels = array_keys($perks);
    rsort($perk_levels);
    foreach ($perk_levels as $perk_level) {
        if ($user_level >= $perk_level) {
            $user_perks = $perks[$perk_level];
            break;
        }
    }
}

// Cek apakah mode debugging aktif
$debug_mode = isset($_GET['debug']) && $_GET['debug'] === 'true';

// Proses hapus item dari keranjang (hanya untuk user)
if ($is_user && isset($_GET['remove'])) {
    $id_menu = $_GET['remove'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id_menu'] == $id_menu) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
    header("Location: /TugasAkhir/pages/pesanan.php" . ($debug_mode ? "?debug=true" : ""));
    exit();
}

// Proses update jumlah (hanya untuk user)
if ($is_user && isset($_POST['update_quantity']) && isset($_POST['id_menu']) && isset($_POST['quantity'])) {
    $id_menu = $_POST['id_menu'];
    $quantity = (int)$_POST['quantity'];
    
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id_menu'] == $id_menu) {
            if ($quantity > 0) {
                $item['quantity'] = $quantity;
            } else {
                unset($_SESSION['cart'][array_search($item, $_SESSION['cart'])]);
            }
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
    header("Location: /TugasAkhir/pages/pesanan.php" . ($debug_mode ? "?debug=true" : ""));
    exit();
}

// Proses checkout (hanya untuk user)
if ($is_user && isset($_POST['checkout'])) {
    $id_user = $_SESSION['user']['id_user'];
    $tanggal = date('Y-m-d H:i:s');
    $is_debug = $debug_mode ? 1 : 0;
    $discount = $user_perks['discount'];
    $total_before_discount = 0;

    // Hitung total sebelum diskon
    foreach ($_SESSION['cart'] as $item) {
        $total_before_discount += $item['harga'] * $item['quantity'];
    }
    $total_after_discount = $total_before_discount * (1 - $discount / 100);

    // Mulai transaksi untuk memastikan konsistensi
    mysqli_begin_transaction($kon);

    try {
        // Insert ke tabel transaksi dengan id_level
        $query_transaksi = "INSERT INTO transaksi (id_user, id_level, tanggal, is_debug, total_amount) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($kon, $query_transaksi);
        $current_level = $_SESSION['user']['id_level'];
        mysqli_stmt_bind_param($stmt, "iisid", $id_user, $current_level, $tanggal, $is_debug, $total_after_discount);
        mysqli_stmt_execute($stmt);
        $id_transaksi = mysqli_insert_id($kon);
        mysqli_stmt_close($stmt);

        // Insert detail item ke tabel transaksi_detail
        $query_detail = "INSERT INTO transaksi_detail (id_transaksi, id_menu, jumlah) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($kon, $query_detail);
        foreach ($_SESSION['cart'] as $item) {
            $id_menu = $item['id_menu'];
            $jumlah = $item['quantity'];
            mysqli_stmt_bind_param($stmt, "iii", $id_transaksi, $id_menu, $jumlah);
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);

        // Commit transaksi
        mysqli_commit($kon);

        // Kosongkan keranjang setelah checkout
        $_SESSION['cart'] = [];
        echo "<script>alert('Pesanan berhasil dicheckout dengan diskon $discount%!'); window.location='/TugasAkhir/pages/indegs.php';</script>";
        exit();
    } catch (Exception $e) {
        mysqli_rollback($kon);
        file_put_contents('debug_checkout.txt', "Checkout error: " . $e->getMessage() . "\n", FILE_APPEND);
        echo "<script>alert('Terjadi kesalahan saat checkout. Silakan coba lagi!'); window.location='/TugasAkhir/pages/pesanan.php';</script>";
        exit();
    }
}

include '../layout/header.php'; 
?>

<style>
    .quantity-control {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .quantity-btn {
        background-color: #a64dd6;
        color: white;
        border: none;
        padding: 2px 8px;
        border-radius: 3px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .quantity-btn:hover {
        background-color: #8b3cb1;
    }
    .quantity-display {
        width: 40px;
        text-align: center;
        border: 1px solid #ccc;
        border-radius: 3px;
        padding: 2px;
    }
    .discount-info {
        color: #28a745;
        font-weight: bold;
        margin-top: 10px;
    }
</style>

<div class="container">
    <h2 class="mt-4">Pesanan Anda</h2>

    <?php if ($debug_mode): ?>
        <div class="alert alert-warning">Anda sedang dalam mode debugging. Pesanan ini akan ditandai sebagai data uji.</div>
    <?php endif; ?>

    <?php if ($is_karyawan): ?>
        <div class="alert alert-info">Anda login sebagai karyawan. Anda hanya dapat melihat pesanan tanpa bisa memesan.</div>
    <?php endif; ?>

    <?php if (empty($_SESSION['cart'])): ?>
        <p class="text-center">Keranjang Anda kosong. <a href="/TugasAkhir/pages/indegs.php">Kembali ke menu</a></p>
    <?php else: ?>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Gambar</th>
                    <th>Nama Menu</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_before_discount = 0;
                foreach ($_SESSION['cart'] as $item):
                    $subtotal = $item['harga'] * $item['quantity'];
                    $total_before_discount += $subtotal;
                ?>
                    <tr>
                        <td>
                            <?php if ($item['gambar']): ?>
                                <img src="../SSC/img/<?php echo htmlspecialchars($item['gambar']); ?>" alt="<?php echo htmlspecialchars($item['nama_masakan']); ?>" style="width: 50px; height: 50px; object-fit: cover;">
                            <?php else: ?>
                                <img src="../SSC/img/default-food.jpg" alt="Default Image" style="width: 50px; height: 50px; object-fit: cover;">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($item['nama_masakan']); ?></td>
                        <td>Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                        <td>
                            <?php if ($is_user): ?>
                                <form method="post" class="quantity-control">
                                    <input type="hidden" name="id_menu" value="<?php echo $item['id_menu']; ?>">
                                    <input type="hidden" name="update_quantity" value="1">
                                    <button type="button" class="quantity-btn" onclick="updateQuantity(this, -1)">-</button>
                                    <span class="quantity-display"><?php echo $item['quantity']; ?></span>
                                    <input type="hidden" name="quantity" value="<?php echo $item['quantity']; ?>">
                                    <button type="button" class="quantity-btn" onclick="updateQuantity(this, 1)">+</button>
                                </form>
                            <?php else: ?>
                                <span class="quantity-display"><?php echo $item['quantity']; ?></span>
                            <?php endif; ?>
                        </td>
                        <td>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                        <td>
                            <?php if ($is_user): ?>
                                <a href="/TugasAkhir/pages/pesanan.php?remove=<?php echo $item['id_menu'] . ($debug_mode ? '&debug=true' : ''); ?>" class="btn btn-sm btn-danger">Hapus</a>
                            <?php else: ?>
                                <span class="text-muted">Hapus (hanya untuk pelanggan)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total Sebelum Diskon</strong></td>
                    <td><strong>Rp <?php echo number_format($total_before_discount, 0, ',', '.'); ?></strong></td>
                    <td></td>
                </tr>
                <?php if ($is_user && $user_perks['discount'] > 0): ?>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Diskon (<?php echo $user_perks['discount']; ?>%)</strong></td>
                        <td><strong>- Rp <?php echo number_format($total_before_discount * $user_perks['discount'] / 100, 0, ',', '.'); ?></strong></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Total Setelah Diskon</strong></td>
                        <td><strong>Rp <?php echo number_format($total_before_discount * (1 - $user_perks['discount'] / 100), 0, ',', '.'); ?></strong></td>
                        <td></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Form Checkout (hanya untuk user) -->
        <?php if ($is_user): ?>
            <form method="post">
                <div class="mb-3">
                    <label for="nama_user" class="form-label">Nama Pelanggan</label>
                    <input type="text" name="nama_user" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user']['nama_user']); ?>" readonly>
                </div>
                <?php if ($user_perks['discount'] > 0): ?>
                    <div class="discount-info">
                        Anda mendapatkan diskon <?php echo $user_perks['discount']; ?>% dari level <?php echo $ranks[$user_level]; ?>!
                    </div>
                <?php endif; ?>
                <button type="submit" name="checkout" class="btn btn-primary">Checkout</button>
                <a href="/TugasAkhir/pages/indegs.php" class="btn btn-secondary">Kembali ke Menu</a>
            </form>
        <?php else: ?>
            <a href="/TugasAkhir/pages/indegs.php" class="btn btn-secondary">Kembali ke Menu</a>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function updateQuantity(button, change) {
    const form = button.closest('form');
    const quantityDisplay = form.querySelector('.quantity-display');
    const quantityInput = form.querySelector('input[name="quantity"]');
    let quantity = parseInt(quantityDisplay.textContent) + change;

    if (quantity < 0) quantity = 0;
    quantityDisplay.textContent = quantity;
    quantityInput.value = quantity;

    // Submit form untuk update session
    form.submit();
}
</script>

<?php include '../layout/footer.php'; ?>