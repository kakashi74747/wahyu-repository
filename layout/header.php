<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ob_start();
$page_title = "Cafe Cassie";

// Periksa apakah user atau karyawan sudah login
if (!isset($_SESSION['username']) || !isset($_SESSION['login_type']) || !in_array($_SESSION['login_type'], ['user', 'karyawan'])) {
    header("Location: /TugasAkhir/login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> ✧ meow :3</title>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&family=Quicksand:wght@500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
      :root {
        --bg-primary: #C9B6FF;
        --bg-secondary: #8F5BFF;
        --dark-space: #1A1A2E;
        --text-main: #1F1F1F;
        --font-judul: 'Yuji Syuku', serif;
        --font-body: 'Poppins', sans-serif;
      }

      body {
        margin: 0;
        background: linear-gradient(to bottom right, #EADFFF, #F9F4FF);
        font-family: var(--font-body);
        color: var(--text-main);
      }

      .navbar-cassie {
        background-color: white;
        border-radius: 20px;
        margin: 15px auto;
        padding: 12px 24px;
        width: 95%;
        box-shadow: 0 8px 20px rgba(140, 100, 255, 0.15);
        display: flex;
        align-items: center;
      }

      .navbar-brand {
        font-family: var(--font-judul);
        font-size: 2rem;
        color: var(--bg-secondary);
        text-shadow: 1px 1px 2px rgba(140, 100, 255, 0.3);
        display: flex;
        align-items: center;
      }

      .profile-btn {
        margin-right: 15px;
        padding: 8px;
        border-radius: 90%;
        background-color: var(--bg-primary);
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        transition: 0.2s ease-in-out;
        margin-left: -50px; /* Geser ke kiri sedikit */
      }

      .profile-btn:hover {
        background-color: var(--bg-secondary);
        transform: scale(1.1);
      }

      .nav-link {
        color: var(--dark-space);
        margin: 0 10px;
        font-weight: 500;
        transition: 0.2s ease-in-out;
      }

      .nav-link:hover {
        color: var(--bg-secondary);
        transform: scale(1.1);
      }

      .cart-btn {
        margin-left: 15px;
        padding: 8px;
        border-radius: 50%;
        background-color: var(--bg-primary);
        color: white;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        transition: 0.2s ease-in-out;
      }

      .cart-btn:hover {
        background-color: var(--bg-secondary);
        transform: scale(1.1);
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

      .dropdown-menu {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(140, 100, 255, 0.15);
      }

      .dropdown-item {
        color: var(--dark-space);
        font-weight: 500;
        transition: 0.2s ease-in-out;
      }

      .dropdown-item:hover {
        color: var(--bg-secondary);
        background-color: #f9e5ff;
      }

      @media (max-width: 768px) {
        .navbar-brand {
          font-size: 1.5rem;
        }
        .profile-btn {
          padding: 6px;
          width: 30px;
          height: 30px;
          margin-left: -8px;
        }
        .cart-btn {
          padding: 6px;
          width: 30px;
          height: 30px;
          margin-left: 5px;
        }
        .nav-link, .dropdown-item {
          margin: 5px 0;
        }
      }
    </style>
    <script src="../SSC/js/interactive.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-cassie">
            <div class="container">
                <div class="d-flex align-items-center">
                    <a href="<?php echo $_SESSION['login_type'] === 'user' ? '/TugasAkhir/login/profile.php' : '/TugasAkhir/login/profilekaryawan.php'; ?>" class="profile-btn">
                        <i class="bi bi-person-circle"></i>
                    </a>
                    <a class="navbar-brand" href="/TugasAkhir/pages/indegs.php">
                        <img src="../SSC/img/castoricepfp.jpg" class="rounded-circle me-3.5" style="width: 36px; height: 36px; object-fit: cover;" alt="Logo">
                        Cafe Cassie
                    </a>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCafe">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarCafe">
                    <ul class="navbar-nav ms-auto">
                        <?php if ($_SESSION['login_type'] === 'user'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/TugasAkhir/pages/indegs.php"><i class="fas fa-home"></i> Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/TugasAkhir/pages/pesanan.php"><i class="fas fa-clipboard-list"></i> Pesanan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/TugasAkhir/pages/history.php"><i class="fas fa-history"></i> History Order</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/TugasAkhir/pages/gallery.php"><i class="fas fa-images"></i> Gallery</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/TugasAkhir/login/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                            </li>
                        <?php elseif ($_SESSION['login_type'] === 'karyawan'):
                            $bagian = $_SESSION['bagian'];
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/TugasAkhir/pages/indegs.php"><i class="fas fa-home"></i> Home</a>
                            </li>
                            <?php if ($bagian === 'A'): // Hanya Admin yang bisa melihat menu "Admin" ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-users"></i> Admin
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                        <li><a class="dropdown-item" href="/TugasAkhir/pages/pelanggan.php">Pelanggan</a></li>
                                        <li><a class="dropdown-item" href="/TugasAkhir/pages/kasir.php">Karyawan</a></li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            <?php if ($bagian === 'A' || $bagian === 'B'): // Admin dan Kasir ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/TugasAkhir/pages/transaksi.php"><i class="fas fa-file-alt"></i> Transaksi</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/TugasAkhir/pages/report.php"><i class="fas fa-chart-bar"></i> Report</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($bagian === 'A' || $bagian === 'C' || $bagian === 'D'): // Admin, Dapur, Pelayan ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/TugasAkhir/pages/menu.php"><i class="fas fa-utensils"></i> Menu</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($bagian === 'C' || $bagian === 'D'): // Dapur dan Pelayan ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/TugasAkhir/pages/stok.php"><i class="fas fa-warehouse"></i> Stock</a>
                                </li>
                            <?php endif; ?>
                            <?php if ($bagian === 'D'): // Hanya Pelayan ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/TugasAkhir/pages/report.php"><i class="fas fa-chart-bar"></i> Report</a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/TugasAkhir/pages/gallery.php"><i class="fas fa-images"></i> Gallery</a>
                            </li>
                            <?php if ($bagian !== 'A'): // Sembunyikan Logout untuk Admin (bagian A) ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/TugasAkhir/login/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
<?php
?>