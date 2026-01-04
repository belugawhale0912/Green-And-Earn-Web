<?php
    include "../include/conn.php";

    $sql = "SELECT * FROM user WHERE user_id = {$_SESSION['user_id']}";

    $result = mysqli_query($dbConn, $sql);

    if (mysqli_num_rows($result)>0) {
        $user_info = mysqli_fetch_assoc($result);
    }
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
