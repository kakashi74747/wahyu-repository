<?php
session_start();
include '../konkon.php';

// Ambil data user dari database
$id_user = $_SESSION['id_user'] ?? null;
if (!$id_user) {
    header("Location: ../login/login.php");
    exit;
}

$query = "SELECT * FROM user WHERE id_user = ?";
$stmt = $kon->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "Data user tidak ditemukan.";
    exit();
}

// Define ranks and perks
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

// Define exp requirements for each level (in Rupiah)
$exp_requirements = [
    2 => 200000,
    3 => 500000,
    4 => 1000000,
    5 => 2000000,
    6 => 3500000,
    7 => 5000000,
    8 => 7000000,
    9 => 9000000,
    10 => 10000000,
    11 => 14000000,
    12 => 16000000,
    13 => 17000000,
    14 => 18000000,
    15 => 20000000
];

// Hitung total spent
$total_spent_query = "
    SELECT COALESCE(SUM(m.harga * td.jumlah), 0) as total 
    FROM transaksi_detail td 
    JOIN transaksi t ON td.id_transaksi = t.id_transaksi 
    JOIN masakan m ON td.id_menu = m.id_menu 
    WHERE t.id_user = ?
";
$stmt = $kon->prepare($total_spent_query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$total_spent_result = $stmt->get_result();
$total_spent = $total_spent_result->fetch_assoc()['total'] ?? 0;

// Tentukan level berdasarkan total spent
$current_level = 1;
foreach ($exp_requirements as $level => $required_exp) {
    if ($total_spent >= $required_exp) {
        $current_level = $level;
    }
}

// Tidak perlu update database jika hanya untuk display
$user_rank = $ranks[$current_level] ?? 'Moonlit Guest';
$user_perks = ['name' => 'No Perks', 'discount' => 0, 'items' => []];
$perk_levels = array_keys($perks);
rsort($perk_levels);
foreach ($perk_levels as $perk_level) {
    if ($current_level >= $perk_level) {
        $user_perks = $perks[$perk_level];
        break;
    }
}

// Tentukan perk berdasarkan level tertinggi dengan perk yang tersedia
$user_perks = ['name' => 'No Perks', 'discount' => 0, 'items' => []];
$perk_levels = array_keys($perks);
rsort($perk_levels); // Urutkan dari level tertinggi ke terendah
foreach ($perk_levels as $perk_level) {
    if ($user['id_level'] >= $perk_level) {
        $user_perks = $perks[$perk_level];
        break;
    }
}

$user_rank = $ranks[$user['id_level']] ?? 'Moonlit Guest';

$orders_count = mysqli_fetch_assoc(mysqli_query($kon, "SELECT COUNT(*) as count FROM transaksi WHERE id_user = $id_user"))['count'];
$reviews_count = mysqli_fetch_assoc(mysqli_query($kon, "SELECT COUNT(*) as count FROM reviews WHERE id_user = $id_user"))['count'];

// Ambil data favorites
$favorites_query = "
    SELECT m.* 
    FROM favorites f
    JOIN masakan m ON f.id_menu = m.id_menu
    WHERE f.id_user = ?
";
$stmt = $kon->prepare($favorites_query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$favorites_result = $stmt->get_result();
$favorites = $favorites_result->fetch_all(MYSQLI_ASSOC);

// Ambil data reviews
$reviews_query = "
    SELECT r.*, m.nama_masakan 
    FROM reviews r
    JOIN masakan m ON r.id_menu = m.id_menu
    WHERE r.id_user = ?
    ORDER BY r.tanggal DESC
";
$stmt = $kon->prepare($reviews_query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$reviews_result = $stmt->get_result();
$reviews = $reviews_result->fetch_all(MYSQLI_ASSOC);

// Ambil data transaksi untuk Recent Orders
$transaksi_query = "
    SELECT t.* 
    FROM transaksi t
    WHERE t.id_user = ?
    ORDER BY t.tanggal DESC
    LIMIT 5
";
$stmt = $kon->prepare($transaksi_query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$transaksi_result = $stmt->get_result();
$transaksi = $transaksi_result->fetch_all(MYSQLI_ASSOC);

// Proses update nama
if (isset($_POST['update_name'])) {
    $new_name = $_POST['name'] ?? '';
    if ($new_name) {
        $update_query = "UPDATE user SET nama_user = ? WHERE id_user = ?";
        $update_stmt = $kon->prepare($update_query);
        $update_stmt->bind_param("si", $new_name, $id_user);
        $update_stmt->execute();
        $user['nama_user'] = $new_name;
    }
}

// Proses update bio
if (isset($_POST['update_bio'])) {
    $new_bio = $_POST['bio'] ?? '';
    $update_query = "UPDATE user SET bio = ? WHERE id_user = ?";
    $update_stmt = $kon->prepare($update_query);
    $update_stmt->bind_param("si", $new_bio, $id_user);
    $update_stmt->execute();
    $user['bio'] = $new_bio;
}

// Proses update avatar
if (isset($_POST['update_avatar'])) {
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../SSC/img/';
        $file_name = 'user_avatar_' . $id_user . '_' . time() . '.' . pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $file_path = "{$upload_dir}{$file_name}";

        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $file_path)) {
            if ($user['avatar'] && file_exists($upload_dir . $user['avatar'])) {
                unlink($upload_dir . $user['avatar']);
            }
            $update_query = "UPDATE user SET avatar = ? WHERE id_user = ?";
            $update_stmt = $kon->prepare($update_query);
            $update_stmt->bind_param("si", $file_name, $id_user);
            $update_stmt->execute();
            $user['avatar'] = $file_name;
        }
    }
}

// Proses update pengaturan akun
if (isset($_POST['update_settings'])) {
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';

    $update_query = "UPDATE user SET telepon = ?";
    $params = [$phone];
    $types = "s";

    if ($password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_query .= ", password = ?";
        $params[] = $hashed_password;
        $types .= "s";
    }

    $update_query .= " WHERE id_user = ?";
    $params[] = $id_user;
    $types .= "i";

    $update_stmt = $kon->prepare($update_query);
    $update_stmt->bind_param($types, ...$params);
    $update_stmt->execute();

    $user['telepon'] = $phone;
}

// Proses hapus favorit
if (isset($_POST['remove_from_favorites']) && isset($_POST['id_menu'])) {
    $id_menu = $_POST['id_menu'];
    $delete_query = "DELETE FROM favorites WHERE id_user = ? AND id_menu = ?";
    $delete_stmt = $kon->prepare($delete_query);
    $delete_stmt->bind_param("ii", $id_user, $id_menu);
    $delete_stmt->execute();
    $delete_stmt->close();
    header("Location: profile.php");
    exit();
}

// Ambil data leaderboard (top 10 users by total spending)
$leaderboard_query = "
    SELECT u.id_user, u.nama_user, u.id_level, COALESCE(SUM(t.total_amount), 0) as total_spent
    FROM user u
    LEFT JOIN transaksi t ON u.id_user = t.id_user
    GROUP BY u.id_user, u.nama_user, u.id_level
    ORDER BY total_spent DESC
    LIMIT 10
";
$leaderboard_result = mysqli_query($kon, $leaderboard_query);
$leaderboard = mysqli_fetch_all($leaderboard_result, MYSQLI_ASSOC);

include '../layout/header.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600&display=swap" rel="stylesheet">

<style>
    .profile-container {
        max-width: 1000px;
        margin: 40px auto;
        background-color: rgba(255, 255, 255, 0.9);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 30px rgba(140, 100, 255, 0.15);
        backdrop-filter: blur(5px);
    }
    
    .profile-header {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
    }
    
    .profile-avatar-container {
        position: relative;
        margin-right: 30px;
    }
    
    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid white;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .profile-avatar:hover {
        opacity: 0.8;
    }
    
    .avatar-edit {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: var(--bg-secondary);
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    .profile-info {
        flex: 1;
        min-width: 250px;
    }
    
    .profile-name {
        font-family: 'Pacifico', cursive;
        color: var(--bg-secondary);
        font-size: 2.2rem;
        margin: 0 0 5px;
        display: flex;
        align-items: center;
    }
    
    .edit-icon {
        margin-left: 10px;
        cursor: pointer;
        color: #aaa;
        transition: all 0.2s ease;
    }
    
    .edit-icon:hover {
        color: var(--bg-secondary);
    }
    
    .profile-username {
        color: #666;
        font-size: 1.1rem;
        margin: 0 0 15px;
    }
    
    .profile-bio {
        color: #555;
        line-height: 1.5;
        position: relative;
        padding-right: 25px;
    }
    
    .bio-edit {
        position: absolute;
        right: 0;
        top: 0;
        color: #aaa;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .bio-edit:hover {
        color: var(--bg-secondary);
    }
    
    .edit-form {
        display: none;
        margin-top: 10px;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-family: 'Quicksand', sans-serif;
        font-size: 1rem;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--bg-secondary);
        box-shadow: 0 0 0 2px rgba(143, 91, 255, 0.2);
    }
    
    .btn {
        padding: 8px 20px;
        border-radius: 8px;
        border: none;
        font-family: 'Quicksand', sans-serif;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .btn-primary {
        background-color: var(--bg-secondary);
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #7a4be6;
    }
    
    .btn-outline {
        background: none;
        border: 1px solid #ddd;
        color: #666;
        margin-left: 10px;
    }
    
    .btn-outline:hover {
        border-color: #aaa;
    }
    
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        display: none;
    }
    
    .modal-content {
        background: white;
        padding: 25px;
        border-radius: 15px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 5px 30px rgba(0,0,0,0.2);
    }
    
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .modal-title {
        font-family: 'Pacifico', cursive;
        color: var(--bg-secondary);
        font-size: 1.5rem;
        margin: 0;
    }
    
    .close-modal {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #666;
    }
    
    .avatar-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 20px;
        display: block;
        border: 3px solid #eee;
    }
    
    .profile-stats {
        display: flex;
        margin: 20px 0;
        gap: 20px;
    }
    
    .stat-item {
        background: rgba(201, 182, 255, 0.2);
        padding: 15px 20px;
        border-radius: 12px;
        text-align: center;
        flex: 1;
        min-width: 100px;
    }
    
    .stat-number {
        font-weight: 700;
        color: var(--bg-secondary);
        font-size: 1.5rem;
        margin-bottom: 5px;
    }
    
    .stat-label {
        color: #666;
        font-size: 0.9rem;
    }
    
    .profile-tabs {
        display: flex;
        border-bottom: 2px solid #eee;
        margin-bottom: 20px;
    }
    
    .profile-perks {
        margin-top: 10px;
        color: #555;
    }
    .profile-perks strong {
        color: #5a2e8d;
    }

    .tab-button {
        padding: 12px 20px;
        background: none;
        border: none;
        font-family: 'Quicksand', sans-serif;
        font-weight: 600;
        color: #666;
        cursor: pointer;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .tab-button.active {
        color: var(--bg-secondary);
    }
    
    .tab-button.active:after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background: var(--bg-secondary);
    }
    
    .profile-section {
        margin-bottom: 30px;
    }
    
    .section-title {
        font-family: 'Pacifico', cursive;
        color: var(--bg-secondary);
        font-size: 1.8rem;
        margin-bottom: 15px;
    }
    
    .logout-btn {
        display: inline-block;
        padding: 10px 20px;
        background: #d9534f;
        color: #fff;
        border: none;
        border-radius: 8px;
        text-align: center;
        text-decoration: none;
        font-family: 'Quicksand', sans-serif;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    
    .logout-btn:hover {
        background: #c9302c;
    }

    .order-card, .review-card, .favorite-card {
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }

    .order-header, .review-header, .favorite-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .order-id, .review-user, .favorite-title {
        font-weight: 600;
        color: #5a2e8d;
        font-size: 1.2rem;
    }

    .order-date, .review-date {
        color: #666;
        font-size: 0.9rem;
    }

    .order-status {
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 0.9rem;
    }

    .status-completed {
        background-color: #d4edda;
        color: #155724;
    }

    .order-items, .review-comment {
        margin-bottom: 10px;
    }

    .order-item, .favorite-item {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
    }

    .item-name, .favorite-name {
        color: #555;
    }

    .item-price, .favorite-price {
        color: #a64dd6;
        font-weight: 700;
    }

    .order-total {
        font-weight: 700;
        color: #5a2e8d;
        text-align: right;
    }

    .review-rating {
        color: #a64dd6;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .favorite-delete {
        background: none;
        border: none;
        color: #d9534f;
        cursor: pointer;
        font-size: 0.9rem;
        margin-left: 10px;
    }

    .favorite-delete:hover {
        color: #c9302c;
    }

    .favorites-container {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 10px;
    }
    
    .leaderboard-section {
        margin-top: 30px;
    }
    .leaderboard-table {
        width: 100%;
        border-collapse: collapse;
    }
    .leaderboard-table th, .leaderboard-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    .leaderboard-table th {
        background-color: #a64dd6;
        color: white;
    }
    .leaderboard-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .leaderboard-table tr:hover {
        background-color: #f1f1f1;
    }
    .rank-number {
        font-weight: bold;
        color: #5a2e8d;
    }

    @media (max-width: 768px) {
        .profile-header {
            flex-direction: column;
            text-align: center;
        }
        
        .profile-avatar-container {
            margin: 0 auto 20px;
        }
        
        .profile-stats {
            flex-wrap: wrap;
        }
        
        .stat-item {
            min-width: calc(50% - 10px);
        }
        
        .profile-name {
            justify-content: center;
        }
        
        .profile-bio {
            padding-right: 0;
            text-align: center;
        }
        
        .bio-edit {
            position: static;
            display: block;
            margin-top: 10px;
        }

        .order-header, .review-header, .favorite-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .order-date, .review-date {
            margin-top: 5px;
        }
    }
</style>

<div class="container">
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar-container">
                <img src="../SSC/img/<?php echo htmlspecialchars($user['avatar'] ?? 'user-avatar.jpg'); ?>" class="profile-avatar" alt="User Avatar" id="userAvatar">
                <div class="avatar-edit" id="editAvatarBtn">
                    <i class="fas fa-camera"></i>
                </div>
            </div>
            <div class="profile-info">
                <h1 class="profile-name" id="profileName">
                    <?php echo htmlspecialchars($user['nama_user']); ?>
                    <i class="fas fa-pencil-alt edit-icon" id="editNameBtn"></i>
                </h1>
                <div class="edit-form" id="nameEditForm">
                    <form method="POST" action="">
                        <div class="form-group">
                            <input type="text" class="form-control" id="nameInput" name="name" value="<?php echo htmlspecialchars($user['nama_user']); ?>">
                        </div>
                        <button type="submit" class="btn btn-primary" name="update_name">Save</button>
                        <button type="button" class="btn btn-outline" id="cancelNameBtn">Cancel</button>
                    </form>
                </div>
                
                <p class="profile-username">@<?php echo htmlspecialchars($user['username']); ?></p>
                
                <div class="profile-bio">
                    <span id="profileBioText"><?php echo htmlspecialchars($user['bio'] ?? 'Tell us about yourself!'); ?></span>
                    <i class="fas fa-pencil-alt bio-edit" id="editBioBtn"></i>
                    <div class="edit-form" id="bioEditForm">
                        <form method="POST" action="">
                            <div class="form-group">
                                <textarea class="form-control" id="bioInput" name="bio" rows="3"><?php echo htmlspecialchars($user['bio'] ?? 'Tell us about yourself!'); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary" name="update_bio">Save</button>
                            <button type="button" class="btn btn-outline" id="cancelBioBtn">Cancel</button>
                        </form>
                    </div>
                </div>
                <div class="profile-perks">
                    <strong>Rank:</strong> <?php echo $user_rank; ?>
                    <?php
                    if ($user_perks['discount'] > 0) {
                        echo "<br><strong>Perk:</strong> " . $user_perks['name'] . " (" . $user_perks['discount'] . "% off)";
                        if (!empty($user_perks['items'])) {
                            echo "<br><strong>Items:</strong> " . implode(", ", $user_perks['items']);
                        }
                    } else {
                        echo "<br><strong>Perk:</strong> No Perks (0% off)";
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <div class="profile-stats">
            <div class="stat-item">
                <div class="stat-number"><?php echo htmlspecialchars($orders_count); ?></div>
                <div class="stat-label">Orders</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo htmlspecialchars(count($favorites)); ?></div>
                <div class="stat-label">Favorites</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo htmlspecialchars($reviews_count); ?></div>
                <div class="stat-label">Reviews</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">Rp <?php echo number_format($total_spent, 0, ',', '.'); ?></div>
                <div class="stat-label">Total Spent</div>
            </div>
        </div>
        
        <div class="profile-tabs">
            <button class="tab-button active">Order History</button>
            <button class="tab-button">Favorites</button>
            <button class="tab-button">Reviews</button>
            <button class="tab-button">Account Settings</button>
            <button class="tab-button">Leaderboard</button>
        </div>
        
        <div class="profile-section">
            <h2 class="section-title">Recent Orders</h2>
            <?php if (count($transaksi) > 0): ?>
                <?php foreach ($transaksi as $row): ?>
                    <?php
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
                    $total = 0;
                    while ($detail = $detailResult->fetch_assoc()) {
                        $subtotal = $detail['harga'] * $detail['jumlah'];
                        $items[] = [
                            'nama_masakan' => $detail['nama_masakan'],
                            'jumlah' => $detail['jumlah'],
                            'subtotal' => $subtotal
                        ];
                        $total += $subtotal;
                    }
                    $detailStmt->close();
                    ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-id">Order #CA-<?php echo htmlspecialchars($row['id_transaksi']); ?></div>
                            <div class="order-date"><?php echo htmlspecialchars($row['tanggal']); ?></div>
                            <div class="order-status status-completed">Completed</div>
                        </div>
                        <div class="order-items">
                            <?php foreach ($items as $item): ?>
                                <div class="order-item">
                                    <div class="item-name"><?php echo htmlspecialchars($item['nama_masakan']); ?> (x<?php echo $item['jumlah']; ?>)</div>
                                    <div class="item-price">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="order-total">Total: Rp <?php echo number_format($total, 0, ',', '.'); ?></div>
                        <?php if ($user_perks['discount'] > 0): ?>
                            <div class="order-total">Discount Applied: <?php echo $user_perks['discount']; ?>% (Rp <?php echo number_format($total * $user_perks['discount'] / 100, 0, ',', '.'); ?>)</div>
                            <div class="order-total">Final Total: Rp <?php echo number_format($total * (1 - $user_perks['discount'] / 100), 0, ',', '.'); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada transaksi terbaru.</p>
            <?php endif; ?>
        </div>
        
        <div class="profile-section" style="display: none;">
            <h2 class="section-title">Your Favorites</h2>
            <div class="favorites-container">
                <?php if (count($favorites) > 0): ?>
                    <?php foreach ($favorites as $favorite): ?>
                        <div class="favorite-card">
                            <div class="favorite-header">
                                <div class="favorite-title"><?php echo htmlspecialchars($favorite['nama_masakan']); ?></div>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="id_menu" value="<?= $favorite['id_menu'] ?>">
                                    <button type="submit" name="remove_from_favorites" class="favorite-delete">Hapus</button>
                                </form>
                            </div>
                            <div class="favorite-item">
                                <div class="favorite-name"><?php echo htmlspecialchars($favorite['deskripsi'] ?? 'Deskripsi tidak tersedia'); ?></div>
                                <div class="favorite-price">Rp <?php echo number_format($favorite['harga'], 0, ',', '.'); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Tidak ada menu favorit.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="profile-section" style="display: none;">
            <h2 class="section-title">Your Reviews</h2>
            <div class="favorites-container">
                <?php if (count($reviews) > 0): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="review-user"><?php echo htmlspecialchars($review['nama_masakan']); ?></div>
                                <div class="review-date"><?php echo htmlspecialchars($review['tanggal']); ?></div>
                            </div>
                            <div class="review-rating">Rating: <?php echo htmlspecialchars($review['rating']); ?>/5</div>
                            <div class="review-comment"><?php echo htmlspecialchars($review['komentar']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Belum ada review yang diberikan.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="profile-section" style="display: none;">
            <h2 class="section-title">Account Settings</h2>
            <form id="accountSettingsForm" method="POST" action="">
                <div class="form-group">
                    <label for="phoneInput">Phone Number</label>
                    <input type="tel" class="form-control" id="phoneInput" name="phone" value="<?php echo htmlspecialchars($user['telepon']); ?>">
                </div>
                <div class="form-group">
                    <label for="passwordInput">Change Password</label>
                    <input type="password" class="form-control" id="passwordInput" name="password" placeholder="Enter new password">
                </div>
                <div class="form-group">
                    <label for="confirmPasswordInput">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmPasswordInput" placeholder="Confirm new password">
                </div>
                <button type="submit" class="btn btn-primary" name="update_settings">Save Changes</button>
            </form>
        </div>
        
        <div class="profile-section leaderboard-section" style="display: none;">
            <h2 class="section-title">Leaderboard</h2>
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Name</th>
                        <th>Level</th>
                        <th>Total Spent</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $rank = 1; foreach ($leaderboard as $entry): ?>
                        <tr>
                            <td class="rank-number"><?php echo $rank; ?></td>
                            <td><?php echo htmlspecialchars($entry['nama_user']); ?></td>
                            <td><?php echo $ranks[$entry['id_level']] ?? 'Moonlit Guest'; ?></td>
                            <td>Rp <?php echo number_format($entry['total_spent'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php $rank++; endforeach; ?>
                    <?php if (empty($leaderboard)): ?>
                        <tr><td colspan="4">No data available.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<div class="modal-overlay" id="avatarModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Change Profile Picture</h3>
            <button class="close-modal" id="closeModalBtn">:D</button>
        </div>
        <img src="../SSC/img/<?php echo htmlspecialchars($user['avatar'] ?? 'user-avatar.jpg'); ?>" class="avatar-preview" id="avatarPreview">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <input type="file" id="avatarUpload" name="avatar" accept="image/*" style="display: none;">
                <button type="button" class="btn btn-primary" id="uploadBtn" style="width: 100%;">Choose Image</button>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" id="saveAvatarBtn" name="update_avatar" style="width: 100%;" disabled>Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            button.classList.add('active');
            document.querySelectorAll('.profile-section').forEach(section => {
                section.style.display = 'none';
            });
            const sections = document.querySelectorAll('.profile-section');
            const index = Array.from(button.parentElement.children).indexOf(button);
            if (sections[index]) {
                sections[index].style.display = 'block';
            }
        });
    });
    
    const editNameBtn = document.getElementById('editNameBtn');
    const profileName = document.getElementById('profileName');
    const nameEditForm = document.getElementById('nameEditForm');
    const nameInput = document.getElementById('nameInput');
    const cancelNameBtn = document.getElementById('cancelNameBtn');
    
    editNameBtn.addEventListener('click', () => {
        profileName.style.display = 'none';
        nameEditForm.style.display = 'block';
        nameInput.focus();
    });
    
    cancelNameBtn.addEventListener('click', () => {
        profileName.style.display = 'flex';
        nameEditForm.style.display = 'none';
    });
    
    const editBioBtn = document.getElementById('editBioBtn');
    const profileBioText = document.getElementById('profileBioText');
    const bioEditForm = document.getElementById('bioEditForm');
    const bioInput = document.getElementById('bioInput');
    const cancelBioBtn = document.getElementById('cancelBioBtn');
    
    editBioBtn.addEventListener('click', () => {
        profileBioText.style.display = 'none';
        editBioBtn.style.display = 'none';
        bioEditForm.style.display = 'block';
        bioInput.focus();
    });
    
    cancelBioBtn.addEventListener('click', () => {
        profileBioText.style.display = 'inline';
        editBioBtn.style.display = 'inline';
        bioEditForm.style.display = 'none';
    });
    
    const editAvatarBtn = document.getElementById('editAvatarBtn');
    const avatarModal = document.getElementById('avatarModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const avatarUpload = document.getElementById('avatarUpload');
    const uploadBtn = document.getElementById('uploadBtn');
    const saveAvatarBtn = document.getElementById('saveAvatarBtn');
    const avatarPreview = document.getElementById('avatarPreview');
    
    editAvatarBtn.addEventListener('click', () => {
        avatarModal.style.display = 'flex';
    });
    
    closeModalBtn.addEventListener('click', () => {
        avatarModal.style.display = 'none';
    });
    
    uploadBtn.addEventListener('click', (e) => {
        e.preventDefault();
        avatarUpload.click();
    });
    
    avatarUpload.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                avatarPreview.src = event.target.result;
                saveAvatarBtn.disabled = false;
            };
            reader.readAsDataURL(file);
        }
    });
    
    const accountSettingsForm = document.getElementById('accountSettingsForm');
    const passwordInput = document.getElementById('passwordInput');
    const confirmPasswordInput = document.getElementById('confirmPasswordInput');
    
    accountSettingsForm.addEventListener('submit', (e) => {
        if (passwordInput.value !== confirmPasswordInput.value) {
            e.preventDefault();
            alert('Passwords do not match!');
            return;
        }
        alert('Account settings saved successfully!');
    });
</script>

<?php include '../layout/footer.php'; ?>