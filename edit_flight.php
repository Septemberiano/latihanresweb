<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login-page.php");
    exit;
}

// Cek role admin
$username = $_SESSION['username'];
$cek = $koneksi->query("SELECT role FROM users WHERE username='$username'");
$user = $cek->fetch_assoc();
if ($user['role'] != 'admin') {
    die("Akses ditolak. Hanya Admin yang boleh edit penerbangan.");
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = $_GET['id'];

// Ambil data flight
$sql = "SELECT * FROM flights WHERE id = '$id'";
$result = $koneksi->query($sql);
$flight = $result->fetch_assoc();

if (!$flight) {
    die("Penerbangan tidak ditemukan!");
}

// Proses update
if (isset($_POST['simpan'])) {
    $flight_code     = $_POST['flight_code'];
    $airline         = $_POST['airline'];
    $origin          = $_POST['origin'];
    $destination     = $_POST['destination'];
    $departure_time  = $_POST['departure_time'];
    $arrival_time    = $_POST['arrival_time'];
    $available_seats = $_POST['available_seats'];
    $price           = $_POST['price'];

    $update = "UPDATE flights SET 
                flight_code = '$flight_code',
                airline = '$airline',
                origin = '$origin',
                destination = '$destination',
                departure_time = '$departure_time',
                arrival_time = '$arrival_time',
                available_seats = '$available_seats',
                price = '$price'
                WHERE id = '$id'";

    if ($koneksi->query($update)) {
        header("Location: dashboard.php?update=success");
        exit;
    } else {
        echo "Error: " . $koneksi->error;
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
    <title>Edit Penerbangan</title>
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-custom-blue">
        <div class="container-fluid">
            <a class="navbar-brand text-light" href="#">Nigga Airline</a>
            <a href="dashboard.php" class="btn btn-light btn-sm">← Kembali ke Dashboard</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Edit Data Penerbangan</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Kode Penerbangan (Read-only)</label>
                                <input type="text" class="form-control" value="<?= $flight['flight_code'] ?>" readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Maskapai</label>
                                <input type="text" name="airline" class="form-control" value="<?= $flight['airline'] ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kota Keberangkatan</label>
                                <input type="text" name="origin" class="form-control" value="<?= $flight['origin'] ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kota Kedatangan</label>
                                <input type="text" name="destination" class="form-control" value="<?= $flight['destination'] ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Waktu Keberangkatan</label>
                                <input type="datetime-local" name="departure_time" class="form-control" value="<?= str_replace(' ', 'T', $flight['departure_time']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Waktu Kedatangan</label>
                                <input type="datetime-local" name="arrival_time" class="form-control" value="<?= str_replace(' ', 'T', $flight['arrival_time']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Total Kursi</label>
                                <input type="number" name="available_seats" class="form-control" value="<?= $flight['available_seats'] ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Harga Tiket (Rp)</label>
                                <input type="number" name="price" class="form-control" value="<?= $flight['price'] ?>" required>
                            </div>

                            <button type="submit" name="simpan" class="btn btn-warning w-100 text-white fw-bold">
                                Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>