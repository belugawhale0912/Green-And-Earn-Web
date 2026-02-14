<?php
    include "../include/conn.php";
    include "../include/session.php";

    // ensure the user is admin, if not redirect to landing page
    if($_SESSION['role_id'] != 1){
        header("Location: ../pages/Landingpage.php");
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM contact_us WHERE mark_as_read = 0 ORDER BY contact_id ASC";
    $result = mysqli_query($dbConn, $sql);

    //ensure form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST['action'])){
            //mark as read button in table
            if($_POST['action'] == "markAsRead"){
                
                $contact_id = $_POST['contact_id'];

                $sql = "UPDATE contact_us SET mark_as_read = 1 WHERE contact_id = ?";
                //prevent sql injection
                $stmt = mysqli_prepare($dbConn, $sql);
                mysqli_stmt_bind_param($stmt, "i", $contact_id);
                //sweet alert
                if(mysqli_stmt_execute($stmt)){
                    $_SESSION['popupMessage']=[
                        'title' => 'Success',
                        'text' => 'Message Read',
                        'icon' => 'success'
                    ];
                    mysqli_stmt_close($stmt);

                    header("Location: ../admin/adminContactUs.php");
                    exit();
                }else{
                    $_SESSION['popupMessage']=[
                        'title' => 'Error',
                        'text' => 'Message Not Read',
                        'icon' => 'error'
                    ];
                    mysqli_stmt_close($stmt);

                    header("Location: ../admin/adminContactUs.php");
                    exit();
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us Inbox</title>
    <link rel="icon" href="/Green-Earn-Web/favicon.ico">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/admin.css">
    <?php include "../include/libraries.php";?>
</head>
<body>
    <script src="../../js/customSwal.js"></script>
    <?php 
        include "../include/header.php"; 
    ?>
    <h2 class="pageTitle">Contact Us Inbox</h2>

    <div id="contactUsTable">
            <?php if(mysqli_num_rows($result)>0):?>
                <table>
                    <thead>
                        <tr id="header">
                            <th>Contact ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Message</th>
                            <th></th> <!-- mark as mark_as_read -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($contact_info = mysqli_fetch_assoc($result)):?>
                            <tr>
                                <td data-label="Contact ID"><?php echo $contact_info['contact_id'];?></td>
                                <td data-label="First Name"><?php echo $contact_info['firstName'];?></td>
                                <td data-label="Last Name"><?php echo $contact_info['lastName'];?></td>
                                <td data-label="Email"><?php echo $contact_info['email'];?></td>
                                <td data-label="Phone Number"><?php echo $contact_info['phoneNumber'];?></td>
                                <td data-label="Message"><?php echo $contact_info['message'];?></td>
                                <td data-label="Mark As Read">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="markAsRead">
                                        <input type="hidden" name="contact_id" value="<?php echo $contact_info['contact_id'];?>">
                                        <button type="submit" class="activateBtn">Mark As Read</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?> 
                    </tbody>
                </table>

            <?php else: ?>
                <h1>No messages at the current moment</h1>
            <?php endif; ?>
    </div>

    <?php include "../include/showSWAL.php"?>
    <script src="../../js/admin.js"> </script>
</body>
</html>
<?php mysqli_close($dbConn)?>