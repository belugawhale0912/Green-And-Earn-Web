<?php
    include "../include/conn.php";
    include "../include/session.php";

    $user_id = $_SESSION['user_id'];

    $filter = isset($_GET['status']) ? $_GET['status'] : 'all';
    if($filter == "Available"){
        $sql = "SELECT * FROM reward WHERE partner_id = ? AND status = 'Available'";
    }else if($filter == "Rejected"){
        $sql = "SELECT * FROM reward WHERE partner_id = ? AND status = 'Rejected'";
    }else if($filter == "Pending"){
        $sql = "SELECT * FROM reward WHERE partner_id = ? AND status = 'Pending'";
    }else if($filter == "Deactivated"){
        $sql = "SELECT * FROM reward WHERE partner_id = ? AND status = 'Deactivated'";
    }else{
        $sql = "SELECT * FROM reward WHERE partner_id = ? AND status != 'Deleted' ORDER BY reward_id ASC";
    }

    $stmt_fetch = mysqli_prepare($dbConn, $sql);
    mysqli_stmt_bind_param($stmt_fetch, "i", $user_id);
    mysqli_stmt_execute($stmt_fetch);
    $result = mysqli_stmt_get_result($stmt_fetch);

    // 1. ACCESS CONTROL
    if(!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 4){ // Assuming Partner ID is 2
       header("Location: ../pages/Landingpage.php");
       exit();
    }
    
    if($_SERVER["REQUEST_METHOD"] == "POST"){

        if(isset($_POST['action'])){
            
            if($_POST['action']=="add"){
                $title = $_POST['title'];
                $description = $_POST['description'];
                $required_points = $_POST['required_points'];
                $stock = $_POST['stock'];

                // if file ok execute the action
                if(isset($_FILES['reward_img'])&&$_FILES['reward_img']['error'] === UPLOAD_ERR_OK){

                    $fileName = $_FILES['reward_img']['name'];
                    $fileTempPath = $_FILES['reward_img']['tmp_name'];
                    $fileExt = pathinfo($fileName,PATHINFO_EXTENSION);
                    $supportedExt = ['jpg','jpeg','png','gif'];
                
                    if(in_array($fileExt,$supportedExt)){
                        $formattedFileName = $title.".".$fileExt;
                        $saveDir = "../../uploads/rewardImages/".$formattedFileName;

                        if(move_uploaded_file($fileTempPath, $saveDir)){
                            $sql = "INSERT INTO reward (partner_id, status, title, description, required_points, stock, reward_img)
                                    VALUES (?, 'pending', ?, ?, ?, ?, ?)";

                            $stmt = mysqli_prepare($dbConn, $sql);
                            mysqli_stmt_bind_param($stmt, "issiis", $user_id, $title, $description, $required_points, $stock, $saveDir);

                            if(mysqli_stmt_execute($stmt)){
                                $_SESSION['popupMessage']=[
                                    'title' => 'Success',
                                    'text' => 'Successfully Added Reward!',
                                    'icon' => 'success'
                                ];
                                header("Location: partnerRewardList.php");
                                exit();
                            }else{
                                $_SESSION['popupMessage']=[
                                    'title' => 'Failed',
                                    'text' => 'Failed to Add Reward!',
                                    'icon' => 'error'
                                ];
                                header("Location: partnerRewardList.php");
                                exit();
                            }
                        }
                    }
                }
            }

            if($_POST['action']=="update"){
                $reward_id = $_POST['rewardID'];
                $title = $_POST['title'];
                $description = $_POST['description'];
                $required_points = $_POST['required_points'];
                $stock = $_POST['stock'];

                // if file ok execute the action
                if(isset($_FILES['reward_img']) && $_FILES['reward_img']['error'] === UPLOAD_ERR_OK){

                    $fileName = $_FILES['reward_img']['name'];
                    $fileTempPath = $_FILES['reward_img']['tmp_name'];

                    $fileExt = pathinfo($fileName,PATHINFO_EXTENSION);
                    $supportedExt = ['jpg','jpeg','png','gif'];
                    
                    if(in_array($fileExt, $supportedExt)){
                        $formattedFileName = $title.".".$fileExt;
                        $saveDir = "../../uploads/rewardImages/".$formattedFileName;
                        
                        if(move_uploaded_file($fileTempPath, $saveDir)){

                        }
                    }
                }else{
                    $saveDir = $reward_img;
                }

                $sql = "UPDATE reward SET title = ?, description = ?, required_points = ?,
                        stock = ?, reward_img = ? WHERE reward_id = ?";

                $stmt = mysqli_prepare($dbConn, $sql);
                mysqli_stmt_bind_param($stmt, "ssissi", $title, $description, $required_points, $stock, $saveDir, $reward_id);

                
                if(mysqli_stmt_execute($stmt)){
                    $_SESSION['popupMessage']=[
                        'title' => 'Success',
                        'text' => 'Successfully Updated Reward!',
                        'icon' => 'success'
                    ];
                    header("Location: partnerRewardList.php");
                    exit();
                }else{
                    $_SESSION['popupMessage']=[
                        'title' => 'Failed',
                        'text' => 'Failed to Update Reward!',
                        'icon' => 'error'
                    ];
                    header("Location: partnerRewardList.php");
                    exit();
                }
                mysqli_stmt_close($stmt);
            }
        }
        if(isset($_POST['delete_btn'])){
            $reward_id = $_POST['reward_id'];

            $sql = "UPDATE reward SET status = 'Deleted' WHERE reward_id = ? AND partner_id = ?";
            $stmt = mysqli_prepare($dbConn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $reward_id, $user_id);

            if(mysqli_stmt_execute($stmt)){
                    $_SESSION['popupMessage']=[
                        'title' => 'Success',
                        'text' => 'Successfully Removed Reward!',
                        'icon' => 'success'
                    ];
                    header("Location: partnerRewardList.php");
                    exit();
            }else{
                echo"Error";
            }
            mysqli_stmt_close($stmt);
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
    <link rel="stylesheet" href="../../css/partner.css">
    <?php include "../include/libraries.php";?>
    <title>Partner Reward List</title>
</head>
<body>
    <script src="../../js/customSwal.js"></script>
    <?php
        include "../include/header.php";
    ?>
    <h2>List of Rewards</h2>
    <div class="topSection">
        <form method="GET" class="filterForm">
                <label for="statusFilter"></label>
                <select name="status" id="statusFilter" class="dropdown" onchange="this.form.submit()">
                    <option value="All" <?php if ($filter == "All") echo 'selected'; ?>>All</option>
                    <option value="Available" <?php if ($filter == "Available") echo 'selected'; ?>>Available</option>
                    <option value="Rejected" <?php if ($filter == "Rejected") echo 'selected'; ?>>Rejected</option>
                    <option value="Pending" <?php if ($filter == "Pending") echo 'selected'; ?>>Pending</option>
                    <option value="Deactivated" <?php if ($filter == "Deactivated") echo 'selected'; ?>>Deactivated</option>
                </select>
            </form>
        <button class="addRewardButton">Add Reward</button>
    </div>
    <?php if(mysqli_num_rows($result)>0):?>
        <table>
            <thead>
                <tr id="header">
                    <th>Reward Title</th>
                    <th>Description</th>
                    <th>Admin ID</th>
                    <th>Required Points</th>
                    <th>Status</th>
                    <th>Stock</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while($reward_info = mysqli_fetch_assoc($result)):?>
                    <tr>
                        <td data-label="Reward Title"><?php echo $reward_info['title']?></td>
                        <td data-label="Description"><?php echo $reward_info['description']?></td>
                        <td data-label="Admin ID"><?php echo $reward_info['admin_id']?></td>
                        <td data-label="Required Points"><?php echo $reward_info['required_points']?></td>
                        <td data-label="Status"><p class="rewardStatus <?php echo $reward_info['status']?>"><?php echo $reward_info['status']?></p></td>
                        <td data-label="Stock"><?php echo $reward_info['stock']?></td>
                        <td data-label="Update Reward">
                            <form action="">
                                <button class="editRewardBtn" 
                                    data-id="<?php echo $reward_info['reward_id']; ?>"
                                    data-title="<?php echo htmlspecialchars($reward_info['title'], ENT_QUOTES); ?>"
                                    data-desc="<?php echo htmlspecialchars($reward_info['description'], ENT_QUOTES); ?>"
                                    data-points="<?php echo $reward_info['required_points']; ?>"
                                    data-stock="<?php echo $reward_info['stock']; ?>"
                                >
                                    Update
                                </button>
                            </form>
                        </td>
                        <td data-label="Delete Reward">
                            <form action="" method="POST">
                                <input type="hidden" name="reward_id" value="<?php echo $reward_info['reward_id'];?>">
                                <button name="delete_btn"type="submit" class="deleteRewardBtn">Delete</button>
                            </form>
                        </td>
                    </tr>
                
                <?php endwhile;?>
            </tbody>
        </table>
        <!-- Responsive modal -->
        <div id="rewardModal" class="modal">
            <div class="modalContent">
                <h2 id="modalTitle"></h2>
                <form action="" id="rewardForm" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="action" name="action">
                    <input type="hidden" id="rewardID" name="rewardID">
                    <label for="title">Title</label>
                    <input id="rewardTitle" type="text" name="title" class="modal-input" required>

                    <label for="description">Description</label>
                    <textarea id="rewardDescription" name="description" class="modal-input" rows="3"></textarea>

                    <label for="required_points">Required Points</label>
                    <input id="requiredPoints" type="number" name="required_points" class="modal-input" required>

                    <label for="stock">Stock</label>
                    <input id="stock" type="number" name="stock" class="modal-input" required>

                    <label for="reward_img">Reward Image</label>
                    <input id="rewardImg" type="file" name="reward_img" class="modal-input" accept="image/*">
                    
                    <button type="submit" class="confirmBtn">Confirm</button>
                    <button type="button" id="cancelBtn">Cancel</button>
                </form>
            </div>
        </div>
    <?php endif;?>
    <script src="../../js/partner.js"></script>
    <?php include "../include/showSWAL.php"?>
</body>
</html>