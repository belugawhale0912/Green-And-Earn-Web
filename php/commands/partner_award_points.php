<?php
    include "../include/conn.php";
    include "../include/session.php";

    $username = $_POST['username'];
    $amt_spent = $_POST['amt_spent'];

    $checkSql = "SELECT * FROM user WHERE username = '$username'";

    $checkResult = mysqli_query($dbConn,$checkSql);
    if(mysqli_num_rows($checkResult)>0){
        $member_info = mysqli_fetch_assoc($checkResult);
        $member_id = $member_info['user_id'];
        $member_totalPoints = $member_info['total_point_accumulated'];
        $member_pointBal = $member_info['point_balance']; 
    }
    else{
        header("Location: ../pages/partnerPointAward.php");
        die("User not found! Session Abort");
    }

    //calculations
    $points_award = round($amt_spent, 1) * 10; //RM 1 = 10 Points

    //insertQuery
    $sql_insert_partnerTransaction = "INSERT INTO partner_point_transaction (user_id, partner_id, amount_spent, points_awarded) VALUES($member_id, {$_SESSION['user_id']}, $amt_spent, $points_award) ";
    $sql_update_userPoint = "UPDATE user SET total_point_accumulated = $member_totalPoints+$points_award, point_balance = $member_pointBal+$points_award WHERE user_id = $member_id";


    $dbConn->begin_transaction();

    try{
        if (!mysqli_query($dbConn, $sql_update_userPoint)) { throw new Exception("update failed"); }
        if (!mysqli_query($dbConn, $sql_insert_partnerTransaction)) { throw new Exception("update failed"); }
        $partnerTrans_id = mysqli_insert_id($dbConn);
        $sql_insert_transaction = "INSERT INTO point_transaction (user_id, partner_transaction_id, amount, type) VALUES($member_id, $partnerTrans_id, $points_award, 'Earned')";
        if (!mysqli_query($dbConn, $sql_insert_transaction)) { throw new Exception("Insert Transaction failed"); }        
        //if all query ran without error, save the changes
        $dbConn->commit();
        // JavaScript Feedback (TEMP)
        echo "<script>";
        echo "alert('Success! Member Awarded.');";
        echo "window.location.href = '../pages/partnerPointAward.php';"; // Redirects user back
        echo "</script>";

    }
    catch(Exception $e){
        //if any of the query fail, undo the changes and send error message
        $dbConn->rollback();
        echo "Error:". $e->getMessage();
    }
?>