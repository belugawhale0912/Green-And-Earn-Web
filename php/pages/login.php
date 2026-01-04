<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/layout.css">
    <title>Login</title>
</head>
<body>
    <a href="Landingpage.php">Back</a>
    <h1>Login to Green&Earn</h1>
    <?php
        include("../include/conn.php");
    ?>
    <div class="form_container">
        <form action="../commands/login_auth.php" method="post">
            <label for="username">Username</label>
            <input type="text" name="username" placeholder="Enter Username" required>
            <label for="password">Password</label>
            <input type="password" name="password" placeholder="Enter Password" required>
            <a href="user_register.php">Register</a>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>