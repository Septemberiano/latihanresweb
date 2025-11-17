<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login-page.php");
    exit;
}

if (!isset($_GET['flight_id'])) {
    die("Flight tidak ditemukan!");
}

$flight_id = intval($_GET['flight_id']);

// Ambil data flight
$stmt = $koneksi->prepare("SELECT * FROM flights WHERE id = ?");
$stmt->bind_param("i", $flight_id);
$stmt->execute();
$result = $stmt->get_result();
$flight = $result->fetch_assoc();

if (!$flight) {
    die("Data penerbangan tidak valid atau sudah dihapus.");
}

// Proses booking saat tombol ditekan
if (isset($_POST['confirm'])) {
    $passenger_name = trim($_POST['passenger']);
    $id_number      = trim($_POST['id_number']);
    $username       = $_SESSION['username'];

    // Ambil user_id dari tabel users
    $stmt_user = $koneksi->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
    $stmt_user->bind_param("s", $username);
    $stmt_user->execute();
    $user_result = $stmt_user->get_result();
    $user_row    = $user_result->fetch_assoc();

    if (!$user_row) {
        die("Error: User tidak ditemukan di database.");
    }
    $user_id = $user_row['id'];

    // Generate kode booking unik
    $booking_code = "BK" . date("YmdHis") . rand(100, 999);

    // Insert ke tabel bookings
    $insert = $koneksi->prepare("
        INSERT INTO bookings 
            (booking_code, username, user_id, flight_id, passenger_name, id_number, status, booking_date)
        VALUES 
            (?, ?, ?, ?, ?, ?, 'booked', NOW())
    ");

    $insert->bind_param(
        "ssiiss",
        $booking_code,
        $username,
        $user_id,
        $flight_id,
        $passenger_name,
        $id_number
    );

    if ($insert->execute()) {
        // Kurangi available_seats di tabel flights (opsional, tapi bagus)
        $koneksi->query("UPDATE flights SET available_seats = available_seats - 1 WHERE id = $flight_id");

        header("Location: dashboard.php?success=1");
        exit;
    } else {
        echo "<div class='alert alert-danger text-center'>Gagal melakukan booking: " . $insert->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pemesanan Tiket - Airline Booking</title>
    <link rel="stylesheet" href="global.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand text-white fw-bold" href="#">Airline Booking</a>
        <a href="dashboard.php" class="btn btn-outline-light">Kembali ke Dashboard</a>
    </div>
</nav>

<div class="container mt-5">
    <div class="card mx-auto shadow-lg" style="max-width: 600px;">
        <div class="card-body p-4">
            <h4 class="text-center mb-4 text-primary">Konfirmasi Pemesanan Tiket</h4>
            <hr>

            <div class="bg-light p-3 rounded border mb-4">
                <h6 class="fw-bold text-primary"><?= htmlspecialchars($flight['flight_code']) ?> - <?= htmlspecialchars($flight['airline']) ?></h6>
                <p class="mb-1"><strong>Rute:</strong> <?= htmlspecialchars($flight['origin']) ?> → <?= htmlspecialchars($flight['destination']) ?></p>
                <p class="mb-1"><strong>Berangkat:</strong> <?= date("d M Y H:i", strtotime($flight['departure_time'])) ?></p>
                <p class="mb-1"><strong>Tiba:</strong> <?= date("d M Y H:i", strtotime($flight['arrival_time'])) ?></p>
                <p class="mb-1 text-danger fw-bold"><strong>Harga:</strong> Rp <?= number_format($flight['price'], 0, ',', '.') ?></p>
                <p class="mb-0"><strong>Kursi tersedia:</strong> <?= $flight['available_seats'] ?></p>
            </div>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Penumpang</label>
                    <input type="text" name="passenger" class="form-control" required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Nomor Identitas (KTP/Paspor)</label>
                    <input type="text" name="id_number" class="form-control" required>
                </div>

                <button type="submit" name="confirm" class="btn btn-primary btn-lg w-100">
                    Konfirmasi & Pesan Sekarang
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>