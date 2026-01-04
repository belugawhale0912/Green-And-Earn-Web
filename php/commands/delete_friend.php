<?php
    include "../include/conn.php";

    if(isset($_POST['friendship_id'])){
        $friendship_id = $_POST['friendship_id'];
        $sql = "DELETE FROM friendship WHERE friendship_id = $friendship_id";

        if(mysqli_query($dbConn,$sql)){
            echo "<script> 
                    alert('Successfully Deleted Friend! :(')
                    window.location.href = '../pages/memberFriends.php?view=list'
                </script>";
        }else{
            echo "Error";
        }
    }else{
        header("Location: ../pages/memberFriends.php?view=list");
    }





?>