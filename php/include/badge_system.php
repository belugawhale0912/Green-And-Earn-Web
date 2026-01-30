<?php
// TODO remove checkTask from partner point
// TODO add checkPoint to partnerpoint and submission


function checkTaskBadges($dbConn, $user_id) {
    
    // 1. COUNT: How many approved tasks does this user have?
    $sql_taskCount = "SELECT COUNT(*) as total FROM activity_submission 
                  WHERE user_id = '$user_id' AND status = 'Approved'";
    
    $result_taskCount = mysqli_query($dbConn, $sql_taskCount);
    $taskCount_info = mysqli_fetch_assoc($result_taskCount);
    $total_tasks = $taskCount_info['total'];

    // 2. FETCH MATCHES: Find badges that this user qualifies for based on the count
    //    We check for badges where their total is MORE THAN or EQUAL TO the requirement .
    $sql_badges = "SELECT * FROM badge 
                   WHERE criteria_category = 'tasks_completed' 
                   AND $total_tasks >= criteria_value";
                   
    $result_badges = mysqli_query($dbConn, $sql_badges);

    // 3. LOOP & AWARD: Check each badge found
    while ($badge = mysqli_fetch_assoc($result_badges)) {
        
        $badge_id = $badge['badge_id'];

        // 4. IDEMPOTENCY CHECK: Do they already have THIS specific badge?
        $check_owned = "SELECT * FROM badge_obtain 
                        WHERE user_id = '$user_id' AND badge_id = '$badge_id'";
        $result_owned = mysqli_query($dbConn, $check_owned);

        // If 'num_rows' is 0, they don't have it yet. AWARD IT!
        if (mysqli_num_rows($result_owned) == 0) {
            
            // A. Insert into database
            $insert = "INSERT INTO badge_obtain (user_id, badge_id) VALUES ('$user_id', '$badge_id')";
            if(mysqli_query($dbConn, $insert)) {
                
            }
        }
    }
}


function checkPointBadges($dbConn, $user_id) {
    
    // 1. COUNT: How many approved tasks does this user have?
    $sql_pointCount = "SELECT total_point_accumulated
                       FROM user WHERE user_id = $user_id";
    
    $result_pointCount = mysqli_query($dbConn, $sql_pointCount);
    $pointCount_info = mysqli_fetch_assoc($result_pointCount);
    $total_point = $pointCount_info['total_point_accumulated'];

    // 2. FETCH MATCHES: Find badges that this user qualifies for based on the count
    //    We check for badges where their total is MORE THAN or EQUAL TO the requirement .
    $sql_badges = "SELECT * FROM badge 
                   WHERE criteria_category = 'points_earned' 
                   AND $total_point >= criteria_value";
                   
    $result_badges = mysqli_query($dbConn, $sql_badges);

    // 3. LOOP & AWARD: Check each badge found
    while ($badge = mysqli_fetch_assoc($result_badges)) {
        
        $badge_id = $badge['badge_id'];

        // 4. IDEMPOTENCY CHECK: Do they already have THIS specific badge?
        $check_owned = "SELECT * FROM badge_obtain 
                        WHERE user_id = '$user_id' AND badge_id = '$badge_id'";
        $result_owned = mysqli_query($dbConn, $check_owned);

        // If 'num_rows' is 0, they don't have it yet. AWARD IT!
        if (mysqli_num_rows($result_owned) == 0) {
            
            // A. Insert into database
            $insert = "INSERT INTO badge_obtain (user_id, badge_id) VALUES ('$user_id', '$badge_id')";
            if(mysqli_query($dbConn, $insert)) {
                
            }
        }
    }
}
?>