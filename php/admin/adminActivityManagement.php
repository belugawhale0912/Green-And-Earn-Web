<?php
    include "../include/conn.php";
    include "../include/session.php";

    $user_id = $_SESSION['user_id'];
    $filter = isset($_GET['status']) ? $_GET['status'] : 'all';

    if($filter == "Active"){
        $sql = "SELECT * FROM activity WHERE status = 'Active'";
    }else if($filter == "Deleted"){
        $sql = "SELECT * FROM activity WHERE status = 'Deleted'";
    }else{
        $sql = "SELECT * FROM activity ORDER BY status ASC";
    }
    
    $result = mysqli_query($dbConn, $sql);

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST['action'])){
            $adminID = $_SESSION['user_id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $pointAward = $_POST['point'];
            $start = $_POST['startDate'];
            $end = $_POST['endDate'];

            if($_POST['action']=="add"){
                $sql = "INSERT INTO activity (admin_id, title, description, points_awarded, status, start_date, end_date)
                        VALUES ($adminID, '$title', '$description', $pointAward, 'Active', '$start', '$end')";

                if(mysqli_query($dbConn,$sql)){
                    echo("<script>
                            alert('Successfully Added Activity!');
                            window.location.href = '../admin/adminActivityManagement.php';
                        </script>");
                }else{
                    echo("<script>
                            alert('Failed to Add Activity!');
                            window.location.href = '../admin/adminActivityManagement.php';
                        </script>");
                }
            }
            if($_POST['action']=="update"){
                $activity_id = $_POST['activity_id'];

                $sql = "UPDATE activity SET
                        admin_id = $adminID, 
                        title = '$title', 
                        description = '$description', 
                        points_awarded = $pointAward, 
                        start_date = '$start', 
                        end_date = '$end'
                        WHERE activity_id = $activity_id";

                if(mysqli_query($dbConn,$sql)){
                    echo("<script>
                            alert('Successfully Updated Activity');
                            window.location.href = '../admin/adminActivityManagement.php';
                        </script>");
                }else{
                    echo("<script>
                            alert('Failed to Add Activity!');
                            window.location.href = '../admin/adminActivityManagement.php';
                        </script>");
                }
            }
        }
        if(isset($_POST['delete_btn'])){
            $activity_id = $_POST['activity_id'];

            $sql = "UPDATE activity SET 
                    status = 'Deleted' 
                    WHERE activity_id = $activity_id";

            if(mysqli_query($dbConn,$sql)){
                echo "<script> 
                        alert('Successfully Deleted Activity! :(')
                        window.location.href = '../admin/adminActivityManagement.php?'
                    </script>";
            }else{
                echo("<script>
                        alert('Error!');
                        window.location.href = '../admin/adminUserManagement.php';
                    </script>"); 
            }
        }

        if(isset($_POST['restore_btn'])){
            $activity_id = $_POST['activity_id'];

            $sql = "UPDATE activity SET 
                    status = 'Active' 
                    WHERE activity_id = $activity_id";

            if(mysqli_query($dbConn,$sql)){
                echo "<script> 
                        alert('Successfully Restored Activity! :)')
                        window.location.href = '../admin/adminActivityManagement.php?'
                    </script>";
            }else{
                echo("<script>
                        alert('Error!');
                        window.location.href = '../admin/adminUserManagement.php';
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
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <title>Manage Activities</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <h2>Activity Management</h2>
    <div>
    <button id="addActivityBtn">Add Activity</button>
    <h1></h1>
    <form method="GET" id="filterForm">
        <label for="statusFilter">Filter:</label>
        <select name="status" id="statusFilter" class="dropdown">
                <option value="All" <?php if ($filter == "All") echo 'selected'; ?>>All</option>
                <option value="Active" <?php if ($filter == "Active") echo 'selected'; ?>>Active</option>
                <option value="Deleted" <?php if ($filter == "Deleted") echo 'selected'; ?>>Deleted</option>
        </select>
    </form>
    </div>
    <h3>Activities Table</h3>
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
                        <td data-label="Status"><?php echo $activity_info['status'];?></td>
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
                                <form action="Delete/Restore Button" method="POST">
                                    <input type="hidden" name="activity_id" value="<?php echo $activity_info['activity_id'];?>">
                                    <button type="submit" class="deleteBtn">Delete</button>
                                </form>
                            <?php else:;?>
                                <form action="" method="POST">
                                    <input type="hidden" name="activity_id" value="<?php echo $activity_info['activity_id'];?>">
                                    <button type="submit" class="restoreBtn">Restore</button>
                                </form>
                            <?php endif;?>
                        </td>
                    </tr>
                
                <?php endwhile;?>
            </tbody>
        </table>

        <div id="activityModal" class="modal">
            <div class="modalContent">
                <h2 id="activityModalTitle"></h2>
                <form action="" id="activityForm" method="POST">
                    <input type="hidden" id="action" name="action">
                    <input type="hidden" id="activityID" name="activity_id">
                    <label for="title">Title</label>
                    <input id="activityTitle" type="text" name="title" required>

                    <label for="description">Description</label>
                    <textarea id="activityDescription" name="description" rows="3"></textarea>
                    
                    <label for="pointAward">Points Awarded</label>
                    <input type="number" id="activityPoint" name="point"></input>

                    <label for="startDate">Start Date</label>
                    <input type="date" id="startDate" name="startDate">

                    <label for="startDate">End Date</label>
                    <input type="date" id="endDate" name="endDate">

                    <button type="submit" id="confirmBtn">Confirm</button>
                    <button type="button" class="cancelBtn" data-modal="activityModal">Cancel</button>
                </form>
            </div>
        </div>
    <?php endif;?>
    <script src="../../js/admin.js"></script>
</body>
</html>

