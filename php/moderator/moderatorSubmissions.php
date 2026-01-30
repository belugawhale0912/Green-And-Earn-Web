<?php
    include "../include/conn.php";
    include "../include/session.php";

    if(!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3){
        header("Location: ../pages/Landingpage.php"); // Redirect non-moderators
        exit();
    }

    $moderator_id = $_SESSION['user_id'];

    // Pending verification (User Join to show username and activity name)
    $sql_submission = "SELECT s.*, u.username, a.title AS activity_title
                       FROM activity_submission s
                       JOIN user u ON s.user_id = u.user_id
                       JOIN activity a ON s.activity_id = a.activity_id
                       WHERE s.status = 'Pending'
                       ORDER BY s.submission_time ASC";

    $submissionResult = mysqli_query($dbConn,$sql_submission);

    // Moderator Approval Logic
    if ($_SERVER["REQUEST_METHOD"] == "POST") { 

        $submission_id = $_POST['submission_id'];
        $decision = $_POST['moderatorDecision']; // Will be 'approve' or 'reject'

        if ($decision == 'approve') {

            $sql_info = "SELECT s.user_id, a.points_awarded 
                         FROM activity_submission s 
                         JOIN activity a ON s.activity_id = a.activity_id 
                         WHERE s.submission_id = ?";

            $stmt = mysqli_prepare($dbConn,$sql_info);
            mysqli_stmt_bind_param($stmt, "i", $submission_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $submission_info = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            
            if($submission_info){
                $userID = $submission_info['user_id'];
                $points = $submission_info['points_awarded'];
            }

            mysqli_begin_transaction($dbConn);
            $success = true;
            $err = "";

            $sql_update_status = "UPDATE activity_submission 
                                  SET status = 'Approved', 
                                                moderator_id = ? 
                                  WHERE submission_id = ?";

            $stmt_update = mysqli_prepare($dbConn, $sql_update_status);
            mysqli_stmt_bind_param($stmt_update, "ii", $moderator_id, $submission_id);
            if(!mysqli_stmt_execute($stmt_update)){
                $success = false;
                $err = "Status Update Failed";
            }
            mysqli_stmt_close($stmt_update);

            if($success){
                $sql_add_points = "UPDATE user 
                                SET point_balance = point_balance + ?, total_point_accumulated = total_point_accumulated + ? 
                                WHERE user_id = ?";

                $stmt_points = mysqli_prepare($dbConn, $sql_add_points);
                mysqli_stmt_bind_param($stmt_points, "iii", $points, $points, $userID);
                if(!mysqli_stmt_execute($stmt_points)){
                    $success = false;
                    $err = "Update User Point Failed";
                }
                mysqli_stmt_close($stmt_points);
            }

            if($success){
                $sql_insert_transaction = "INSERT INTO point_transaction (user_id, submission_id, amount, type) 
                                        VALUES (?, ?, ?, 'Earned')";
                $stmt_transaction = mysqli_prepare($dbConn, $sql_insert_transaction);
                mysqli_stmt_bind_param($stmt_transaction, "iii", $userID, $submission_id, $points);
                if(!mysqli_stmt_execute($stmt_transaction)) {
                    $success = false;
                    $err = "Transaction log failed";
                }
                mysqli_stmt_close($stmt_transaction);
            }

            if($success){
                mysqli_commit($dbConn);
                include "../include/badge_system.php";
                checkTaskBadges($dbConn, $submission_info['user_id']);
                checkPointBadges($dbConn, $submission_info['user_id']);
                $_SESSION['popupMessage']=[
                    'title' => 'Success',
                    'text' => 'Points Awarded Successfully',
                    'icon' => 'success'
                ];
                header("Location: moderatorSubmissions.php");
                exit();

            }else{
                mysqli_rollback($dbConn);
                $_SESSION['popupMessage']=[
                    'title' => 'Error',
                    'text' => 'Error While Awarding Points',
                    'icon' => 'error'
                ];
                header("Location: moderatorSubmissions.php");
                exit();
            }

        }else if ($decision == 'reject') {
            // mark as Rejected (No points added)
            $sql_reject = "UPDATE activity_submission 
                           SET status = 'Rejected',
                               moderator_id = ?
                           WHERE submission_id = ?";

            $stmt = mysqli_prepare($dbConn, $sql_reject);
            mysqli_stmt_bind_param($stmt, "ii", $moderator_id, $submission_id);

            if(mysqli_stmt_execute($stmt)){
                $_SESSION['popupMessage']=[
                    'title' => 'Success',
                    'text' => 'Points Awarding Rejected',
                    'icon' => 'success'
                ];
                header("Location: moderatorSubmissions.php");
                exit();
                mysqli_stmt_close($stmt);
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
    <link rel="stylesheet" href="../../css/moderator.css">
    <?php include "../include/libraries.php";?>  
    <title>Submission Viewing</title>
</head>
<body>
    <script src="../../js/customSwal.js"></script>
    <?php include "../include/header.php";?>
    <h1>Submissions</h1>
    <section id="submissions">
        <?php if(mysqli_num_rows($submissionResult)>0):?>
            <table>
                <thead>
                    <tr>
                        <th>Submission ID</th>
                        <th>User</th>
                        <th>Activity</th>
                        <th>Status</th>
                        <th>Submission Time</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($submission_info = mysqli_fetch_assoc($submissionResult)):?>
                        <tr>
                            <td data-label="Submission ID"><?php echo $submission_info['submission_id']?></td>
                            <td data-label="User"><?php echo $submission_info['username']?></td>
                            <td data-label="Activity"><?php echo $submission_info['activity_title']?></td>
                            <td data-label="Status"><p class="taskStatus <?php echo $submission_info['status']; ?>">
                                <?php echo $submission_info['status']?></p></td>
                            <td data-label="Submission Time"><?php echo $submission_info['submission_time']?></td>
                            <td data-label="View Button">
                                <button class="viewSubmissionBtn"
                                    data-id="<?php echo $submission_info['submission_id'];?>"
                                    data-username="<?php echo htmlspecialchars($submission_info['username'], ENT_QUOTES);?>"
                                    data-activity="<?php echo htmlspecialchars($submission_info['activity_title'], ENT_QUOTES);?>"
                                    data-time="<?php echo $submission_info['submission_time'];?>"
                                    data-proof="<?php echo $submission_info['proof'];?>"
                                >
                                    View
                                </button>
                            </td>
                        </tr>
                    <?php endwhile?>
                </tbody>
            </table>
        <?php else:?>
            <h2>No Pending Submission At The Moment!</h2>
        <?php endif?>
    </section>

    <!-- Popup window -->
    <div id="submissionPopup" class="modal">
        <div class="modalContent" id=submissionModal>
            <button class="closeModalBtn" id="closeModalBtn">
                <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 5L4.99998 19M5.00001 5L19 19" stroke="#1a5021" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <h1>Review Submission</h1>
            <div id="submissionInfo">
                <p>Username: <span id="popupUsername"></span></p>
                <p>Activity: <span id="popupActivity"></span></p>
                <p>Submitted on: <span id="popupSubmissionTime"></span></p>
                <p>Proof Image:</p>
            </div>

            <div id="submissionUpload">
                <img id="popupProof" src="" alt="Proof Image">
            </div>

            <form action="" method="POST">
                    <input type="hidden" name="submission_id" id="submission_id">
                    <div class="buttonGroup">
                        <button type="submit" name="moderatorDecision" value="approve">Approve</button>
                        <button type="submit" name="moderatorDecision" value="reject">Reject</button>
                    </div>
            </form>
        </div>
    </div>
    <?php include "../include/showSWAL.php"?>
    <script src="../../js/moderator.js"></script>
</body>
</html>