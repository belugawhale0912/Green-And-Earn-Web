<?php
    session_start();
    $activity_id=$_GET['activity_id'];
    include "../include/conn.php";
    $sql = "SELECT * FROM activity WHERE activity_id = $activity_id";
    $result = mysqli_query($dbConn, $sql);
    if (mysqli_num_rows($result)>0) {
        $activity_info = mysqli_fetch_assoc($result);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <a href="memberTaskList.php">Back</a>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/layout.css">
    <title>Submit Proof</title>
</head>
<body>
    <?php
        echo('<h1>'.$activity_info['title'].'</h1>');
        echo('<div class="cards">');
        echo('<p>'.$activity_info['description'].'</p>');
        echo('</div>');
    ?>
    <div class="form_container">
        <form action="../commands/submit_proof_action.php" method="POST" enctype="multipart/form-data">
            
            <input type="hidden" name="activity_id" value="<?php echo $activity_id; ?>">

            <label>Upload Proof (Image):</label>
            <input type="file" name="proof_img" required accept="image/*">

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>