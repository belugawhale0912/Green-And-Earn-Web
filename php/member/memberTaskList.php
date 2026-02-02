<?php
    include "../include/conn.php";
    include "../include/session.php";

    $user_id = $_SESSION['user_id'];
    $today = date("Y-m-d");

    // get activities that is not deleted and is started and not passed
    $view = isset($_GET['view'])? $_GET['view']:'available';
    if($view=='available'){
        // Logic:
        // 1. Must be Active
        // 2. Must be within Date Range
        // 3. Must NOT be in the list of tasks the user has already done/submitted
        
        $sqlGet = "SELECT * FROM activity 
                   WHERE status = 'Active' 
                   AND '$today' BETWEEN start_date AND end_date
                   AND activity_id NOT IN (
                       SELECT activity_id 
                       FROM activity_submission 
                       WHERE user_id = ? 
                       AND (status = 'Pending' OR status = 'Approved')
                   )";
        $stmtGet = mysqli_prepare($dbConn, $sqlGet);
        mysqli_stmt_bind_param($stmtGet, "i", $user_id);
        mysqli_stmt_execute($stmtGet);
        $resultGet = mysqli_stmt_get_result($stmtGet);
    }
    
    elseif($view == 'submitted'){
        $sqlGet= "SELECT s.status, s.submission_time, a.title, a.points_awarded, s.proof
                  FROM activity_submission s
                  JOIN activity a on s.activity_id = a.activity_id
                  WHERE s.user_id = ?";

        $stmtGet = mysqli_prepare($dbConn, $sqlGet);
        mysqli_stmt_bind_param($stmtGet, "i", $user_id);
        mysqli_stmt_execute($stmtGet);
        $resultGet = mysqli_stmt_get_result($stmtGet);

    }


    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $user_id = $_SESSION['user_id'];
        $activity_id = $_POST['activity_id'];

        //File processing
        //check file exists and no error
        if(isset($_FILES['proof_img']) && $_FILES['proof_img']['error'] === UPLOAD_ERR_OK){
            
            // get file temporary path on server
            $fileName = $_FILES['proof_img']['name'];
            $fileServerPath = $_FILES['proof_img']['tmp_name'];
            
            $fileExtension = pathinfo($fileName,PATHINFO_EXTENSION);

            $supportedExt = ['jpg','jpeg','png','gif'];
            
            if(in_array($fileExtension,$supportedExt)){
                //format filename according and set file to .png
                // TODO change unix time to general time
                $formattedFileName = "proof_userID{$user_id}_activityID{$activity_id}_" . time() .".". $fileExtension;

                $saveDir = "../../uploads/proofs/".$formattedFileName;

                // move file from server temporary storage to upload path
                if (move_uploaded_file($fileServerPath, $saveDir)){

                    $sqlSubmit = "INSERT INTO activity_submission (user_id, activity_id, proof, status) 
                            VALUES (?, ?, ?, 'Pending')";
                            
                    $stmtSubmit = mysqli_prepare($dbConn, $sqlSubmit);
                    mysqli_stmt_bind_param($stmtSubmit, "iis", $user_id, $activity_id, $saveDir);
                    if(mysqli_stmt_execute($stmtSubmit)){
                        $_SESSION['popupMessage']=[
                            'title' => 'Success!',
                            'text' => 'Successfully Submitted Task!',
                            'icon' => 'success'
                        ];
                        header("Location: memberTaskList.php?view=available");
                        exit();
                    } else{
                        echo "Error While Saving";
                    }
                }else{
                    //debug
                    echo "Failed To Move File";
                }
            }else{
                $_SESSION['popupMessage']=[
                    'title' => 'Error!',
                    'text' => "Unsupported File Format! Supported formats('jpg','jpeg','png','gif')",
                    'icon' => 'error'
                ];
                header("Location: memberTaskList.php?view=available");
                exit();
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
    <link rel="icon" href="/GreenAndEarnWeb/favicon.ico">
    <?php include "../include/libraries.php";?>
    <title>TaskLists</title>
</head>
<body>
    <script src="../../js/customSwal.js"></script>    
    <?php
        include "../include/header.php";
    ?>
    <div class="viewToggle">
        <a href="?view=available" class="<?php echo ($view == 'available')? 'active':'' ?>">Available</a>
        <a href="?view=submitted" class="<?php echo ($view == 'submitted')? 'active':'' ?>">Submitted</a>
    </div>

    <?php if($view=='available'):?>
        <h1>Available Tasks</h1>

        <div class="taskContainer">
            <?php if(mysqli_num_rows($resultGet)>0):?>
                <?php while($activity_info = mysqli_fetch_assoc($resultGet)):?>
                    <div class="card taskCard">
                        <h2><?php echo $activity_info['title']?></h2>
                        <p>Points Reward: <?php echo $activity_info['points_awarded'];?></p>
                        <p>Task Due Date: <?php echo $activity_info['end_date'];?></p>
                        <button 
                            type="button"
                            class="openSubmissionModalBtn" 
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

        <div class="modal" id="submitTask">
            <div class="modalContent" id="submissionModal">
                <button class="closeModalBtn" id="closeModalBtn">
                    <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 5L4.99998 19M5.00001 5L19 19" stroke="#1a5021" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <div id="submissionWindow">
                    <h1 id="activityTitle"></h1>
                    <p id="activityDescription"></p>
                    <h2 id="activityPoints"></h2>
                    <!-- TODO read about enctype -->
                    <form action="" method="POST" enctype="multipart/form-data">
                        
                        <input type="hidden" name="activity_id" id="activity_id" value="">

                        <label>Upload Proof (Image):</label>
                        <input type="file" name="proof_img" required accept="image/*">

                        <button id="submitProofBtn" type="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>


    <?php elseif($view=='submitted'):?>
        <h1>Submitted Tasks</h1>

        <div class="taskContainer">
            <?php if(mysqli_num_rows($resultGet)>0):?>
                <select class="filter" id="submissionFilter">
                    <option value="All">All</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Rejected">Reject</option>
                </select>
                <!-- TODO add show reason of reject logic -->
                <?php while($submission_info = mysqli_fetch_assoc($resultGet)):?>
                    <div class="card taskCard" data-status="<?php echo $submission_info['status'];?>">
                        <h2><?php echo $submission_info['title']?></h2>
                        <p>Submitted on: <?php echo $submission_info['submission_time'];?></p>
                        <p>Points Reward: <?php echo $submission_info['points_awarded'];?></p>

                        <div class="tstatusContainer">
                            <p>Status:</p>
                            <p class="taskStatus <?php echo $submission_info['status']?>"><?php echo $submission_info['status']?></p>
                        </div>
                        <button 
                            class="viewSubmissionBtn"
                            data-img="<?php echo $submission_info['proof']?>">
                            View Submitted Proof
                        </button>
                    </div>
                <?php endwhile?>
            <?php else:?>
                <?php echo("<h2>No submmited task!</h2>");?>
            <?php endif?>
        </div>

        <div class="modal" id="viewProof">
            <div class="modalContent" id="vSubmissionModal">
                <button class="closeModalBtn" id="closeModalBtn">
                    <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 5L4.99998 19M5.00001 5L19 19" stroke="#1a5021" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
                <h1>Submmited Proof:</h1>
                <img id="proofImg" src="" alt="submittedProof">
            </div>
        </div>
    <?php endif?>

    <?php include "../include/showSWAL.php"?>
    <script src="../../js/member.js"></script>
</body>
</html>