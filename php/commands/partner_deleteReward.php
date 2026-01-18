<?php
    include "../include/conn.php";
    include "../include/session.php";

    $reward_id = $_POST['reward_id'];

    $sql = "UPDATE reward SET status = 'deleted' WHERE reward_id = $reward_id";

    if(mysqli_query($dbConn,$sql)){
        echo "<script> 
                alert('Successfully Removed Reward')
                window.location.href = '../pages/partnerRewardList.php'
            </script>";
    }else{
        echo"Error";
    }

    // TODO refine Logic
?>