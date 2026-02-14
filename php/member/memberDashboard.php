<?php
    include "../include/conn.php";
    include "../include/session.php";
    
    // ensure the user is member, if not redirect to landing page
    if($_SESSION['role_id'] != 2){
        header("Location: ../pages/Landingpage.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $today = date("Y-m-d");

    $sql = "SELECT * FROM user WHERE user_id = ?";
    $stmt = mysqli_prepare($dbConn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    if (mysqli_num_rows($result)>0) {
        $user_info = mysqli_fetch_assoc($result);
    }

    
    //Bar Chart Points Activity 
    $last7Days = [];
    // Initialize 0 values for the last 7 days to ensure that no day is skipped
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $last7Days[$date] = [
            'label' => date('d-m-D', strtotime($date)), 
            'earned' => 0,
            'spent' => 0
        ];
    }

    // sum the amount and group by date and type
    $sql_chart = "SELECT DATE(transaction_time) as trans_date, type, SUM(amount) as total 
                  FROM point_transaction 
                  WHERE user_id = ? 
                  AND transaction_time >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                  GROUP BY DATE(transaction_time), type";

    $stmtBar = mysqli_prepare($dbConn, $sql_chart);
    mysqli_stmt_bind_param($stmtBar, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmtBar);
    $res_chart = mysqli_stmt_get_result($stmtBar);
    mysqli_stmt_close($stmtBar);

    while($data = mysqli_fetch_assoc($res_chart)){
        $dbDate = $data['trans_date'];
        
        if(isset($last7Days[$dbDate])){
            if($data['type'] == 'Earned'){
                // update earned value 
                $last7Days[$dbDate]['earned'] = $data['total'];
            } 

            else if($data['type'] == 'Redemption'){
                // update spent value 
                $last7Days[$dbDate]['spent'] = $data['total'];
            }
        }
    }

    // Separate into simple arrays for Chart
    $chartLabels = array_column($last7Days, 'label');
    $chartEarned = array_column($last7Days,'earned');
    $chartSpent = array_column($last7Days,'spent');


    // Pie Chart (My Task Status)
    // initialize default values to ensure chart works even with 0 tasks
    $taskData = [
        'Pending' => 0,
        'Approved' => 0,
        'Rejected' => 0
    ];

    // count and group by status
    $sql_pie = "SELECT status, COUNT(*) as total 
                FROM activity_submission 
                WHERE user_id = ? 
                GROUP BY status";

    $stmtPie = mysqli_prepare($dbConn, $sql_pie);
    mysqli_stmt_bind_param($stmtPie, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmtPie);
    $res_pie = mysqli_stmt_get_result($stmtPie);
    mysqli_stmt_close($stmtPie);


    while($data = mysqli_fetch_assoc($res_pie)){
        $status = $data['status']; 
        $taskData[$status] = $data['total'];
    }

    // Prepare simple arrays for Chart.js
    $pieLabels = ['Approved', 'Pending', 'Rejected'];
    $pieValues = [
        $taskData['Approved'], 
        $taskData['Pending'], 
        $taskData['Rejected']
    ];

    $sqlGetTask = "SELECT COUNT(*) AS availableTask 
               FROM activity 
               WHERE status = 'Active' 
               AND '$today' BETWEEN start_date AND end_date
               AND activity_id NOT IN (
                    SELECT activity_id 
                    FROM activity_submission 
                    WHERE user_id = ? 
                    AND (status = 'Pending' OR status = 'Approved')
                )";

    $stmtTask = mysqli_prepare($dbConn, $sqlGetTask);
    mysqli_stmt_bind_param($stmtTask, "i", $user_id);
    mysqli_stmt_execute($stmtTask);
    $resultGet = mysqli_stmt_get_result($stmtTask);
    mysqli_stmt_close($stmtTask);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/member.css">
    <link rel="icon" href="/GreenAndEarnWeb/favicon.ico">
    <?php include "../include/libraries.php";?>
    <title>Dashboard</title>
</head>
<body>
    <?php
        include("../include/header.php");
        echo("<h1>Welcome back, ".$_SESSION['username']."</h1>");
    ?>
    <div class="dashboardContainer">
        <div class="chartContainer">
            <div class="card chartCard">
                <h3>Points Activity (Last 7days)</h3>
                <div class="canvas-container">
                    <canvas id="weeklyActivityChart"></canvas>
                </div>
            </div>

            <div class="card chartCard">
                <h3>My Task Status</h3>
                <div class="canvas-container">
                    <canvas id="taskStatusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="quickAccess">
            <section class=" card quickAccessCard" id="point_balance">
                <h2>Point Balance:</h2>
                <?php
                    echo('<h3>'.$user_info['point_balance'].' pts'.'</h3>');
                ?>
                <a class="card_links" href="memberTransactionHistory.php">View Transactions</a>
            </section>

        <section class="card quickAccessCard" id="eco_facts">
                <h2>Fun Fact</h2>
                <p>
                    <?php
                        // list of fun facts 
                        $facts = [
                            "Recycling one aluminum can saves enough energy to run a TV for 3 hours.",
                            "A single tree can absorb 48 pounds of carbon dioxide per year.",
                            "Glass is 100% recyclable and can be recycled endlessly without loss in quality.",
                            "Turning off the tap while brushing your teeth can save up to 8 gallons of water a day.",
                            "Plastic bags take anywhere from 10 to 1,000 years to decompose.",
                            "Energy-efficient LED light bulbs use 75% less energy than incandescent lighting.",
                            "The average person generates over 4 pounds of trash every day."
                        ];

                        // use array_rand to get random index so that the facts shuffle 
                        $randomIndex = array_rand($facts);
                        echo $facts[$randomIndex];
                    ?>
                </p>
            </section>

            <section class="card quickAccessCard" id="tasks">
                <h2>Tasks</h2>
                <?php if(mysqli_num_rows($resultGet)>0):?>
                    <?php $availableTaskCount = mysqli_fetch_assoc($resultGet)['availableTask'];?>
                    <p><?php echo $availableTaskCount?> New Tasks Available</p>
                    <a href="memberTaskList.php">Complete Now</a>
                <?php endif?>
            </section>
        </div>
    </div>


    <script>
    
    // Bar chart
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('weeklyActivityChart');

        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    // use json_encode to turn php var into json format so javascript can read the value
                    labels: <?php echo json_encode($chartLabels); ?>, 
                    datasets: [
                        {
                            label: 'Points Earned',
                            data: <?php echo json_encode($chartEarned); ?>,
                            backgroundColor: '#4CAF50', 
                            borderRadius: 4,
                            barPercentage: 0.6,
                        },
                        {
                            label: 'Points Spent',
                            data: <?php echo json_encode($chartSpent); ?>,
                            backgroundColor: '#FF5252', 
                            borderRadius: 4,
                            barPercentage: 0.6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index', 
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.raw;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f0f0f0' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        }

    // Pie chart
    const ctxPie = document.getElementById('taskStatusChart');

    if (ctxPie) {
        new Chart(ctxPie, {
            type: 'pie',  
            data: {
                labels: <?php echo json_encode($pieLabels); ?>, 
                datasets: [{
                    label: 'Tasks',
                    data: <?php echo json_encode($pieValues); ?>, 
                    backgroundColor: [
                        '#4CAF50', 
                        '#FFC107', 
                        '#F44336'  
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom', 
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


