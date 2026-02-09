<?php
    include "../include/conn.php";
    include "../include/session.php";

    // 1. ACCESS CONTROL (Only Partners)
    if(!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 4){
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


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $redemption_id = $_POST['redemption_id'];

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
                header("Location: partnerGiveReward.php");
                exit();
            }else{
                $_SESSION['popupMessage']=[
                    'title' => 'Failed',
                    'text' => 'Failed to Approve Reward!',
                    'icon' => 'error'
                ];
                header("Location: partnerGiveReward.php");
                exit();
            }
        }

        if(isset($_POST['btnReject'])){

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

                mysqli_begin_transaction($dbConn);
                $success = true;
                $err = "";

                // Refund Points to User
                $sql_refundPoints = "UPDATE user 
                                     SET point_balance = point_balance + ? 
                                     WHERE user_id = ?";
                $stmtRefund = mysqli_prepare($dbConn, $sql_refundPoints);
                mysqli_stmt_bind_param($stmtRefund, "ii", $amount, $userId); 
                if(!mysqli_stmt_execute($stmtRefund)){
                    $success = false;
                    $err = "Error While Refunding Point";
                } 
                mysqli_stmt_close($stmtRefund);

                // Mark Redemption as Refunded
                if($success){
                    $sql_rewardRejected = "UPDATE reward_redemption 
                                           SET status = 'Refunded' 
                                           WHERE redemption_id = ?";

                    $stmtUpdate = mysqli_prepare($dbConn, $sql_rewardRejected);
                    mysqli_stmt_bind_param($stmtUpdate, "i", $redemption_id);
                    if(!mysqli_stmt_execute($stmtUpdate)){
                        $success = false;
                        $err = "Error While Updating Status";
                    } 
                    mysqli_stmt_close($stmtUpdate);
                }

                // Add Transaction Record
                if($success){
                    $sql_Transaction = "INSERT INTO point_transaction (user_id, redemption_id, amount, type) 
                                VALUES (?, ?, ?, 'Refund')";
                    $stmtTransaction = mysqli_prepare($dbConn, $sql_Transaction);
                    mysqli_stmt_bind_param($stmtTransaction, "iid", $userId, $redemption_id, $amount);
                    if(!mysqli_stmt_execute($stmtTransaction)){
                        $success = false;
                    } 
                    mysqli_stmt_close($stmtTransaction);
                }

                if($success){
                    mysqli_commit($dbConn);
                    $_SESSION['popupMessage']=[
                        'title' => 'Success',
                        'text' => 'Reward Rejected! Points Refunded.',
                        'icon' => 'success'
                    ];
                    header("Location: partnerGiveReward.php");
                    exit();
                } else {
                    mysqli_rollback($dbConn);
                    $_SESSION['popupMessage']=[
                        'title' => 'Failed',
                        'text' => 'Error Processing Refund.',
                        'icon' => 'error'
                    ];
                    header("Location: partnerGiveReward.php");
                    exit();
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