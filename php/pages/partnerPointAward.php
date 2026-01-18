<?php
    include "../include/conn.php";
    include "../include/session.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/layout.css">
    <title>Point Award</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <div class="form_container">
        <form action="../commands/partner_award_points.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <label for="amt_spent">Amount Spent:</label>
            <input type="number" name="amt_spent" step="0.01" required>
            <button type="submit">Comfirm</button>
        </form> 
    </div>

</body>
</html>
