<?php
    include "../include/conn.php";
    include "../include/session.php";

    $adminID = $_SESSION['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $pointAward = $_POST['point'];
    $start = $_POST['startDate'];
    $end = $_POST['endDate'];

    $sql = "UPDATE activity SET
            admin_id = $adminID, 
            title = '$title', 
            description = '$description', 
            points_awarded = $pointAward, 
            start_date = '$start', 
            end_date = '$end'";

    if(mysqli_query($dbConn,$sql)){
        echo("<script>
                alert('Successfully Updated Activity');
                window.location.href = '../pages/adminActivityManagement.php';
            </script>");
    }else{
        echo("<script>
                alert('Failed to Add Reward!');
                window.location.href = '../pages/adminActivityManagement.php';
            </script>");
    }
?>