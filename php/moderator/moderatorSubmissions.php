<?php
    include "../include/conn.php"; // connect to the database
    include "../include/session.php"; // start session to access $_SESSION variables

    // to check the user's role
    if(!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3){
        header("Location: ../pages/Landingpage.php"); // Redirect non-moderators
        exit();
    }

    $moderator_id = $_SESSION['user_id']; // store the logged-in moderator's id

    // Pending verification (User Join to show username and activity name, along with all pending submissions.)
    $sql_submission = "SELECT s.*, u.username, a.title AS activity_title
                       FROM activity_submission s
                       JOIN user u ON s.user_id = u.user_id
                       JOIN activity a ON s.activity_id = a.activity_id
                       WHERE s.status = 'Pending'
                       ORDER BY s.submission_time ASC";

    $submissionResult = mysqli_query($dbConn,$sql_submission); // execute query

    // Moderator Approval Logic
    if ($_SERVER["REQUEST_METHOD"] == "POST") { // if moderator clicks a button

        $submission_id = $_POST['submission_id'];
        $decision = $_POST['moderatorDecision']; // Will be either 'approve' or 'reject'

        if ($decision == 'approve') {

            // get user id and points from the submission
            $sql_info = "SELECT s.user_id, a.points_awarded 
                         FROM activity_submission s 
                         JOIN activity a ON s.activity_id = a.activity_id 
                         WHERE s.submission_id = ?";

            $stmt = mysqli_prepare($dbConn,$sql_info); // prepare sql for execution
            mysqli_stmt_bind_param($stmt, "i", $submission_id); // to make sure the placeholder has value
            mysqli_stmt_execute($stmt); // executes the query
            $result = mysqli_stmt_get_result($stmt); // to retrieve result from the executed query
            $submission_info = mysqli_fetch_assoc($result); // convert result as an associative array
            mysqli_stmt_close($stmt); // close task
            
            // store user id and points for later updates
            if($submission_info){
                $userID = $submission_info['user_id'];
                $points = $submission_info['points_awarded'];
            }

            try{
                mysqli_begin_transaction($dbConn);

                // update submission status to approved
                $sql_update_status = "UPDATE activity_submission 
                                      SET status = 'Approved', moderator_id = ? 
                                      WHERE submission_id = ?";

                $stmt_update = mysqli_prepare($dbConn, $sql_update_status);
                mysqli_stmt_bind_param($stmt_update, "ii", $moderator_id, $submission_id);
                mysqli_stmt_execute($stmt_update);
                mysqli_stmt_close($stmt_update);

                // update user points
                $sql_add_points = "UPDATE user 
                                   SET point_balance = point_balance + ?, total_point_accumulated = total_point_accumulated + ? 
                                   WHERE user_id = ?";

                $stmt_points = mysqli_prepare($dbConn, $sql_add_points);
                mysqli_stmt_bind_param($stmt_points, "iii", $points, $points, $userID); 
                mysqli_stmt_execute($stmt_points);
                mysqli_stmt_close($stmt_points);

                // insert transaction record
                $sql_insert_transaction = "INSERT INTO point_transaction (user_id, submission_id, amount, type) 
                                        VALUES (?, ?, ?, 'Earned')";
                $stmt_transaction = mysqli_prepare($dbConn, $sql_insert_transaction);
                mysqli_stmt_bind_param($stmt_transaction, "iii", $userID, $submission_id, $points);
                mysqli_stmt_execute($stmt_transaction);
                mysqli_stmt_close($stmt_transaction);

                // commit changes to db after all query executed without error
                mysqli_commit($dbConn); 

                // include badge system to reward member badge
                include "../include/badge_system.php";
                checkTaskBadges($dbConn, $submission_info['user_id']); // check if the user qualifies for new task badges
                checkPointBadges($dbConn, $submission_info['user_id']); // check if the user qualifies for new point badges

                $_SESSION['popupMessage']=[
                    'title' => 'Success',
                    'text' => 'Points Awarded Successfully',
                    'icon' => 'success'
                ];

            } catch (Exception $e){
                // undo all the changes when any of the query fails
                mysqli_rollback($dbConn); 
                
                $_SESSION['popupMessage']=[
                    'title' => 'Error',
                    'text' => 'Error While Awarding Points',
                    'icon' => 'error'
                ];
            }
            header("Location: moderatorSubmissions.php");
            exit();

        }else if ($decision == 'reject') {
            // mark as rejected and no points are awarded
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
                mysqli_stmt_close($stmt);

                header("Location: moderatorSubmissions.php");
                exit();

            }
        }     
    } 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- ensure page fits properly on mobile -->
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/moderator.css">
    <?php include "../include/libraries.php";?> 
    <link rel="icon" href="../../favicon.ico"> 
    <title>Submission Viewing</title>
</head>
<body>
    <script src="../../js/customSwal.js"></script> <!-- to include sweet alerts -->
    <?php include "../include/header.php";?>
    <h1>Submissions</h1>
    <section id="submissions"> <!-- section for pending submissions table -->
        <?php if(mysqli_num_rows($submissionResult)>0):?> <!-- check if there is any pending submissions in the db -->
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
                    <?php while($submission_info = mysqli_fetch_assoc($submissionResult)):?> <!-- loops through all pending submissions -->
                        <tr>
                            <td data-label="Submission ID"><?php echo $submission_info['submission_id']?></td>
                            <td data-label="User"><?php echo $submission_info['username']?></td>
                            <td data-label="Activity"><?php echo $submission_info['activity_title']?></td>
                            <td data-label="Status"><p class="taskStatus <?php echo $submission_info['status']; ?>">
                                <?php echo $submission_info['status']?></p></td>
                            <td data-label="Submission Time"><?php echo $submission_info['submission_time']?></td>
                            <td data-label="View Button">
                                <!-- to open modal popup -->
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
            <h2>No Pending Submission At The Moment!</h2> <!-- display if no pending submissions -->
        <?php endif?>
    </section>

    <!-- Popup window -->
    <div id="submissionPopup" class="modal">
        <div class="modalContent" id=submissionModal>
            <button class="closeModalBtn" id="closeModalBtn"> <!-- to close the modal -->
                <svg width="30px" height="30px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 5L4.99998 19M5.00001 5L19 19" stroke="#1a5021" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/> <!-- for a close (X) icon -->
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
                <img id="popupProof" src="" alt="Proof Image"> <!-- container for the image -->
            </div>

            <form action="" method="POST"> <!-- to submit the moderator's decision -->
                    <input type="hidden" name="submission_id" id="submission_id"> <!-- to store the id of the submission -->
                    <!-- for the approve and reject buttons for the moderator's decision -->
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
<?php mysqli_close($dbConn)?>