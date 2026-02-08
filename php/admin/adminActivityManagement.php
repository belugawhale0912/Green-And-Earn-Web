<?php
    include "../include/conn.php";
    include "../include/session.php";

    $user_id = $_SESSION['user_id'];

    //filter status php, all as default
    $filter = isset($_GET['status']) ? $_GET['status'] : 'all';

    if($filter == "Active"){
        $sql = "SELECT * FROM activity WHERE status = 'Active'";
    }else if($filter == "Deleted"){
        $sql = "SELECT * FROM activity WHERE status = 'Deleted'";
    }else{
        $sql = "SELECT * FROM activity ORDER BY status ASC";
    }
    
    $result = mysqli_query($dbConn, $sql);

    //ensure form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        //set action
        if(isset($_POST['action'])){
            //set variables
            $admin_id = $_SESSION['user_id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $pointAward = $_POST['point'];
            $start = $_POST['startDate'];
            $end = $_POST['endDate'];

            //add activity
            if($_POST['action']=="add"){
                $sql = "INSERT INTO activity (admin_id, title, description, points_awarded, status, start_date, end_date) VALUES (?, ?, ?, ?, 'Active', ?, ?)";
                //prevent sql injection
                $stmt =  mysqli_prepare($dbConn, $sql);
                mysqli_stmt_bind_param($stmt, "ississ", $admin_id, $title, $description, $pointAward, $start, $end);

                //sweet alert
                if(mysqli_stmt_execute($stmt)){
                    $_SESSION['popupMessage']=[
                        'title' => 'Success',
                        'text' => 'Activity Added Successfully',
                        'icon' => 'success'
                    ];
                    header("Location: ../admin/adminActivityManagement.php");
                    exit();
                }else{
                    $_SESSION['popupMessage']=[
                        'title' => 'Error',
                        'text' => 'Failed to Add Activity',
                        'icon' => 'error'
                    ];
                    header("Location: ../admin/adminActivityManagement.php");
                    exit();
                }
            }
            //add update
            if($_POST['action']=="update"){
                $activity_id = $_POST['activity_id'];
                $sql = "UPDATE activity SET admin_id = ?, title = ?, description = ?, points_awarded = ?, start_date = ?, end_date = ? WHERE activity_id = ?";
                //prevent sql injection
                $stmt = mysqli_prepare($dbConn, $sql);
                mysqli_stmt_bind_param($stmt, "ississi", $admin_id, $title, $description, $pointAward, $start, $end, $activity_id);
                //sweet alert
                if(mysqli_stmt_execute($stmt)){
                    $_SESSION['popupMessage']=[
                        'title' => 'Success',
                        'text' => 'Activity Updated Successfully',
                        'icon' => 'success'
                    ];
                    header("Location: ../admin/adminActivityManagement.php");
                    exit();
                }else{
                    $_SESSION['popupMessage']=[
                        'title' => 'Error',
                        'text' => 'Failed to Update Activity',
                        'icon' => 'error'
                    ];
                    header("Location: ../admin/adminActivityManagement.php");
                    exit();
                }
            }
        }
        //delete button in table
        if(isset($_POST['deleteBtn'])){
            $activity_id = $_POST['activity_id'];
            $sql = "UPDATE activity SET status = 'Deleted' WHERE activity_id = ?";
            //prevent sql injection
            $stmt = mysqli_prepare($dbConn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $activity_id);
            //sweet alert
            if(mysqli_stmt_execute($stmt)){
                $_SESSION['popupMessage']=[
                        'title' => 'Success',
                        'text' => 'Activity Deleted Successfully',
                        'icon' => 'success'
                    ];
                    header("Location: ../admin/adminActivityManagement.php");
                    exit();
            }else{
                $_SESSION['popupMessage']=[
                        'title' => 'Error',
                        'text' => 'Failed to Delete Activity',
                        'icon' => 'error'
                    ];
                    header("Location: ../admin/adminActivityManagement.php");
                    exit();
            }
        }
        //restore button in table
        if(isset($_POST['restoreBtn'])){
            $activity_id = $_POST['activity_id'];
            //prevent sql injection
            $sql = "UPDATE activity SET status = 'Active' WHERE activity_id = ?";
            $stmt = mysqli_prepare($dbConn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $activity_id);
            //sweet alert
            if(mysqli_stmt_execute($stmt)){
                $_SESSION['popupMessage']=[
                        'title' => 'Success',
                        'text' => 'Activity Restored Successfully',
                        'icon' => 'success'
                    ];
                    header("Location: ../admin/adminActivityManagement.php");
                    exit();
            }else{
                $_SESSION['popupMessage']=[
                        'title' => 'Error',
                        'text' => 'Failed to Restore Activity',
                        'icon' => 'error'
                    ];
                    header("Location: ../admin/adminActivityManagement.php");
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
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <?php include "../include/libraries.php";?>
    <title>Manage Activities</title>
    <link rel="icon" href="/Green-Earn-Web/favicon.ico">
</head>
<body>
    <script src="../../js/customSwal.js"></script>  
    <?php
        include "../include/header.php";
    ?>
    <h2 class="pageTitle">Activity Management</h2>
    
    <h3 class="tableTitle">Activities Table</h3>
    <div class="activityBar">
        <!-- filter dropdown menu -->
        <form method="GET" class="filterForm">
            <label for="statusFilter"></label>
            <select name="status" id="statusFilter" class="dropdown" onchange="this.form.submit()">
                <option value="All" <?php if ($filter == "All") echo 'selected'; ?>>All</option>
                <option value="Active" <?php if ($filter == "Active") echo 'selected'; ?>>Active</option>
                <option value="Deleted" <?php if ($filter == "Deleted") echo 'selected'; ?>>Deleted</option>
            </select>
        </form>
        <button id="addActivityBtn">Add Activity</button>
    </div>
    <?php if(mysqli_num_rows($result)>0):?>
        <table>
            <thead>
                <tr id="header">
                    <th>Title</th>
                    <th>Description</th>
                    <th>Admin ID</th>
                    <th>Points Awarded</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while($activity_info = mysqli_fetch_assoc($result)):?>
                    <tr>
                        <td data-label="Title"><?php echo $activity_info['title'];?></td>
                        <td data-label="Description"><?php echo $activity_info['description'];?></td>
                        <td data-label="Admin ID"><?php echo $activity_info['admin_id'];?></td>
                        <td data-label="Points Awarded"><?php echo $activity_info['points_awarded'];?></td>
                        <td data-label="Status"><p class="activityStatus <?php echo $activity_info['status']?>"><?php echo $activity_info['status'];?></p></td>
                        <td data-label="Start Date"><?php echo $activity_info['start_date'];?></td>
                        <td data-label="End Date"><?php echo $activity_info['end_date'];?></td>
                        <td data-label="Edit Button">
                                <button class="editActivityBtn"
                                    data-id="<?php echo $activity_info['activity_id'];?>"
                                    data-title="<?php echo $activity_info['title'];?>"
                                    data-desc="<?php echo $activity_info['description'];?>"
                                    data-point="<?php echo $activity_info['points_awarded'];?>"
                                    data-start="<?php echo $activity_info['start_date'];?>"
                                    data-end="<?php echo $activity_info['end_date'];?>"
                                >Edit
                                </button>
                        </td>
                        <td data-label="Delete/Restore Button">
                            <?php if($activity_info['status'] !='Deleted'):;?>
                                <form action="" method="POST">
                                    <input type="hidden" name="activity_id" value="<?php echo $activity_info['activity_id'];?>">
                                    <button type="submit" class="deleteBtn" name="deleteBtn">Delete</button>
                                </form>
                            <?php else:;?>
                                <form action="" method="POST">
                                    <input type="hidden" name="activity_id" value="<?php echo $activity_info['activity_id'];?>">
                                    <button type="submit" class="restoreBtn" name="restoreBtn">Restore</button>
                                </form>
                            <?php endif;?>
                        </td>
                    </tr>
                
                <?php endwhile;?>
            </tbody>
        </table>

        <div id="activityModal" class="modal">
            <div class="modalContent" id="activityAdditionModal">
                <h2 id="activityModalTitle"></h2>
                <form action="" id="activityForm" method="POST">
                    <input type="hidden" id="action" name="action">
                    <input type="hidden" id="activityID" name="activity_id">
                    <label for="title">Title</label>
                    <input id="activityTitle" type="text" name="title" required>

                    <label for="description">Description</label>
                    <textarea id="activityDescription" name="description" rows="3"></textarea>
                    
                    <label for="pointAward">Points Awarded</label>
                    <input type="number" id="activityPoint" name="point" required></input>

                    <label for="startDate">Start Date</label>
                    <input type="date" id="startDate" name="startDate" required>

                    <label for="startDate">End Date</label>
                    <input type="date" id="endDate" name="endDate" required>

                <div class="buttonWrapper">
                    <button type="submit" id="confirmBtn">Confirm</button>
                    <button type="button" class="cancelBtn" data-modal="activityModal">Cancel</button>
                </div>
                </form>
            </div>
        </div>
    <?php endif;?>
    
    <?php include "../include/showSWAL.php"?>
    <script src="../../js/admin.js"></script>
</body>
</html>

