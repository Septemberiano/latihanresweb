<?php
include 'koneksi.php';
if (isset($_POST['kirim'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    if (empty($name) || empty($username) || empty($password) || empty($confirm)) {
        echo "<script> alert('Semua field wajib diisi!')</script>";
        exit;
    }
    $stmt = $koneksi->prepare("SELECT id FROM users WHERE email = ? OR username =?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('email/username sudah terpakai')</script>";
        exit;
    }
    $stmt = $koneksi->prepare("INSERT INTO users (name,username,email,password) VALUES (?,?,?,?)");
    $stmt->bind_param("ssss", $name, $username, $email, $password);
    if ($stmt->execute()) {
        $_SESSION['signup_success'] = "Akun berhasil dibuat! Silakan login.";
        header("Location: login-page.php");
        exit;
    } else {
        echo "<script>alert('gagal mendaftarkan akun')</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="global.css">
    <title>Signup </title>
</head>

<body class="bg-gradient-custom">

    <div class="card " style="width: 20rem; margin-top:100px; margin-left :600px;">
        <div class="card-body">
            <center>
                <h5 class="card-title">Signup</h5>
            </center>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="Nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" aria-describedby="Masukan Nama" name="name">
                </div>
                <div class="mb-3">
                    <label for="Nama" class="form-label">Username</label>
                    <input type="text" class="form-control" aria-describedby="Masukan Username" name="username">
                </div>
                <div class="mb-3">
                    <label for="Email" class="form-label">Email</label>
                    <input type="email" class="form-control" aria-describedby="Masukan Email" name="email">
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password">
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword3" class="form-label">Konfrimasi Password</label>
                    <input type="password" class="form-control" name="confirm_password">
                </div>
                <center>
                    <p>Sudah punya akun?<a href="login-page.php">Login Disini</a></p>
                    <button type="submit" class="btn btn-primary" name="kirim">Submit</button>
                </center>
            </form>
        </div>
    </div>
</body>

</html>