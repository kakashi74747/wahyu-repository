<?php
include '../konkon.php';
include '../layout/header.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($kon, $_POST['nama']);
    $username = mysqli_real_escape_string($kon, $_POST['username']);
    $password = mysqli_real_escape_string($kon, $_POST['password']);
    $id_level = mysqli_real_escape_string($kon, $_POST['id_level']);

    // Insert data into the database
    $query = "INSERT INTO user(nama_user, username, password, id_level) 
              VALUES ('$nama', '$username', '$password', '$id_level')";
    $insert = mysqli_query($kon, $query);
    if ($insert) {
        echo "<script>alert('Pelanggan berhasil ditambahkan!'); window.location.href='../pages/pelanggan.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan pelanggan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Tambah Pelanggan</h1>
        <form action="tambahpelanggan.php" method="POST" class="shadow p-4 rounded bg-light">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama:</label>
                <input type="text" id="nama" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username:</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="id_level" class="form-label">Id Level:</label>
                <input type="number" id="id_level" name="id_level" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Pelanggan</button>
            <a href="../pages/pelanggan.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>

<?php include '../layout/footer.php'; ?>