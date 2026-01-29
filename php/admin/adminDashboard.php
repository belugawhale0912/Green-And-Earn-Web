<?php
    include "../include/conn.php";
    include "../include/session.php";

    // 1. Security Check (Role ID 1 = Admin)
    if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
        header("Location: ../pages/Landingpage.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    $sql_countUsers = "SELECT COUNT(*) AS totalUser FROM user";
    $result_userCount = mysqli_query($dbConn, $sql_countUsers);
    $userCount = mysqli_fetch_assoc($result_userCount)['totalUser'];

    $sql_countMembers = "SELECT COUNT(*) AS totalMember FROM user WHERE role_id =2";
    $result_memberCount = mysqli_query($dbConn, $sql_countMembers);
    $memberCount = mysqli_fetch_assoc($result_memberCount)['totalMember'];

    $sql_countModerators = "SELECT COUNT(*) AS totalModerator FROM user WHERE role_id =3";
    $result_moderatorCount = mysqli_query($dbConn, $sql_countModerators);
    $moderatorCount = mysqli_fetch_assoc($result_moderatorCount)['totalModerator'];

    $sql_countPartners = "SELECT COUNT(*) AS totalPartner FROM user WHERE role_id =4";
    $result_partnerCount = mysqli_query($dbConn, $sql_countPartners);
    $partnerCount = mysqli_fetch_assoc($result_partnerCount)['totalPartner'];

    $sql_countRewardPending = "SELECT COUNT(*) AS totalRewardPending FROM reward WHERE status = 'pending' ";
    $result_rewardPendingCount = mysqli_query($dbConn, $sql_countRewardPending);
    $rewardPendingCount = mysqli_fetch_assoc($result_rewardPendingCount)['totalRewardPending'];

    $sql_countRewardApproved = "SELECT COUNT(*) AS totalRewardApproved FROM reward WHERE status = 'approved' ";
    $result_rewardApprovedCount = mysqli_query($dbConn, $sql_countRewardApproved);
    $rewardApprovedCount = mysqli_fetch_assoc($result_rewardApprovedCount)['totalRewardApproved'];

    $sql_countRewardAvailable = "SELECT COUNT(*) AS totalRewardAvailable FROM reward WHERE status = 'available' ";
    $result_rewardAvailableCount = mysqli_query($dbConn, $sql_countRewardAvailable);
    $rewardAvailableCount = mysqli_fetch_assoc($result_rewardAvailableCount)['totalRewardAvailable'];

    $sql_countActivity = "SELECT COUNT(*) AS totalActivity FROM activity WHERE start_date <= CURDATE() AND end_date >= CURDATE()";
    $result_activityCount = mysqli_query($dbConn, $sql_countActivity);
    $activityCount = mysqli_fetch_assoc($result_activityCount)['totalActivity'];

    $sql_countPoints = "SELECT 
                                DATE(transaction_time) AS day,
                                SUM(CASE WHEN type = 'Issued' THEN amount ELSE 0 END) AS issued,
                                SUM(CASE WHEN type = 'Redemption' THEN amount ELSE 0 END) AS redeemed
                                FROM point_transaction
                                WHERE transaction_time >= CURDATE() - INTERVAL 6 DAY
                                GROUP BY day
                                ORDER BY day
                            ";
    $result_countPoints = mysqli_query($dbConn, $sql_countPoints);

    $memberLabels = ['Members', 'Moderators', 'Partners'];
    $memberData = [$memberCount, $moderatorCount, $partnerCount];

    $pointLabels = [];
    $pointsIssuedData = [];
    $pointsRedeemedData = [];

    while ($row = mysqli_fetch_assoc($result_countPoints)) {
    $pointLabels[] = $row['day'];
    $pointsIssuedData[] = (int)$row['issued'];
    $pointsRedeemedData[] = (int)$row['redeemed'];
}
?>

<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" href="/Green-Earn-Web/favicon.ico">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/admin.css">
</head>
<body>
    <?php 
        include "../include/header.php"; 
    ?>

    <h2 class="pageTitle">Dashboard</h2>
    <section id="stats" class="dashboardContainer">
        <div class="statsContainer">
            <h3 class="statsTitle">Statistics</h3>
                <div class="cardContainer">
                    <div class="cards">
                        <h3><span class="statistics"><?php echo $rewardPendingCount?></span>
                            <span class="statLabel">Total Pending Rewards</span>
                        </h3>
                    </div>

                    <div class="cards">
                        <h3><span class="statistics"><?php echo $rewardApprovedCount?></span>
                            <span class="statLabel">Total Approved Rewards</span>
                        </h3>
                    </div>

                    <div class="cards">
                        <h3><span class="statistics"><?php echo $rewardAvailableCount?></span>
                            <span class="statLabel">Total Available Rewards</span>
                        </h3>
                    </div>

                    <div class="cards">
                        <h3><span class="statistics"><?php echo $activityCount?></span>
                            <span class="statLabel">Total Active Activities</span>
                        </h3>
                    </div>
                </div>
        </div>
            <div class="chartContainer">
                <div class="memberChartWrapper">
                    <h3 class="statsTitle">User Distribution</h3>
                        <div>
                            <canvas id="memberChart"></canvas>
                        </div>
                </div>
                <div class="pointChartWrapper">
                    <h3 class="statsTitle">Points Distribution</h3>
                        <div>
                            <canvas id="pointChart"></canvas>
                        </div>
                </div>
            </div>
        <script>
            const memberLabels = <?php echo json_encode($memberLabels); ?>;
            const memberData = <?php echo json_encode($memberData); ?>;

            new Chart(document.getElementById('memberChart'), {
                type: 'pie',
                data: {
                    labels: memberLabels,
                    datasets: [{
                        data: memberData,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                }
            });
        </script>

        <script>
            const pointLabels = <?php echo json_encode($pointLabels); ?>;
            const pointsIssuedData =  <?php echo json_encode($pointsIssuedData); ?>;
            const pointsRedeemedData =  <?php echo json_encode($pointsRedeemedData); ?>;

            new Chart(document.getElementById('pointChart'), {
                type: 'line',
                data: {
                    labels: pointLabels,
                    datasets: [{
                        label: 'Points Issued',
                        data: pointsIssuedData,
                        fill: false,
                        borderColor: 'green',
                        tension: 0.3,
                    },{
                        label: 'Points Redeemed',
                        data: pointsRedeemedData,
                        fill: false,
                        borderColor: 'red',
                        tension: 0.3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                    
                }
            });
        </script>
    </section>
        
    

</body>
</html>