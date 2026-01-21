<?php
    session_start();
    include "../include/conn.php";

    if(!isset($_SESSION['user_id'])){
        header("Location: Landingpage.php");
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $current_view = isset($_GET['view'])? $_GET['view']:'list';

    if($current_view=='list'){
        $sql = "SELECT u.user_id, u.username, u.profile_photo, f.friendship_id
                FROM friendship f
                JOIN user u ON u.user_id = (CASE WHEN f.user_id = $user_id THEN f.friend_id ELSE f.user_id END)
                WHERE (f.user_id = $user_id OR f.friend_id = $user_id) AND f.status = 'Accepted'";
    }
    elseif($current_view=='requests'){
        $sql = "SELECT u.user_id, u.username, u.profile_photo, f.friendship_id
                FROM friendship f
                JOIN user u ON f.user_id = u.user_id
                WHERE f.friend_id = $user_id AND f.status = 'Pending'";
    }
    elseif($current_view=='search'){
        if(isset($_POST['searchKey'])){
            // TODO prevent sql injection (mysqli_real_escape_string)
            $searched_name = $_POST['searchKey'];
            $sql = "SELECT user_id, username, profile_photo FROM user
                    WHERE username LIKE '%$searched_name%' AND role_id = 2 AND user_id !=$user_id";
        }
        // Add query to prevent showing people that are already friends
        else{
            $sql = "SELECT user_id, username, profile_photo FROM user
                    WHERE role_id = 2 AND user_id !=$user_id";
        }
    }

    if(!empty($sql)){
        $result = mysqli_query($dbConn,$sql);
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["action"])){
            if($_POST["action"]=="delete"){

                if(isset($_POST['friendship_id'])){
                    $friendship_id = $_POST['friendship_id'];
                    $sql = "DELETE FROM friendship WHERE friendship_id = $friendship_id";

                    if(mysqli_query($dbConn,$sql)){
                        echo "<script> 
                                alert('Successfully Deleted Friend! :(')
                                window.location.href = 'memberFriends.php?view=list'
                            </script>";
                    }else{
                        echo "Error";
                    }
                }else{
                    header("Location: memberFriends.php?view=list");
                }
            }
            if($_POST["action"]=="approve"){

                $friendship_id = $_POST['friendship_id'];
                $decision = $_POST['decision'];

                if($decision=="Accept"){
                    $sql = "UPDATE friendship SET status = 'Accepted' WHERE friendship_id = $friendship_id";
                    $msg = "You are now friends!";
                }
                else{
                    $sql = "DELETE FROM friendship WHERE friendship_id = $friendship_id";
                    $msg = "Rejected";
                }

                if(mysqli_query($dbConn,$sql)){
                    echo "<script> 
                            alert('$msg')
                            window.location.href = 'memberFriends.php?view=request'
                        </script>";
                }else{
                    echo "Error";
                }
            }
            if($_POST["action"]=="add"){
                $user_id = $_SESSION['user_id'];
                $friend_id = $_POST['friend_id'];

                $checkSql = "SELECT * FROM friendship
                            WHERE (user_id = $user_id AND friend_id = $friend_id) OR (user_id = $friend_id AND friend_id = $user_id)";
                
                $checkResult = mysqli_query($dbConn,$checkSql);

                if(mysqli_num_rows($checkResult)>0){
                    echo "<script> 
                            alert('You are already friend with this user or a request is pending')
                            window.location.href = 'memberFriends.php?view=search'
                        </script>";
                }
                else{
                    $insertSql = "INSERT INTO friendship (user_id, friend_id, status)
                            VALUES ($user_id,$friend_id,'Pending')";

                    if(mysqli_query($dbConn,$insertSql)){
                        echo "<script> 
                                alert('Friend Request Sent! :)')
                                window.location.href = 'memberFriends.php?view=search'
                            </script>";            
                    }
                    else{
                        echo "Error";
                    }
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/member.css">
    <title>Friends</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <div class="viewToggle">
        <a href="?view=list">My Friends</a>
        <a href="?view=requests">Requests</a>
        <a href="?view=search">Find Friends</a>
    </div>

    <?php if(isset($_SESSION['role_id']) && $current_view=='list'): ?>
        <?php if(mysqli_num_rows($result)>0):?>
            <div class="friendContainer">
                <?php while($user_info = mysqli_fetch_assoc($result)):?>
                        <div class="friendCard">                
                            <img src="<?php echo $user_info['profile_photo']?>" alt="pfp">
                            <h3><?php echo $user_info['username']?></h3>
                            <form action="" method="POST">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="friendship_id" value="<?php echo $user_info['friendship_id']?>">
                                <button type="submit">Delete Friend</button>
                            </form>
                        </div>
                <?php endwhile;?>
            </div>
        <?php else:?>
            <div class="friendContainer">
                <h1>You have no friends!</h1>
            </div>
            
        <?php endif?>
    <?php endif;?>

    <?php if(isset($_SESSION['role_id']) && $current_view=='requests'): ?>
        <?php if(mysqli_num_rows($result)>0):?>
            <div class="friendContainer">
                <?php while($user_info = mysqli_fetch_assoc($result)):?>
                    <div class="friendCard">
                        <img src="<?php echo $user_info['profile_photo']?>" alt="pfp">
                        <h3><?php echo $user_info['username']?></h3>
                        <form action="" method="POST">
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="friendship_id" value="<?php echo $user_info['friendship_id']?>">
                            <button type="submit" name="decision" value="Accept">Accept</button>
                            <button type="submit" name="decision" value="Reject">Reject</button>
                        </form>
                    </div>
                <?php endwhile;?>
            </div>
        <?php else:?>
            <div class="friendContainer">
                <h1>No Pending Reqeust</h1>
            </div>
        <?php endif?>
    <?php endif;?>

    <?php if(isset($_SESSION['role_id']) && $current_view=='search'): ?>
        <form action="?view=search" method="POST">
            <input type="text" name="searchKey" placeholder="Enter friend name to search" required>
            <button type="submit">Search</button>
        </form>
        <div class="friendContainer">
            <?php while($user_info = mysqli_fetch_assoc($result)):?>
                <div class="friendCard">
                    <img src="<?php echo $user_info['profile_photo']?>" alt="pfp">
                    <h3><?php echo $user_info['username']?></h3>
                    <form action="" method="POST">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="friend_id" value="<?php echo $user_info['user_id']?>">
                        <button type="submit">Add Friend</button>
                    </form>
                </div>
            <?php endwhile;?>
        </div>
    <?php endif;?>

</body>
</html>