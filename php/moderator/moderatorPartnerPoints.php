<?php
include "../include/conn.php";
include "../include/session.php";

$moderator_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $transaction_id = $_POST['partner_transaction_id'];
    $decision = $_POST['decision'];

    $dbConn->begin_transaction();

    try {

        if ($decision == 'approve') {
            $sql_update = "UPDATE partner_point_transaction
                           SET status = 'Approved'
                           WHERE partner_transaction_id = '$transaction_id'";
        } 
        elseif ($decision == 'reject') {
            $sql_update = "UPDATE partner_point_transaction
                           SET status = 'Rejected'
                           WHERE partner_transaction_id = '$transaction_id'";
        }

        if (!mysqli_query($dbConn, $sql_update)) {
            throw new Exception("Update failed");
        }

        $dbConn->commit();

        echo "<script>
                alert('Transaction {$decision}!');
                window.location.href = 'moderatorPartnerPoints.php';
              </script>";
        exit();
    }
    catch (Exception $e) {
        $dbConn->rollback();
        echo "<script>
                alert('Error while processing transaction.');
                window.location.href = 'moderatorPartnerPoints.php';
              </script>";
        exit();
    }
}

$sql_pending = "SELECT p.*, u.username
FROM partner_point_transaction p
JOIN user u ON p.user_id = u.user_id
WHERE p.status = 'Pending'
ORDER BY p.award_time DESC";

$result_pending = mysqli_query($dbConn, $sql_pending);
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

    <?php while ($row = mysqli_fetch_assoc($result_pending)): ?>
        <tr>
            <td data-label="Transaction ID"><?php echo $row['partner_transaction_id']; ?></td>
            <td data-label="User"><?php echo $row['username']; ?></td>
            <td data-label="Partner"><?php echo $row['partner_id']; ?></td>
            <td data-label="Award Time"><?php echo $row['award_time']; ?></td>
            <td data-label="Amount Spent"><?php echo $row['amount_spent']; ?></td>
            <td data-label="Points Awarded"><?php echo $row['points_awarded']; ?></td>
            <td data-label="Status"><?php echo $row['status']; ?></td>
            <td>
                <button onclick="openPartnerModal(
                    '<?php echo $row['partner_transaction_id']; ?>',
                    '<?php echo htmlspecialchars($row['username'], ENT_QUOTES); ?>',
                    '<?php echo $row['amount_spent']; ?>',
                    '<?php echo $row['points_awarded']; ?>',
                    '<?php echo $row['reciept_img']; ?>'
                )">
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
        <p>Username: <span id="partnerUsername"></span></p>
        <p>Amount Spent: <span id="partnerAmount"></span></p>
        <p>Points Awarded: <span id="partnerPoints"></span></p>
        <p>Receipt:</p>
        <img id="partnerReciept" src="" width="200">

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