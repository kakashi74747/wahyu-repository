<?php
session_start();
include '../konkon.php';

if (isset($_POST['register'])) {
    $nama = $_POST['nama_user'];
    $username = $_POST['username'];
    $password = $_POST['password']; // Tidak di-hash
    
    // Cek username
    $cek = mysqli_query($kon, "SELECT username FROM user WHERE username = '$username'");
    
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Username sudah digunakan!');</script>";
    } else {
        // Default level (1 = user biasa)
        $id_level = 1;
        
        $insert = mysqli_query($kon, "INSERT INTO user (nama_user, username, password, id_level) 
                  VALUES ('$nama', '$username', '$password', '$id_level')");
        
        if ($insert) {
            echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Registrasi gagal: ".mysqli_error($kon)."');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | Cafe Cassie</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #C9B6FF;
            --bg-secondary: #8F5BFF;
            --text-main: #1F1F1F;
            --text-light: #666;
        }
        
        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(to bottom right, #EADFFF, #F9F4FF);
            font-family: 'Quicksand', sans-serif;
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 30px rgba(140, 100, 255, 0.15);
            text-align: center;
            box-sizing: border-box;
        }
        
        .login-header {
            margin-bottom: 30px;
        }
        
        .login-title {
            font-family: 'Pacifico', cursive;
            color: var(--bg-secondary);
            font-size: 2.2rem;
            margin: 0 0 10px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.1);
        }
        
        .login-subtitle {
            color: var(--text-light);
            font-size: 1rem;
            margin: 0;
        }
        
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 30px;
        }
        
        .input-group {
            position: relative;
            width: 100%;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--bg-secondary);
            font-size: 1rem;
        }
        
        .form-input {
            width: 100%;
            padding: 13px 15px 13px 45px;
            border: 2px solid #eee;
            border-radius: 10px;
            font-family: 'Quicksand', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--bg-secondary);
            box-shadow: 0 0 0 3px rgba(143, 91, 255, 0.2);
        }
        
        .login-button {
            background-color: var(--bg-secondary);
            color: white;
            border: none;
            padding: 13px;
            border-radius: 10px;
            font-family: 'Quicksand', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            width: 100%;
        }
        
        .login-button:hover {
            background-color: #7a4be6;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(143, 91, 255, 0.3);
        }
        
        .login-footer {
            margin-top: 25px;
            color: var(--text-light);
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .login-link {
            color: var(--bg-secondary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .login-link:hover {
            color: #7a4be6;
            text-decoration: underline;
        }
        
        .error-message {
            color: #ff4444;
            font-size: 0.9rem;
            margin-top: -15px;
            margin-bottom: 10px;
            text-align: left;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .login-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1 class="login-title">Buat Akun Baru</h1>
            <p class="login-subtitle">Daftar ke Cafe Cassie</p>
        </div>
        
        <form method="POST" class="login-form">
            <div class="input-group">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="nama_user" class="form-input" placeholder="Nama Lengkap" required>
            </div>
            
            <div class="input-group">
                <i class="fas fa-at input-icon"></i>
                <input type="text" name="username" class="form-input" placeholder="Username" required>
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password" class="form-input" placeholder="Password" required>
            </div>
            
            <button type="submit" name="register" class="login-button">Daftar</button>
        </form>
        
        <div class="login-footer">
            Sudah punya akun? <a href="login.php" class="login-link">Masuk di sini</a>
        </div>
    </div>
</body>
</html>