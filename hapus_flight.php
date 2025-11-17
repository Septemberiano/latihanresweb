<?php
session_start();
include 'koneksi.php';

// Cek login + role admin
if (!isset($_SESSION['username'])) {
    header("Location: login-page.php");
    exit;
}

$username = $_SESSION['username'];
$cek = $koneksi->query("SELECT role FROM users WHERE username='$username'");
$user = $cek->fetch_assoc();

if ($user['role'] != 'admin') {
    die("Akses ditolak! Hanya Admin yang boleh menghapus penerbangan.");
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = $_GET['id'];

// Ambil data flight buat ditampilin di konfirmasi
$sql = "SELECT flight_code, airline, origin, destination FROM flights WHERE id = '$id'";
$result = $koneksi->query($sql);
$flight = $result->fetch_assoc();

if (!$flight) {
    die("Penerbangan tidak ditemukan!");
}

// Kalau konfirmasi "OK" di klik
if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    $delete = "DELETE FROM flights WHERE id = '$id'";
    if ($koneksi->query($delete)) {
        header("Location: dashboard.php?hapus=success");
        exit;
    } else {
        die("Gagal menghapus: " . $koneksi->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Hapus Penerbangan</title>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg bg-custom-blue">
    <div class="container-fluid">
        <a class="navbar-brand text-light" href="#">Airline Booking System</a>
        <a href="dashboard.php" class="btn btn-light btn-sm">← Kembali</a>
    </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-danger text-white text-center">
                    <h4>Konfirmasi Hapus Penerbangan</h4>
                </div>
                <div class="card-body text-center">
                    <h5 class="text-danger">Yakin ingin menghapus penerbangan ini?</h5>
                    <div class="mt-4 p-4 bg-light rounded">
                        <p><strong>Kode:</strong> <?= $flight['flight_code'] ?></p>
                        <p><strong>Maskapai:</strong> <?= $flight['airline'] ?></p>
                        <p><strong>Rute:</strong> <?= $flight['origin'] ?> → <?= $flight['destination'] ?></p>
                    </div>
                    <div class="mt-4">
                        <h6 class="text-danger">Data akan dihapus permanen!</h6>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="hapus_flight.php?id=<?= $id ?>&confirm=yes" class="btn btn-danger btn-lg px-4">
                        OK, Hapus
                    </a>
                    <a href="dashboard.php" class="btn btn-secondary btn-lg px-4">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>



</body>
</html>