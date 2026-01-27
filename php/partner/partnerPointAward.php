<?php
    include "../include/conn.php";
    include "../include/session.php";
    $partnerName = $_SESSION['username'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $amt_spent = $_POST['amt_spent'];

        if($amt_spent <= 0){
            echo "<script>";
            echo "alert('Session Aborted. Amount spent must be more than 0');";
            echo "window.location.href = '../partner/partnerPointAward.php';";
            echo "</script>";
        }

        $checkSql = "SELECT * FROM user WHERE username = '$username'";

        $checkResult = mysqli_query($dbConn,$checkSql);
        if(mysqli_num_rows($checkResult)>0){
            $member_info = mysqli_fetch_assoc($checkResult);
            $member_id = $member_info['user_id'];
            $member_totalPoints = $member_info['total_point_accumulated'];
            $member_pointBal = $member_info['point_balance']; 
        }
        else{
            header("Location: ../partner/partnerPointAward.php");
            die("User not found! Session Abort");
        }

        
        if(isset($_FILES['receipt_img'])&&$_FILES['receipt_img']['error'] === UPLOAD_ERR_OK){

            $fileName = $_FILES['receipt_img']['name'];
            $fileTempPath = $_FILES['receipt_img']['tmp_name'];

            $fileExt = pathinfo($fileName,PATHINFO_EXTENSION);

            $supportedExt = ['jpg','jpeg','png','gif'];

            if(in_array($fileExt,$supportedExt)){
                $formattedFileName = "member_".$username."partner_"."$partnerName".time().".".$fileExt;

                $saveDir = "../../uploads/receipt/".$formattedFileName;

                if(move_uploaded_file($fileTempPath, $saveDir)){

                    //calculations
                    $points_award = round($amt_spent, 1) * 10; //RM 1 = 10 Points

                    //insertQuery
                    $sql_insert_partnerTransaction = "INSERT INTO 
                        partner_point_transaction (user_id, partner_id, amount_spent, points_awarded, receipt_img, status) 
                        VALUES($member_id, {$_SESSION['user_id']}, $amt_spent, $points_award, '$saveDir','Pending') ";
                    $sql_update_userPoint = "UPDATE user SET total_point_accumulated = $member_totalPoints+$points_award, 
                        point_balance = $member_pointBal+$points_award WHERE user_id = $member_id";

                    mysqli_begin_transaction($dbConn);

                    try{
                        if (!mysqli_query($dbConn, $sql_update_userPoint)) { throw new Exception("update failed"); }
                        if (!mysqli_query($dbConn, $sql_insert_partnerTransaction)) { throw new Exception("update failed"); }
                        $partnerTrans_id = mysqli_insert_id($dbConn);
                        $sql_insert_transaction = "INSERT INTO point_transaction (user_id, partner_transaction_id, amount, type) VALUES($member_id, $partnerTrans_id, $points_award, 'Earned')";
                        if (!mysqli_query($dbConn, $sql_insert_transaction)) { throw new Exception("Insert Transaction failed"); }        
                        //if all query ran without error, save the changes
                        mysqli_commit($dbConn);
                        // JavaScript Feedback (TEMP)
                        echo "<script>";
                        echo "alert('Success! Member Awarded.');";
                        echo "window.location.href = '../partner/partnerPointAward.php';"; // Redirects user back
                        echo "</script>";

                    }
                    catch(mysqli_sql_exception $exception){
                        //if any of the query fail, undo the changes and send error message
                        mysqli_rollback($dbConn);
                        echo "Error:". $exception;
                    }

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
    <title>Point Award</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <div class="formContainer">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="formCard">
                <div class="formItem">
                    <label for="username">Username:</label>
                    <input type="text" name="username" required>
                    <label for="amt_spent">Amount Spent:</label>
                    <input type="number" name="amt_spent" step="0.01" required>
                    <label>Upload Receipt (Image):</label>
                    <input type="file" name="receipt_img" required accept="image/*">
                <div class="formItem">
                    <button type="submit">Comfirm</button>
                </div>
            </div>
        </form> 
    </div>
</body>
</html>
