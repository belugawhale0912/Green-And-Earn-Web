<?php
    include "../include/conn.php";
    include "../include/session.php";
    $user_id = $_SESSION['user_id'];

    $sqllistReward = "SELECT * FROM reward WHERE partner_id = $user_id AND status != 'deleted'";
    $result = mysqli_query($dbConn,$sqllistReward);

    $sqlTotal = "SELECT COUNT(*) as total
                 FROM reward_redemption rr
                 JOIN reward r ON rr.reward_id = r.reward_id
                 WHERE r.partner_id = $user_id";
    
    $result_total = mysqli_query($dbConn, $sqlTotal);
    $countResult = mysqli_fetch_assoc($result_total);
    $totalRewardRedeemed = $countResult['total'];

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/layout.css">
    <title>Document</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <h1>Welcome Back! <?php echo $_SESSION['username']?>

    <h2>Statistics</h2>
    <div class="container">
        <h3>Total Reward Redeemed</h3>
        <p><?php echo $totalRewardRedeemed;?></p>
    </div>

    <h2>List of Rewards</h2>
    <div class="container">
        <?php if(mysqli_num_rows($result)>0):?>
            <?php while($reward_info = mysqli_fetch_assoc($result)):?>
                <div class="card">
                    <img src="<?php echo $reward_info['reward_img']?>" alt="">
                    <p><?php echo $reward_info['title']?></p>
                </div>
            <?php endwhile;?>
        <?php endif;?>
    </div>
</body>
</html>
