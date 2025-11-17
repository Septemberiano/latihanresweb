<?php
$hostname = "localhost";
$username = "root";
$pass = "";
$db = "airline_booking";
$koneksi = mysqli_connect($hostname,$username,$pass,$db);
if(!$koneksi){
 die("koneksi gagal".mysqli_connect_error());
}
?>