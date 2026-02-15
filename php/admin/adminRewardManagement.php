<?php
    include "../include/conn.php";
    include "../include/session.php";

    // ensure the user is admin, if not redirect to landing page
    if($_SESSION['role_id'] != 1){
        header("Location: ../pages/Landingpage.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    //filter php
    $filter = isset($_GET['status']) ? $_GET['status'] : 'All';
    //toggle page php
    $view = isset($_GET['view']) ? $_GET['view'] : 'approvedPage';
    
    $sqlPending = "SELECT * FROM reward WHERE status='Pending' ORDER BY reward_ID ASC";
    $resultPending = mysqli_query($dbConn, $sqlPending);
    
    if($filter == "Available"){
        $sql = "SELECT * FROM reward WHERE status = 'Available'";
        }else if($filter == "Deactivated"){
            $sql = "SELECT * FROM reward WHERE status = 'Deactivated'";
        }else{
            $sql = "SELECT * FROM reward WHERE status IN ('Available','Deactivated') ORDER BY reward_id ASC";
        }
    $resultComplete = mysqli_query($dbConn, $sql);
                 
    //ensure form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        //approve button in table
        if(isset($_POST['approveBtn'])){
            $reward_id = $_POST['reward_id'];
            $admin_id = $_SESSION['user_id'];

            $sql = "UPDATE reward SET status = 'Available', admin_id = ? WHERE reward_id = ?";
            //prevent sql injection
            $stmt = mysqli_prepare($dbConn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $admin_id, $reward_id);

            //sweet alert
            if(mysqli_stmt_execute($stmt)){
                $_SESSION['popupMessage']=[
                    'title' => 'Success',
                    'text' => 'Approved Reward Successfully',
                    'icon' => 'success'
                ];
                mysqli_stmt_close($stmt);

                header("Location: ../admin/adminRewardManagement.php ?view=$view");
                exit();
            }else{
                $_SESSION['popupMessage']=[
                    'title' => 'Error',
                    'text' => 'Failed to Approve Reward',
                    'icon' => 'error'
                ];
                mysqli_stmt_close($stmt);

                header("Location: ../admin/adminRewardManagement.php ?view=$view");
                exit();
            }
        }
        //reject button in table
        if(isset($_POST['rejectBtn'])){
            $reward_id = $_POST['reward_id'];
            $admin_id = $_SESSION['user_id'];

            $sql = "UPDATE reward SET status = 'Rejected', admin_id = ? WHERE reward_id = ?";
            //prevent sql injection
            $stmt = mysqli_prepare($dbConn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $admin_id, $reward_id);

            if(mysqli_stmt_execute($stmt)){
                $_SESSION['popupMessage']=[
                    'title' => 'Success',
                    'text' => 'Rejected Reward Successfully',
                    'icon' => 'success'
                ];
                mysqli_stmt_close($stmt);

                header("Location: ../admin/adminRewardManagement.php ?view=$view");
                exit();
            }else{
                $_SESSION['popupMessage']=[
                    'title' => 'Error',
                    'text' => 'Failed to Reject Reward',
                    'icon' => 'error'
                ];
                mysqli_stmt_close($stmt);

                header("Location: ../admin/adminRewardManagement.php ?view=$view");
                exit();
            }
        }
        //activate buttonin table
        if(isset($_POST['activateBtn'])){
            $reward_id = $_POST['reward_id'];
            $admin_id = $_SESSION['user_id'];

            $sql = "UPDATE reward SET status = 'Available', admin_id = ? WHERE reward_id = ?";
            //prevent sql injection
            $stmt = mysqli_prepare($dbConn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $admin_id, $reward_id);
            //sweet alert
            if(mysqli_stmt_execute($stmt)){
                $_SESSION['popupMessage']=[
                    'title' => 'Success',
                    'text' => 'Activated Reward Successfully',
                    'icon' => 'success'
                ];
                mysqli_stmt_close($stmt);

                header("Location: ../admin/adminRewardManagement.php ?view=$view");
                exit();
            }else{
                $_SESSION['popupMessage']=[
                    'title' => 'Error',
                    'text' => 'Failed to Activate Reward',
                    'icon' => 'error'
                ];
                mysqli_stmt_close($stmt);

                header("Location: ../admin/adminRewardManagement.php ?view=$view");
                exit();
            }
        }
        //deactivate button in table
        if(isset($_POST['deactivateBtn'])){
            $reward_id = $_POST['reward_id'];
            $admin_id = $_SESSION['user_id'];

            $sql = "UPDATE reward SET status = 'Deactivated', admin_id = ? WHERE reward_id = ?";
            //prevent sql injection
            $stmt = mysqli_prepare($dbConn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $admin_id, $reward_id);
            //sweet alert
            if(mysqli_stmt_execute($stmt)){
                $_SESSION['popupMessage']=[
                    'title' => 'Success',
                    'text' => 'Deactivated Reward Successfully',
                    'icon' => 'success'
                ];
                mysqli_stmt_close($stmt);

                header("Location: ../admin/adminRewardManagement.php ?view=$view");
                exit();
            }else{
                $_SESSION['popupMessage']=[
                    'title' => 'Error',
                    'text' => 'Failed to Deactivate Reward',
                    'icon' => 'error'
                ];
                mysqli_stmt_close($stmt);

                header("Location: ../admin/adminRewardManagement.php ?view=$view");
                exit();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reward Management</title>
    <link rel="icon" href="../../favicon.ico">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <?php include "../include/libraries.php";?>
</head>
<body>
    <script src="../../js/customSwal.js"></script>      
    <?php include "../include/header.php"; ?>

    <h2 class="pageTitle">Reward Management</h2>
    <!-- view page toggle -->
    <div class="viewToggle">
        <a href="?view=approvedPage" class="<?php echo ($view == 'approvedPage')? 'active':'' ?>">Approved Rewards</a>
        <a href="?view=pendingPage" class="<?php echo ($view == 'pendingPage')? 'active':'' ?>">Pending Rewards</a>
    </div>

    <!-- pending page -->
    <?php if($view=='pendingPage'):?>
        <div id="pendingTable">
            <h3 class="tableTitle">Pending Rewards Table</h3>
                <?php if(mysqli_num_rows($resultPending)>0):?>
                    <table>
                        <thead>
                            <tr id="header">
                                <th>Reward ID</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Required Points</th>
                                <th>Status</th>
                                <th></th> <!-- Approve -->
                                <th></th> <!-- Reject -->
                            </tr>
                        </thead>   
                        <tbody>
                            <?php while($reward_info = mysqli_fetch_assoc($resultPending)):?>
                                <tr>
                                    <td data-label="Reward ID"><?php echo $reward_info['reward_id'];?></td>
                                    <td data-label="Title"><?php echo $reward_info['title'];?></td>
                                    <td data-label="Description"><?php echo $reward_info['description'];?></td>
                                    <td data-label="Required Points"><?php echo $reward_info['required_points'];?></td>
                                    <td data-label="Status"><p class="rewardStatus <?php echo $reward_info['status']?>"><?php echo $reward_info['status'];?></p></td>
                                    <td data-label="Approve Button"> 
                                    <!--Approve Button -->
                                        <form method="POST">
                                            <input type="hidden" name="reward_id" value="<?php echo $reward_info['reward_id'];?>">
                                            <button type="submit" name="approveBtn" class="approveBtn">Approve</button>
                                        </form>
                                    </td>         
                                    <td data-label="Reject Button">    
                                    <!--Reject Button -->
                                        <form method="POST">
                                            <input type="hidden" name="reward_id" value="<?php echo $reward_info['reward_id'];?>">
                                            <button type="submit" name="rejectBtn" class="rejectBtn">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile;?>        
                        </tbody>
                </table>
            <?php endif; ?>
        </div>

    <!-- approved page -->
    <?php elseif($view=='approvedPage'):?>
        <div id="approvedTable">
            <h3 class="tableTitle">Approved Rewards Table</h3> 
            <form method="GET" class="filterForm" id="rewardFilter">
                <input type="hidden" name="view" value="approvedPage">
                <label for="statusFilter"></label>
                <select name="status" id="statusFilter" class="dropdown" onchange="this.form.submit()">
                    <option value="All" <?= $filter=='All'?'selected':'' ?>>All</option>
                    <option value="Available" <?= $filter=='Available'?'selected':'' ?>>Available</option>                
                    <option value="Deactivated" <?= $filter=='Deactivated'?'selected':'' ?>>Deactivated</option>
                </select>
            </form>
                <?php if(mysqli_num_rows($resultComplete)>0):?>
                    <table>
                        <thead>
                            <tr id="header">
                                <th>Reward ID</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Required Points</th>
                                <th>Status</th>
                                <th></th> <!-- Activate & Deactivate -->
                            </tr>
                        </thead>
                        <tbody>
                                <?php while($reward_info = mysqli_fetch_assoc($resultComplete)):?>
                                    <tr>
                                        <td data-label="Reward ID"><?php echo $reward_info['reward_id'];?></td>
                                        <td data-label="Title"><?php echo $reward_info['title'];?></td>
                                        <td data-label="Description"><?php echo $reward_info['description'];?></td>
                                        <td data-label="Required Points"><?php echo $reward_info['required_points'];?></td>
                                        <td data-label="Status"><p class="rewardStatus <?php echo $reward_info['status']?>"><?php echo $reward_info['status'];?></p></td>
                                        <td data-label="Deactivate Button"> 
                                                <?php if($reward_info['status'] =='Available'):;?>
                                                <!--Deactivate Button -->
                                                <form method="POST">
                                                    <input type="hidden" name="reward_id" value="<?php echo $reward_info['reward_id'];?>">
                                                    <button type="submit" name="deactivateBtn" class="deactivateBtn">Deactivate</button>
                                                </form>
                                                <?php elseif($reward_info['status'] =='Deactivated'):;?>
                                                <!--Deactivate Button -->
                                                <form method="POST">
                                                    <input type="hidden" name="reward_id" value="<?php echo $reward_info['reward_id'];?>">
                                                    <button type="submit" name="activateBtn" class="activateBtn">Activate</button>
                                                </form>
                                            <?php endif;?>
                                        </td>
                                    </tr>
                                <?php endwhile;?>
                            </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif?>

    <?php include "../include/showSWAL.php"?>
    <script src="../../js/admin.js"> </script>
</body>
</html>
<?php mysqli_close($dbConn)?>