<?php
    include "../include/conn.php";
    include "../include/session.php";

    $activity_id = $_POST['activity_id'];

    $sql = "UPDATE activity SET status = 'deleted' WHERE activity_id = $activity_id";

    if(mysqli_query($dbConn,$sql)){
        echo "<script> 
                alert('Successfully Deleted Activity! :(')
                window.location.href = '../pages/adminActivityManagement.php?'
            </script>";
    }else{
        echo "Error";
    }

?>