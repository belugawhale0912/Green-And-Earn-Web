<?php
    include "../include/conn.php";
    include "../include/session.php";

    if($_SESSION['role_id']!= 3){
        header("Location: ../pages/Landingpage.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Retrive moderator profile
    $sql_profile = "SELECT * FROM user WHERE user_id = $user_id";
    $profileResult = mysqli_query($dbConn,$sql_profile);
    $user_info = mysqli_fetch_assoc($profileResult); 

    // Pending verification (User Join to show username and activity name)
    $sql_submission = "SELECT s.*, u.username, a.title AS activity_title
                       FROM activity_submission s
                       JOIN user u ON s.user_id = u.user_id
                       JOIN activity a ON s.activity_id = a.activity_id
                       WHERE s.status = 'Pending'";

    $submissionResult = mysqli_query($dbConn,$sql_submission);
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
    <section id="profileSection">
        <div class="container">
            <h1>Profile</h1>
            <img src="<?php echo $user_info['profile_photo']?>" alt="profileImg" class="profileImg">
            <div id="profileInfo">
                <h2>Username: <?php echo $user_info['username']?></h2>
                <h2>Email: <?php echo $user_info['email']?></h2>
            </div>

        </div>
    </section>
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

            <form action="../commands/moderator_process_submission.php" method="POST">
                    <input type="hidden" name="submission_id" id="submission_id">
                    <button type="submit" name="moderatorDecision" value="approve">Approve</button>
                    <button type="submit" name="moderatorDecision" value="reject">Reject</button>
            </form>
        </div>
    </div>
    <script src="../../js/moderator.js"></script>
</body>
</html>