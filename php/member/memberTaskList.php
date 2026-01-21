<?php
    include "../include/conn.php";
    include "../include/session.php";
    include "../include/libraries.php";

    $today = date("Y-m-d");

    // get activities that is not deleted and is started and not passed
    $sqlGet= "SELECT * FROM activity
              WHERE status != 'Deleted'
              AND '$today' BETWEEN start_date AND end_date";

    $resultGet = mysqli_query($dbConn, $sqlGet);

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $user_id = $_SESSION['user_id'];
        $activity_id = $_POST['activity_id'];

        //File processing
        //check file exists and no error
        if(isset($_FILES['proof_img']) && $_FILES['proof_img']['error'] === UPLOAD_ERR_OK){
            
            // get file temporary path on server
            $fileServerPath = $_FILES['proof_img']['tmp_name'];

            //format filename according and set file to .png
            $formattedFileName = "proof_userID{$user_id}_activityID{$activity_id}_".time().".png";

            $saveDir = "../../uploads/proofs/".$formattedFileName;

            // move file from server temporary storage to upload path
            if (move_uploaded_file($fileServerPath, $saveDir)){
                $sql = "INSERT INTO activity_submission (user_id, activity_id, proof, status) VALUES ('$user_id', '$activity_id', '$saveDir', 'Pending')";
                if(mysqli_query($dbConn, $sql)){
                    $taskMessage = true;
                    // todo bug: after submitting when click on refresh the page will submit again
                          
                } else{
                    echo "Error While Saving";
                }
            }else{
                //debug
                echo "Failed To Move File";
            }
        }else{
            //debug
            echo "No file received!";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/member.css">
    <title>TaskLists</title>
</head>
<body>    
    <?php
        include "../include/header.php";
    ?>
    <h1>Available Tasks</h1>
    <div class="taskContainer">
        <?php if(mysqli_num_rows($resultGet)>0):?>
            <?php while($activity_info = mysqli_fetch_assoc($resultGet)):?>
                <div class="taskCard">
                    <h2><?php echo $activity_info['title']?></h2>
                    <p>Points Reward: <?php echo $activity_info['points_awarded']?></p>
                    <p>Task Due Date: <?php echo $activity_info['end_date']?></p>
                    <button 
                        type="button"
                        class="openModalBtn" 
                        data-id="<?php echo $activity_info['activity_id']; ?>"
                        data-title="<?php echo htmlspecialchars($activity_info['title'], ENT_QUOTES); ?>"
                        data-desc="<?php echo htmlspecialchars($activity_info['description'], ENT_QUOTES); ?>"
                        data-points="<?php echo $activity_info['points_awarded']; ?>"
                    >
                        Submit
                    </button>
                </div>
            <?php endwhile?>
        <?php else:?>
            <?php echo("<h2>No tasks available right now!</h2>");?>
        <?php endif?>
    </div>

    <div class="modal">
        <div class="modalContent" id="submissionModal">
            <div id="submissionWindow">
                <h1 id="activityTitle"></h1>
                <p id="activityDescription"></p>
                <h2 id="activityPoints"></h2>
                <!-- TODO read about enctype -->
                <form action="" method="POST" enctype="multipart/form-data">
                    
                    <input type="hidden" name="activity_id" id="activity_id" value="">

                    <label>Upload Proof (Image):</label>
                    <input type="file" name="proof_img" required accept="image/*">

                    <button type="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <?php if(isset($taskMessage)&&$taskMessage):?>
        <script>
            Swal.fire({
                title: 'Success!',
                text: 'The task is submitted!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif;?>

    <script src="../../js/member.js"></script>
</body>
</html>