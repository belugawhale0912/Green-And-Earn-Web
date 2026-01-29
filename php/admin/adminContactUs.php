<?php
    include "../include/conn.php";
    include "../include/session.php";

    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM contact_us WHERE read = 0 ORDER BY contact_id ASC";
    $result = mysqli_query($dbConn, $sql);

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST['action'])){
            if($_POST['action'] == "markAsRead"){
                $contact_id = $_POST['contact_id'];

                $sql = "UPDATE contact_us SET read = 1 WHERE contact_id = $contact_id";

                if(mysqli_query($dbConn, $sql)){
                    $_SESSION['popupMessage']=[
                        'title' => 'Success',
                        'text' => 'Message Read',
                        'icon' => 'success'
                    ];
                    header("Location: ../admin/adminContactUs.php");
                    exit();
                }else{
                    $_SESSION['popupMessage']=[
                        'title' => 'Error',
                        'text' => 'Message Not Read',
                        'icon' => 'error'
                    ];
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
        <h3 class="tableTitle">List of Inquiries Via Contact Us</h3>
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
                            <th></th> <!-- mark as read -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($contact_info = mysqli_fetch_assoc($result)):?>
                            <tr>
                                <td data-label="Contact ID"><?php echo $contact_info['contact_id'];?></td>
                                <td data-label="First Name"><?php echo $contact_info['firstName'];?></td>
                                <td data-label="Last Name"><?php echo $contact_info['lastName'];?></td>
                                <td data-label="Email"><?php echo $contact_info['email'];?></td>
                                <td data-label="Phone Number"><?php echo $contact_info['poneNumber'];?></td>
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
            <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['popupMessage'])): ?>
        <script>
            Swal.fire({
                title: <?= json_encode($_SESSION['popupMessage']['title']) ?>,
                text: <?= json_encode($_SESSION['popupMessage']['text']) ?>,
                icon: <?= json_encode($_SESSION['popupMessage']['icon']) ?>,
                confirmButtonColor: '#3085d6'
            });
        </script>
    <?php unset($_SESSION['popupMessage']); endif; ?>

    <?php include "../include/showSWAL.php"?>
    <script src="../../js/admin.js"> </script>
</body>
</html>