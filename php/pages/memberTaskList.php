<?php
    session_start();
    include "../include/conn.php";

    $sql = "SELECT * FROM activity";

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
    <link rel="stylesheet" href="../../css/memberTaskList.css">
    <title>TaskLists</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <h1>Available Tasks</h1>
    <div class="container">
        <?php
            if (mysqli_num_rows($result)>0) {
                while($activity_info = mysqli_fetch_assoc($result)){
                    echo("<div class='card'>");
                    echo('<h2>'.$activity_info['title'].'</h2>');
                    echo('<h3>'.$activity_info['description'].'</h3>');
                    echo('<h3>'.'Points Reward: '.$activity_info['points_awarded'].'</h3>');
                    echo('<a href="memberSubmitProof.php?activity_id='.$activity_info['activity_id'].'">Submit Proof</a>');
                    echo('</div>');                 
                }
            }
            else{
                echo("<h2>No tasks available right now!</h2>");
            }
        ?>
    </div>
</body>
</html>