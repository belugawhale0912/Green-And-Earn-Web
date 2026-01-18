<?php
    include "../include/conn.php";
    include "../include/session.php";

    $moderator_id = $_SESSION['user_id'];

    // Pending verification (User Join to show username and activity name)
    $sql_submission = "SELECT s.*, u.username, a.title AS activity_title
                       FROM activity_submission s
                       JOIN user u ON s.user_id = u.user_id
                       JOIN activity a ON s.activity_id = a.activity_id
                       WHERE s.status = 'Pending'";

    $submissionResult = mysqli_query($dbConn,$sql_submission);

    
    // Moderator Approval Logic
    if ($_SERVER["REQUEST_METHOD"] == "POST") {        
        $submission_id = $_POST['submission_id'];
        $decision = $_POST['moderatorDecision']; // Will be 'approve' or 'reject'

        if ($decision == 'approve') {
            $sql_info = "SELECT s.user_id, a.points_awarded 
                         FROM activity_submission s 
                         JOIN activity a ON s.activity_id = a.activity_id 
                         WHERE s.submission_id = '$submission_id'";
            
            $result = mysqli_query($dbConn, $sql_info);
            $submission_info = mysqli_fetch_assoc($result);
            
            

            // Update submission status
            $sql_update_status = "UPDATE activity_submission 
                                  SET status = 'Approved', 
                                                moderator_id = '$moderator_id' 
                                  WHERE submission_id = '$submission_id'";

            // Update user point balance
            $sql_add_points = "UPDATE user SET point_balance = point_balance + {$submission_info['points_awarded']} WHERE user_id = '{$submission_info['user_id']}'";

            $dbConn->begin_transaction();

            try{
                if (!mysqli_query($dbConn, $sql_update_status)) { throw new Exception("Status Update failed"); }
                if (!mysqli_query($dbConn, $sql_add_points)) { throw new Exception("Point award failed"); }
                $sql_insert_transaction= "INSERT INTO point_transaction (user_id, submission_id, amount, type) VALUES ({$submission_info['user_id']}, $submission_id, {$submission_info['points_awarded']},'Earned')";
                if (!mysqli_query($dbConn, $sql_insert_transaction)) { throw new Exception("Insert Transaction failed"); }        
                //if all query ran without error, save the changes
                $dbConn->commit();
                // TODO JavaScript Feedback (TEMP)
                echo "<script>
                        alert('Success! Points awarded.');
                        window.location.href = '../moderator/moderatorSubmissions.php';
                    </script>";
                exit(); // Stops PHP here so the browser has time to show the alert

            }
            catch(Exception $e){
                //if any of the query fail, undo the changes and send error message
                $dbConn->rollback();
                echo "Error:". $e->getMessage();
                echo "<script>
                        alert('Error While Processing:'".$e->getMessage().");
                        window.location.href = '../moderator/moderatorSubmissions.php';
                    </script>";
                exit(); // Stops PHP here so the browser has time to show the alert
            }

        } else if ($decision == 'reject') {
            // mark as Rejected (No points added)
            $sql_reject = "UPDATE activity_submission 
                           SET status = 'Rejected',
                               moderator_id ='$moderator_id'
                           WHERE submission_id = '$submission_id'";

            mysqli_query($dbConn, $sql_reject);
            if(mysqli_query($dbConn, $sql_reject)){
                echo "<script>
                        alert('Submission Rejected.');
                        window.location.href = '../moderator/moderatorSubmissions.php';
                      </script>";
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
    <link rel="stylesheet" href="../../css/moderator.css">
    <title>Document</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <h1>Submissions</h1>
    <section id="submissions">
        <table>
            <thead>
                <tr>
                    <td>SubmissionID</td>
                    <td>User</td>
                    <td>Activity</td>
                    <td>Status</td>
                    <td>Submission Time</td>
                    <td></td>
                </tr>
            </thead>
            <tbody>
                <?php while($submission_info = mysqli_fetch_assoc($submissionResult)):?>
                    <tr>
                        <td><?php echo $submission_info['submission_id']?></td>
                        <td><?php echo $submission_info['username']?></td>
                        <td><?php echo $submission_info['activity_title']?></td>
                        <td><?php echo $submission_info['status']?></td>
                        <td><?php echo $submission_info['submission_time']?></td>
                        <td>
                            <button onclick="openSubmissionModal(
                                '<?php echo $submission_info['submission_id']; ?>',
                                '<?php echo htmlspecialchars($submission_info['username'], ENT_QUOTES); ?>',
                                '<?php echo htmlspecialchars($submission_info['activity_title'], ENT_QUOTES); ?>',
                                '<?php echo $submission_info['submission_time']; ?>',
                                '<?php echo $submission_info['proof']; ?>'
                            )">
                                View
                            </button>
                        </td>
                        <!-- Map View Overlay -->
                    </tr>
                <?php endwhile?>
            </tbody>
        </table>
    </section>

    <!-- Popup window -->
    <div id="submissionPopup" class="modal">
        <div class="modalContent">
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
                    <button type="submit" name="moderatorDecision" value="approve">Approve</button>
                    <button type="submit" name="moderatorDecision" value="reject">Reject</button>
            </form>
        </div>
    </div>
    <script src="../../js/moderator.js"></script>
</body>
</html>