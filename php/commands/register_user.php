<?php
echo "<script>alert('Register Service Connected')</script>";
$username = $_POST["username"];
$password = $_POST["password"];
$password_confirm = $_POST["confirm_password"];
$email = $_POST["email"];
$role_id = 2;

if ($password == $password_confirm) {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
}else{
    die("<script>alert('Password Does Not Match!')</script>");
}
include "../include/conn.php";

$sql = "INSERT INTO user(username,role_id,password_hash,email)
        VALUES('$username','$role_id','$password_hash','$email')";

mysqli_query($dbConn, $sql);

if (mysqli_affected_rows($dbConn) == 0) {
    die("<script>alert('Error While Adding User!')</script>");
}

echo "<script>alert('Successfully Added User!')window.location.href = '../pages/login.php'</script>";
?>
