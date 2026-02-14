<?php
    include "../include/conn.php";
    include "../include/session.php";

    $user_id = $_SESSION['user_id'];

    if(!isset($_SESSION['user_id'])){
        header("Location: ../index.php");
        exit();
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $sql = "SELECT * FROM user WHERE user_id = $user_id";
        $result = mysqli_query($dbConn,$sql);
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
            header("Location: profile.php");
            exit();

        }else{
            $_SESSION['popupMessage']=[
                'title' => 'Error',
                'text' => 'Error while updating profile, please try again!',
                'icon' => 'error'
            ];
            header("Location: profile.php");
            exit();
        }
    }

    $sql = "SELECT * FROM user JOIN role ON role.role_id = user.role_id WHERE user.user_id = ?";
    $stmt = mysqli_prepare($dbConn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if(mysqli_num_rows($result)>0){
        $user_info = mysqli_fetch_assoc($result);
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="icon" href="../../favicon.ico">
    <?php include "../include/libraries.php";?>
    <title>Profile</title>
</head>
<body>
    <script src="../../js/customSwal.js"></script>
    <?php
        include "../include/header.php";
    ?>
    <h2>Profile</h2>
    <div class="profileCard">
        <img class="profileImg" src="<?php echo $user_info['profile_photo']?>" alt="Profile Photo"><br>
    </div>
    <button class="editBtn" id="editBtn">Edit Profile</button>
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
    <script src="../../js/profile.js"></script>

</body>
</html>
<?php mysqli_close($dbConn)?>