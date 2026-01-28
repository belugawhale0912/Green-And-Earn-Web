<?php
    include "../include/conn.php";
    include "../include/session.php";

    if(!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3){
        header("Location: ../pages/Landingpage.php"); // Redirect non-moderators
        exit();
    }

    $moderator_id = $_SESSION['user_id'];

    $sql_pending = "SELECT p.*, u.username
                    FROM partner_point_transaction p
                    JOIN user u ON p.user_id = u.user_id
                    WHERE p.status = 'Pending'
                    ORDER BY p.award_time DESC";

    $result_pending = mysqli_query($dbConn, $sql_pending);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $transaction_id = $_POST['partner_transaction_id'];
        $decision = $_POST['decision'];

        if ($decision == 'approve') {

            $sql_fetch = "SELECT user_id, points_awarded FROM partner_point_transaction WHERE partner_transaction_id = ?";

            $stmt = mysqli_prepare($dbConn, $sql_fetch);
            mysqli_stmt_bind_param($stmt, "i", $transaction_id);
            mysqli_stmt_execute($stmt);

            $resultFetch = mysqli_stmt_get_result($stmt);
            $data = mysqli_fetch_assoc($resultFetch);
            mysqli_stmt_close($stmt);

            if($data){
                $userID = $data['user_id'];
                $points = $data['points_awarded'];
            }

            mysqli_begin_transaction($dbConn);
            $success = true;
            $err = "";

            $sql_update_status = "UPDATE partner_point_transaction 
                                    SET status = 'Approved' 
                                    WHERE partner_transaction_id = ?";
            
            $stmt_update = mysqli_prepare($dbConn, $sql_update_status);
            mysqli_stmt_bind_param($stmt_update, "i", $transaction_id);
            if(!mysqli_stmt_execute($stmt_update)){
                $success = false;
                $err = "Error while updating status";
            }
            mysqli_stmt_close($stmt_update);

            if($success){
                $sql_give_points = "UPDATE user 
                                    SET point_balance = point_balance + ?, 
                                        total_point_accumulated = total_point_accumulated + ? 
                                    WHERE user_id = ?";

                $stmt_points = mysqli_prepare($dbConn, $sql_give_points);
                mysqli_stmt_bind_param($stmt_points, "iii", $points, $points, $userID);
                if(!mysqli_stmt_execute($stmt_points)){
                    $success = false;
                    $err = "Error while updating points";
                }
                mysqli_stmt_close($stmt_points);
            }
            
            if($success){
                $sql_transactionRecord = "INSERT INTO point_transaction (user_id, partner_transaction_id, amount, type) 
                                            VALUES (?, ?, ?, 'Earned')";
                
                $stmt_transaction = mysqli_prepare($dbConn, $sql_transactionRecord);
                mysqli_stmt_bind_param($stmt_transaction, "iii", $userID, $transaction_id, $points);
                if(!mysqli_stmt_execute($stmt_transaction)){
                    $success = false;
                    $err = "Error while inserting transaction record";
                }
                mysqli_stmt_close($stmt_transaction);
            }

            if($success){
                mysqli_commit($dbConn);
                include "../include/badge_system.php";
                // TODO add checkPointBadges
                checkTaskBadges($dbConn, $submission_info['user_id']);
                echo "<script>alert('Success! Points awarded.'); window.location.href = 'morderatorPartnerPoints.php';</script>";
            }else{
                mysqli_rollback($dbConn);
                echo "<script>alert('Error: $err'); window.location.href = 'morderatorPartnerPoints.php';</script>";
            }
                
        }
        elseif ($decision == 'reject') {
            $sql_update = "UPDATE partner_point_transaction
                            SET status = 'Rejected'
                            WHERE partner_transaction_id = ?";
                
            $stmt = mysqli_prepare($dbConn, $sql_update);
            mysqli_stmt_bind_param($stmt, "i", $transaction_id);
            if(mysqli_stmt_execute($stmt)){
                echo "<script>alert('Rejected Point Award!'); window.location.href = 'morderatorPartnerPoints.php';</script>";
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Partner Points Approval</title>
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/moderator.css">
</head>
<body>

<?php include "../include/header.php"; ?>

<h1>Pending Partner Point Transactions</h1>

<table>
    <thead>
        <tr>
            <th>Transaction ID</th>
            <th>User</th>
            <th>Partner</th>
            <th>Award Time</th>
            <th>Amount Spent</th>
            <th>Points Awarded</th>
            <th>Status</th>
            <th></th>
        </tr>
    </thead>
    <tbody>

    <?php while ($partnerRequest_info = mysqli_fetch_assoc($result_pending)): ?>
        <tr>
            <td data-label="Transaction ID"><?php echo $partnerRequest_info['partner_transaction_id']; ?></td>
            <td data-label="User"><?php echo $partnerRequest_info['username']; ?></td>
            <td data-label="Partner"><?php echo $partnerRequest_info['partner_id']; ?></td>
            <td data-label="Award Time"><?php echo $partnerRequest_info['award_time']; ?></td>
            <td data-label="Amount Spent"><?php echo $partnerRequest_info['amount_spent']; ?></td>
            <td data-label="Points Awarded"><?php echo $partnerRequest_info['points_awarded']; ?></td>
            <td data-label="Status"><?php echo $partnerRequest_info['status']; ?></td>
            <td>
                <button class="viewPartnerBtn"
                    data-id="<?php echo $partnerRequest_info['partner_transaction_id']; ?>"
                    data-username="<?php echo htmlspecialchars($partnerRequest_info['username'], ENT_QUOTES); ?>"
                    data-amount="<?php echo $partnerRequest_info['amount_spent']; ?>"
                    data-points="<?php echo $partnerRequest_info['points_awarded']; ?>"
                    data-receipt="<?php echo $partnerRequest_info['receipt_img']; ?>"
                >
                    View
                </button>
            </td>
        </tr>
    <?php endwhile; ?>

    </tbody>
</table>

<div id="partnerPopup" class="modal">
    <div class="modalContent">
        <h1>Review Partner Points</h1>
        <p>Member: <span id="partnerUsername"></span></p>
        <p>Amount Spent: <span id="partnerAmount"></span></p>
        <p>Points Awarded: <span id="partnerPoints"></span></p>
        <p>Receipt:</p>
        <img id="partnerReceipt" src="" width="200">

        <form method="POST">
            <input type="hidden" name="partner_transaction_id" id="partnerTransactionID">
            <div class="buttonGroup">
                <button type="submit" name="decision" value="approve">Approve</button>
                <button type="submit" name="decision" value="reject">Reject</button>
            </div>
        </form>
    </div>
</div>
    <script src="../../js/moderator.js"></script>
</body>
</html>