<?php
    include "../include/conn.php"; // include database connectin file
    include "../include/session.php"; // include session handling

    // to check the user's role
    if(!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3){
        header("Location: ../pages/Landingpage.php"); // Redirect non-moderators
        exit();
    }

    $moderator_id = $_SESSION['user_id']; // store the logged-in moderator's id

    // sql query to fetch all pending partner point transactions
    $sql_pending = "SELECT p.*, u.username
                    FROM partner_point_transaction p
                    JOIN user u ON p.user_id = u.user_id
                    WHERE p.status = 'Pending'
                    ORDER BY p.award_time DESC";

    $result_pending = mysqli_query($dbConn, $sql_pending); // execute the query

    if ($_SERVER["REQUEST_METHOD"] == "POST") { // check if form is submitted

        $transaction_id = $_POST['partner_transaction_id']; 
        $decision = $_POST['decision']; // Will be either 'approve' or 'reject'

        if ($decision == 'approve') {

            // fetch user id and pionts for the selected transaction
            $sql_fetch = "SELECT user_id, points_awarded FROM partner_point_transaction WHERE partner_transaction_id = ?";

            $stmt = mysqli_prepare($dbConn, $sql_fetch); // prepare sql for execution
            mysqli_stmt_bind_param($stmt, "i", $transaction_id); // to make sure the placeholder has value
            mysqli_stmt_execute($stmt); 

            $resultFetch = mysqli_stmt_get_result($stmt); // to retrieve result from the executed query
            $data = mysqli_fetch_assoc($resultFetch); // convert result as an associative array
            mysqli_stmt_close($stmt); 

            // store user id and points for later updates
            if($data){
                $userID = $data['user_id'];
                $points = $data['points_awarded'];
            }

            try{
                //use transaction to prevent broken record in the database
                mysqli_begin_transaction($dbConn);

                // update submission status to approved
                $sql_update_status = "UPDATE partner_point_transaction 
                                        SET status = 'Approved' 
                                        WHERE partner_transaction_id = ?";
                
                $stmt_update = mysqli_prepare($dbConn, $sql_update_status);
                mysqli_stmt_bind_param($stmt_update, "i", $transaction_id);
                mysqli_stmt_execute($stmt_update);
                mysqli_stmt_close($stmt_update);

                // update user's points
                $sql_give_points = "UPDATE user 
                                    SET point_balance = point_balance + ?, 
                                        total_point_accumulated = total_point_accumulated + ? 
                                    WHERE user_id = ?";

                $stmt_points = mysqli_prepare($dbConn, $sql_give_points); // prepare sql for execution
                mysqli_stmt_bind_param($stmt_points, "iii", $points, $points, $userID); // make sure plceholders have values
                mysqli_stmt_execute($stmt_points);
                mysqli_stmt_close($stmt_points);


                // insert transaction record
                $sql_transactionRecord = "INSERT INTO point_transaction (user_id, partner_transaction_id, amount, type) 
                                            VALUES (?, ?, ?, 'Earned')";
                
                $stmt_transaction = mysqli_prepare($dbConn, $sql_transactionRecord);
                mysqli_stmt_bind_param($stmt_transaction, "iii", $userID, $transaction_id, $points);
                mysqli_stmt_execute($stmt_transaction);
                mysqli_stmt_close($stmt_transaction);

                // commit when all query executed without error
                mysqli_commit($dbConn);

                // include badge system to reward member with badge
                include "../include/badge_system.php";
                checkPointBadges($dbConn, $data['user_id']); // check if user qualifies for new badges

                $_SESSION['popupMessage']=[
                    'title' => 'Success',
                    'text' => 'Points Awarded Successfully',
                    'icon' => 'success'
                ];

            } catch (Exception $e){
                // rollback all changes if any of the query fail to run
                mysqli_rollback($dbConn);

                $_SESSION['popupMessage']=[
                    'title' => 'Error',
                    'text' => 'Error While Awarding Points',
                    'icon' => 'error'
                ];
            }
            header("Location: moderatorPartnerPoints.php");
            exit();

        }
        elseif ($decision == 'reject') {
            // mark as rejected and no points are awarded
            $sql_update = "UPDATE partner_point_transaction
                            SET status = 'Rejected'
                            WHERE partner_transaction_id = ?";
                
            $stmt = mysqli_prepare($dbConn, $sql_update);
            mysqli_stmt_bind_param($stmt, "i", $transaction_id);
            if(mysqli_stmt_execute($stmt)){
                $_SESSION['popupMessage']=[
                    'title' => 'Success',
                    'text' => 'Points Awarding Rejected',
                    'icon' => 'success'
                ];
                mysqli_stmt_close($stmt);

                header("Location: moderatorPartnerPoints.php");
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
    <link rel="stylesheet" href="../../css/moderator.css">
    <?php include "../include/libraries.php";?>
    <link rel="icon" href="../../favicon.ico"> 
    <title>Partner Points Approval</title>   
</body>
</html>
</head>
<body>
    <script src="../../js/customSwal.js"></script> <!-- to include sweet alerts -->
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
    <?php while ($partnerRequest_info = mysqli_fetch_assoc($result_pending)): ?> <!-- loops through all pending submissions -->
        <tr>
            <td data-label="Transaction ID"><?php echo $partnerRequest_info['partner_transaction_id']; ?></td>
            <td data-label="User"><?php echo $partnerRequest_info['username']; ?></td>
            <td data-label="Partner"><?php echo $partnerRequest_info['partner_id']; ?></td>
            <td data-label="Award Time"><?php echo $partnerRequest_info['award_time']; ?></td>
            <td data-label="Amount Spent"><?php echo $partnerRequest_info['amount_spent']; ?></td>
            <td data-label="Points Awarded"><?php echo $partnerRequest_info['points_awarded']; ?></td>
            <td data-label="Status"><p class="taskStatus <?php echo $partnerRequest_info['status']; ?>">
                <?php echo $partnerRequest_info['status']; ?></p></td>
            <td data-label="View Button">
                <!-- to open modal popup -->
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

    <!-- Popup window -->
<div id="partnerPopup" class="modal">
    <div class="modalContent" id="partnerModal">
        <button class="closeModalBtn" id="closeModalBtn"> <!-- to close the modal -->
            <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 5L4.99998 19M5.00001 5L19 19" stroke="#1a5021" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/> <!-- for a close (X) icon -->
            </svg>
        </button>
        <h1>Review Partner Points</h1>
        <div id=partnerInfo>
            <p>Member: <span id="partnerUsername"></span></p>
            <p>Amount Spent: <span id="partnerAmount"></span></p>
            <p>Points Awarded: <span id="partnerPoints"></span></p>
            <p>Receipt:</p>
        <img id="partnerReceipt" src="" width="Receipt Image"> <!-- container for the image -->

        <form method="POST"> <!-- to submit the moderator's decision -->
            <input type="hidden" name="partner_transaction_id" id="partnerTransactionID"> <!-- to store the id of the partner transaction -->
            <!-- for the approve and reject buttons for the moderator's decision -->
            <div class="buttonGroup">
                <button type="submit" name="decision" value="approve">Approve</button>
                <button type="submit" name="decision" value="reject">Reject</button>
            </div>
        </form>
    </div>
</div>
    <?php include "../include/showSWAL.php"?>
    <script src="../../js/moderator.js"></script>
</body>
</html>
<?php mysqli_close($dbConn)?>