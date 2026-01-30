<?php
    include "../include/conn.php";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $firstName = $_POST["firstName"];
        $lastName = $_POST["lastName"];
        $email = $_POST["email"];
        $phoneNumber = $_POST["phoneNumber"];
        $message = $_POST["message"];

        $sql = "INSERT INTO contact_us(firstName, lastName, email, phoneNumber, message) VALUES ('$firstName', '$lastName', '$email', '$phoneNumber', '$message')";
        mysqli_query($dbConn, $sql);

        if(mysqli_query($dbConn, $sql)){
            $_SESSION['popupMessage']=[
                'title' => 'Success',
                'text' => 'Message Successfully Delivered',
                'icon' => 'success'
            ];
            header("Location: contactUs.php");
            exit();
        }else{
            $_SESSION['popupMessage']=[
                'title' => 'Error',
                'text' => 'Failed to Add User',
                'icon' => 'error'
            ];
            header("Location: contactUs.php");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/contactUs.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="icon" href="/GreenAndEarnWeb/favicon.ico">
    <?php include "../include/libraries.php"?>
</head>

<body>
    <script src="../../js/customSwal.js"></script>
    <?php include "../include/header.php"; ?>

    <h2>Contact Our Team</h2>
    <p class="description">Got any questions or inquiries about our platform? We're here to help. Chat with us now!</p>
    
    <div class="big_container">
        <div class="form_container">
            <form action="" method="POST">
                <label for="First Name">First Name</label>
                <input type="text" name="firstName" id="firstName" placeholder="First Name" required>

                <label for="Last Name">Last Name</label>
                <input type="text" name="lastName" id="lastName" placeholder="Last Name" required>

                <label for="Email">Email Address</label>
                <input type="text" name="email" id="email" placeholder="you@email.com" required>

                <label for="Phone Number">Phone Number</label>
                <input type="text" name="phoneNumber" id="phoneNumber" placeholder="+(60)00-000-0000" required>

                <label for="Message">Message</label>
                <textarea name="message" id="message" placeholder="Leave us a message..." required></textarea>

                <button type="submit" id="submitBtn">Submit Message</button>
            </form>
        </div>
        <div class="contacts_container">
            <h3 class="infoTitle">Chat With Us</h3>
            <p>Speak to our team directly.</p>
            <h4>Tan Jing Yu</h4>
            <p>Email: leon0912tan@gmail.com</p>
            <h4>Jeremiah Lim Chen Kai</h4>
            <p>Email: jeremiahlck1116@gmail.com</p>
            <h4>Pang Min Dao-Nicholas</h4>
            <p>Email: minecraftet28@gmail.com</p>
            <h4>Chan Xi Zhe</h4>
            <p>Email: chanxizhe67@gmail.com</p>
            <h3></h3>
            <h3 class="infoTitle">Visit Us</h3>
            <p>Chat to us in person.</p>
            <p class="address">Parkhill Residence</p>
        </div>
    </div>
    <?php include "../include/showSWAL.php";?>
</body>
</html>