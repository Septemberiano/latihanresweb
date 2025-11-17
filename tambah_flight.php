<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login-page.php");
    exit;
}

// Cek admin
$username = $_SESSION['username'];
$cek = $koneksi->query("SELECT role FROM users WHERE username='$username'");
$user = $cek->fetch_assoc();
if ($user['role'] != 'admin') {
    die("Hanya admin yang boleh nambah penerbangan!");
}

if (isset($_POST['tambah'])) {
    $flight_code     = $_POST['flight_code'];
    $airline         = $_POST['airline'];
    $origin          = $_POST['origin'];
    $destination     = $_POST['destination'];
    $departure_time  = $_POST['departure_time'];
    $arrival_time    = $_POST['arrival_time'];
    $available_seats = $_POST['available_seats'];
    $price           = $_POST['price'];

    $sql = "INSERT INTO flights 
            (flight_code, airline, origin, destination, departure_time, arrival_time, available_seats, price, created_at)
            VALUES 
            ('$flight_code', '$airline', '$origin', '$destination', '$departure_time', '$arrival_time', '$available_seats', '$price', NOW())";

    if ($koneksi->query($sql)) {
        header("Location: dashboard.php?tambah=success");
        exit;
    } else {
        echo "Gagal: " . $koneksi->error;
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
    <title>Tambah Penerbangan Baru</title>
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-header text-white text-center " style="background-color: #667EEA;;">
                        <h5>Tambah Penerbangan Baru</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Kode Penerbangan <small class="text-muted">(misal: GA101)</small></label>
                                <input type="text" name="flight_code" class="form-control" placeholder="misal: GA101" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Maskapai</label>
                                <input type="text" name="airline" class="form-control" placeholder="Garuda Indonesia" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kota Keberangkatan</label>
                                <input type="text" name="origin" class="form-control" placeholder="Jakarta" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kota Kedatangan</label>
                                <input type="text" name="destination" class="form-control" placeholder="Bali" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Waktu Keberangkatan</label>
                                <input type="datetime-local" name="departure_time" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Waktu Kedatangan</label>
                                <input type="datetime-local" name="arrival_time" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Total Kursi</label>
                                <input type="number" name="available_seats" class="form-control" value="150" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Harga Tiket (Rp)</label>
                                <input type="number" name="price" class="form-control" placeholder="1000000" required>
                            </div>

                            <button type="submit" name="tambah" class="btn btn-success w-100 fw-bold">
                                Tambah Penerbangan
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="dashboard.php" class="text-decoration-none text-muted">
                                ← Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>