<?php
    if($_SERVER["REQUEST_METHOD"] == "POST"){
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
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/layout.css">
</head>
<body>
    <a id="back" href="Landingpage.php">Back</a>
    <h1>Register</h1>
    <?php
        include("../include/conn.php");
    ?>
    <div class="form_container">
        <form action="" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter Username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter Password" required>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Enter Password Again" required>
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" placeholder="eg: 123@example.com" required>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>