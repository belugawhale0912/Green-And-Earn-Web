<?php
    session_start();
    include "../include/conn.php";

    // 1. Fetch all data into an Array first
    $sql = "SELECT user_id, username, profile_photo, total_point_accumulated 
            FROM user
            WHERE role_id=2
            ORDER BY total_point_accumulated DESC";
    $result = mysqli_query($dbConn, $sql);
    
    $ranking = [];
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            $ranking[] = $row;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/layout.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/member.css"> 
    <title>Leaderboard</title>
</head>
<body>
    <?php include "../include/header.php"; ?>
    
    <div class="leaderboardContainer">
        <h1>Leaderboard</h1>    
        <?php if (!empty($ranking)): ?>
            <div class="top3Container">

                <?php if(isset($ranking[0])):?>
                <div class="top3 firstPlace">
                    <img src="<?php echo $ranking[0]['profile_photo'];?>" alt="1st">
                    <div class="rank-number">1</div>
                    <h3><?php echo $ranking[0]['username']; ?></h3>
                    <p><?php echo $ranking[0]['total_point_accumulated'];?>pts</p>
                </div>
                <?php endif; ?>

                <?php if(isset($ranking[1])):?>
                <div class="top3 secondPlace">
                    <img src="<?php echo $ranking[1]['profile_photo'];?>" alt="2nd">
                    <div class="rank-number">2</div>
                    <h3><?php echo $ranking[1]['username'];?></h3>
                    <p><?php echo $ranking[1]['total_point_accumulated'];?> pts</p>
                </div>
                <?php endif; ?>

                <?php if(isset($ranking[2])): ?>
                <div class="top3 thirdPlace">
                    <img src="<?php echo $ranking[2]['profile_photo'];?>" alt="3rd">
                    <div class="rank-number">3</div>
                    <h3><?php echo $ranking[2]['username']; ?></h3>
                    <p><?php echo $ranking[2]['total_point_accumulated'];?> pts</p>
                </div>
                <?php endif; ?>

            </div>

            <div class="leaderboardList">
                <?php for($i = 3; $i < count($ranking); $i++):?>
                    <div class="listItem">
                        <div class="listRank"><?php echo $i + 1;?></div>
                        <img class="listImg" src="<?php echo $ranking[$i]['profile_photo'];?>">
                        <div class="listInfo">
                            <h3><?php echo $ranking[$i]['username'];?></h3>
                        </div>
                        <div class="listPoints">
                            <?php echo $ranking[$i]['total_point_accumulated'];?> pts
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

        <?php else: ?>
            <h2>No records found!</h2>
        <?php endif; ?>
    </div>
</body>
</html>