<?php
    include "../include/conn.php";
    include "../include/session.php";

    // 1. Security Check (Role ID 1 = Admin)
    if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
        header("Location: ../pages/Landingpage.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    
    $sql_profile = "SELECT * FROM user WHERE user_id = $user_id";
    $result_profile = mysqli_query($dbConn, $sql_profile);
    $admin = mysqli_fetch_assoc($result_profile);

    $sql_countMembers = "SELECT COUNT(*) AS totalMember FROM user WHERE role_id =2";
    $result_memberCount = mysqli_query($dbConn, $sql_countMembers);
    $memberCount = mysqli_fetch_assoc($result_memberCount)['totalMember'];

    $sql_countPartners = "SELECT COUNT(*) AS totalPartner FROM user WHERE role_id =4";
    $result_partnerCount = mysqli_query($dbConn, $sql_countPartners);
    $partnerCount = mysqli_fetch_assoc($result_partnerCount)['totalPartner'];

    $sql_countReward = "SELECT COUNT(*) AS totalReward FROM reward WHERE status = 'pending' ";
    $result_rewardCount = mysqli_query($dbConn, $sql_countReward);
    $rewardCount = mysqli_fetch_assoc($result_rewardCount)['totalReward'];

    $sql_countActivity = "SELECT COUNT(*) AS totalActivity FROM activity WHERE start_date <= CURDATE() AND end_date >= CURDATE()";
    $result_activityCount = mysqli_query($dbConn, $sql_countActivity);
    $activityCount = mysqli_fetch_assoc($result_activityCount)['totalActivity'];
?>

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
    <h2>Profile</h2>
    <section id="profileSection">   
        <div id="profilePhoto">
            <img src="<?php echo $admin['profile_photo']?>" alt="profileImg" class="profileImg">
        </div>
        <div id="profileContainer">
            <h3>Username : <?php echo $admin['username']?></h3>
            <h3>Email : <?php echo $admin['email']?></h3>
        </div>
    </section>
    <h2>Statistics</h2>
    <section id="stats">
        <div class="cardContainer">
            <div class="cards">
                <h3>
                    <span class="statistics"><?php echo $memberCount?></span> <br> 
                    Total Members 
                </h3>
            </div>

            <div class="cards">
                <h3><span class="statistics"><?php echo $partnerCount?></span> <br>
                    Total Partners & Sponsors
                </h3>
            </div>


            <div class="cards">
                <h3><span class="statistics"><?php echo $rewardCount?></span> <br>
                    Total Pending Rewards
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