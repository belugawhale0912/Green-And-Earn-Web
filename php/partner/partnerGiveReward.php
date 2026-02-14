<?php
    include "../include/conn.php";
    include "../include/session.php";

    // check if the current user is partner, redirect to landing page if not partner
    if($_SESSION['role_id'] != 4){
        header("Location: ../pages/Landingpage.php");
        exit();
    }

    $partner_id = $_SESSION['user_id'];

    $sqlRequest = "SELECT u.user_id, rr.redemption_id, rr.reward_id, u.username, rr.redemption_time 
                   FROM reward_redemption rr
                   JOIN user u ON rr.user_id = u.user_id
                   JOIN reward r ON rr.reward_id = r.reward_id 
                   WHERE rr.status = 'Pending' AND r.partner_id = ?";

    $stmt = mysqli_prepare($dbConn, $sqlRequest);
    mysqli_stmt_bind_param($stmt, "i", $partner_id);
    mysqli_stmt_execute($stmt);
    $resultRequest = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $redemption_id = $_POST['redemption_id'];

        // if approve button is pressed
        if(isset($_POST['btnApprove'])){
            $sql = "UPDATE reward_redemption SET status = 'Approved' WHERE redemption_id = ?";
            $stmt = mysqli_prepare($dbConn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $redemption_id);

            if(mysqli_stmt_execute($stmt)){
                $_SESSION['popupMessage']=[
                    'title' => 'Success',
                    'text' => 'Reward Approved!',
                    'icon' => 'success'
                ];
                mysqli_stmt_close($stmt);

                header("Location: partnerGiveReward.php");
                exit();
            }else{
                $_SESSION['popupMessage']=[
                    'title' => 'Failed',
                    'text' => 'Failed to Approve Reward!',
                    'icon' => 'error'
                ];
                mysqli_stmt_close($stmt);

                header("Location: partnerGiveReward.php");
                exit();
            }
        }

        // if reject button is pressed
        if(isset($_POST['btnReject'])){

            // grab member information
            $sql_getData = "SELECT user_id, points_spent FROM reward_redemption WHERE redemption_id = ?";
            
            $stmt = mysqli_prepare($dbConn, $sql_getData);
            mysqli_stmt_bind_param($stmt, "i", $redemption_id);
            mysqli_stmt_execute($stmt);

            $resultData = mysqli_stmt_get_result($stmt);
            $data = mysqli_fetch_assoc($resultData);
            mysqli_stmt_close($stmt);
            
            if($data){
                $userId = $data['user_id'];
                $amount = $data['points_spent'];

                try{
                    mysqli_begin_transaction($dbConn);

                    // Mark Redemption as Refunded
                    $sql_rewardRejected = "UPDATE reward_redemption 
                                           SET status = 'Refunded' 
                                           WHERE redemption_id = ?";

                    $stmtUpdate = mysqli_prepare($dbConn, $sql_rewardRejected);
                    mysqli_stmt_bind_param($stmtUpdate, "i", $redemption_id);
                    mysqli_stmt_execute($stmtUpdate);
                    mysqli_stmt_close($stmtUpdate);

                    // Refund Points to User
                    $sql_refundPoints = "UPDATE user 
                                         SET point_balance = point_balance + ? 
                                         WHERE user_id = ?";
                    $stmtRefund = mysqli_prepare($dbConn, $sql_refundPoints);
                    mysqli_stmt_bind_param($stmtRefund, "ii", $amount, $userId); 
                    mysqli_stmt_execute($stmtRefund);
                    mysqli_stmt_close($stmtRefund);

                    // Insert transaction record
                    $sql_Transaction = "INSERT INTO point_transaction (user_id, redemption_id, amount, type) 
                                        VALUES (?, ?, ?, 'Refund')";
                    $stmtTransaction = mysqli_prepare($dbConn, $sql_Transaction);
                    mysqli_stmt_bind_param($stmtTransaction, "iid", $userId, $redemption_id, $amount);
                    mysqli_stmt_execute($stmtTransaction);
                    mysqli_stmt_close($stmtTransaction);

                    mysqli_commit($dbConn);
                    $_SESSION['popupMessage']=[
                        'title' => 'Success',
                        'text' => 'Reward Rejected! Points Refunded.',
                        'icon' => 'success'
                    ];
                } catch (Exception $e){
                    mysqli_rollback($dbConn);
                    $_SESSION['popupMessage']=[
                        'title' => 'Failed',
                        'text' => 'Error Processing Refund.',
                        'icon' => 'error'
                    ];
                }
                header("Location: partnerGiveReward.php");
                exit();         
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
    <?php include "../include/libraries.php";?>
    <title>Document</title>
</head>
<body>
    <script src="../../js/customSwal.js"></script>
    <?php
        include "../include/header.php";    
    ?>
    <h2>Reward Approval</h2>
    <div class="requestContainer">
        <?php if(mysqli_num_rows($resultRequest)>0):
            while($request = mysqli_fetch_assoc($resultRequest)):?>
                <div class='requestCard'>
                    <div class="requestDetail">
                        <h3>Reward ID : <br><?php echo $request['reward_id']?></h3>
                        <h3>Username : <br><?php echo $request['username']?></h3>
                        <h3>Redemption Time : <br><?php echo $request['redemption_time']?></h3>
                    </div>
                    <div class="requestDetail">
                        <form action="" method="POST">
                                <input type="hidden" name="redemption_id" value="<?php echo $request['redemption_id'];?>">
                                <button name="btnApprove" type="submit" class="approveBtn">Approve</button>
                        </form>
                        <form action="" method="POST">
                                <input type="hidden" name="redemption_id" value="<?php echo $request['redemption_id'];?>">
                                <button name="btnReject" type="submit" class="rejectBtn">Reject</button>
                        </form>
                    </div>
                </div>
            <?php endwhile;?>

        <?php else:
            echo("<p>No Pending Redemption!</p>");?>
        <?php endif;?>
    </div>
    <?php include "../include/showSWAL.php"?>
</body>
</html>
<?php mysqli_close($dbConn)?>