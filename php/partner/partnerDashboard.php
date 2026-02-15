<?php
    include "../include/conn.php";
    include "../include/session.php";

    // check if the current user is partner, redirect to landing page if not partner
    if($_SESSION['role_id'] != 4){
        header("Location: ../pages/Landingpage.php");
        exit();
    }

    $partner_id = $_SESSION['user_id'];

    $sqllistReward = "SELECT * FROM reward WHERE partner_id = $partner_id AND status != 'deleted'
        ORDER BY reward_id ASC LIMIT 4";
    $result = mysqli_query($dbConn,$sqllistReward);

    $sqlTotal = "SELECT COUNT(*) as total
                 FROM reward_redemption rr
                 JOIN reward r ON rr.reward_id = r.reward_id
                 WHERE r.partner_id = $partner_id AND rr.status = 'Approved'";
    
    $result_total = mysqli_query($dbConn, $sqlTotal);
    $countResult = mysqli_fetch_assoc($result_total);
    $totalRewardRedeemed = $countResult['total'];

    //Generate last 7 days (Empty Skeleton)
    $last7Days = []; //Create empty array
    //Loops 7 times (6 = six days ago, 0 is today)
    for ($i = 6; $i >= 0; $i--) { //Initialise; condition; decrement
        $date = date('Y-m-d', strtotime("-$i days")); //Strtotime convert text to time
        $last7Days[$date] = [
            'label' => date('D d', strtotime($date)), //Day of week, day of month
            'sales' => 0 
        ];
    }

    //Query database (Summing amount_spent now)
    $sql_partner_chart1 = "SELECT DATE(award_time) as trans_date, SUM(amount_spent) as total_sales
                          FROM partner_point_transaction 
                          WHERE partner_id = ?
                          AND status != 'Rejected'
                          AND award_time >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                          GROUP BY DATE(award_time)";

    $stmt = mysqli_prepare($dbConn, $sql_partner_chart1);
    mysqli_stmt_bind_param($stmt, "i", $partner_id);
    mysqli_stmt_execute($stmt);
    $res_partner_chart = mysqli_stmt_get_result($stmt);

    //Fill data
    while($row = mysqli_fetch_assoc($res_partner_chart)){
        $dbDate = $row['trans_date'];
        if(isset($last7Days[$dbDate])){
            $last7Days[$dbDate]['sales'] = $row['total_sales'];
        }
    }

    //Prepare for js
    $pChartLabels = [];
    $pChartData = [];
    $totalSalesCheck = 0;

    foreach ($last7Days as $day) {
        $pChartLabels[] = $day['label'];
        $pChartData[] = $day['sales'];
        $totalSalesCheck += $day['sales'];
    }

    $sql_partner_chart2 = "SELECT r.title, COUNT(*) as redemption_count
                FROM reward_redemption rr
                JOIN reward r ON rr.reward_id = r.reward_id WHERE r.partner_id = ?
                GROUP BY r.reward_id, r.title
                ORDER BY COUNT(*) DESC LIMIT 5";
    $stmt = mysqli_prepare($dbConn, $sql_partner_chart2);
    mysqli_stmt_bind_param($stmt, "i", $partner_id);
    mysqli_stmt_execute($stmt);
    $res_partner_chart2 = mysqli_stmt_get_result($stmt);

    //Fetch all 5 results into an array
    $topRewards = [];
    while ($row = mysqli_fetch_assoc($res_partner_chart2)) {
        $topRewards[] = [
            'title' => $row['title'],
            'redemption_count' => $row['redemption_count']
        ];
    }

    //Prepare data for js
    $topRewardLabels = array_column($topRewards, 'title');
    $topRewardCount = array_column($topRewards, 'redemption_count');
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/partner.css">
    <?php include "../include/libraries.php";?>
    <link rel="icon" href="../../favicon.ico">
    <title>Partner Dashboard</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <h1>Welcome Back, <?php echo $_SESSION['username']?>!</h1>
    <div class="dashboardContainer">
        <div class="chartContainer">      
            <div class="chartCard">
                <h2>Revenue From Members (Last 7 Days)</h2>
                <div class="chart">
                    <canvas id="partnerSalesChart"></canvas>
                </div>
            </div>

            <div class="chartCard">
                <h2>Most Redeemed Reward</h2>
                <div class="chart">
                    <canvas id="topRewardChart"></canvas>
                </div>
            </div>
        </div>
        
        <h2>List of Rewards</h2>
        <a href="../../php/partner/partnerRewardList.php" class="viewAllReward">View All Rewards</a>
        <div class="rewardListContainer">
            <?php if(mysqli_num_rows($result)>0):?>
                <?php while($reward_info = mysqli_fetch_assoc($result)):?>
                    <div class="rewardListCard">
                        <img src="<?php echo $reward_info['reward_img']?>" alt="rewardImage">
                        <p><?php echo $reward_info['title']?></p>
                        <p>Points Required: <?php echo $reward_info['required_points']?></p>
                    </div>
                <?php endwhile;?>
            <?php endif;?>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() { //Wait page to be fully loaded
            const ctxPartner = document.getElementById('partnerSalesChart'); //Get ID store in constant

            if (ctxPartner && ctxPartner.style.display !== 'none') { //Safety check (canva exists+not hidden)
                new Chart(ctxPartner, { //Create new chart
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($pChartLabels); ?>, //X-axis labels
                        datasets: [{
                            label: 'Sales Revenue (RM)',
                            data: <?php echo json_encode($pChartData); ?>,
                            // Teal/Green color to represent Money/Profit
                            backgroundColor: 'rgba(0, 150, 136, 0.7)', 
                            borderColor: 'rgba(0, 150, 136, 1)',
                            borderWidth: 1,
                            borderRadius: 4,
                            barThickness: 'flex',
                            maxBarThickness: 50
                        }]
                    },
                    options: { //Controls layout, behavior, plugins, scales
                        responsive: true,
                        maintainAspectRatio: false, //Allow stretch
                        plugins: {
                            legend: { display: false }, //Dont need legend
                            tooltip: { //When hover
                                callbacks: {
                                    label: function(context) { //Make hover show actual data value
                                        // Format tooltip as Currency (e.g. RM 50.00)
                                        return 'RM ' + Number(context.raw).toFixed(2);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Amount (RM)' },
                                grid: { color: '#f0f0f0' },
                                ticks: {
                                    callback: function(value) { //Adds RM in front of each Y-axis value.    
                                        return 'RM ' + value;
                                    }
                                }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() { 
            const ctxPartner2 = document.getElementById('topRewardChart');

            if (ctxPartner2 && ctxPartner2.style.display !== 'none') {
                new Chart(ctxPartner2, {
                    type: 'bar',
                    data: {
                        labels: <?php echo json_encode($topRewardLabels); ?>,
                        datasets: [{
                            label: 'Number of Redemptions',
                            data: <?php echo json_encode($topRewardCount); ?>,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        indexAxis: 'y', // Horizontal Bar
                        responsive: true,
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Top 5 Most Redeemed Rewards'
                            },
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
<?php mysqli_close($dbConn)?>