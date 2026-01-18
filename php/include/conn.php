<?php
//first '' -> Server ip address
//second '' -> username for login to database
//third '' -> password for login to database
//fourth '' -> database name

$localhost = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'green_and_earn';

$dbConn = mysqli_connect($localhost, $user, $pass, $dbName);

if (mysqli_connect_errno()) {
    die('<script>alert("Connection failed: Please check your SQL connection!");</script>');
}

// echo"<script>alert('Successfully connect to Green And Earn!');</script>";

?>