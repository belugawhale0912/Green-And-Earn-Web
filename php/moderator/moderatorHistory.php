<?php
    include "../include/conn.php";
    include "../include/session.php";

    $view = isset($_GET['view']) ? $_GET['view'] : 'submissions';
    $filter = isset($_GET['status']) ? $_GET['status'] : 'all';

    if ($filter == "Approved"){
        $sql = "SELECT s.*, u.username, a.title AS activity_title
        FROM activity_submission s
        JOIN user u ON s.user_id = u.user_id
        JOIN activity a ON s.activity_id = a.activity_id
        WHERE s.status = 'Approved'";
    }else if ($filter == "Rejected"){
        $sql = "SELECT s.*, u.username, a.title AS activity_title
        FROM activity_submission s
        JOIN user u ON s.user_id = u.user_id
        JOIN activity a ON s.activity_id = a.activity_id
        WHERE s.status = 'Rejected'";
    }else{
        $sql = "SELECT s.*, u.username, a.title AS activity_title
        FROM activity_submission s
        JOIN user u ON s.user_id = u.user_id
        JOIN activity a ON s.activity_id = a.activity_id
        WHERE s.status IN ('Approved', 'Rejected')";
    }

    $result = mysqli_query($dbConn, $sql);

    $partnerFilter = isset($_GET['status']) ? $_GET['status'] : 'all';

    if($partnerFilter == "Approved"){
        $sql_partner = "SELECT p.*, u.username
        FROM partner_point_transaction p
        JOIN user u ON p.user_id = u.user_id
        WHERE p.status = 'Approved'
        ORDER BY p.award_time DESC";
    }else if ($partnerFilter == "Rejected"){
        $sql_partner = "SELECT p.*, u.username
        FROM partner_point_transaction p
        JOIN user u ON p.user_id = u.user_id
        WHERE p.status = 'Rejected'
        ORDER BY p.award_time DESC";
    }else{
        $sql_partner = "SELECT p.*, u.username
        FROM partner_point_transaction p
        JOIN user u ON p.user_id = u.user_id
        WHERE p.status IN ('Approved', 'Rejected')
        ORDER BY p.award_time DESC";
    }

    $result_partner = mysqli_query($dbConn, $sql_partner);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/moderator.css">
    <title>History Viewing</title>
</head>
<body>
<?php
    include "../include/header.php";
?>
<div id="historyViewControl">
    <a href="moderatorHistory.php?view=submissions"
        class="<?php echo ($view ?? 'submissions')=='submissions' ? 'active' : ''; ?>">Submission</a>
    <a href="moderatorHistory.php?view=partnerPoints"
        class="<?php echo ($view ?? '')=='partnerPoints' ? 'active' : ''; ?>">Partner Points</a>
</div>
<h1>Approval History</h1>
<?php if($view == 'submissions'): ?>
    <section id="History">
        <form method="GET" id="filterForm">
            <input type="hidden" name="view" value="submissions">
            <label for="statusFilter"></label>
            <select class="filter" name="status" id="statusFilter">
                <option value="all" <?php if ($filter == "all") echo 'selected'; ?>>All</option>
                <option value="Approved" <?php if ($filter == "Approved") echo 'selected'; ?>>Approved</option>
                <option value="Rejected" <?php if ($filter == "Rejected") echo 'selected'; ?>>Rejected</option>
            </select>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Submission ID</th>
                    <th>User</th>
                    <th>Activity</th>
                    <th>Status</th>
                    <th>Submission Time</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td data-label="Submission ID"><?php echo $row['submission_id']; ?></td>
                        <td data-label="User"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td data-label="Activity"><?php echo htmlspecialchars($row['activity_title']); ?></td>
                        <td data-label="Status"><p class="taskStatus <?php echo $row['status']; ?>">
                            <?php echo $row['status']; ?></p></td>
                        <td data-label="Submission Time"><?php echo $row['submission_time']; ?></td>
                        <td data-label="View Attachment">
                            <button type="button" class="viewSubmissionBtn"
                                data-id="<?php echo $row['submission_id']; ?>"
                                data-username="<?php echo htmlspecialchars($row['username'], ENT_QUOTES); ?>"
                                data-activity="<?php echo htmlspecialchars($row['activity_title'], ENT_QUOTES); ?>"
                                data-time="<?php echo $row['submission_time']; ?>"
                                data-proof="<?php echo $row['proof']; ?>"
                            >
                                View Attachment
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
    <div id="submissionPopup" class="modal">
        <div class="modalContent" id="submissionModal">
            <h1>Submission Details</h1>
            <div id="submissionInfo">
                <p>Username: <span id="popupUsername"></span></p>
                <p>Activity: <span id="popupActivity"></span></p>
                <p>Submission Time: <span id="popupSubmissionTime"></span></p>
                <p>Proof:</p>
            </div>
            <img id="popupProof" src="" alt="Proof Image">
            <input type="hidden" id="submission_id" name="submission_id">
        </div>
    </div>
<?php endif; ?>


<?php if($view == 'partnerPoints'): ?>
    <section id="PartnerPointsHistory">
        <form method="GET" id="filterFormPartner">
            <input type="hidden" name="view" value="partnerPoints">
            <label for="statusFilterPartner"></label>
            <select class="filter" name="status" id="statusFilterPartner">
                <option value="all" <?php if($partnerFilter == 'all') echo 'selected'; ?>>All</option>
                <option value="Approved" <?php if($partnerFilter == 'Approved') echo 'selected'; ?>>Approved</option>
                <option value="Rejected" <?php if($partnerFilter == 'Rejected') echo 'selected'; ?>>Rejected</option>
            </select>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>User</th>
                    <th>Partner</th>
                    <th>Amount Spent</th>
                    <th>Points Awarded</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result_partner)): ?>
                <tr>
                    <td data-label="Transaction ID"><?php echo $row['partner_transaction_id']; ?></td>
                    <td data-label="User"><?php echo htmlspecialchars($row['username']); ?></td>
                    <td data-label="Partner"><?php echo $row['partner_id']; ?></td>
                    <td data-label="Amount Spent"><?php echo $row['amount_spent']; ?></td>
                    <td data-label="Points Awarded"><?php echo $row['points_awarded']; ?></td>
                    <td data-label="Status"><p class="taskStatus <?php echo $row['status']; ?>">
                        <?php echo $row['status']; ?></p></td>
                    <td data-label="View Attachment">
                        <button type="button" class="viewPartnerBtn"
                            data-id="<?php echo $row['partner_transaction_id']; ?>"
                            data-username="<?php echo htmlspecialchars($row['username'], ENT_QUOTES); ?>"
                            data-amount="<?php echo $row['amount_spent']; ?>"
                            data-points="<?php echo $row['points_awarded']; ?>"
                            data-receipt="<?php echo $row['receipt_img']; ?>"
                        >
                            View Attachment
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
    <div id="partnerPopup" class="modal">
        <div class="modalContent" id="partnerModal">
            <h1>Partner Points Details</h1>
            <div id="partnerInfo">
                <p>Username: <span id="partnerUsername"></span></p>
                <p>Amount Spent: <span id="partnerAmount"></span></p>
                <p>Points Awarded: <span id="partnerPoints"></span></p>
                <p>Receipt:</p>
            </div>
            <img id="partnerReceipt" src="" alt="Receipt Image">
            <input type="hidden" id="partnerTransactionID" name="partnerTransactionID">
        </div>
    </div>
<?php endif; ?>

    <script src="../../js/moderator.js" defer></script>
</body>
</html>