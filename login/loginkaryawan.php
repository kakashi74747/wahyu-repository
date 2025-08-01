<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../konkon.php';

if (isset($_POST['login'])) {
    $idKaryawan = $_POST['nama'];
    $bagian = $_POST['bagian'];

    //cek idKaryawan dan bagian menggunakan prepared statement? (idek what this is😭💀)
    $query = "SELECT * FROM data_karyawan WHERE idKaryawan = ? AND bagian = ?";
    $stmt = $kon->prepare($query);
    $stmt->bind_param("ss", $idKaryawan, $bagian);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $karyawan = $result->fetch_assoc();
        
    //akanmenyimpan data ke sesi
    $_SESSION['idKaryawan'] = $karyawan['idKaryawan'];
    $_SESSION['nama'] = $karyawan['nama'];
    $_SESSION['bagian'] = $karyawan['bagian'];
    $_SESSION['login_type'] = 'karyawan';
    $_SESSION['username'] = $karyawan['nama'];
    $_SESSION['logged_in'] = true; // Pastikan ini ada

    //redirect berdasarkan bagian
    $redirect_map = [
        'A' => 'pelanggan.php', //admin
        'B' => 'transaksi.php', //kasir
        'C' => 'menu.php',  //dapur
        'D' => 'report.php' //pelayan
    ];
    $redirect = $redirect_map[$karyawan['bagian']] ?? 'indegs.php';
    header("Location: ../pages/$redirect");
    exit();
    } else {
        $error = "ID Karyawan atau bagian salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Karyawan | Cafe Cassie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #C9B6FF;
            --bg-secondary: #8F5BFF;
            --text-main: #1F1F1F;
        }
        
        body {
            background: linear-gradient(135deg, #F9F5FF, #F5EBFF);
            min-height: 100vh;
            font-family: 'Quicksand', sans-serif;
        }
        
        .login-container {
            max-width: 450px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(140, 100, 255, 0.2);
        }
        
        .login-header {
            background-color: var(--bg-secondary);
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .login-body {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            backdrop-filter: blur(5px);
        }
        
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #eee;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--bg-primary);
            box-shadow: 0 0 0 3px rgba(201, 182, 255, 0.3);
        }
        
        .btn-login {
            background-color: var(--bg-secondary);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-login:hover {
            background-color: #7a4be6;
            transform: translateY(-2px);
        }
        
        .form-select {
            border-radius: 10px;
            padding: 12px 15px;
        }
        
        .role-icon {
            font-size: 1.2rem;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="login-container">
            <div class="login-header">
                <h2><i class="fas fa-user-tie"></i> Login Karyawan</h2>
                <p class="mb-0">Cafe Cassie</p>
            </div>
            
            <div class="login-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Id Karyawan</label>
                        <input type="text" class="form-control" id="nama" name="nama" required autofocus>
                    </div>
                    
                    <div class="mb-4">
                        <label for="bagian" class="form-label">Bagian</label>
                        <select class="form-select" id="bagian" name="bagian" required>
                            <option value="" selected disabled>Pilih Bagian</option>
                            <option value="A"><i class="fas fa-user-shield role-icon"></i> Admin</option>
                            <option value="B"><i class="fas fa-cash-register role-icon"></i> Kasir</option>
                            <option value="C"><i class="fas fa-utensils role-icon"></i> Dapur</option>
                            <option value="D"><i class="fas fa-concierge-bell role-icon"></i> Pelayan</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="login" class="btn btn-login text-white w-100">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>