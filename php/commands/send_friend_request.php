<?php
    session_start();
    include "../include/conn.php";

    if(!isset($_SESSION['user_id'])){
        header("Location: ../pages/Landingpage.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $friend_id = $_POST['friend_id'];

    $checkSql = "SELECT * FROM friendship
                 WHERE (user_id = $user_id AND friend_id = $friend_id) OR (user_id = $friend_id AND friend_id = $user_id)";
    
    $checkResult = mysqli_query($dbConn,$checkSql);

    if(mysqli_num_rows($checkResult)>0){
        echo "<script> 
                alert('You are already friend with this user or a request is pending')
                window.location.href = '../pages/memberFriends.php?view=search'
              </script>";
    }
    else{
        $insertSql = "INSERT INTO friendship (user_id, friend_id, status)
                VALUES ($user_id,$friend_id,'Pending')";

        if(mysqli_query($dbConn,$insertSql)){
            echo "<script> 
                    alert('Friend Request Sent! :)')
                    window.location.href = '../pages/memberFriends.php?view=search'
                  </script>";            
        }
        else{
            echo "Error";
        }
    }
?>