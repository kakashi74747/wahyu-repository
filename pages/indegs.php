<?php
session_start(); // Mulai session untuk keranjang
include '../konkon.php';

if (!$kon) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Proses tambah ke keranjang
if (isset($_POST['add_to_cart'])) {
    $id_menu = $_POST['id_menu'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Ambil data menu dari database
    $query = "SELECT * FROM masakan WHERE id_menu = ?";
    $stmt = mysqli_prepare($kon, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_menu);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $menu = mysqli_fetch_assoc($result);

    if ($menu) {
        $item = [
            'id_menu' => $menu['id_menu'],
            'nama_masakan' => $menu['nama_masakan'],
            'harga' => $menu['harga'],
            'quantity' => $quantity,
            'gambar' => $menu['gambar']
        ];

        // Tambah atau update item di keranjang
        $found = false;
        foreach ($_SESSION['cart'] as &$cart_item) {
            if ($cart_item['id_menu'] == $id_menu) {
                $cart_item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $_SESSION['cart'][] = $item;
        }

        // Redirect untuk mencegah form resubmission
        header("Location: indegs.php");
        exit();
    }
    mysqli_stmt_close($stmt);
}

// Proses tambah ke favorit dan hapus favorit
if (isset($_POST['add_to_favorites']) || isset($_POST['remove_from_favorites'])) {
    if (!isset($_SESSION['id_user'])) {
        header("Location: ../login/login.php");
        exit();
    }
    $id_user = $_SESSION['id_user'];
    $id_menu = $_POST['id_menu'];
    $action = isset($_POST['add_to_favorites']) ? 'add' : 'remove';

    if ($action === 'add') {
        // Cek apakah menu sudah ada di favorit
        $check_query = "SELECT * FROM favorites WHERE id_user = ? AND id_menu = ?";
        $check_stmt = mysqli_prepare($kon, $check_query);
        mysqli_stmt_bind_param($check_stmt, "ii", $id_user, $id_menu);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) == 0) {
            $insert_query = "INSERT INTO favorites (id_user, id_menu) VALUES (?, ?)";
            $insert_stmt = mysqli_prepare($kon, $insert_query);
            mysqli_stmt_bind_param($insert_stmt, "ii", $id_user, $id_menu);
            mysqli_stmt_execute($insert_stmt);
            mysqli_stmt_close($insert_stmt);
        }
        mysqli_stmt_close($check_stmt); // Close $check_stmt here
    } else {
        $delete_query = "DELETE FROM favorites WHERE id_user = ? AND id_menu = ?";
        $delete_stmt = mysqli_prepare($kon, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "ii", $id_user, $id_menu);
        mysqli_stmt_execute($delete_stmt);
        mysqli_stmt_close($delete_stmt); // Close $delete_stmt here
    }

    header("Location: indegs.php");
    exit();
}

// Proses submit review
if (isset($_POST['submit_review'])) {
    if (!isset($_SESSION['id_user'])) {
        header("Location: ../login/login.php");
        exit();
    }
    $id_user = $_SESSION['id_user'];
    $id_menu = $_POST['id_menu'];
    $rating = $_POST['rating'];
    $komentar = $_POST['komentar'];

    $query = "INSERT INTO reviews (id_user, id_menu, rating, komentar) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($kon, $query);
    mysqli_stmt_bind_param($stmt, "iiis", $id_user, $id_menu, $rating, $komentar);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: indegs.php");
    exit();
}

include '../layout/header.php'; 

// Ambil data Today's Special (hanya is_special = 1, tanpa fallback)
$special_query = "SELECT * FROM masakan WHERE is_special = 1 LIMIT 1";
$special_result = mysqli_query($kon, $special_query);
$special_menu = mysqli_fetch_assoc($special_result);

// Ambil data Featured Desserts (kategori = 'dessert' dan is_featured = 1)
$desserts = mysqli_query($kon, "SELECT * FROM masakan WHERE kategori = 'dessert' AND is_featured = 1 ORDER BY id_menu ASC LIMIT 15") or die(mysqli_error($kon));

// Ambil data Signature Drinks (kategori = 'minuman' dan is_featured = 1)
$drinks = mysqli_query($kon, "SELECT * FROM masakan WHERE kategori = 'minuman' AND is_featured = 1 ORDER BY id_menu ASC LIMIT 15") or die(mysqli_error($kon));

