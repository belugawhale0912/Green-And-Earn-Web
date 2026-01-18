<?php

    include"../include/conn.php";
    include"../include/session.php";

    $user_id = $_SESSION['user_id'];
    
    $reward_id = $_POST['rewardID'];
    $title =$_POST['title'];
    $description =$_POST['description'];
    $required_points =$_POST['required_points'];
    $stock =$_POST['stock'];

    $sql = "UPDATE reward SET 
            title = '$title',
            description = '$description',
            required_points=$required_points,
            stock=$stock
            WHERE reward_id = $reward_id";

    if(mysqli_query($dbConn,$sql)){
        echo("<script>
                alert('Successfully Updated Reward');
                window.location.href = '../pages/partnerRewardList.php';
            </script>");
    }else{
        echo("<script>
                alert('Update Failed!');
                window.location.href = '../pages/partnerRewardList.php';
            </script>");
    }

?>