<?php
    session_start();
    include "../include/conn.php";

    $sql = "SELECT user_id, username, total_point_accumulated 
            FROM user
            WHERE role_id=2
            ORDER BY total_point_accumulated DESC";
    $result = mysqli_query($dbConn,$sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/layout.css">
    <link rel="stylesheet" href="../../css/header.css">
    <title>LeaderBoard</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <div class="list_container">
        <?php
        $rank = 1;
            if (mysqli_num_rows($result)>0) {
                while($user_info = mysqli_fetch_assoc($result)){
                    echo("<div class='list_card'>");
                    echo("<h2>$rank. {$user_info['username']}</h2>");
                    echo("<h3>Total Points Accumulated:{$user_info['total_point_accumulated']}</h3>");
                    echo("</div>");
                    $rank++;                 
                }
            }
            else{
                echo("<h2>No Transaction Record!</h2>");
            }
        ?>
</body>
</html>