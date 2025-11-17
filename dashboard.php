<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login-page.php");
    exit;
}

$username = $_SESSION['username'];

// Cek role pake cara paling gampang
$cek = $koneksi->query("SELECT role FROM users WHERE username='$username'");
$user = $cek->fetch_assoc();
$role = $user['role'] ?? 'user';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Dashboard - Nigga Airline</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-custom-blue">
        <div class="container-fluid">
            <a class="navbar-brand text-light" href="#">Nigga Airline</a>
            <div class="d-flex">
                <h5 class="text-light me-4">Selamat datang <?= $username ?> (<?= ucfirst($role) ?>)</h5>
                <a href="logout.php" class="btn btn-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">

        <!-- ADMIN: Manajemen Penerbangan -->
        <?php if ($role == 'admin'): ?>
            <h4 class="mb-3 text-primary">Manajemen Penerbangan</h4>
            <a href="tambah_flight.php" class="btn btn-success mb-3">+ Tambah Penerbangan</a>
            <div class="row">
                <?php
                $flight = $koneksi->query("SELECT * FROM flights ORDER BY departure_time ASC");
                while ($f = $flight->fetch_assoc()):
                ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6><strong><?= $f['flight_code'] ?> - <?= $f['airline'] ?></strong></h6>
                                <p>Rute: <?= $f['origin'] ?> → <?= $f['destination'] ?></p>
                                <p>Berangkat: <?= $f['departure_time'] ?></p>
                                <p>Harga: Rp <?= number_format($f['price'], 0, ',', '.') ?></p>
                                <p>Status: <?= $f['available_seats'] > 0 ? 'Tersedia' : 'Penuh' ?></p>
                            </div>
                            <div class="card-footer text-center">
                                <a href="edit_flight.php?id=<?= $f['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="hapus_flight.php?id=<?= $f['id'] ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin hapus?')">Hapus</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <hr>
        <?php endif; ?>

        <!-- Daftar Penerbangan buat Pesan -->
        <h4>Pesan Tiket Pesawat</h4>
        <div class="row mt-3">
            <?php
            $sql = "SELECT * FROM flights WHERE available_seats > 0 ORDER BY departure_time ASC";
            $result = $koneksi->query($sql);
            while ($row = $result->fetch_assoc()):
            ?>
                <div class="col-md-4 mb-4">
                    <div class="card p-3 shadow-sm">
                        <h6><strong><?= $row['flight_code'] ?> - <?= $row['airline'] ?></strong></h6>
                        <p>Rute: <?= $row['origin'] ?> → <?= $row['destination'] ?></p>
                        <p>Keberangkatan: <?= $row['departure_time'] ?></p>
                        <p>Kedatangan: <?= $row['arrival_time'] ?></p>
                        <p>Kursi Tersedia: <strong><?= $row['available_seats'] ?></strong></p>
                        <p>Harga: <strong>Rp <?= number_format($row['price'], 0, ',', '.') ?></strong></p>
                        <a href="pesantiket.php?flight_id=<?= $row['id'] ?>" class="btn btn-primary">
                            Pesan Tiket
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Riwayat Pemesanan -->
        <h4 class="mt-5">Riwayat Pemesanan <?= $role == 'admin' ? 'Semua User' : 'Saya' ?></h4>
        <table class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>Kode Booking</th>
                    <th>Penerbangan</th>
                    <th>Penumpang</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Tanggal Booking</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($role == 'admin') {
                    $booking = $koneksi->query("SELECT b.*, f.flight_code, f.origin, f.destination, f.price as harga_flight 
                                                FROM bookings b 
                                                JOIN flights f ON b.flight_id = f.id 
                                                ORDER BY b.booking_date DESC");
                } else {
                    $booking = $koneksi->query("SELECT b.*, f.flight_code, f.origin, f.destination, f.price as harga_flight 
                                                FROM bookings b 
                                                JOIN flights f ON b.flight_id = f.id 
                                                WHERE b.username = '$username' 
                                                ORDER BY b.booking_date DESC");
                }

                while ($b = $booking->fetch_assoc()):
                ?>
                    <tr>
                        <td><strong><?= $b['booking_code'] ?></strong></td>
                        <td><?= $b['flight_code'] ?> (<?= $b['origin'] ?> → <?= $b['destination'] ?>)</td>
                        <td><?= $b['passenger_name'] ?></td>
                        <td>Rp <?= number_format($b['harga_flight'], 0, ',', '.') ?></td>
                        <td>
                            <?php if ($b['status'] == 'booked'): ?>
                                <span class="badge bg-success">Confirmed</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Cancelled</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $b['booking_date'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>
</body>

</html>