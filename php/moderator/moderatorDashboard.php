<?php
    include "../include/conn.php";
    include "../include/session.php";

    if($_SESSION['role_id']!= 3){
        header("Location: ../pages/Landingpage.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Retrive moderator profile
    $sql_profile = "SELECT * FROM user WHERE user_id = $user_id";
    $profileResult = mysqli_query($dbConn,$sql_profile);
    $user_info = mysqli_fetch_assoc($profileResult); 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/moderator.css">
    <title>Document</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <section id="profileSection">
        <div class="container">
            <h1>Profile</h1>
            <img src="<?php echo $user_info['profile_photo']?>" alt="profileImg" class="profileImg">
            <div id="profileInfo">
                <h2>Username: <?php echo $user_info['username']?></h2>
                <h2>Email: <?php echo $user_info['email']?></h2>
            </div>

        </div>
    </section>
</body>
</html>