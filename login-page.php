<?php
include 'koneksi.php';

session_start();
if (isset($_SESSION['signup_success'])) {
    echo "<script>alert('" . $_SESSION['signup_success'] . "');</script>";
    unset($_SESSION['signup_success']);
}

if (isset($_POST['kirim'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    if (empty($username) || empty($password)) {
        echo "<script>alert('Password / Nama Masih Kosong Atau Tidak Ditemukan')</script>";
    } else {
        $stmt = $koneksi->prepare("SELECT username,password FROM users WHERE username = ? AND password = ? ");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();

        $resut = $stmt->get_result();
        if ($resut->num_rows > 0) {
            $_SESSION['username'] = $username;

            header("Location: dashboard.php?");
            exit;
        } else {
            echo "<script>alert('Username atau Password salah')</script>";
        }
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
    <title>Login </title>
</head>

<body class="bg-gradient-custom">

    <div class="card " style="width: 20rem; margin-top:100px; margin-left :600px;">
        <div class="card-body">
            <center>
                <h5 class="card-title">Login</h5>
            </center>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Username</label>
                    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="username">
                </div>
                <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" name="password">
                </div>
                <center>
                    <p>Belum Punya akun?<a href="signup.php">Daftar Disini</a></p>
                    <button type="submit" class="btn btn-primary" name="kirim">Submit</button>
                </center>
            </form>
        </div>
    </div>
</body>

</html>