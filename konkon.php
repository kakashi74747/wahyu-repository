<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "cafe_akmal";

$kon = mysqli_connect($host, $user, $pass, $db);

if ($kon->connect_error) {
    die("Koneksi gagal: " . $kon->connect_error);
}
?>