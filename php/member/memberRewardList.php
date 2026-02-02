<?php
    include "../include/conn.php";
    include "../include/session.php";

    $user_id = $_SESSION['user_id'];

    $view = isset($_GET['view'])? $_GET['view']:'available';
    if($view=='available'){
        $sql = "SELECT * FROM reward WHERE status = 'Available' AND stock > 0";
        $result = mysqli_query($dbConn, $sql);
        }

    elseif($view == 'redeemed'){
        $sql= "SELECT rr.status, rr.redemption_time, r.title, r.description, r.reward_img
                  FROM reward_redemption rr
                  JOIN reward r on rr.reward_id = r.reward_id
                  WHERE rr.user_id = ?
                  ORDER BY rr.redemption_time DESC";
        $stmtHistory = mysqli_prepare($dbConn, $sql);
        mysqli_stmt_bind_param($stmtHistory, "i", $user_id);
        mysqli_stmt_execute($stmtHistory);
        $result = mysqli_stmt_get_result($stmtHistory);
    }


    if($_SERVER["REQUEST_METHOD"]=="POST"){

        $reward_id = $_POST['reward_id'];
        
        //validations
        $sql_reward = "SELECT * FROM reward WHERE reward_id = ?";
        $stmtReward = mysqli_prepare($dbConn, $sql_reward);
        mysqli_stmt_bind_param($stmtReward, "i", $reward_id);
        mysqli_stmt_execute($stmtReward);
        $result_reward = mysqli_stmt_get_result($stmtReward);
        $reward_info = mysqli_fetch_assoc($result_reward);

        // if reward exists
        if (!$reward_info) {
            die("Error: Reward not found");
        }

        // is it available
        if ($reward_info['status'] != 'Available' || $reward_info['stock'] <= 0) {
            die("Error: Reward is out of stock or unavailable");
        }

        // Fetch User info from database
        $user_id = $_SESSION['user_id'];
        $sql_user = "SELECT * FROM user WHERE user_id = ?";
        $stmtUser = mysqli_prepare($dbConn, $sql_user);
        mysqli_stmt_bind_param($stmtUser, "i", $user_id);
        mysqli_stmt_execute($stmtUser);
        $result_user = mysqli_stmt_get_result($stmtUser);
        $user_info = mysqli_fetch_assoc($result_user);

        // check if user have sufficient points
        if ($user_info['point_balance'] < $reward_info['required_points']) {
            $_SESSION['popupMessage']=[
                'title' => 'Error',
                'text' => 'Insufficient Points',
                'icon' => 'error'
            ];
            header("Location: memberRewardList.php?view=available");
            exit();
        }

        //execution

        //calculate new values 
        $new_stock = $reward_info['stock']-1;
        $new_point_balance = $user_info['point_balance']-$reward_info['required_points'];

        //query
        $sql_update_stock = "UPDATE reward SET stock = ? WHERE reward_id = ?";
        $stmtUpdateStock = mysqli_prepare($dbConn, $sql_update_stock);
        mysqli_stmt_bind_param($stmtUpdateStock, "ii", $new_stock, $reward_id);
        
        $sql_update_pointbal = "UPDATE user SET point_balance = $new_point_balance WHERE user_id = ?";
        $stmtUpdatePoint = mysqli_prepare($dbConn, $sql_update_pointbal);
        mysqli_stmt_bind_param($stmtUpdatePoint, "i", $user_id);

        $sql_insert_redemption = "INSERT INTO reward_redemption (reward_id, user_id, points_spent, status) 
                                  VALUES (?, ?, ?, 'Pending')";
        $stmtInsertRedemption = mysqli_prepare($dbConn, $sql_insert_redemption);
        mysqli_stmt_bind_param($stmtInsertRedemption, "iii", $reward_id, $user_id, $reward_info['required_points']);

        //turn off autosave to prevent data corruption if query fails
        mysqli_begin_transaction($dbConn);

        $success = true;
        $errMsg = "";
        
        if(!mysqli_stmt_execute($stmtUpdateStock)){
            $success = false;
            $errMsg = "Error While Updating Stock";
        }
        mysqli_stmt_close($stmtUpdateStock);

        if($success){
            if(!mysqli_stmt_execute($stmtUpdatePoint)){
                $success = false;
                $errMsg = "Error While Updating Point";
            }
            mysqli_stmt_close($stmtUpdatePoint);
        }

        if($success){
            if(!mysqli_stmt_execute($stmtInsertRedemption)){
                $success = false;
                $errMsg = "Error While Inserting Redemption";
            }
            mysqli_stmt_close($stmtInsertRedemption);
        }

        if($success){
            $redemption_id = mysqli_insert_id($dbConn);
            $sql_insert_transaction= "INSERT INTO point_transaction (user_id, redemption_id, amount, type) 
                                    VALUES (?, ?, ?,'Redemption')";

            $stmtInsertTransaction = mysqli_prepare($dbConn, $sql_insert_transaction);
            mysqli_stmt_bind_param($stmtInsertTransaction, "iii", $user_id, $redemption_id, $reward_info['required_points']);

            if(!mysqli_stmt_execute($stmtInsertTransaction)){
                $success = false;
                $errMsg = "Error While Inserting Transaction";
            }
            mysqli_stmt_close($stmtInsertTransaction);
        }


        if($success){
            mysqli_commit($dbConn);
            //set message flag to true to show success message
            $_SESSION['popupMessage']=[
                'title' => 'Success!',
                'text' => 'The reward is redeeemed successfully!',
                'icon' => 'success'
            ];
            header("Location: memberRewardList.php?view=available");
            exit();
        }

        if(!$success){
            mysqli_rollback($dbConn);

            $_SESSION['popupMessage']=[
                'title' => 'Error',
                'text' => $errMsg,
                'icon' => 'error'
            ];
            header("Location: memberRewardList.php?view=available");
            exit();
        }

    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/member.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="icon" href="/GreenAndEarnWeb/favicon.ico">
    <?php include "../include/libraries.php";?>
    <title>Rewards</title>
</head>
<body>
    <script src="../../js/customSwal.js"></script>
    <?php
        include "../include/header.php";
    ?>
    <div class="viewToggle">
        <a href="?view=available" class="<?php echo ($view == 'available')? 'active':'' ?>">Available</a>
        <a href="?view=redeemed" class="<?php echo ($view == 'redeemed')? 'active':'' ?>">Redeemed</a>
    </div>

    <?php if($view=='available'):?>
        <h1>Available Rewards</h1>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="rewardContainer">
                <?php while($reward_info = mysqli_fetch_assoc($result)): ?>
                
                    <div class='card rewardCard'>
                        <img src="<?php echo $reward_info['reward_img'];?>" alt="Reward Image">
                        <h2><?php echo $reward_info['title']; ?></h2>
                        <h3><?php echo $reward_info['description']; ?></h3>
                        <h3>Points Required: <?php echo $reward_info['required_points'];?></h3>
                        <form action="" method="POST" class="redemptionForm">
                            <input type="hidden" name="reward_id" value="<?php echo $reward_info['reward_id'];?>">
                            <button>Redeem</button>
                        </form>
                    </div>

                <?php endwhile; ?>
            </div>    
        <?php else: ?>
            <h2 class="msg">No Reward available right now!</h2>
        <?php endif; ?>
        
    <?php elseif($view=='redeemed'):?>
        <h1>Redemption History</h1>
        
        <div class="rewardHistoryContainer">
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($redemptionInfo = mysqli_fetch_assoc($result)): ?>
                    <div class="historyCard">
                        <img src="<?php echo $redemptionInfo['reward_img']; ?>" alt="Reward Image">
                        
                        <div class="historyContent">
                            <h2><?php echo $redemptionInfo['title'];?></h2>
                            <p><?php echo $redemptionInfo['description'];?></p>
                        </div>

                        <div class="historyStatus">
                            <p class="status-pill <?php echo $redemptionInfo['status'];?>"><?php echo $redemptionInfo['status'];?></p>
                            <p class="history-date"><?php echo date("d M Y", strtotime($redemptionInfo['redemption_time']));?></p>
                        </div>
                    </div>
                <?php endwhile; ?>

            <?php else: ?>
                <div>
                    <h2>No redemption history yet</h2>
                    <p>Items you redeem will appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    
    <?php if(isset($_SESSION['popupMessage'])):?>
        <script>
            showMessage(
                "<?php echo $_SESSION['popupMessage']['title']?>",
                "<?php echo $_SESSION['popupMessage']['text']?>",
                "<?php echo $_SESSION['popupMessage']['icon']?>",
            );
        </script>
        <?php unset($_SESSION['popupMessage'])?>
    <?php endif;?>
    <script src="../../js/member.js"></script>
</body>
</html>