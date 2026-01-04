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
            $searched_name = $_POST['searchKey'];
            $sql = "SELECT user_id, username, profile_photo FROM user
                    WHERE username LIKE '%$searched_name%' AND role_id = 2 AND user_id !=$user_id";
        }
        else{
            $sql = "SELECT user_id, username, profile_photo FROM user
                    WHERE role_id = 2 AND user_id !=$user_id";
        }
    }

    if(!empty($sql)){
        $result = mysqli_query($dbConn,$sql);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/layout.css">
    <title>Friends</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <a href="?view=list">My Friends</a>
    <a href="?view=requests">Requests</a>
    <a href="?view=search">Find Friends</a>
    <?php if(isset($_SESSION['role_id']) && $current_view=='list'): ?>
        <?php if(mysqli_num_rows($result)>0):?>
            <div class="list_container">
                <?php while($user_info = mysqli_fetch_assoc($result)):?>
                        <div class="list_card">                
                            <img src="<?php echo $user_info['profile_photo']?>" alt="pfp">
                            <h3><?php echo $user_info['username']?></h3>
                            <form action="../commands/delete_friend.php" method="POST">
                                <input type="hidden" name="friendship_id" value="<?php echo $user_info['friendship_id']?>">
                                <button type="submit">Delete Friend</button>
                            </form>
                        </div>
                <?php endwhile;?>
            </div>
        <?php else:?>
            <h1>You have no friends!</h1>
        <?php endif?>
    <?php endif;?>

    <?php if(isset($_SESSION['role_id']) && $current_view=='requests'): ?>
        <div class="list_container">
            <?php while($user_info = mysqli_fetch_assoc($result)):?>
                <div class="list_card">
                    <img src="<?php echo $user_info['profile_photo']?>" alt="pfp">
                    <h3><?php echo $user_info['username']?></h3>
                    <form action="../commands/accept_friend_request.php" method="POST">
                        <input type="hidden" name="friendship_id" value="<?php echo $user_info['friendship_id']?>">
                        <button type="submit" name="action" value="Accept">Accept</button>
                        <button type="submit" name="action" value="Reject">Reject</button>
                    </form>
                </div>
            <?php endwhile;?>
        </div>
    <?php endif;?>

    <?php if(isset($_SESSION['role_id']) && $current_view=='search'): ?>
        <form action="?view=search" method="POST">
            <input type="text" name="searchKey" placeholder="Enter friend name to search" required>
            <button type="submit">Search</button>
        </form>
        <div class="list_container">
            <?php while($user_info = mysqli_fetch_assoc($result)):?>
                <div class="list_card">
                    <img src="<?php echo $user_info['profile_photo']?>" alt="pfp">
                    <h3><?php echo $user_info['username']?></h3>
                    <form action="../commands/send_friend_request.php" method="POST">
                        <input type="hidden" name="friend_id" value="<?php echo $user_info['user_id']?>">
                        <button type="submit">Add Friend</button>
                    </form>
                </div>
            <?php endwhile;?>
        </div>
    <?php endif;?>

</body>
</html>