<?php

function checkTaskBadges($dbConn, $user_id) {
    
    // count how many approved tasks does the user have

    $sql_taskCount = "SELECT COUNT(*) as total FROM activity_submission 
                  WHERE user_id = ? AND status = 'Approved'";

    $stmtTaskCount = mysqli_prepare($dbConn, $sql_taskCount);
    mysqli_stmt_bind_param($stmtTaskCount, "i", $user_id);
    mysqli_stmt_execute($stmtTaskCount);
    $result_taskCount = mysqli_stmt_get_result($stmtTaskCount);
    $taskCount_info = mysqli_fetch_assoc($result_taskCount);
    $total_tasks = $taskCount_info['total'];

    // find badges that this user qualifies
    $sql_badges = "SELECT * FROM badge 
                   WHERE criteria_category = 'tasks_completed' 
                   AND $total_tasks >= criteria_value";

    $stmtBadges = mysqli_prepare($dbConn, $sql_badges);
    mysqli_stmt_bind_param($stmtBadges, "i", $total_tasks);
    mysqli_stmt_execute($stmtBadges);
    $result_badges = mysqli_stmt_get_result($stmtBadges);


    while ($badge = mysqli_fetch_assoc($result_badges)) {
        
        $badge_id = $badge['badge_id'];

        // check if the user already owned the badge
        $sql_owned = "SELECT * FROM badge_obtain 
                        WHERE user_id = ? AND badge_id = '$badge_id'";
        $stmtOwned = mysqli_prepare($dbConn, $sql_owned);
        mysqli_stmt_bind_param($stmtOwned, "i", $user_id);
        mysqli_stmt_execute($stmtOwned);
        $result_owned = mysqli_stmt_get_result($stmtOwned);

        // if row = 0 that means the user dont have the badge
        if (mysqli_num_rows($result_owned) == 0) {
            
            // insert badge obtain record to award the user
            $insert = "INSERT INTO badge_obtain (user_id, badge_id) VALUES (?, ?)";
            $stmtInsert = mysqli_prepare($dbConn, $insert);
            mysqli_stmt_bind_param($stmtOwned, "ii", $user_id, $badge_id);
            if(mysqli_query($dbConn, $insert)) {
                
            }
        }
    }
}


function checkPointBadges($dbConn, $user_id) {
    
    // check user total point
    $sql_pointCount = "SELECT total_point_accumulated
                       FROM user WHERE user_id = ?";
    
    $stmtPointCount =  mysqli_prepare($dbConn, $sql_pointCount);
    mysqli_stmt_bind_param($stmtPointCount, "i", $user_id);
    mysqli_stmt_execute($stmtPointCount);
    $result_pointCount = mysqli_stmt_get_result($stmtPointCount);
    $pointCount_info = mysqli_fetch_assoc($result_pointCount);
    $total_point = $pointCount_info['total_point_accumulated'];

    //    find the badge that the user is eligible of
    $sql_badges = "SELECT * FROM badge 
                   WHERE criteria_category = 'points_earned' 
                   AND ? >= criteria_value";

    $stmtBadges = mysqli_prepare($dbConn, $sql_badges);
    mysqli_stmt_bind_param($stmtBadges, "i", $total_point);
    mysqli_stmt_execute($stmtBadges);
                   
    $result_badges = mysqli_stmt_get_result($stmtBadges);

    while ($badge = mysqli_fetch_assoc($result_badges)) {
        
        $badge_id = $badge['badge_id'];

        // check if the user already owned the badge
        $sql_owned = "SELECT * FROM badge_obtain 
                        WHERE user_id = ? AND badge_id = '$badge_id'";
        $stmtOwned = mysqli_prepare($dbConn, $sql_owned);
        mysqli_stmt_bind_param($stmtOwned, "i", $user_id);
        mysqli_stmt_execute($stmtOwned);
        $result_owned = mysqli_stmt_get_result($stmtOwned);

        // if row = 0 that means the user dont have the badge
        if (mysqli_num_rows($result_owned) == 0) {
            
            // insert badge obtain record to award the user
            $insert = "INSERT INTO badge_obtain (user_id, badge_id) VALUES (?, ?)";
            $stmtInsert = mysqli_prepare($dbConn, $insert);
            mysqli_stmt_bind_param($stmtOwned, "ii", $user_id, $badge_id);
            if(mysqli_query($dbConn, $insert)) {
                
            }
        }
    }
}

?>