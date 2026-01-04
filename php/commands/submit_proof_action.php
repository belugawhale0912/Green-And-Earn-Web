<?php
    session_start();
    include "../include/conn.php";

    //Fetch Variables
    $user_id = $_SESSION['user_id'];
    $activity_id = $_POST['activity_id'];
    //placeholder for debug
    $moderator_id = 1;
    $status = "Pending";

    //File processing
    //check file exists and no error
    if(isset($_FILES['proof_img']) && $_FILES['proof_img']['error'] === UPLOAD_ERR_OK){
        
        $fileServerPath = $_FILES['proof_img']['tmp_name'];
        $fileName = $_FILES['proof_img']['tmp_name'];

        $formattedFileName = "proof_userID{$user_id}_activityID{$activity_id}_".time().".png";

        $saveDir = "../../uploads/proofs/".$formattedFileName;

        if (move_uploaded_file($fileServerPath, $saveDir)){
            $sql = "INSERT INTO activity_submission (user_id, moderator_id, activity_id, proof, status) VALUES ('$user_id', '$moderator_id', '$activity_id', '$formattedFileName', '$status')";
            if(mysqli_query($dbConn, $sql)){
                echo "<script>alert('Success!'); window.location.href='../pages/memberTaskList.php';</script>";
            } else{
                echo "Error While Saving";
            }
        }else{
            //debug
            echo "Failed To Move File";
        }
    }else{
        //debug
        echo "No file received!";
    }

?>