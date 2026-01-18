<?php
    session_start();
    $user_id = $_SESSION['user_id'];

    include "../include/conn.php";
    // $sql = "SELECT * FROM point_transaction WHERE user_id = $user_id ORDER BY transaction_time DESC";
    $sql = "
        SELECT
            pt.*, 
            -- The Magic Line: Pick the first non-empty title it finds
            COALESCE(r.title, a.title, pt.type) AS transaction_name
        FROM 
            point_transaction pt
        -- Path 1: Connect to Rewards
        LEFT JOIN 
            reward_redemption rr ON pt.redemption_id = rr.redemption_id
        LEFT JOIN 
            reward r ON rr.reward_id = r.reward_id
            
        -- Path 2: Connect to Activities
        LEFT JOIN 
            activity_submission asub ON pt.submission_id = asub.submission_id
        LEFT JOIN 
            activity a ON asub.activity_id = a.activity_id
            
        -- Path 3: Connect to Partner Transactions
        -- (Assuming you have a partner_trans_id column in point_transaction)
        LEFT JOIN
            partner_point_transaction ptrans ON pt.partner_transaction_id = ptrans.partner_transaction_id

        WHERE 
            pt.user_id = $user_id 
        ORDER BY 
            pt.transaction_time DESC
    ";
    $result = mysqli_query($dbConn,$sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/layout.css">
    <link rel="stylesheet" href="../../css/header.css">
    <title>Point Transaction</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <div class="list_container">
        <?php
            if (mysqli_num_rows($result)>0) {
                while($transaction_info = mysqli_fetch_assoc($result)){
                    echo("<div class='list_card'>");
                    echo("<h2>{$transaction_info['transaction_name']}</h2>");
                    echo("<h3>Amount:{$transaction_info['amount']}</h3>");
                    echo("<h3>{$transaction_info['transaction_time']}</h3>");
                    echo("</div>");                 
                }
            }
            else{
                echo("<h2>No Transaction Record!</h2>");
            }
        ?>
    </div>
</body>
</html>