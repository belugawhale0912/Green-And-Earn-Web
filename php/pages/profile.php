<?php
    session_start();
    include "../include/conn.php";

    if(!isset($_SESSION['user_id'])){
        header("Location: ../index.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    $sql = "SELECT * FROM user JOIN role ON role.role_id = user.role_id WHERE user.user_id = $user_id ";
    $result = mysqli_query($dbConn,$sql);
    if(mysqli_num_rows($result)>0){
        $user_info = mysqli_fetch_assoc($result);
    }

    $profile_img = !empty($user_info['profile_photo']) ? $user_info['profile_photo']:"../../uploads/profileImg/Placeholder.png";

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
    <link rel="icon" href="/Green-Earn-Web/favicon.ico">
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <h2>Profile</h2>
    <div class="profileCard">
        <img class="profileImg" src="<?php echo $profile_img?>" alt="Profile Photo"><br>
    </div>
    <button class="editBtn" onclick="openModal()">Edit Profile</button>
    <div class="list_container">

        <div class="list_card">
            <h3><span class="title">Username | </span>
                <span class="subject"><?php echo $user_info['username']?> </span>
            </h3>
        </div>
        
        <div class="list_card">
            <h3><span class="title">Role | </span>
                <span class="subject"><?php echo $user_info['role_name']?> </span>
            </h3>
        </div>

        <div class="list_card">
            <h3><span class="title">Email | </span>
                <span class="subject"><?php echo $user_info['email']?> </span>
            </h3>
        </div>

        <div class="list_card">
            <h3><span class="title">Member Since | </span>
                <span class="subject"><?php echo $user_info['register_date']?> </span>
            </h3>
        </div>

    </div>
    <h4></h4>
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