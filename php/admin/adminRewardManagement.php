<?php
    include "../include/conn.php";
    include "../include/session.php";

    $user_id = $_SESSION['user_id'];
    $filter = isset($_GET['status']) ? $_GET['status'] : 'All';
    $view = isset($_GET['view']) ? $_GET['view'] : 'pendingPage';
    
    // $sqlComplete = "SELECT * FROM reward WHERE status IN ('Approved', 'Available', 'Deactivated') ORDER BY reward_id ASC";
    $sqlPending = "SELECT * FROM reward WHERE status='pending' ORDER BY reward_ID ASC";
    $resultPending = mysqli_query($dbConn, $sqlPending);
    
    if($filter == "Approved"){
        $sql = "SELECT * FROM reward WHERE status = 'Approved'";
        }else if($filter == "Available"){
            $sql = "SELECT * FROM reward WHERE status = 'Available'";
        }else if($filter == "Deactivated"){
            $sql = "SELECT * FROM reward WHERE status = 'Deactivated'";
        }else{
            $sql = "SELECT * FROM reward WHERE status IN ('Approved','Available','Deactivated') ORDER BY reward_id ASC";
        }
    $resultComplete = mysqli_query($dbConn, $sql);
                 

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['approveBtn'])){
            $reward_id = $_POST['reward_id'];
            $admin_id = $_SESSION['user_id'];

            $sql = "UPDATE reward SET status = 'Approved', admin_id = $admin_id WHERE reward_id = $reward_id";
            if(mysqli_query($dbConn, $sql)){
                echo "<script>
                        alert('Successfully Approved Reward! :)')
                        window.location.href = '../admin/adminRewardManagement.php'
                    </script>";
            }else{
                echo("<script>
                        alert('Error!');
                        window.location.href = '../admin/adminRewardManagement.php';
                    </script>"); 
            }
        }
        if(isset($_POST['rejectBtn'])){
            $reward_id = $_POST['reward_id'];
            $admin_id = $_SESSION['user_id'];

            $sql = "UPDATE reward SET status = 'Rejected', admin_id = $admin_id WHERE reward_id = $reward_id";

            if(mysqli_query($dbConn, $sql)){
                echo "<script>
                        alert('Successfully Rejected Reward! :)')
                        window.location.href = '../admin/adminRewardManagement.php'
                    </script>";
            }else{
                echo("<script>
                        alert('Error!');
                        window.location.href = '../admin/adminRewardManagement.php';
                    </script>"); 
            }
        }
        if(isset($_POST['activateBtn'])){
            $reward_id = $_POST['reward_id'];
            $admin_id = $_SESSION['user_id'];

            $sql = "UPDATE reward SET status = 'Available', admin_id = $admin_id WHERE reward_id = $reward_id";
            if(mysqli_query($dbConn, $sql)){
                echo "<script>
                        alert('Successfully Activated Reward! :)')
                        window.location.href = '../admin/adminRewardManagement.php'
                    </script>";
            }else{
                echo("<script>
                        alert('Error!');
                        window.location.href = '../admin/adminRewardManagement.php';
                    </script>"); 
            }
        }
        if(isset($_POST['deactivateBtn'])){
            $reward_id = $_POST['reward_id'];
            $admin_id = $_SESSION['user_id'];

            $sql = "UPDATE reward SET status = 'Deactivated', admin_id = $admin_id WHERE reward_id = $reward_id";

            if(mysqli_query($dbConn, $sql)){
                echo "<script>
                        alert('Successfully Deactivated Reward! :)')
                        window.location.href = '../admin/adminRewardManagement.php'
                    </script>";
            }else{
                echo("<script>
                        alert('Error!');
                        window.location.href = '../admin/adminRewardManagement.php';
                    </script>"); 
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

    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/admin.css">
</head>
<body>
    <?php 
        include "../include/header.php"; 
    ?>
    <h2>Reward Management</h2>

    <!-- <div class="tabs">
        <button class="tab active" data-target="pendingTable">Pending Rewards</button>
        <button class="tab" data-target="approvedTable">Approved Rewards</button>
    </div> -->

    <a href="?view=pendingPage" class="<?php echo ($view == 'available')? 'active':'' ?>">Pending Rewards</a>
    <a href="?view=approvedPage" class="<?php echo ($view == 'available')? 'active':'' ?>">Approved Rewards</a>

    <?php if(isset($_GET['view'])&&$_GET['view']=='pendingPage'):?>
        <div id="pendingTable">
            <h3>Pending Rewards Table</h3>
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
                                    <td data-label="Status"><?php echo $reward_info['status'];?></td>
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
    <?php elseif(isset($_GET['view'])&&$_GET['view']=='approvedPage'):?>
        <div id="approvedTable">
            <h3>Approved Rewards Table</h3> 
            <form method="GET" id="filterForm">
                <input type="hidden" name="view" value="approvedPage">
                <label for="statusFilter">Filter:</label>
                <select name="status" id="statusFilter" class="dropdown" onchange="this.form.submit()">
                    <option value="All" <?= $filter=='All'?'selected':'' ?>>All</option>
                    <option value="Approved" <?= $filter=='Approved'?'selected':'' ?>>Approved</option>
                    <option value="Available" <?= $filter=='Available'?'selected':'' ?>>Available</option>                
                    <option value="Deactivated" <?= $filter=='Deactivated'?'selected':'' ?>>Deactivated</option>
                </select>
            </form>
            <hr>
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
                                        <td data-label="Status"><?php echo $reward_info['status'];?></td>
                                        <td data-label="Activate/Deactivate Button"> 
                                            <?php if($reward_info['status'] =='Approved'):;?>
                                            <!--Activate Button -->
                                                <form method="POST">
                                                    <input type="hidden" name="reward_id" value="<?php echo $reward_info['reward_id'];?>">
                                                    <button type="submit" name="activateBtn" class="activateBtn">Activate</button>
                                                </form>
                                                <?php elseif($reward_info['status'] =='Available'):;?>
                                                <!--Deactivate Button -->
                                                <form method="POST">
                                                    <input type="hidden" name="reward_id" value="<?php echo $reward_info['reward_id'];?>">
                                                    <button type="submit" name="deactivateBtn" class="deactivateBtn">Deactivate</button>
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
        <script src="../../js/admin.js"> </script>
</body>
</html>