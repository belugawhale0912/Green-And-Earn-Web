<?php
    include "../include/conn.php";
    include "../include/session.php";

    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM activity ORDER BY activity_id DESC";
    $result = mysqli_query($dbConn, $sql);
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
    <h1>Activity Management</h1>
    <button onclick="openAddActivityModal()">+ Add activity</button>
    <?php if(mysqli_num_rows($result)>0):?>
        <table>
            <thead>
                <tr id="header">
                    <th>Title</th>
                    <th>Description</th>
                    <th>AdminID</th>
                    <th>Point award</th>
                    <th>status</th>
                    <th>Start</th>
                    <th>End</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while($activity_info = mysqli_fetch_assoc($result)):?>
                    <tr>
                        <td><?php echo $activity_info['title'];?></td>
                        <td><?php echo $activity_info['description'];?></td>
                        <td><?php echo $activity_info['admin_id'];?></td>
                        <td><?php echo $activity_info['points_awarded'];?></td>
                        <td><?php echo $activity_info['status'];?></td>
                        <td><?php echo $activity_info['start_date'];?></td>
                        <td><?php echo $activity_info['end_date'];?></td>
                        <td>
                            <!-- Call js function to fill update form with existing data -->
                            <button onclick="openUpdateActivityModal(
                                             '<?php echo $activity_info['title'];?>',
                                             '<?php echo $activity_info['description'];?>',
                                             '<?php echo $activity_info['points_awarded'];?>',
                                             '<?php echo $activity_info['start_date'];?>',
                                             '<?php echo $activity_info['end_date'];?>',
                                             )">Update
                            </button>
                        </td>
                        <td>
                            <form action="../commands/admin_deleteActivity.php" method="POST">
                                <input type="hidden" name="activity_id" value="<?php echo $activity_info['activity_id'];?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                
                <?php endwhile;?>
            </tbody>
        </table>

        <div id="activityModal" class="modal">
            <div class="modalContent">
                <h2 id="modalTitle"></h2>
                <form id="activityForm" method="POST">
                    <input type="hidden" id="activityID" name="activityID">
                    <label for="title">Title</label>
                    <input id="activityTitle" type="text" name="title" required>

                    <label for="description">Description</label>
                    <textarea id="activityDescription" name="description" rows="3"></textarea>
                    
                    <label for="pointAward">Point award</label>
                    <input type="number" id="activityPoint" name="point"></input>

                    <label for="startDate">Start Date</label>
                    <input type="date" id="startDate" name="startDate">

                    <label for="startDate">End Date</label>
                    <input type="date" id="endDate" name="endDate">

                    <button type="submit" class="saveBtn">Confirm</button>
                    <button type="button" onclick="closeModal()">Cancel</button>
                </form>
            </div>
        </div>
    <?php endif;?>
    <script src="../../js/admin.js"></script>
</body>
</html>

