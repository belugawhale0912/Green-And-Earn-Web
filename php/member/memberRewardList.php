<?php
    session_start();
    include "../include/conn.php";

    $sql = "SELECT * FROM reward WHERE status = 'available' AND stock > 0";

    $result = mysqli_query($dbConn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/layout.css">
    <link rel="stylesheet" href="../../css/header.css">
    <title>Rewards</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <div class="container">
        <?php
            if (mysqli_num_rows($result)>0) {
                while($reward_info = mysqli_fetch_assoc($result)){
                    echo("<div class='card'>");
                    echo('<img src="'.$reward_info['reward_img'].'" alt="Reward Image">');
                    echo('<h2>'.$reward_info['title'].'</h2>');
                    echo('<h3>'.$reward_info['description'].'</h3>');
                    echo('<h3>'.'Points Required: '.$reward_info['required_points'].'</h3>');
                    echo('<a href="../commands/redeem_reward.php?reward_id='.$reward_info['reward_id'].'">Redeem</a>');
                    echo('</div>');                 
                }
            }
            else{
                echo("<h2>No Reward available right now!</h2>");
            }
        ?>
    </div>
</body>
</html>