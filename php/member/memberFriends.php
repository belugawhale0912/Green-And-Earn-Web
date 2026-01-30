<?php
    include "../include/conn.php";
    include "../include/session.php";

    if(!isset($_SESSION['user_id'])){
        header("Location: Landingpage.php");
        exit();
    }
    $user_id = $_SESSION['user_id'];
    $view = isset($_GET['view'])? $_GET['view']:'list';

    if($view=='list'){
        $sql = "SELECT * 
                FROM friendship f
                JOIN user u ON u.user_id = (CASE WHEN f.user_id = $user_id THEN f.friend_id ELSE f.user_id END)
                WHERE (f.user_id = $user_id OR f.friend_id = $user_id) AND f.status = 'Accepted'";
    }
    elseif($view=='requests'){
        $sql = "SELECT u.user_id, u.username, u.profile_photo, f.friendship_id
                FROM friendship f
                JOIN user u ON f.user_id = u.user_id
                WHERE f.friend_id = $user_id AND f.status = 'Pending'";
    }
    elseif($view=='search'){
        // Understand this query
        $sql = "SELECT user_id, username, profile_photo 
            FROM user 
            WHERE role_id = 2 
            AND user_id != $user_id 
            AND user_id NOT IN (
                SELECT friend_id FROM friendship WHERE user_id = $user_id
                UNION
                SELECT user_id FROM friendship WHERE friend_id = $user_id
            )";
    }

    $result = mysqli_query($dbConn,$sql);

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["action"])){
            if($_POST["action"]=="delete"){

                if(isset($_POST['friendship_id'])){
                    $friendship_id = $_POST['friendship_id'];
                    $sql = "DELETE FROM friendship WHERE friendship_id = $friendship_id";

                    if(mysqli_query($dbConn,$sql)){
                        $_SESSION['popupMessage']=[
                            'title' => 'Success!',
                            'text' => 'Successfully Deleted Friend',
                            'icon' => 'success'
                        ];

                        header("Location: memberFriends.php?view=list");
                        exit();
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
                    $msg = "Approved";
                }
                else{
                    $sql = "DELETE FROM friendship WHERE friendship_id = $friendship_id";
                    $msg = "Rejected";
                }

                if(mysqli_query($dbConn,$sql)){
                    $_SESSION['popupMessage']=[
                        'title' => 'Success!',
                        'text' => 'Friend Request is '.$msg,
                        'icon' => 'success'
                    ];

                    header("Location: memberFriends.php?view=requests");
                    exit();
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
                    $_SESSION['popupMessage']=[
                        'title' => 'Action Failed!',
                        'text' => 'You already have a request sent',
                        'icon' => 'error'
                    ];
                    header("Location: memberFriends.php?view=search");
                    exit();
                }
                else{
                    $insertSql = "INSERT INTO friendship (user_id, friend_id, status)
                            VALUES ($user_id,$friend_id,'Pending')";

                    if(mysqli_query($dbConn,$insertSql)){
                        $_SESSION['popupMessage']=[
                            'title' => 'Success',
                            'text' => 'Friend Request Successfully Sent!',
                            'icon' => 'success'
                        ];    
                        header("Location: memberFriends.php?view=search");
                        exit();
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
    <link rel="icon" href="/GreenAndEarnWeb/favicon.ico">
    <?php include "../include/libraries.php";?>
    <title>Friends</title>
</head>
<body>
    <script src="../../js/customSwal.js"></script>
    <?php
        include "../include/header.php";
    ?>

    <div class="viewToggle">
        <a href="memberFriends.php?view=list" class="<?php echo ($view == 'list')? 'active' : ''; ?>">My Friends</a>

        <a href="memberFriends.php?view=requests" class="<?php echo ($view == 'requests')? 'active' : ''; ?>">Requests</a>

        <a href="memberFriends.php?view=search" class="<?php echo ($view == 'search')? 'active' : ''; ?>">Find Friends</a>
    </div>
    <?php if(isset($_SESSION['role_id']) && $view=='list'): ?>
        <?php if(mysqli_num_rows($result)>0):?>
            <div class="friendContainer">
                <?php while($friend_info = mysqli_fetch_assoc($result)):?>
                    <div class="card friendCard">                
                        <img class="friendpfp" src="<?php echo $friend_info['profile_photo']?>" alt="pfp">
                        <h1><?php echo $friend_info['username']?></h1>

                        <div class="friendAction">
                            <button class="viewFriendProfileBtn"
                                data-friendName="<?php echo $friend_info['username']?>"
                                data-friendEmail = "<?php echo $friend_info['email']?>"
                                data-friendBio = "<?php echo $friend_info['bio']?>"
                                data-img="<?php echo $friend_info['profile_photo']?>"
                                data-totalPoints="<?php echo $friend_info['total_point_accumulated']?>"
                                data-friendSince="<?php echo date("d F Y",strtotime($friend_info['creation_date']))?>"
                            >View Profile</button>

                            <form action="" method="POST" class="deleteFriendForm">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="friendship_id" value="<?php echo $friend_info['friendship_id']?>">
                                <button class="" type="submit">Delete Friend</button>
                            </form>
                        </div>

                    </div>
                <?php endwhile;?>
            </div>
        <?php else:?>
            <div class="friendContainer">
                <h1>You have no friends!</h1>
            </div>
        <?php endif?>
        <div class="modal" id="friendProfile">
            <div class="modalContent" id="profileWindow">
                <button class="closeModalBtn" id="closeModalBtn">
                    <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 5L4.99998 19M5.00001 5L19 19" stroke="#1a5021" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div class="fpTop">
                    <h1><span id="profileTitle"></span>'s Profile</h1>
                    <img id="friendImg" src="" alt="friendPFP">
                    <div id="friendInfo">
                        <h3>Username: <span id="friendName"></span></h3>
                        <h3>Email: <span id="friendEmail"></span></h3>
                    </div>
                </div>
                <div class="fpContainer">

                    <div class="fpCard">
                        <h3>Bio</h3><br>
                        <p id="bio"></p>
                    </div>

                    <div class="fpCard">
                        <h3>Total Accumulated Point</h3><br>
                        <p id="totalPoint"></p>
                    </div>
                    
                    <div class="fpCard">
                        <h3>Friend Since</h3><br>
                        <p id="friendSince"></p>
                    </div>        
                </div>
            </div>
        </div>
    <?php endif;?>

    <?php if(isset($_SESSION['role_id']) && $view=='requests'): ?>
        <?php if(mysqli_num_rows($result)>0):?>
            <div class="friendContainer">
                <?php while($friend_info = mysqli_fetch_assoc($result)):?>
                    <div class="card friendCard">
                        <img class="friendpfp" src="<?php echo $friend_info['profile_photo']?>" alt="pfp">
                        <h1><?php echo $friend_info['username']?></h1>
                        <form class="fCardButtons" action="" method="POST">
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="friendship_id" value="<?php echo $friend_info['friendship_id']?>">
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

    <?php if(isset($_SESSION['role_id']) && $view=='search'): ?>
        <?php if(mysqli_num_rows($result)>0):?>
            <div class="friendContainer">
                <input type="text" id="friendSearch" placeholder="enter friend username!">
                <?php while($friend_info = mysqli_fetch_assoc($result)):?>
                    <div class="card friendCard" data-username="<?php echo $friend_info['username']?>">
                        <img class="friendpfp" src="<?php echo $friend_info['profile_photo']?>" alt="pfp">
                        <h1><?php echo $friend_info['username']?></h1>
                        <form class="fCardButtons" action="" method="POST">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="friend_id" value="<?php echo $friend_info['user_id']?>">
                            <button type="submit">Add Friend</button>
                        </form>
                    </div>
                <?php endwhile;?>
            </div>
        <?php else:?>
            <div class="friendContainer">
                <h1>Ops Looks Like no one is here</h1>
            </div>
        <?php endif;?>
    <?php endif;?>


    <?php include "../include/showSWAL.php"?>
    <script src="../../js/member.js"></script>
</body>
</html>