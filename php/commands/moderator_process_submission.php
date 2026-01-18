<?php
    include "../include/conn.php";
    include "../include/session.php";

    $moderator_id = $_SESSION['user_id'];

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
                        window.location.href = '../pages/moderatorDashboard.php';
                    </script>";
                exit(); // Stops PHP here so the browser has time to show the alert

            }
            catch(Exception $e){
                //if any of the query fail, undo the changes and send error message
                $dbConn->rollback();
                echo "Error:". $e->getMessage();
                echo "<script>
                        alert('Error While Processing:'".$e->getMessage().");
                        window.location.href = '../pages/moderatorDashboard.php';
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
        }
    }

    // 2. Redirect back to dashboard
    header("Location: ../pages/moderatorDashboard.php");
    exit();
?>