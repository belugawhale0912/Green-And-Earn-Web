<?php
// TODO review code here
    include "../include/conn.php";
    include "../include/session.php";

    // $sqlNewBadge = "SELECT COUNT(*) as new_count 
    //                 FROM badge_obtain 
    //                 WHERE user_id = '$user_id' AND isNew = 1";
    // $resultNewBadge = mysqli_query($dbConn,$sqlNewBadge);

    // $newBadge_info  = mysqli_fetch_assoc($resultNewBadge);
    // $newBadgeCount = $newBadge_info['new_count'];

    $user_id = $_SESSION['user_id'];

    $sqlNewBadge = "SELECT b.title 
                    FROM badge_obtain bo
                    JOIN badge b ON bo.badge_id = b.badge_id
                    WHERE bo.user_id = '$user_id' AND bo.isNew = 1";

    $newBadgeResult = mysqli_query($dbConn, $sqlNewBadge);

    if(mysqli_num_rows($newBadgeResult)>0){

        $badgeNames = [];
        // Loop through results and collect titles
        while($badgeInfo = mysqli_fetch_assoc($newBadgeResult)){
            $badgeNames[] = $badgeInfo['title'];
            $count = count($badgeNames);
        }

        $badgeString = implode(",", $badgeNames);
        
        $_SESSION['popupMessage']=[
            'title' => 'Congratulations',
            'text' => "You have earned $count new badges:$badgeString",
            'icon' => 'info'
        ];

        $sql_mark_read = "UPDATE badge_obtain SET isNew = 0 WHERE user_id = '$user_id'";
        mysqli_query($dbConn, $sql_mark_read);

        header("Location: memberBadge.php");
        exit();

    }
    

    // 2. FETCH ALL BADGES (The "Menu" of what is available)
    $sql_all_badges = "SELECT * FROM badge ORDER BY criteria_value ASC";
    $result_all = mysqli_query($dbConn, $sql_all_badges);

    // 3. FETCH USER'S BADGES (The "Inventory")
    // We store these in an array for easy checking inside the loop
    $my_inventory = []; 
    $sql_my_badges = "SELECT badge_id, obtain_date FROM badge_obtain WHERE user_id = '$user_id'";
    $result_my = mysqli_query($dbConn, $sql_my_badges);

    while($row = mysqli_fetch_assoc($result_my)){
        // Store as: [badge_id] => '2023-10-25'
        $my_inventory[$row['badge_id']] = $row['obtain_date'];
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/member.css">
    <?php include "../include/libraries.php";?>
    <title>Badges</title>
</head>
<body>
    <script src="../../js/customSwal.js"></script>
    <?php include "../include/header.php"; ?>

    <a href="memberProfile.php">Back to Profile</a>
    <h1>Badges</h1>
    <p>Complete tasks and earn points to unlock them all!</p>

    <div class="badgeContainer">
        <?php while($badge = mysqli_fetch_assoc($result_all)): ?>
            <?php 
                $b_id = $badge['badge_id'];
                $isEarned = array_key_exists($b_id, $my_inventory);
                $statusClass = $isEarned ? 'unlocked' : 'locked'; 
            ?>

            <div class="card badgeCard <?php echo $statusClass; ?>">
                <img src="../../assets/badges/<?php echo $badge['badge_img']; ?>" alt="Icon">
                
                <h3><?php echo $badge['title']; ?></h3>
                <p class="desc"><?php echo $badge['description']; ?></p>

                <?php if($isEarned): ?>
                    <div class="statusBox earned">
                        <p class="status-main">Unlocked</p>
                        <p class="status-sub">on <?php echo date("d M Y", strtotime($my_inventory[$b_id])); ?></p>
                    </div>
                <?php else: ?>
                    <div class="statusBox lockedInfo">
                        <p class="status-main">Locked</p>
                        <p class="status-sub">Need: <?php echo $badge['criteria_value']; ?> <?php echo $badge['criteria_category']; ?></p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
    <?php include "../include/showSWAL.php"?>
</body>
</html>