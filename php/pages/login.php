<?php
    session_start();
    include "../include/conn.php";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $username = $_POST["username"];
        $password = $_POST["password"];

        $sql = "SELECT * FROM user WHERE username = '$username'";
        $result = mysqli_query($dbConn, $sql);

        if (mysqli_num_rows($result)>0) {
            $user_info = mysqli_fetch_assoc($result);
            if (password_verify($password, $user_info['password_hash'])){
                $_SESSION['username'] = $user_info['username']; 
                $_SESSION['role_id'] = $user_info['role_id'];
                $_SESSION['user_id'] = $user_info['user_id'];
                switch($user_info['role_id']){
                    case 1:
                        header("Location: ../admin/adminDashboard.php");
                        break;
                    case 2:
                        header("Location: ../member/memberDashboard.php");
                        break;
                    case 3:
                        header("Location: ../moderator/moderatorDashboard.php");
                        break;
                    case 4:
                        header("Location: ../partner/partnerDashboard.php");
                        break;
                }
                exit();
            }
            else{
                $error = "Invalid Password";
            }
        }
        else{
            $error = "User not found!";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/login.css">
    <title>Login</title>
</head>
<body>
    <?php
        include("../include/conn.php");
    ?>
    <div id="login_container">
        <a href="Landingpage.php">Back</a>
        <div id="left_pane">
            <h1>Login to Green&Earn</h1>
            <div class="form_container">
                <form action="" method="post">
                    <label for="username">Username</label>
                    <input type="text" name="username" placeholder="Enter Username" required>
                    <label for="password">Password</label>
                    <input type="password" name="password" placeholder="Enter Password" required>
                    <a href="user_register.php">Register</a>
                    <button type="submit">Login</button>
                </form>
            </div>
        </div>

        <div id="right_pane">
            <img src="../../assets/placeholder.jpg" alt="">
        </div>
    </div>

</body>
</html>