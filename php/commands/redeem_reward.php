<?php
    session_start();
    $reward_id = $_GET['reward_id'];
    include "../include/conn.php";
    include "../include/libraries.php";

    //validations
    $sql_reward = "SELECT * FROM reward WHERE reward_id = $reward_id";
    $result_reward = mysqli_query($dbConn, $sql_reward);
    $reward_info = mysqli_fetch_assoc($result_reward);

    // if reward exists
    if (!$reward_info) {
        die("Error: Reward not found");
    }

    // is it available
    if ($reward_info['status'] != 'available' || $reward_info['stock'] <= 0) {
        die("Error: Reward is out of stock or unavailable");
    }

    // Fetch User info from database
    $user_id = $_SESSION['user_id'];
    $sql_user = "SELECT * FROM user WHERE user_id = $user_id";
    $result_user = mysqli_query($dbConn, $sql_user);
    $user_info = mysqli_fetch_assoc($result_user);

    // check if user have sufficient points
    if ($user_info['point_balance'] < $reward_info['required_points']) {
        die("Error: Insufficient points");
    }

    //execution

    //calculate new values 
    $new_stock = $reward_info['stock']-1;
    $new_point_balance = $user_info['point_balance']-$reward_info['required_points'];

    //query
    $sql_update_stock = "UPDATE reward SET stock = $new_stock WHERE reward_id = $reward_id";
    
    $sql_update_pointbal = "UPDATE user SET point_balance = $new_point_balance WHERE user_id = $user_id";

    $sql_insert_redemption = "INSERT INTO reward_redemption (reward_id, user_id, points_spent, status) VALUES ($reward_id, $user_id, {$reward_info['required_points']}, 'Approved')";

    //turn off autosave to prevent data corruption if query fails
    $dbConn->begin_transaction();

    try{
        if (!mysqli_query($dbConn, $sql_update_stock)) { throw new Exception("Reward update failed"); }
        if (!mysqli_query($dbConn, $sql_update_pointbal)) { throw new Exception("User update failed"); }
        if (!mysqli_query($dbConn, $sql_insert_redemption)) { throw new Exception("Insert Redemption failed"); }
        $redemption_id = mysqli_insert_id($dbConn);
        $sql_insert_transaction= "INSERT INTO point_transaction (user_id, redemption_id, amount, type) VALUES ($user_id, $redemption_id, {$reward_info['required_points']},'Redemption')";
        if (!mysqli_query($dbConn, $sql_insert_transaction)) { throw new Exception("Insert Transaction failed"); }        
        //if all query ran without error, save the changes
        $dbConn->commit();
        // JavaScript Feedback (TEMP)
        echo "<html>
            <body>
                <script>
                    Swal.fire({
                        title: 'Success!',
                        text: 'The Reward is redeemed!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        // This code runs ONLY after the button is clicked
                        if (result.isConfirmed) {
                            window.location.href = '../pages/memberRewardList.php';
                        }
                    });
                </script>
            </body>
        </html>";

    }
    catch(Exception $e){
        //if any of the query fail, undo the changes and send error message
        $dbConn->rollback();
        echo "Error:". $e->getMessage();
    }

    
    

    