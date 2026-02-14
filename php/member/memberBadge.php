<?php
    include "../include/conn.php";
    include "../include/session.php";

    // ensure the user is member, if not redirect to landing page
    if($_SESSION['role_id'] != 2){
        header("Location: ../pages/Landingpage.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // check if user have any new obtained badge
    $sqlNewBadge = "SELECT b.title 
                    FROM badge_obtain bo
                    JOIN badge b ON bo.badge_id = b.badge_id
                    WHERE bo.user_id = ? AND bo.isNew = 1";

    $stmtNewBadge = mysqli_prepare($dbConn, $sqlNewBadge);
    mysqli_stmt_bind_param($stmtNewBadge,'i', $user_id);
    mysqli_stmt_execute($stmtNewBadge);

    $newBadgeResult = mysqli_stmt_get_result($stmtNewBadge);

    if(mysqli_num_rows($newBadgeResult)>0){

        $badgeNames = [];
        // Loop through results and collect titles
        while($badgeInfo = mysqli_fetch_assoc($newBadgeResult)){
            $badgeNames[] = $badgeInfo['title'];
            $count = count($badgeNames);
        }

        // join values into badge1,badge2,badge3
        $badgeString = implode(",", $badgeNames);
        
        $_SESSION['popupMessage']=[
            'title' => 'Congratulations',
            'text' => "You have earned $count new badges:$badgeString",
            'icon' => 'info'
        ];
        mysqli_stmt_close($stmtNewBadge);

        // remove new status after the user know they earned the badge
        $sql_mark_read = "UPDATE badge_obtain SET isNew = 0 WHERE user_id = ?";
        $stmtMarkRead = mysqli_prepare($dbConn, $sql_mark_read);
        mysqli_stmt_bind_param($stmtMarkRead, "i", $user_id);
        mysqli_stmt_execute($stmtMarkRead);
        mysqli_stmt_close($stmtMarkRead);

        header("Location: memberBadge.php");
        exit();

    }
    
    // fetch all badges
    $sql_all_badges = "SELECT * FROM badge ORDER BY criteria_value ASC";
    $result_all = mysqli_query($dbConn, $sql_all_badges);

    // fetch user badges 
    $userBadges = []; 
    $sql_my_badges = "SELECT badge_id, obtain_date FROM badge_obtain WHERE user_id = ?";

    $stmtMyBadge = mysqli_prepare($dbConn, $sql_my_badges);
    mysqli_stmt_bind_param($stmtMyBadge, "i", $user_id);
    mysqli_stmt_execute($stmtMyBadge);
    $result_my = mysqli_stmt_get_result($stmtMyBadge);
    mysqli_stmt_close($stmtMyBadge);

    while($row = mysqli_fetch_assoc($result_my)){
        // store as associative arry key = badge_id value = obtain_date
        $userBadges[$row['badge_id']] = $row['obtain_date'];
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
                $badge_id = $badge['badge_id'];
                $isEarned = array_key_exists($badge_id, $userBadges);
                $statusClass = $isEarned ? 'unlocked' : 'locked'; 
            ?>

            <div class="card badgeCard <?php echo $statusClass; ?>">
                <img src="../../assets/badges/<?php echo $badge['badge_img']; ?>" alt="Icon">
                
                <h3><?php echo $badge['title']; ?></h3>
                <p class="desc"><?php echo $badge['description']; ?></p>

                <?php if($isEarned):?>
                    <div class="statusBox earned">
                        <p class="status-main">Unlocked</p>
                        <p class="status-sub">on <?php echo date("d M Y", strtotime($userBadges[$badge_id])); ?></p>
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
<?php mysqli_close($dbConn)?>