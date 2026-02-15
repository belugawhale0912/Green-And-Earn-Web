<?php
    include "../include/conn.php"; // to establish database connection
    include "../include/session.php"; // start session and manages login data
    include "../include/libraries.php"; // loads any shared libraries

    // to check the user's role
    if($_SESSION['role_id']!= 3){ // if not moderator
        header("Location: ../pages/Landingpage.php"); // redirect to landing page
        exit();
    }

    $user_id = $_SESSION['user_id']; // stores the current logged-in user id

    //Pending counts for cards
    $sql_countSub = "SELECT COUNT(*) AS subCount FROM activity_submission WHERE status = 'Pending'"; // counts how many are pending
    $result_pendingSub = mysqli_query($dbConn, $sql_countSub); // execute the query
    $pendingSub = mysqli_fetch_assoc($result_pendingSub)['subCount']; // to get the value

    $sql_countPoint = "SELECT COUNT(*) AS pointCount FROM partner_point_transaction WHERE status = 'Pending'";
    $result_pendingPoint = mysqli_query($dbConn, $sql_countPoint);
    $pendingPoint = mysqli_fetch_assoc($result_pendingPoint)['pointCount'];

    //Monthly Submissions Status
    $statusLabels = ['Approved', 'Pending', 'Rejected']; // define labels for chart
    $statusData = [0,0,0]; // initialize to zero to avoid undefined errors when empty
 
    // count total submissions by status this month
    $sql_monthly = "SELECT status, COUNT(*) AS count 
    FROM activity_submission
    WHERE YEAR(submission_time) = YEAR(CURDATE())
    AND MONTH(submission_time) = MONTH(CURDATE())
    GROUP BY status";

    $result_monthly = mysqli_query($dbConn, $sql_monthly); // execute the query

    // fetch result as an assotiative array
    while($row = mysqli_fetch_assoc($result_monthly)){
        // make sure that it counts from the correct position  
        switch($row['status']){
            case 'Approved' : $statusData[0] = (int)$row['count']; break;
            case 'Pending' : $statusData[1] = (int)$row['count']; break;
            case 'Rejected' : $statusData[2] = (int)$row['count']; break;
        }
    }

    //Monthly Partner Points Awarded
    $partnerLabels = ['Approved', 'Pending', 'Rejected']; // define labels for chart
    $partnerData = [0, 0, 0,]; // initialize to zero to avoid undefined errors when empty

    // count total submissions by status this month
    $sql_monthlyPartner = "SELECT status, COUNT(*) AS count
    FROM partner_point_transaction
    WHERE YEAR(award_time) = YEAR(CURDATE())
    AND MONTH(award_time) = MONTH(CURDATE())
    GROUP BY status";

    $result_monthlyPartner = mysqli_query($dbConn, $sql_monthlyPartner); //execute the query

    // fetch resutl as an assotiative array
    while($row = mysqli_fetch_assoc($result_monthlyPartner)){
        // make sure that it counts from the correct position
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
    <link rel="icon" href="../../favicon.ico">
    <title>Dashboard</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <h2>Pending Approvals</h2>
    <!-- section for dashboard summary cards -->
    <section id="approval">
        <div class="cardContainer">
            <!-- card for pending submissions -->
            <div class="cards">
                <h3>Submissions:</h3>
                <!-- display number of pending submission dynamically -->
                <p class="cardNumber"><?php echo $pendingSub?></p> 
            </div>

            <!-- card for pending partner points -->
            <div class="cards">
                <h3>Partner Points:</h3>
                <!-- display number of pending partner points dynamically -->
                <p class="cardNumber"><?php echo $pendingPoint?></p>
            </div>
        </div>
    </section>

    <!-- section for charts -->
    <div class="chartContainer">
        <!-- chart for monthly submission status -->
        <div class="chartCards">
            <h3>Current Month Submission Status</h3>
            <div class="canvas-container">
                <canvas id="monthlyStatusChart"></canvas>
                <!-- Chart.js will make the pie chart using php data -->
            </div>
        </div>

        <!-- chart for montly partner points status -->
        <div class="chartCards">
            <h3>Current Month Partner Points Awarded</h3>
            <div class="canvas-container">
                <canvas id="monthlyPartnerPointsChart"></canvas>
                <!-- Chart.js will make the pie chart using php date -->
            </div>
        </div>
    </div>

    <script>
        // convert php array into javascript arrays
        const statusLabels = <?php echo json_encode($statusLabels); ?>;
        const statusData = <?php echo json_encode($statusData); ?>;

        // to create the pie chart
        new Chart(document.getElementById('monthlyStatusChart'),{
            type: 'pie',
            data: {
                labels: statusLabels, // slice labels
                datasets: [{
                    data: statusData, // number of submissions per status
                    }]
                },
                options: {
                    responsive: true, // chart resizes with window
                    maintainAspectRatio: false // lets it fit inside the container properly
                }
            });

        // convert php array into javascript arrays
        const partnerLabels = <?php echo json_encode($partnerLabels); ?>;
        const partnerData = <?php echo json_encode($partnerData); ?>;

        // to creat the pie chart
        new Chart(document.getElementById('monthlyPartnerPointsChart'),{
            type: 'pie',
            data: {
                labels: partnerLabels, // slice labels
                datasets: [{
                    data: partnerData, // number of submissions per status
                    }]
                },
                options: {
                    responsive: true, // chart resizes with window
                    maintainAspectRatio: false // lets it fit inside the container properly
                }
            });
    </script>
</body>
</html>
<?php mysqli_close($dbConn)?>