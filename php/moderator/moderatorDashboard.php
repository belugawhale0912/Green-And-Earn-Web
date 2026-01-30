<?php
    include "../include/conn.php";
    include "../include/session.php";
    include "../include/libraries.php";

    if($_SESSION['role_id']!= 3){
        header("Location: ../pages/Landingpage.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    //Pending counts for cards
    $sql_countSub = "SELECT COUNT(*) AS subCount FROM activity_submission WHERE status = 'Pending'";
    $result_pendingSub = mysqli_query($dbConn, $sql_countSub);
    $pendingSub = mysqli_fetch_assoc($result_pendingSub)['subCount'];

    $sql_countPoint = "SELECT COUNT(*) AS pointCount FROM partner_point_transaction WHERE status = 'Pending'";
    $result_pendingPoint = mysqli_query($dbConn, $sql_countPoint);
    $pendingPoint = mysqli_fetch_assoc($result_pendingPoint)['pointCount'];

    //Monthly Submissions Status
    $statusLabels = ['Approved', 'Pending', 'Rejected'];
    $statusData = [0,0,0];

    $sql_monthly = "SELECT status, COUNT(*) AS count
    FROM activity_submission
    WHERE YEAR(submission_time) = YEAR(CURDATE())
    AND MONTH(submission_time) = MONTH(CURDATE())
    GROUP BY status";

    $result_monthly = mysqli_query($dbConn, $sql_monthly);

    while($row = mysqli_fetch_assoc($result_monthly)){
        switch($row['status']){
            case 'Approved' : $statusData[0] = (int)$row['count']; break;
            case 'Pending' : $statusData[1] = (int)$row['count']; break;
            case 'Rejected' : $statusData[2] = (int)$row['count']; break;
        }
    }

    //Monthly Partner Points Awarded
    $partnerLabels = ['Approved', 'Pending', 'Rejected'];
    $partnerData = [0, 0, 0,];

    $sql_monthlyPartner = "SELECT status, COUNT(*) AS count
    FROM partner_point_transaction
    WHERE YEAR(award_time) = YEAR(CURDATE())
    AND MONTH(award_time) = MONTH(CURDATE())
    GROUP BY status";

    $result_monthlyPartner = mysqli_query($dbConn, $sql_monthlyPartner);

    while($row = mysqli_fetch_assoc($result_monthlyPartner)){
        switch($row['status']){
            case 'Approved' : $partnerData[0] = (int)$row['count']; break;
            case 'Pending' : $partnerData[1] = (int)$row['count']; break;
            case 'Rejected' : $partnerData[2] = (int)$row['count']; break;
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
    <title>Document</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <h2>Pending Approvals</h2>
    <section id="approval">
        <div class="cardContainer">
            <div class="cards">
                <h3>Submissions:</h3>
                <p class="cardNumber"><?php echo $pendingSub?></p>
            </div>

            <div class="cards">
                <h3>Partner Points:</h3>
                <p class="cardNumber"><?php echo $pendingPoint?></p>
            </div>
        </div>
    </section>

    <div class="chartContainer">
        <div class="chartCards">
            <h3>Current Month Submission Status</h3>
            <div class="canvas-container">
                <canvas id="monthlyStatusChart"></canvas>
            </div>
        </div>

        <div class="chartCards">
            <h3>Current Month Partner Points Awarded</h3>
            <div class="canvas-container">
                <canvas id="monthlyPartnerPointsChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        const statusLabels = <?php echo json_encode($statusLabels); ?>;
        const statusData = <?php echo json_encode($statusData); ?>;

        new Chart(document.getElementById('monthlyStatusChart'),{
            type: 'pie',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

        const partnerLabels = <?php echo json_encode($partnerLabels); ?>;
        const partnerData = <?php echo json_encode($partnerData); ?>;

        new Chart(document.getElementById('monthlyPartnerPointsChart'),{
            type: 'pie',
            data: {
                labels: partnerLabels,
                datasets: [{
                    data: partnerData,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
    </script>
</body>
</html>