<?php
    include "../include/conn.php";
    include "../include/session.php";

    $user_id = $_SESSION['user_id'];
    $today = date("Y-m-d");

    $sql = "SELECT * FROM user WHERE user_id = {$_SESSION['user_id']}";

    $result = mysqli_query($dbConn, $sql);

    if (mysqli_num_rows($result)>0) {
        $user_info = mysqli_fetch_assoc($result);
    }


    // 5. FETCH DAILY ACTIVITY (Last 7 Days)
    // We create an array of the last 7 dates first
    $last7Days = [];
    $earnedData = [];
    $spentData = [];

    // Initialize 0 values for the last 7 days
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $last7Days[$date] = [
            'label' => date('d-m-D', strtotime($date)), // "Mon", "Tue"
            'earned' => 0,
            'spent' => 0
        ];
    }

    // Query DB
    $sql_chart = "SELECT DATE(transaction_time) as trans_date, type, SUM(amount) as total 
                  FROM point_transaction 
                  WHERE user_id = ? 
                  AND transaction_time >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                  GROUP BY DATE(transaction_time), type";

    $stmt = mysqli_prepare($dbConn, $sql_chart);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $res_chart = mysqli_stmt_get_result($stmt);

    // Fill the data
    while($row = mysqli_fetch_assoc($res_chart)){
        $dbDate = $row['trans_date'];
        
        if(isset($last7Days[$dbDate])){
            if($row['type'] == 'Earned'){
                $last7Days[$dbDate]['earned'] = $row['total'];
            } 
            // Check your DB: is it 'Redeemed', 'Spent', or 'Refund'? 
            // Assuming 'Redeemed' or similar for spending points
            else if($row['type'] == 'Redemption' || $row['type'] == 'Spent'){ 
                $last7Days[$dbDate]['spent'] = $row['total'];
            }
        }
    }

    // Separate into simple arrays for Chart.js
    $chartLabels = [];
    $chartEarned = [];
    $chartSpent = [];

    foreach ($last7Days as $dayData) {
        $chartLabels[] = $dayData['label'];
        $chartEarned[] = $dayData['earned'];
        $chartSpent[]  = $dayData['spent'];
    }

    // 6. FETCH TASK STATUS COUNTS (For Pie Chart)
    // Initialize default values to ensure chart works even with 0 tasks
    $taskData = [
        'Pending' => 0,
        'Approved' => 0,
        'Rejected' => 0
    ];

    $sql_pie = "SELECT status, COUNT(*) as total 
                FROM activity_submission 
                WHERE user_id = ? 
                GROUP BY status";

    $stmt_pie = mysqli_prepare($dbConn, $sql_pie);
    mysqli_stmt_bind_param($stmt_pie, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt_pie);
    $res_pie = mysqli_stmt_get_result($stmt_pie);

    while($row = mysqli_fetch_assoc($res_pie)){
        // Ensure the status from DB matches our array keys (Case sensitive)
        // If your DB uses lowercase, use ucfirst($row['status'])
        $status = $row['status']; 
        if(isset($taskData[$status])){
            $taskData[$status] = $row['total'];
        }
    }

    // Prepare simple arrays for Chart.js
    // We explicitly order them so colors match correctly later
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
                        // 1. Define your list of facts
                        $facts = [
                            "Recycling one aluminum can saves enough energy to run a TV for 3 hours.",
                            "A single tree can absorb 48 pounds of carbon dioxide per year.",
                            "Glass is 100% recyclable and can be recycled endlessly without loss in quality.",
                            "Turning off the tap while brushing your teeth can save up to 8 gallons of water a day.",
                            "Plastic bags take anywhere from 10 to 1,000 years to decompose.",
                            "Energy-efficient LED light bulbs use 75% less energy than incandescent lighting.",
                            "The average person generates over 4 pounds of trash every day."
                        ];

                        // 2. Pick a random index
                        $randomIndex = array_rand($facts);

                        // 3. Display the result
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

    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('weeklyActivityChart');

        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($chartLabels); ?>, // ["Mon", "Tue", ...]
                    datasets: [
                        {
                            label: 'Points Earned',
                            data: <?php echo json_encode($chartEarned); ?>,
                            backgroundColor: '#4CAF50', // Green
                            borderRadius: 4,
                            barPercentage: 0.6,
                        },
                        {
                            label: 'Points Spent',
                            data: <?php echo json_encode($chartSpent); ?>,
                            backgroundColor: '#FF5252', // Red
                            borderRadius: 4,
                            barPercentage: 0.6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index', // Hovering shows both bars for that day
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
    // ... (Your existing Weekly Activity Chart code) ...

    // NEW: Task Status Pie Chart
    const ctxPie = document.getElementById('taskStatusChart');

    if (ctxPie) {
        new Chart(ctxPie, {
            type: 'pie', // You can also change this to 'doughnut' for a ring style
            data: {
                labels: <?php echo json_encode($pieLabels); ?>, // ['Approved', 'Pending', 'Rejected']
                datasets: [{
                    label: 'Tasks',
                    data: <?php echo json_encode($pieValues); ?>, // [5, 2, 1]
                    backgroundColor: [
                        '#4CAF50', // Green for Approved
                        '#FFC107', // Amber/Yellow for Pending
                        '#F44336'  // Red for Rejected
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom', // Legend looks better at the bottom for Pie charts
                    }
                }
            }
        });
    }
    });
    </script>
</body>
</html>



