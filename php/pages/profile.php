<?php
    session_start();
    include "../include/conn.php";

    if(!isset($_SESSION['user_id'])){
        header("Location: ../index.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    $sql = "SELECT * FROM user WHERE user_id = $user_id";
    $result = mysqli_query($dbConn,$sql);
    if(mysqli_num_rows($result)>0){
        $user_info = mysqli_fetch_assoc($result);
    }

    $profile_img = !empty($user_info['profile_img']) ? $user_info['profile_img']:"../../uploads/profileImg/Placeholder.png";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/layout.css">
    <title>Profile</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <img class="profileImg" src="<?php echo $profile_img?>" alt="Profile Photo"><br>
    <h1><?php echo $user_info['username']?></h1>
    <button class="editBtn" onclick="openModal()">Edit Profile</button>
    <div class="list_container">
        <div class="list_card">
            <h2>Email</h2>
            <h3><?php echo $user_info['email']?></h3>
        </div>
        <div class="list_card">
            <h2>Member Since</h2>
            <h3><?php echo $user_info['register_date']?></h3>
        </div>
        <div class="list_card">
            <h2>Total Accumulated Points</h2>
            <h3><?php echo $user_info['total_point_accumulated']?></h3>
        </div>
    </div>
    <form action="../commands/logout.php" method="POST">
        <button class="logoutBtn" type="submit">Log Out</button>
    </form>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit Profile</h2>
            <form action="../commands/update_profile.php" method="POST">
                <label for="username">Username</label>
                <input type="text" name="username" class="modal-input" value="<?php echo $user_info['username']; ?>" required>

                <label for="email">Email</label>
                <input type="email" name="email" class="modal-input" value="<?php echo $user_info['email']; ?>" required>

                <label>Change Password(Leave Empty If dont wish to change)</label><br>
                <label for="password">New Password</label>
                <input type="text" name="password" class="modal-input">
                <label for="confirmPassword">Enter New Password Again </label>
                <input type="text" name="confirmPassword" class="modal-input">

                <button type="submit" class="saveBtn">Save Changes</button>
                <button type="button" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>
    <script src="../../js/popup.js"></script>

</body>
</html>