<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//jika belum login sebagai karyawan, redirect ke login karyawan
if (!isset($_SESSION['login_type']) || $_SESSION['login_type'] !== 'karyawan') {
    header("Location: /TugasAkhir/login/loginkaryawan.php");
    exit();
}

//daftar halaman yang diizinkan per bagian
$allowed_pages = [
    'A' => ['*'], // Admin bisa akses semua
    'B' => ['indegs.php', 'transaksi.php', 'receipt.php', 'report.php'], // Kasir
    'C' => ['indegs.php', 'menu.php', 'stok.php', 'pesanan.php'], // Dapur
    'D' => ['indegs.php', 'menu.php', 'stok.php', 'report.php'] // Pelayan
];

//dapatkan halaman saat ini
$current_page = basename($_SERVER['PHP_SELF']);

//jika bukan admin dan halaman tidak diizinkan, redirect ke indegs.php
if ($_SESSION['bagian'] !== 'A' && !in_array($current_page, $allowed_pages[$_SESSION['bagian']])) {
    header("Location: /TugasAkhir/pages/indegs.php");
    exit();
}
?>