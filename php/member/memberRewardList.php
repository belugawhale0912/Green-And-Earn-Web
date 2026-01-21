<?php
    include "../include/conn.php";
    include "../include/session.php";
    include "../include/libraries.php";

    $sql = "SELECT * FROM reward WHERE status = 'available' AND stock > 0";

    $result = mysqli_query($dbConn, $sql);

    if($_SERVER["REQUEST_METHOD"]=="POST"){

        $reward_id = $_POST['reward_id'];
        
        //validations
        $sql_reward = "SELECT * FROM reward WHERE reward_id = $reward_id";
        $result_reward = mysqli_query($dbConn, $sql_reward);
        $reward_info = mysqli_fetch_assoc($result_reward);

        // if reward exists
        if (!$reward_info) {
            die("Error: Reward not found");
        }

        // is it available
        if ($reward_info['status'] != 'available' || $reward_info['stock'] <= 0) {
            die("Error: Reward is out of stock or unavailable");
        }

        // Fetch User info from database
        $user_id = $_SESSION['user_id'];
        $sql_user = "SELECT * FROM user WHERE user_id = $user_id";
        $result_user = mysqli_query($dbConn, $sql_user);
        $user_info = mysqli_fetch_assoc($result_user);

        // check if user have sufficient points
        if ($user_info['point_balance'] < $reward_info['required_points']) {
            die("Error: Insufficient points");
        }

        //execution

        //calculate new values 
        $new_stock = $reward_info['stock']-1;
        $new_point_balance = $user_info['point_balance']-$reward_info['required_points'];

        //query
        $sql_update_stock = "UPDATE reward SET stock = $new_stock WHERE reward_id = $reward_id";
        
        $sql_update_pointbal = "UPDATE user SET point_balance = $new_point_balance WHERE user_id = $user_id";

        $sql_insert_redemption = "INSERT INTO reward_redemption (reward_id, user_id, points_spent, status) VALUES ($reward_id, $user_id, {$reward_info['required_points']}, 'Approved')";

        //turn off autosave to prevent data corruption if query fails
        mysqli_begin_transaction($dbConn);

        try{
            if (!mysqli_query($dbConn, $sql_update_stock)) { throw new Exception("Reward update failed"); }
            if (!mysqli_query($dbConn, $sql_update_pointbal)) { throw new Exception("User update failed"); }
            if (!mysqli_query($dbConn, $sql_insert_redemption)) { throw new Exception("Insert Redemption failed"); }
            $redemption_id = mysqli_insert_id($dbConn);
            $sql_insert_transaction= "INSERT INTO point_transaction (user_id, redemption_id, amount, type) VALUES ($user_id, $redemption_id, {$reward_info['required_points']},'Redemption')";
            if (!mysqli_query($dbConn, $sql_insert_transaction)) { throw new Exception("Insert Transaction failed"); }        
            //if all query ran without error, save the changes
            mysqli_commit($dbConn);
            //set message flag to true to show success message
            $redeemMessage = true;

        }
        catch(mysqli_sql_exception $exception){
            //if any of the query fail, undo the changes and send error message
            mysqli_rollback($dbConn);
            echo "Error:". $exception;
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
    <title>Rewards</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <h1>Available Rewards</h1>
    <div class="rewardContainer">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($reward_info = mysqli_fetch_assoc($result)): ?>
                <div class='rewardCard'>
                    <img src="<?php echo $reward_info['reward_img']; ?>" alt="Reward Image">
                    <h2><?php echo $reward_info['title']; ?></h2>
                    <h3><?php echo $reward_info['description']; ?></h3>
                    <h3>Points Required: <?php echo $reward_info['required_points']; ?></h3>
                    <form action="" method="POST">
                        <input type="hidden" name="reward_id" value="<?php echo $reward_info['reward_id']?>">
                        <button>Redeem</button>
                    </form>
                </div>
            <?php endwhile; ?>

        <?php else: ?>
            <h2>No Reward available right now!</h2>
        <?php endif; ?>
    </div>

    <!-- If flag is setted true -->
    <?php if(isset($redeemMessage)&&$redeemMessage):?>
        <script>
            Swal.fire({
                title: 'Success!',
                text: 'The Reward is redeemed!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif;?>
</body>
</html>