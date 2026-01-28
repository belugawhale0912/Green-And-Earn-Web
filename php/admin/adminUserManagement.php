<?php
    include "../include/conn.php";
    include "../include/session.php";
   
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM user INNER JOIN role ON role.role_id = user.role_id ORDER BY user_id ASC";
    $sqlMember = "SELECT * FROM user WHERE role_id = '2' ORDER BY user_id ASC";
    $sqlModerator = "SELECT * FROM user WHERE role_id = '3' ORDER BY user_id ASC";
    $sqlPartner = "SELECT * FROM user WHERE role_id = '4' ORDER BY user_id ASC";
    $result = mysqli_query($dbConn, $sql);
    $resultMember = mysqli_query($dbConn, $sqlMember);
    $resultModerator = mysqli_query($dbConn, $sqlModerator);
    $resultPartner = mysqli_query($dbConn, $sqlPartner);
    $view = isset($_GET['view']) ? $_GET['view'] : 'pendingPage';

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST['action'])){
            if($_POST['action'] == "add"){    
                $username = $_POST['username'];
                //password is hashed username
                $password_hash = password_hash($username, PASSWORD_DEFAULT);
                $role = $_POST['role_id'];
                $email = $_POST['email'];
                $profilePhoto = '../../assets/placeHolders/placeholder.png';

                $sql = "INSERT INTO user (role_id, username, password_hash, email, profile_photo)
                        VALUES ('$role','$username', '$password_hash', '$email', '$profilePhoto')";
                
                if(mysqli_query($dbConn, $sql)){
                    echo("<script>
                            alert('Successfully Added User!');
                            window.location.href = '../admin/adminUserManagement.php';
                        </script>");
                }else{
                    echo("<script>
                            alert('Failed to Add User!');
                            window.location.href = '../admin/adminUserManagement.php';
                        </script>");
                }
            }
            if($_POST['action'] == "suspend"){
                $user_id = $_POST['user_id'];

                $sql = "UPDATE user SET active_status = 0 WHERE user_id = $user_id";

                if(mysqli_query($dbConn, $sql)){
                    echo("<script>
                            alert('Successfully Suspended User!');
                            window.location.href = '../admin/adminUserManagement.php';
                        </script>");
                }else{
                    echo("<script>
                            alert('Error!');
                            window.location.href = '../admin/adminUserManagement.php';
                        </script>"); 
                }
            }
            if($_POST['action'] == "activate"){
                $user_id = $_POST['user_id'];

                $sql = "UPDATE user SET active_status = 1 WHERE user_id = $user_id";

                if(mysqli_query($dbConn, $sql)){
                    echo("<script>
                            alert('Successfully Activated User!');
                            window.location.href = '../admin/adminUserManagement.php';
                        </script>");
                }else{
                    echo("<script>
                        alert('Error!');
                        window.location.href = '../admin/adminUserManagement.php';
                    </script>"); 
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
    <title>User Management</title>

    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/admin.css">
</head>
<body>
    <?php 
        include "../include/header.php"; 
    ?>
    <h2>User Management</h2>
    
    <div class="tabs">
    <a class="pages" href="?view=memberPage">Members</a>
    <a class="pages" href="?view=moderatorPage">Moderators</a>
    <a class="pages" href="?view=partnerPage">Partners & Sponsors</a>

        <button id="addUserBtn">Add New User</button>
    </div>

    <?php if(isset($_GET['view'])&&$_GET['view']=='memberPage'):?>
    <div id="memberTable">
        <h3>List of Members</h3>
            <?php if(mysqli_num_rows($resultMember)>0):?>
                <table>
                    <thead>
                        <tr id="header">
                            <th>User ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Point Balance</th>
                            <th>Status</th>
                            <th>Register Date</th>
                            <th></th> <!-- Suspend & Activate -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user_info = mysqli_fetch_assoc($resultMember)):?> 
                            <tr>
                                <td data-label="User ID"><?php echo $user_info['user_id'];?></td>
                                <td data-label="Username"><?php echo $user_info['username'];?></td>
                                <td data-label="Email"><?php echo $user_info['email'];?></td>
                                <td data-label="Point Balance"><?php echo $user_info['point_balance'];?></td>
                                <?php if($user_info['active_status']==1){
                                    $memberStatus = "Active";
                                }else{
                                    $memberStatus = "Inactive";
                                }
                                ?>
                                <td data-label="Status"><?php echo $memberStatus?></td>
                                <td data-label="Register Date"><?php echo $user_info['register_date'];?></td>
                                <td data-label="Suspend/Activate Button"> 
                                <!--Suspend Button -->
                                    <?php if($user_info['active_status'] == 1): ?>
                                        <form  method="POST">
                                            <input type="hidden" name="action" value="suspend">
                                            <input type="hidden" name="user_id" value="<?php echo $user_info['user_id'];?>">
                                            <button type="submit" class="suspendBtn">Suspend</button>
                                        </form> 
                                    <?php else: ?>
                                <!--Activate Button -->
                                        <form method="POST">
                                            <input type="hidden" name="action" value="activate">
                                            <input type="hidden" name="user_id" value="<?php echo $user_info['user_id'];?>">
                                            <button type="submit" class="activateBtn">Activate</button>
                                        </form>
                                    <?php endif;?>    
                                </td>
                            </tr>   
                        <?php endwhile; ?>  
                    </tbody>
                </table>
            <?php endif; ?>
    </div>    

    <?php elseif(isset($_GET['view'])&&$_GET['view']=='moderatorPage'):?>
    <div id="moderatorTable">
        <h3>List of Moderators</h3>
        <?php if(mysqli_num_rows($resultModerator)>0):?>
            <table>
                <thead>
                    <tr id="header">
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Point Balance</th>
                        <th>Status</th>
                        <th>Register Date</th>
                        <th></th> <!-- Suspend & Activate -->
                </thead>
                <tbody>
                    <?php while($user_info = mysqli_fetch_assoc($resultModerator)):?> 
                        <tr>
                            <td data-label="User ID"><?php echo $user_info['user_id'];?></td>
                            <td data-label="Username"><?php echo $user_info['username'];?></td>
                            <td data-label="Email"><?php echo $user_info['email'];?></td>
                            <td data-label="Point Balance"><?php echo $user_info['point_balance'];?></td>
                            <?php if($user_info['active_status']==1){
                                $memberStatus = "Active";
                            }else{
                                $memberStatus = "Inactive";
                            }
                            ?>
                            <td data-label="Status"><?php echo $memberStatus?></td>
                            <td data-label="Register Date"><?php echo $user_info['register_date'];?></td>
                            <td data-label="Suspend/Activate Button"> 
                            <!--Suspend Button -->
                                <?php if($user_info['active_status'] == 1): ?>
                                    <form  method="POST">
                                        <input type="hidden" name="action" value="suspend">
                                        <input type="hidden" name="user_id" value="<?php echo $user_info['user_id'];?>">
                                        <button type="submit" class="suspendBtn">Suspend</button>
                                    </form> 
                                <?php else: ?>
                            <!--Activate Button -->
                                    <form method="POST">
                                        <input type="hidden" name="action" value="activate">
                                        <input type="hidden" name="user_id" value="<?php echo $user_info['user_id'];?>">
                                        <button type="submit" class="activateBtn">Activate</button>
                                    </form>
                                <?php endif;?>    
                            </td>
                        </tr>   
                    <?php endwhile; ?>  
                </tbody>
            </table>
        <?php endif; ?>                   
    </div>

    <?php elseif(isset($_GET['view'])&&$_GET['view']=='partnerPage'):?>
    <div id="partnerTable">
        <h3>List of Partners and Sponsors</h3>
        <?php if(mysqli_num_rows($resultPartner)>0):?>
            <table>
                <thead>
                    <tr id="header">
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Point Balance</th>
                        <th>Status</th>
                        <th>Register Date</th>
                        <th></th> <!-- Suspend & Activate -->
                    </tr>
                </thead>
                <tbody>
                    <?php while($user_info = mysqli_fetch_assoc($resultPartner)):?> 
                        <tr>
                            <td data-label="User ID"><?php echo $user_info['user_id'];?></td>
                            <td data-label="Username"><?php echo $user_info['username'];?></td>
                            <td data-label="Email"><?php echo $user_info['email'];?></td>
                            <td data-label="Point Balance"><?php echo $user_info['point_balance'];?></td>
                            <?php if($user_info['active_status']==1){
                                $memberStatus = "Active";
                            }else{
                                $memberStatus = "Inactive";
                            }
                            ?>
                            <td data-label="Status"><?php echo $memberStatus?></td>
                            <td data-label="Register Date"><?php echo $user_info['register_date'];?></td>
                            <td data-label="Suspend/Activate Button"> 
                            <!--Suspend Button -->
                                <?php if($user_info['active_status'] == 1): ?>
                                    <form  method="POST">
                                        <input type="hidden" name="action" value="suspend">
                                        <input type="hidden" name="user_id" value="<?php echo $user_info['user_id'];?>">
                                        <button type="submit" class="suspendBtn">Suspend</button>
                                    </form> 
                                <?php else: ?>
                            <!--Activate Button -->
                                    <form method="POST">
                                        <input type="hidden" name="action" value="activate">
                                        <input type="hidden" name="user_id" value="<?php echo $user_info['user_id'];?>">
                                        <button type="submit" class="activateBtn">Activate</button>
                                    </form>
                                <?php endif;?>    
                            </td>
                        </tr>   
                    <?php endwhile; ?>  
                </tbody>
            </table>
        <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <div id="userModal" class="modal">
        <div class="modalContent">
            <h2>Add New User</h2>
            <form action="" id="userForm" method="post">
                <input type="hidden" id="action" name="action">
                <input type="hidden" id="user_id" name="user_id">

                <label for="username">Username: </label>
                <input type="text" name="username" required>

                <label for="email">Email: </label>
                <input type="email" name="email" required>

                <label for="role">Role: </label>
                <select name="role_id" id="role_id" required>
                    <option value="2">Member</option>
                    <option value="3">Moderator</option>
                    <option value="4">Partner/Sponsor</option>
                </select>
                
                <button type="submit" id="submitBtn">Submit</button>
                <button type="button" class="cancelBtn" data-modal="userModal">Cancel</button>
            </form>

        </div>
    </div>
    <script src="../../js/admin.js"> </script>
</body>
</html>