// Ambil semua review untuk ditampilkan
$reviews_query = "
    SELECT r.*, m.nama_masakan, u.nama_user 
    FROM reviews r
    JOIN masakan m ON r.id_menu = m.id_menu
    JOIN user u ON r.id_user = u.id_user
    ORDER BY r.tanggal DESC
";
$reviews = mysqli_query($kon, $reviews_query);

// Cek status favorit untuk setiap menu
function isFavorited($id_menu, $id_user, $kon) {
    if (!isset($id_user)) return false;
    $check_query = "SELECT COUNT(*) FROM favorites WHERE id_user = ? AND id_menu = ?";
    $stmt = mysqli_prepare($kon, $check_query);
    mysqli_stmt_bind_param($stmt, "ii", $id_user, $id_menu);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $count = mysqli_fetch_array($result)[0];
    mysqli_stmt_close($stmt);
    return $count > 0;
}
?>

<section class="interactive-features">
    <?php include '../specialfeatures/seasonal-banner.php'; ?>
</section>

<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
    /* Greeting section */
    .greeting-wrapper {
        display: flex;
        justify-content: space-around;
        align-items: center;
        background: linear-gradient(135deg, #f9e5ff, #e6c3ff);
        border-radius: 25px;
        padding: 30px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        width: 100%;
        max-width: 1000px;
        margin: 30px auto;
        position: relative;
        overflow: hidden;
    }

    .greeting-text h2 {
        font-size: 3rem;
        margin: 0;
        font-family: 'Pacifico', cursive;
        color: #5a2e8d;
        text-shadow: 2px 2px 5px rgba(0,0,0,0.2);
        animation: fadeIn 1.5s ease-in-out;
    }

    .greeting-text p {
        margin: 10px 0 0;
        font-family: 'Pacifico', cursive;
        font-style: italic;
        color: #7a4da6;
        font-size: 1.5rem;
    }

    .greeting-img {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #fff;
        box-shadow: 0 6px 20px rgba(0,0,0,0.2);
        transition: transform 0.3s ease;
    }

    .greeting-img:hover {
        transform: scale(1.1);
    }

    /* Special offer */
    .special-offer {
        background: linear-gradient(135deg, #f9f5ff, #f0d2ff);
        border-radius: 20px;
        padding: 30px;
        margin: 40px auto;
        width: 90%;
        max-width: 950px;
        box-shadow: 0 6px 25px rgba(0,0,0,0.15);
        text-align: center;
    }

    .special-title {
        font-family: 'Pacifico', cursive;
        color: #5a2e8d;
        font-size: 2.2rem;
        margin-bottom: 20px;
    }

    .menu-card {
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .menu-card:hover {
        transform: scale(1.05) rotate(-1deg);
        box-shadow: 0 10px 30px rgba(180, 120, 255, 0.3);
    }

    .menu-img {
        width: 180px;
        height: 180px;
        border-radius: 15px;
        object-fit: cover;
        margin-right: 25px;
        border: 3px solid #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }

    .menu-details {
        flex: 1;
        text-align: left;
    }

    .menu-title {
        font-weight: 600;
        color: #5a2e8d;
        margin: 0 0 8px;
        font-size: 1.5rem;
    }

    .menu-desc {
        color: #666;
        font-size: 1rem;
        margin: 0 0 15px;
        line-height: 1.6;
    }

    .menu-price {
        color: #a64dd6;
        font-weight: 700;
        font-size: 1.3rem;
    }

    /* Menu sections */
    .menu-section {
        width: 90%;
        max-width: 1200px;
        margin: 40px auto;
    }

    .section-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .section-header h2 {
        font-family: 'Pacifico', cursive;
        color: #5a2e8d;
        font-size: 2.2rem;
        margin-bottom: 10px;
    }

    .section-header p {
        color: #666;
        font-size: 1rem;
    }

    .menu-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 30px;
        margin-top: 20px;
    }

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

    .add-to-cart-btn {
        background-color: #a64dd6;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
        margin-left: 10px;
    }

    .add-to-cart-btn:hover {
        background-color: #8b3cb1;
    }

    .btn-outline-primary {
        background: none;
        border: 1px solid #a64dd6;
        color: #a64dd6;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-outline-primary:hover {
        background-color: #a64dd6;
        color: white;
    }

    .btn-outline-primary.favorited {
        background-color: #a64dd6;
        color: white;
    }

    .mt-2 {
        margin-top: 10px;
    }

    .cart-icon {
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #a64dd6;
        color: white;
        padding: 10px 15px;
        border-radius: 50%;
        font-size: 1.2rem;
        text-decoration: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .cart-icon:hover {
        background-color: #8b3cb1;
    }

    .cart-count {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #ff4d4d;
        color: white;
        font-size: 0.8rem;
        padding: 2px 6px;
        border-radius: 50%;
    }

    /* Review Section */
    .review-section {
        width: 90%;
        max-width: 1200px;
        margin: 40px auto;
    }

    .review-container {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 10px;
    }

    .review-card {
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .review-user {
        font-weight: 600;
        color: #5a2e8d;
        font-size: 1.2rem;
    }

    .review-date {
        color: #666;
        font-size: 0.9rem;
    }

    .review-rating {
        color: #a64dd6;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .review-comment {
        color: #555;
        line-height: 1.6;
    }

    .review-form {
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        margin-top: 20px;
    }

    .form-group {
        margin-bottom: 15px;
        text-align: left;
    }

    .form-control {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-family: 'Quicksand', sans-serif;
    }

    .form-control:focus {
        outline: none;
        border-color: #a64dd6;
        box-shadow: 0 0 0 2px rgba(166, 77, 214, 0.2);
    }

    .btn {
        padding: 8px 20px;
        border-radius: 5px;
        border: none;
        font-family: 'Quicksand', sans-serif;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background-color: #a64dd6;
        color: white;
    }

    .btn-primary:hover {
        background-color: #8b3cb1;
    }

    /* Star Rating Styling */
    .star-rating {
        display: inline-flex;
        flex-direction: row-reverse;
        justify-content: flex-start;
        gap: 8px;
        margin: 10px 0;
        font-size: 2rem;
        padding-left: 0;
        margin-left: 0;
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        color: #ccc;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        color: #ffca08;
    }

    .star-rating input:checked + label {
        color: #ffca08;
    }

    /* Animation for greeting */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .section-divider {
        border: none;
        height: 2px;
        background: linear-gradient(to right, #f7d6ff, #fff);
        margin: 40px auto;
        width: 80%;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .greeting-wrapper {
            flex-direction: column;
            padding: 20px;
        }

        .greeting-img {
            margin: 20px 0 0;
        }

        .menu-card {
            flex-direction: column;
            text-align: center;
        }

        .menu-img {
            width: 100%;
            height: 300px;
            margin: 0 0 20px;
        }

        .menu-container {
            grid-template-columns: 1fr;
        }

        .quantity-control {
            margin: 0 auto 10px;
        }

        .add-to-cart-btn {
            margin-left: 0;
            margin-top: 10px;
        }

        .review-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .review-date {
            margin-top: 5px;
        }

        .star-rating {
            justify-content: flex-start;
            font-size: 1.5rem;
        }
    }
</style>

<div class="container"> 
    <!-- Tombol Keranjang -->
    <a href="pesanan.php" class="cart-icon">
        🛒
        <?php if (count($_SESSION['cart']) > 0): ?>
            <span class="cart-count"><?= count($_SESSION['cart']) ?></span>
        <?php endif; ?>
    </a>

    <!-- Greeting Section -->
    <div class="greeting-wrapper">
        <div class="greeting-text">
            <h2>Irrashaimase, Goshujin-sama!</h2>
            <p>Welcome to Castorice's Cafe</p>
        </div>
        <img src="../SSC/img/cassie-lucu-bgt-OMAGA.jpg" class="greeting-img" alt="Castorice">
    </div>

    <!-- Special Offer -->
    <div class="special-offer">
        <h2 class="special-title">✧ Today's Special ✧</h2>
        <?php if ($special_menu): ?>
            <div class="menu-card">
                <?php if ($special_menu['gambar']): ?>
                    <img src="../SSC/img/<?= htmlspecialchars($special_menu['gambar']) ?>" class="menu-img" alt="<?= htmlspecialchars($special_menu['nama_masakan']) ?>">
                <?php else: ?>
                    <img src="../SSC/img/default-food.jpg" class="menu-img" alt="Default Image">
                <?php endif; ?>
                <div class="menu-details">
                    <h3 class="menu-title"><?php echo htmlspecialchars($special_menu['nama_masakan']); ?></h3>
                    <p class="menu-desc"><?php echo htmlspecialchars($special_menu['deskripsi'] ?? 'Deskripsi tidak tersedia'); ?></p>
                    <div class="menu-price">Rp <?php echo number_format($special_menu['harga'], 0, ',', '.'); ?></div>
                    <form method="post" class="d-flex align-items-center">
                        <input type="hidden" name="id_menu" value="<?= $special_menu['id_menu'] ?>">
                        <div class="quantity-control">
                            <button type="button" class="quantity-btn" onclick="updateQuantity(this, -1)">-</button>
                            <span class="quantity-display">1</span>
                            <input type="hidden" name="quantity" value="1">
                            <button type="button" class="quantity-btn" onclick="updateQuantity(this, 1)">+</button>
                        </div>
                        <button type="submit" name="add_to_cart" class="add-to-cart-btn">Tambah ke Pesanan</button>
                    </form>
                    <form method="post" class="d-flex align-items-center mt-2">
                        <input type="hidden" name="id_menu" value="<?= $special_menu['id_menu'] ?>">
                        <button type="submit" name="<?= isset($_SESSION['id_user']) && isFavorited($special_menu['id_menu'], $_SESSION['id_user'], $kon) ? 'remove_from_favorites' : 'add_to_favorites' ?>" class="btn btn-outline-primary <?= isset($_SESSION['id_user']) && isFavorited($special_menu['id_menu'], $_SESSION['id_user'], $kon) ? 'favorited' : '' ?>"><i class="fas fa-heart"></i> <?= isset($_SESSION['id_user']) && isFavorited($special_menu['id_menu'], $_SESSION['id_user'], $kon) ? 'Hapus Favorit' : 'Favorit' ?></button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center">Yahh, gaada menu spesial hari ini (╥ᆺ╥;). Cek menu lain yaa! :3</p>
        <?php endif; ?>
    </div>

    <!-- Food Menu -->
    <div class="menu-section">
        <div class="section-header">
            <h2>Featured Foods</h2>
            <p>Delicious dishes to satisfy your hunger</p>
        </div>
        
        <div class="menu-container">
            <?php
            // Ambil data makanan featured (kategori = 'makanan' dan is_featured = 1)
            $foods = mysqli_query($kon, "SELECT * FROM masakan WHERE kategori = 'makanan' AND is_featured = 1 ORDER BY id_menu ASC LIMIT 15") or die(mysqli_error($kon));
            ?>
            <?php if (mysqli_num_rows($foods) > 0): ?>
                <?php while ($food = mysqli_fetch_assoc($foods)): ?>
                    <div class="menu-card">
                        <?php if ($food['gambar']): ?>
                            <img src="../SSC/img/<?= htmlspecialchars($food['gambar']) ?>" class="menu-img" alt="<?= htmlspecialchars($food['nama_masakan']) ?>">
                        <?php else: ?>
                            <img src="../SSC/img/default-food.jpg" class="menu-img" alt="Default Image">
                        <?php endif; ?>
                        <div class="menu-details">
                            <h3 class="menu-title"><?php echo htmlspecialchars($food['nama_masakan']); ?></h3>
                            <p class="menu-desc"><?php echo htmlspecialchars($food['deskripsi'] ?? 'Deskripsi tidak tersedia'); ?></p>
                            <div class="menu-price">Rp <?php echo number_format($food['harga'], 0, ',', '.'); ?></div>
                            <form method="post" class="d-flex align-items-center">
                                <input type="hidden" name="id_menu" value="<?= $food['id_menu'] ?>">
                                <div class="quantity-control">
                                    <button type="button" class="quantity-btn" onclick="updateQuantity(this, -1)">-</button>
                                    <span class="quantity-display">1</span>
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="button" class="quantity-btn" onclick="updateQuantity(this, 1)">+</button>
                                </div>
                                <button type="submit" name="add_to_cart" class="add-to-cart-btn">Tambah ke Pesanan</button>
                            </form>
                            <form method="post" class="d-flex align-items-center mt-2">
                                <input type="hidden" name="id_menu" value="<?= $food['id_menu'] ?>">
                                <button type="submit" name="<?= isset($_SESSION['id_user']) && isFavorited($food['id_menu'], $_SESSION['id_user'], $kon) ? 'remove_from_favorites' : 'add_to_favorites' ?>" class="btn btn-outline-primary <?= isset($_SESSION['id_user']) && isFavorited($food['id_menu'], $_SESSION['id_user'], $kon) ? 'favorited' : '' ?>"><i class="fas fa-heart"></i> <?= isset($_SESSION['id_user']) && isFavorited($food['id_menu'], $_SESSION['id_user'], $kon) ? 'Hapus Favorit' : 'Favorit' ?></button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">Tidak ada makanan yang ditampilkan sebagai featured.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Dessert Menu -->
    <div class="menu-section">
        <div class="section-header">
            <h2>Featured Desserts</h2>
            <p>Sweet treats to complete your dining experience</p>
        </div>
        
        <div class="menu-container">
            <?php if (mysqli_num_rows($desserts) > 0): ?>
                <?php while ($dessert = mysqli_fetch_assoc($desserts)): ?>
                    <div class="menu-card">
                        <?php if ($dessert['gambar']): ?>
                            <img src="../SSC/img/<?= htmlspecialchars($dessert['gambar']) ?>" class="menu-img" alt="<?= htmlspecialchars($dessert['nama_masakan']) ?>">
                        <?php else: ?>
                            <img src="../SSC/img/default-food.jpg" class="menu-img" alt="Default Image">
                        <?php endif; ?>
                        <div class="menu-details">
                            <h3 class="menu-title"><?php echo htmlspecialchars($dessert['nama_masakan']); ?></h3>
                            <p class="menu-desc"><?php echo htmlspecialchars($dessert['deskripsi'] ?? 'Deskripsi tidak tersedia'); ?></p>
                            <div class="menu-price">Rp <?php echo number_format($dessert['harga'], 0, ',', '.'); ?></div>
                            <form method="post" class="d-flex align-items-center">
                                <input type="hidden" name="id_menu" value="<?= $dessert['id_menu'] ?>">
                                <div class="quantity-control">
                                    <button type="button" class="quantity-btn" onclick="updateQuantity(this, -1)">-</button>
                                    <span class="quantity-display">1</span>
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="button" class="quantity-btn" onclick="updateQuantity(this, 1)">+</button>
                                </div>
                                <button type="submit" name="add_to_cart" class="add-to-cart-btn">Tambah ke Pesanan</button>
                            </form>
                            <form method="post" class="d-flex align-items-center mt-2">
                                <input type="hidden" name="id_menu" value="<?= $dessert['id_menu'] ?>">
                                <button type="submit" name="<?= isset($_SESSION['id_user']) && isFavorited($dessert['id_menu'], $_SESSION['id_user'], $kon) ? 'remove_from_favorites' : 'add_to_favorites' ?>" class="btn btn-outline-primary <?= isset($_SESSION['id_user']) && isFavorited($dessert['id_menu'], $_SESSION['id_user'], $kon) ? 'favorited' : '' ?>"><i class="fas fa-heart"></i> <?= isset($_SESSION['id_user']) && isFavorited($dessert['id_menu'], $_SESSION['id_user'], $kon) ? 'Hapus Favorit' : 'Favorit' ?></button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">Tidak ada dessert yang ditampilkan sebagai featured.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <hr class="section-divider">

    <!-- Drink Menu -->
    <div class="menu-section">
        <div class="section-header">
            <h2>Signature Drinks</h2>
            <p>Refreshing beverages to quench your thirst</p>
        </div>
        
        <div class="menu-container">
            <?php if (mysqli_num_rows($drinks) > 0): ?>
                <?php while ($drink = mysqli_fetch_assoc($drinks)): ?>
                    <div class="menu-card">
                        <?php if ($drink['gambar']): ?>
                            <img src="../SSC/img/<?= htmlspecialchars($drink['gambar']) ?>" class="menu-img" alt="<?= htmlspecialchars($drink['nama_masakan']) ?>">
                        <?php else: ?>
                            <img src="../SSC/img/default-food.jpg" class="menu-img" alt="Default Image">
                        <?php endif; ?>
                        <div class="menu-details">
                            <h3 class="menu-title"><?php echo htmlspecialchars($drink['nama_masakan']); ?></h3>
                            <p class="menu-desc"><?php echo htmlspecialchars($drink['deskripsi'] ?? 'Deskripsi tidak tersedia'); ?></p>
                            <div class="menu-price">Rp <?php echo number_format($drink['harga'], 0 , ',', '.'); ?></div>
                            <form method="post" class="d-flex align-items-center">
                                <input type="hidden" name="id_menu" value="<?= $drink['id_menu'] ?>">
                                <div class="quantity-control">
                                    <button type="button" class="quantity-btn" onclick="updateQuantity(this, -1)">-</button>
                                    <span class="quantity-display">1</span>
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="button" class="quantity-btn" onclick="updateQuantity(this, 1)">+</button>
                                </div>
                                <button type="submit" name="add_to_cart" class="add-to-cart-btn">Tambah ke Pesanan</button>
                            </form>
                            <form method="post" class="d-flex align-items-center mt-2">
                                <input type="hidden" name="id_menu" value="<?= $drink['id_menu'] ?>">
                                <button type="submit" name="<?= isset($_SESSION['id_user']) && isFavorited($drink['id_menu'], $_SESSION['id_user'], $kon) ? 'remove_from_favorites' : 'add_to_favorites' ?>" class="btn btn-outline-primary <?= isset($_SESSION['id_user']) && isFavorited($drink['id_menu'], $_SESSION['id_user'], $kon) ? 'favorited' : '' ?>"><i class="fas fa-heart"></i> <?= isset($_SESSION['id_user']) && isFavorited($drink['id_menu'], $_SESSION['id_user'], $kon) ? 'Hapus Favorit' : 'Favorit' ?></button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">Tidak ada minuman yang ditampilkan sebagai featured.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Review Section -->
    <div class="review-section">
        <div class="section-header">
            <h2>Customer Reviews</h2>
            <p>Hear what our customers have to say!</p>
        </div>

        <div class="review-container">
            <?php if (mysqli_num_rows($reviews) > 0): ?>
                <?php while ($review = mysqli_fetch_assoc($reviews)): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-user"><?php echo htmlspecialchars($review['nama_user']); ?> (on <?php echo htmlspecialchars($review['nama_masakan']); ?>)</div>
                            <div class="review-date"><?php echo htmlspecialchars($review['tanggal']); ?></div>
                        </div>
                        <div class="review-rating">Rating: <?php echo htmlspecialchars($review['rating']); ?>/5</div>
                        <div class="review-comment"><?php echo htmlspecialchars($review['komentar']); ?></div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">Belum ada review. Jadilah yang pertama memberikan review!</p>
            <?php endif; ?>
        </div>

        <!-- Form untuk submit review -->
        <?php if (isset($_SESSION['id_user'])): ?>
            <div class="review-form">
                <h3 class="special-title">Tulis Review</h3>
                <form method="post">
                    <div class="form-group">
                        <label for="id_menu">Pilih Menu</label>
                        <select name="id_menu" id="id_menu" class="form-control" required>
                            <?php
                            $menu_query = "SELECT id_menu, nama_masakan FROM masakan ORDER BY nama_masakan ASC";
                            $menu_result = mysqli_query($kon, $menu_query);
                            while ($menu = mysqli_fetch_assoc($menu_result)) {
                                echo "<option value='{$menu['id_menu']}'>" . htmlspecialchars($menu['nama_masakan']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="rating">Rating</label>
                        <div class="star-rating" id="rating">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" required>
                                <label for="star<?php echo $i; ?>" class="star"><i class="fas fa-star"></i></label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="komentar">Komentar</label>
                        <textarea name="komentar" id="komentar" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        <?php else: ?>
            <p class="text-center">Silakan <a href="../login/login.php">login</a> untuk memberikan review.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function updateQuantity(button, change) {
    const form = button.closest('form');
    const quantityDisplay = form.querySelector('.quantity-display');
    const quantityInput = form.querySelector('input[name="quantity"]');
    let quantity = parseInt(quantityDisplay.textContent) + change;

    if (quantity < 1) quantity = 1; // Minimal 1
    quantityDisplay.textContent = quantity;
    quantityInput.value = quantity;
}
</script>

<?php include '../specialfeatures/soundscape.php'; ?>

<?php include '../layout/footer.php'; ?>