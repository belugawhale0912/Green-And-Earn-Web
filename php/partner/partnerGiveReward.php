<?php
    include "../include/conn.php";
    include "../include/session.php";

    $user_id = $_SESSION['user_id'];

    $sqlRequest = "SELECT u.user_id, rr.redemption_id, u.username, rr.redemption_time FROM reward_redemption rr
        JOIN user u ON rr.user_id = u.user_id WHERE rr.status = 'Approved'";//change to Pending afterwards, rn is just to test
    $resultRequest = mysqli_query($dbConn, $sqlRequest);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['btnApprove'])){
            $redemption_id = $_POST['redemption_id'];
            $sql = "UPDATE reward_redemption SET status = 'Approved' WHERE redemption_id = $redemption_id";

            if(mysqli_query($dbConn, $sql)){
                echo "<script> 
                        alert('Reward Approved');
                        window.location.href = '../partner/partnerGiveReward.php';
                    </script>";
            }else{
                echo"Error";
            }
        }

        if(isset($_POST['btnReject'])){
            $redemption_id = $_POST['redemption_id'];
            //get user id and point from redemption table
            $sql_getData = "SELECT user_id, points_spent FROM reward_redemption WHERE redemption_id = $redemption_id";
            $resultData = mysqli_query($dbConn, $sql_getData);
            
            if($result = mysqli_fetch_assoc($resultData)){
                $user_id = $result['user_id'];
                $amount = $result['points_spent'];

                //sql queries to edit 3 tables
                $sql_pointTransaction = "INSERT INTO point_transaction (user_id, redemption_id, amount, type)
                    VALUES ('$user_id', '$redemption_id', '$amount', 'Refund')";
                $sql_updateUser = "UPDATE user SET point_balance = point_balance + $amount WHERE user_id = '$user_id'";
                $sql_rewardRejected = "UPDATE reward_redemption SET status = 'Rejected' WHERE redemption_id = $redemption_id";
                
                // Execute all three queries
                $resultTransaction = mysqli_query($dbConn, $sql_pointTransaction);
                $resultUser = mysqli_query($dbConn, $sql_updateUser);
                $resultReject = mysqli_query($dbConn, $sql_rewardRejected);

                if($resultTransaction && $resultUser && $resultReject){
                    echo "<script> 
                            alert('Reward Rejected');
                            window.location.href = '../partner/partnerGiveReward.php';
                        </script>";
                }else{
                    echo"Error";
                }
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/layout.css">
    <link rel="stylesheet" href="../../css/partner.css">
    <title>Document</title>
</head>
<body>
    <?php
        include "../include/header.php";    
    ?>
    <h2>Reward Approval</h2>
    <div class="requestContainer">
        <?php if(mysqli_num_rows($resultRequest)>0):
            while($request = mysqli_fetch_assoc($resultRequest)):?>
                <div class='requestCard'>
                    <div class="requestDetail">
                        <h3>Redemption ID <br><p><?php echo $request['redemption_id']?></p></h3>
                        <h3>Username <br><p><?php echo $request['username']?></p></h3>
                        <h3>Redemption Time <br><p><?php echo $request['redemption_time']?></p></h3>
                    </div>
                    <div >
                        <form action="" method="POST">
                                <input type="hidden" name="redemption_id" value="<?php echo $request['redemption_id'];?>">
                                <button name="btnApprove" type="submit">Approve</button>
                        </form>
                        <form action="" method="POST">
                                <input type="hidden" name="redemption_id" value="<?php echo $request['redemption_id'];?>">
                                <button name="btnReject" type="submit">Reject</button>
                        </form>
                    </div>
                </div>
            <?php endwhile;?>

        <?php else:
            echo("<p>No Transaction Record!</p>");?>
        <?php endif;?>
    </div>
</body>
</html>