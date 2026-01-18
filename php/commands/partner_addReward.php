<?php

    include"../include/conn.php";
    include"../include/session.php";

    $user_id = $_SESSION['user_id'];
    
    $title =$_POST['title'];
    $description =$_POST['description'];
    $required_points =$_POST['required_points'];
    $stock =$_POST['stock'];

    $sql = "INSERT INTO reward (partner_id, status, title, description, required_points, stock)
            VALUES ($user_id, 'pending', '$title', '$description', $required_points, $stock)";

    if(mysqli_query($dbConn,$sql)){
        echo("<script>
                alert('Successfully Added Reward');
                window.location.href = '../pages/partnerRewardList.php';
            </script>");
    }else{
        echo("<script>
                alert('Failed to Add Reward!');
                window.location.href = '../pages/partnerRewardList.php';
            </script>");
    }

?>