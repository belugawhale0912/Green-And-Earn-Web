<?php
    include "../include/conn.php";
    include "../include/session.php";

    // ensure the user is member, if not redirect to landing page
    if($_SESSION['role_id'] != 2){
        header("Location: ../pages/Landingpage.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $sql = "SELECT * FROM user WHERE user_id = $user_id";
        $stmt = mysqli_prepare($dbConn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        $user_info = mysqli_fetch_assoc($result);
        $user_id = $_SESSION['user_id'];
        $new_username = $_POST['username'];
        $newBio = $_POST['bio'];
        $new_email = $_POST['email'];

        $new_password = $_POST['password'];
        $password_confirm = $_POST['confirmPassword'];

        if(!empty($new_password)){
            if($new_password == $password_confirm){
                // hash the password
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            }
            else{
                $_SESSION['popupMessage']=[
                    'title' => 'Error While Updating Password',
                    'text' => 'Password does not match! Try Again',
                    'icon' => 'error'
                ];
                header("Location: memberProfile.php?view=available");
                exit();
            } 
        }
        else{
            $new_password_hash = $user_info['password_hash'];
        }

        // check that file exists, file received with no error
        if(isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] === UPLOAD_ERR_OK){
            
            // retrive uploaded file name
            $fileName = $_FILES['profilePhoto']['name'];

            // retrive temp path on server
            $fileServerPath = $_FILES['profilePhoto']['tmp_name'];
            
            // retrive extention of the uploaded file
            $fileExtension = pathinfo($fileName,PATHINFO_EXTENSION);


            $supportedExt = ['jpg','jpeg','png','gif'];

            // if the uploaded file is supported
            if(in_array($fileExtension,$supportedExt)){

                $newFileName = "profile_" . $user_id . "_" . date("dmY_His") . "." . $fileExtension;
                
                $saveDir = "../../uploads/profileImg/" . $newFileName;

                if(!move_uploaded_file($fileServerPath, $saveDir)){
                    $_SESSION['popupMessage']=[
                        'title' => 'Error',
                        'text' => 'Error while processing file, please try again!',
                        'icon' => 'warning'
                    ];
                    header("Location: memberProfile.php");
                    exit();
                }
            }else{
                $_SESSION['popupMessage']=[
                    'title' => 'Invalid File Format',
                    'text' => 'Supported File Format: jpg, jpeg, png, gif',
                    'icon' => 'warning'
                ];
                header("Location: memberProfile.php");
                exit();
            }
            
        }else{
            $saveDir = $user_info['profile_photo'];
        }

        $updateSql = "UPDATE user SET
                      username = ?,
                      email = ?,
                      bio = ?,
                      password_hash = ?,
                      profile_photo = ?
                      WHERE user_id = ?
                      ";

        $stmt = mysqli_prepare($dbConn, $updateSql);
        mysqli_stmt_bind_param($stmt, "sssssi", $new_username, $new_email, $newBio, $new_password_hash, $saveDir, $user_id);

        if(mysqli_stmt_execute($stmt)){
            $_SESSION['popupMessage']=[
                'title' => 'Success',
                'text' => 'Profile Updated Successfully',
                'icon' => 'success'
            ];
            mysqli_stmt_close($stmt);
            header("Location: memberProfile.php");
            exit();

        }else{
            $_SESSION['popupMessage']=[
                'title' => 'Error',
                'text' => 'Error while updating profile, please try again!',
                'icon' => 'error'
            ];
            mysqli_stmt_close($stmt);
            header("Location: memberProfile.php");
            exit();
        }
    }

    // fetch user information
    $sqlUser = "SELECT * FROM user WHERE user_id = ?";
    $stmtUser = mysqli_prepare($dbConn, $sqlUser);
    mysqli_stmt_bind_param($stmtUser, "i", $user_id);
    mysqli_stmt_execute($stmtUser);
    $resultUser = mysqli_stmt_get_result($stmtUser);
    $user_info = mysqli_fetch_assoc($resultUser);


    // fetch the 3 most recent owned badges 
    $sql_recent = "SELECT b.title, b.badge_img 
                   FROM badge_obtain bo 
                   JOIN badge b ON bo.badge_id = b.badge_id 
                   WHERE bo.user_id = ? 
                   ORDER BY bo.obtain_date DESC LIMIT 3";
    $stmtRecent = mysqli_prepare($dbConn, $sql_recent);
    mysqli_stmt_bind_param($stmtRecent, "i", $user_id);
    mysqli_stmt_execute($stmtRecent);
    $result_recent = mysqli_stmt_get_result($stmtRecent);
    mysqli_stmt_close($stmtRecent);

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
    <title>Profile</title>
</head>
<body>
    <script src="../../js/customSwal.js"></script>
    <?php
        include "../include/header.php";
    ?>
    <div class="card profileBox">
        <img class="profileImg" src="<?php echo $user_info['profile_photo']?>" alt="Profile Photo">
        <div class="userinfo">
            <h1><?php echo $user_info['username']?></h1>
            <p><?php echo $user_info['email']?></p>
        </div>
        <button id="editProfileBtn">Edit Profile</button>
    </div>

    
    <div class="profileContainer">

        <div class="card profileCard">
            <h2>Bio</h2>
            <p><?php echo $user_info['bio']?></p>
        </div>

        <div class="card profileCard">
            <h2>Member Since:</h2>
            <h3><?php echo date("d M Y",strtotime($user_info['register_date']))?></h3>
        </div>
        <div class="card profileCard">
            <h2>Total Accumulated Points:</h2>
            <h3><?php echo $user_info['total_point_accumulated']?> pts</h3>
        </div>
        <div class="card pbadgeCard" >
            <h2>My Badges</h2>
            <div class="pbadgeContainer">
                <?php if(mysqli_num_rows($result_recent)>0):?>
                    <?php while($recentBadge_info  = mysqli_fetch_assoc($result_recent)):?>
                        <div class="badge">
                            <img src="../../assets/badges/<?php echo $recentBadge_info['badge_img']?>" alt="badgeIcon">
                            <p><?php echo $recentBadge_info['title']?></p>
                        </div>
                    <?php endwhile?>
                <?php else:?>
                    <h3>No badges Unlocked</h3>
                <?php endif?>
            </div>
            <a href="memberBadge.php">view all badges</a>
        </div>
    </div>
    <form action="../commands/logout.php" method="POST">
        <button class="logoutBtn" type="submit">Log Out</button>
    </form>

    <div id="editProfileModal" class="modal">
        <div class="modalContent editProfile">
            <button class="closeModalBtn" id="closeModalBtn">
                <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 5L4.99998 19M5.00001 5L19 19" stroke="#1a5021" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

            <h1>Edit Profile</h1>

            <form action="" method="POST" enctype="multipart/form-data">
                <label>Upload Profile Photo (Image):</label>
                <input type="file" name="profilePhoto" accept="image/*">              

                <label for="username">Username</label>
                <input type="text" name="username" class="modal-input" value="<?php echo $user_info['username'];?>" required>

                <label for="email">Email</label>
                <input type="email" name="email" class="modal-input" value="<?php echo $user_info['email'];?>" required>

                <label for="bio">Bio</label>
                <textarea name="bio" rows="3" class="modal-input" value="" required><?php echo $user_info['bio'];?></textarea>
                <label>Change Password(Leave Empty If dont wish to change)</label><br>
                <label for="password">New Password</label>
                <input type="text" name="password" class="modal-input">
                <label for="confirmPassword">Enter New Password Again </label>
                <input type="text" name="confirmPassword" class="modal-input">

                <button type="submit" class="updFormBtn">Save Changes</button>
                <button type="button" class="updFormBtn">Cancel</button>
            </form>
        </div>
    </div>
    <?php include "../include/showSWAL.php"?>
    <script src="../../js/member.js"></script>
</body>
</html>
<?php mysqli_close($dbConn)?>