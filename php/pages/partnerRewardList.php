<?php
    include "../include/conn.php";
    include "../include/session.php";

    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM reward WHERE partner_id = $user_id AND status != 'deleted'";

    $result = mysqli_query($dbConn,$sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/layout.css">
    <title>Document</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <button onclick="openAddRewardModal()">+ Add Reward</button>
    <?php if(mysqli_num_rows($result)>0):?>
        <table>
            <thead>
                <tr id="header">
                    <th>Reward title</th>
                    <th>Description</th>
                    <th>AdminID</th>
                    <th>Required_points</th>
                    <th>Status</th>
                    <th>Stock</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while($reward_info = mysqli_fetch_assoc($result)):?>
                    <tr>
                        <td><?php echo $reward_info['title']?></td>
                        <td><?php echo $reward_info['description']?></td>
                        <td><?php echo $reward_info['admin_id']?></td>
                        <td><?php echo $reward_info['required_points']?></td>
                        <td><?php echo $reward_info['status']?></td>
                        <td><?php echo $reward_info['stock']?></td>
                        <td>
                            <!-- Call js function to fill update form with existing data -->
                            <button onclick="openUpdateRewardModal(
                                '<?php echo $reward_info['reward_id'] ?>',
                                '<?php echo htmlspecialchars($reward_info['title'], ENT_QUOTES); ?>', 
                                '<?php echo htmlspecialchars($reward_info['description'], ENT_QUOTES); ?>', 
                                <?php echo $reward_info['required_points']; ?>, 
                                <?php echo $reward_info['stock']; ?>)">
                                Update
                            </button>
                        </td>
                        <td>
                            <form action="../commands/partner_deleteReward.php" method="POST">
                                <input type="hidden" name="reward_id" value="<?php echo $reward_info['reward_id'];?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                
                <?php endwhile;?>
            </tbody>
        </table>
        <!-- Responsive modal -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <h2 id="modalTitle"></h2>
                <form id="rewardForm" method="POST">
                    <input type="hidden" id="rewardID" name="rewardID">
                    <label for="title">Title</label>
                    <input id="rewardTitle" type="text" name="title" class="modal-input" required>

                    <label for="description">Description</label>
                    <textarea id="rewardDescription" name="description" class="modal-input" rows="3"></textarea>

                    <label for="required_points">Required Points</label>
                    <input id="requiredPoints" type="number" name="required_points" class="modal-input">

                    <label for="stock">Stock</label>
                    <input id="stock" type="number" name="stock" class="modal-input">

                    <button type="submit" class="saveBtn">Confirm</button>
                    <button type="button" onclick="closeModal()">Cancel</button>
                </form>
            </div>
        </div>
    <?php endif;?>
    <script src="../../js/partner.js"></script>

</body>
</html>

