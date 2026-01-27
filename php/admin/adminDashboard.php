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

    $memberLabels = ['Members', 'Moderators', 'Partners'];
    $memberData = [$memberCount, $moderatorCount, $partnerCount];
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/admin.css">
</head>
<body>
    <?php 
        include "../include/header.php"; 
    ?>

    <h2>Statistics</h2>
    
    <section id="stats" class="dashboardContainer">
        <div class="chartContainer">
            <canvas id="memberChart"></canvas>
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
                    maintainAspectRatio: false
                }
            });
        </script>
        
        <div class="cardContainer">

            <div class="cards">
                <h3><span class="statistics"><?php echo $rewardPendingCount?></span> <br>
                    Total Pending Rewards
                </h3>
            </div>

            <div class="cards">
                <h3><span class="statistics"><?php echo $rewardApprovedCount?></span> <br>
                    Total Approved Rewards
                </h3>
            </div>

            <div class="cards">
                <h3><span class="statistics"><?php echo $rewardAvailableCount?></span> <br>
                    Total Available Rewards
                </h3>
            </div>

            <div class="cards">
                <h3><span class="statistics"><?php echo $activityCount?></span> <br> 
                    Total Active Activities
                </h3>
            </div>
        </div>
    </section>

</body>
</html>