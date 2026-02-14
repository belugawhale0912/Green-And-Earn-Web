<?php
    include "../include/session.php";
    include "../include/conn.php";
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $username = $_POST["username"];
        $password = $_POST["password"];
        $password_confirm = $_POST["confirm_password"];
        $email = $_POST["email"];
        $role_id = 2;

        if ($password == $password_confirm) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
        }else{
            $_SESSION['popupMessage']=[
                'title' =>'Error',
                'text' => 'Password Does Not Match, Please try again!',
                'icon' => 'error'
            ];
            header("Location: user_register.php");
            exit();
        }

        $sql = "INSERT INTO user(username,role_id,password_hash,email,profile_photo)
                VALUES( ?, 2, ?, ?, '../../assets/placeHolders/image.png')";
        
        $stmt = mysqli_prepare($dbConn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $username, $password_hash, $email);

        try{
            if(mysqli_stmt_execute($stmt)){
                $_SESSION['popupMessage']=[
                    'title' =>'Success',
                    'text' => 'Account Created Successfully',
                    'icon' => 'success'
                ];
                mysqli_stmt_close($stmt);
                header("Location: user_register.php");
                exit();
            }
        } catch (Exception $e){
            $_SESSION['popupMessage']=[
                'title' =>'Error',
                'text' => 'Email Or Name Already Exists!',
                'icon' => 'error'
            ];
            mysqli_stmt_close($stmt);
            header("Location: user_register.php");
            exit();
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/register.css">
    <link rel="icon" href="/GreenAndEarnWeb/favicon.ico">
    <?php include "../include/libraries.php"?>
</head>
<body>
    <script src="../../js/customSwal.js"></script>
    <a id="back" href="Landingpage.php">Back</a>
    
    <div class="form_container">
        <h1>Join Green&Earn</h1> <form action="" method="post">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter username" required>
            
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="name@example.com" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Create a password" required>
            
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
            
            <button type="submit">Create Account</button>
        </form>
    </div>
    <?php include "../include/showSWAL.php";?>
    <script src="../../js/header.js"></script>
</body>
</html>
<?php mysqli_close($dbConn)?>