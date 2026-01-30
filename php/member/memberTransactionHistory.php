<?php
    session_start();
    $user_id = $_SESSION['user_id'];

    include "../include/conn.php";

    // TODO check query
    $sql = "
        SELECT pt.*, COALESCE(r.title, a.title, u_partner.username, pt.type) AS transaction_name,

        CASE
            WHEN pt.type = 'Refund' THEN 'Refund' 
            WHEN pt.submission_id IS NOT NULL THEN 'Task Completion'
            WHEN pt.partner_transaction_id IS NOT NULL THEN 'Shop At Partner Store'
            WHEN pt.redemption_id IS NOT NULL THEN 'Redemption'
            ELSE pt.type
        END AS display_category

        FROM point_transaction pt
        LEFT JOIN reward_redemption rr ON pt.redemption_id = rr.redemption_id
        LEFT JOIN reward r ON rr.reward_id = r.reward_id

        LEFT JOIN activity_submission asub ON pt.submission_id = asub.submission_id
        LEFT JOIN activity a ON asub.activity_id = a.activity_id

        LEFT JOIN partner_point_transaction ptrans ON pt.partner_transaction_id = ptrans.partner_transaction_id
        LEFT JOIN user u_partner ON ptrans.partner_id = u_partner.user_id

        WHERE pt.user_id = $user_id 
        ORDER BY pt.transaction_time DESC
    ";
    $result = mysqli_query($dbConn,$sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/member.css">
    <link rel="icon" href="/GreenAndEarnWeb/favicon.ico">
    <title>Point Transaction</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <h1>Transaction History</h1>

    <div class="transactionContainer">
        <?php if (mysqli_num_rows($result)>0):?>
            <select class="filter" id="transactionFilter">
                <option value="All">All</option>
                <option value="Redemption">Spent</option>
                <option value="Earned">Earned</option>
                <option value="Refund">Refund</option>
            </select>

            <?php while($transaction_info = mysqli_fetch_assoc($result)):?>
                <div class='card transactionCard' data-transactionType="<?php echo $transaction_info['type']?>">
                    <div class="transactionInfo">
                        <h2><?php echo $transaction_info['display_category']?></h2>
                        <p class="transactionName"><?php echo $transaction_info['transaction_name']?></p>
                    </div>
                    <div class="transactionInfo">
                        <div class="tamountContainer">
                            <h3>Amount:</h3>
                            <h3 class="transactionType <?php echo $transaction_info['type']?>">
                                <?php echo $transaction_info['amount']?>
                            </h3>
                        </div>    
                        <p class="transactionTime"><?php echo date("d-m-y h:i A", strtotime($transaction_info['transaction_time']))?></p>
                    </div>
                </div>
            <?php endwhile;?>

        <?php else:?>
            <h2>No Transaction Record!</h2>
        <?php endif;?>
    </div>

    <script src="../../js/member.js"></script>
</body>
</html>