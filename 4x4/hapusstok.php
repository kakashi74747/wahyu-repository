<?php
include '../layout/header.php';
include '../konkon.php';
include '../login/auth_check_karyawan.php';

// Cek apakah user sudah login
if (!isset($_SESSION['idKaryawan'])) {
    header("Location: ../login/loginkaryawan.php");
    exit();
}
// Cek akses hanya untuk bagian C (Dapur) dan D (Pelayan)
if ($_SESSION['bagian'] !== 'C' && $_SESSION['bagian'] !== 'D') {
    echo "<script>alert('Anda tidak memiliki akses ke halaman ini!');</script>";
    echo "<script>window.location.href='/TugasAkhir/pages/indegs.php';</script>";
    exit();
}

// Proses hapus stok bahan baku
if (isset($_GET['id_menu']) && isset($_GET['id_bahan'])) {
    $id_menu = intval($_GET['id_menu']);
    $id_bahan = intval($_GET['id_bahan']);

    $delete_query = "DELETE FROM menu_bahan WHERE id_menu = ? AND id_bahan = ?";
    $delete_stmt = $kon->prepare($delete_query);
    $delete_stmt->bind_param("ii", $id_menu, $id_bahan);
    $delete_stmt->execute();
    $delete_stmt->close();

    echo "<script>alert('Bahan baku berhasil dihapus!'); window.location.href='/TugasAkhir/pages/stok.php?id_menu=$id_menu';</script>";
    exit();
} else {
    echo "<script>alert('Parameter tidak lengkap!'); window.location.href='/TugasAkhir/pages/stok.php';</script>";
    exit();
}
?>