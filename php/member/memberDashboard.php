<?php
    session_start();
    include "../include/conn.php";

    $sql = "SELECT * FROM user WHERE user_id = {$_SESSION['user_id']}";

    $result = mysqli_query($dbConn, $sql);

    if (mysqli_num_rows($result)>0) {
        $user_info = mysqli_fetch_assoc($result);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/layout.css">
    <title>Dashboard</title>
</head>
<body>
    <?php
        include("../include/header.php");
        echo("<h1>Welcome,".$_SESSION['username']."</h1>");
    ?>
    <div class="container">
        <section class="card" id="point_balance">
            <?php
                echo('<h2>'.$user_info['point_balance'].'Points'.'</h2>');
            ?>
            <a class="card_links" href="">View Transactions</a>
        </section>

        <section class="card" id="eco_facts">
            <h2>Fun Fact</h2>
            <p>Looking at green trees are good for your eyes</p>
        </section>

        <section class="card" id="tasks">
            <h2>Tasks</h2>
            <a class="card_links" href="../pages/memberTaskList.php">New Tasks Available</a>
        </section>
    </div>

    <a href="../commands/logout.php">Logout</a>
</body>
</html>



