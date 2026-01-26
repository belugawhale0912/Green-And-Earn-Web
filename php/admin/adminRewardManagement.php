<?php
    include "../include/conn.php";
    include "../include/session.php";

    $user_id = $_SESSION['user_id'];
    $sqlComplete = "SELECT * FROM reward WHERE status IN ('approved', 'available', 'disabled') ORDER BY reward_id ASC";
    $sqlPending = "SELECT * FROM reward WHERE status='pending' ORDER BY reward_ID ASC";
    $resultComplete = mysqli_query($dbConn, $sqlComplete);
    $resultPending = mysqli_query($dbConn, $sqlPending);

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST['approve_btn'])){
            $reward_id = $_POST['reward_id'];
            $admin_id = $_SESSION['user_id'];

            $sql = "UPDATE reward SET status = 'Available', admin_id = $admin_id WHERE reward_id = $reward_id";
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
        if(isset($_POST['reject_btn'])){
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
        if(isset($_POST['activate_btn'])){
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
        if(isset($_POST['deactivate_btn'])){
            $reward_id = $_POST['reward_id'];
            $admin_id = $_SESSION['user_id'];

            $sql = "UPDATE reward SET status = 'Disabled', admin_id = $admin_id WHERE reward_id = $reward_id";

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

    <div class="tabs">
        <button class="tab active" data-target="pendingTable">Pending Rewards</button>
        <button class="tab" data-target="approvedTable"="">Approved Rewards</button>
    </div>

    <div id="pendingTable" class="table-section active">
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
                                <td><?php echo $reward_info['reward_id'];?></td>
                                <td><?php echo $reward_info['title'];?></td>
                                <td><?php echo $reward_info['description'];?></td>
                                <td><?php echo $reward_info['required_points'];?></td>
                                <td><?php echo $reward_info['status'];?></td>
                                <td> 
                                <!--Approve Button -->
                                    <form method="POST">
                                        <input type="hidden" name="reward_id" value="<?php echo $reward_info['reward_id'];?>">
                                        <button type="submit" name="approve_btn">Approve</button>
                                    </form>
                                </td>         
                                <td>    
                                <!--Reject Button -->
                                    <form method="POST">
                                        <input type="hidden" name="reward_id" value="<?php echo $reward_info['reward_id'];?>">
                                        <button type="submit" name="reject_btn">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile;?>        
                    </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div id="approvedTable" class="table-section">
        <h3>Approved Rewards Table</h3>
            <?php if(mysqli_num_rows($resultComplete)>0):?>
                <table>
                    <thead>
                        <tr id="header">
                            <th>Reward ID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Required Points</th>
                            <th>Status</th>
                            <th></th> <!-- Activate -->
                            <th></th> <!-- Deactivate -->
                            
                        </tr>
                    </thead>
                    <tbody>
                            <?php while($reward_info = mysqli_fetch_assoc($resultComplete)):?>
                                <tr>
                                    <td><?php echo $reward_info['reward_id'];?></td>
                                    <td><?php echo $reward_info['title'];?></td>
                                    <td><?php echo $reward_info['description'];?></td>
                                    <td><?php echo $reward_info['required_points'];?></td>
                                    <td><?php echo $reward_info['status'];?></td>
                                    <td> 
                                    <!--Activate Button -->
                                        <form method="POST">
                                            <input type="hidden" name="reward_id" value="<?php echo $reward_info['reward_id'];?>">
                                            <button type="submit" name="activate_btn">Activate</button>
                                        </form>
                                    </td>         
                                    <td>    
                                    <!--Deactivate Button -->
                                        <form method="POST">
                                            <input type="hidden" name="reward_id" value="<?php echo $reward_info['reward_id'];?>">
                                            <button type="submit" name="deactivate_btn">Deactivate</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile;?>
                        </tbody>
                </table>
            <?php endif; ?>
    </div>
    <script src="../../js/admin.js"> </script>
</body>
</html>