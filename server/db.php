<?php
// Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'technokeeper_database';

$connect = mysqli_connect($host, $user, $pass, $db);

if(!$connect){
    die(mysqli_connect_error());
}
?>
