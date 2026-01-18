<?php
    include "../include/conn.php";
    include "../include/session.php";

    $adminID = $_SESSION['user_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $pointAward = $_POST['point'];
    $start = $_POST['startDate'];
    $end = $_POST['endDate'];

    $sql = "INSERT INTO activity (admin_id, title, description, points_awarded, status, start_date, end_date)
            VALUES ($adminID, '$title', '$description', $pointAward, 'Active', '$start', '$end')";

    if(mysqli_query($dbConn,$sql)){
        echo("<script>
                alert('Successfully Added Activity');
                window.location.href = '../pages/adminActivityManagement.php';
            </script>");
    }else{
        echo("<script>
                alert('Failed to Add Reward!');
                window.location.href = '../pages/adminActivityManagement.php';
            </script>");
    }
?>