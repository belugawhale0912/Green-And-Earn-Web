<?php
    include "../include/conn.php";
    include "../include/session.php";


    // check if the current user is partner, redirect to landing page if not partner
    if($_SESSION['role_id'] != 4){
        header("Location: ../pages/Landingpage.php");
        exit();
    }

    $partnerName = $_SESSION['username'];
    $partner_id = $_SESSION['user_id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Retrieve values
        $username = $_POST['username'];
        $amt_spent = $_POST['amt_spent'];

        if($amt_spent <= 0){ //Ensure amount spent input is more than 0
            $_SESSION['popupMessage']=[
                'title' => 'Failed',
                'text' => 'Amount spent must be more than 0!',
                'icon' => 'error'
            ];
            header("Location: partnerPointAward.php");
            exit();
        }

        
        $checkSql = "SELECT * FROM user WHERE username = ?";
        $stmt = mysqli_prepare($dbConn, $checkSql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if($member_info = mysqli_fetch_assoc($result)){
            $member_id = $member_info['user_id']; 
        }else{
            $_SESSION['popupMessage']=[
                'title' => 'Error',
                'text' => 'User Not Found!',
                'icon' => 'error'
            ];
            header("Location: partnerPointAward.php");
            exit();
        }
        mysqli_stmt_close($stmt);

        //check file exists and no error
        if(isset($_FILES['receipt_img'])&&$_FILES['receipt_img']['error'] === UPLOAD_ERR_OK){

            // retrive uploaded file name
            $fileName = $_FILES['receipt_img']['name'];

            // retrive temp path on server
            $fileTempPath = $_FILES['receipt_img']['tmp_name'];

            // retrive extention of the uploaded file
            $fileExt = pathinfo($fileName,PATHINFO_EXTENSION);

            $supportedExt = ['jpg','jpeg','png','gif'];

            // if the uploaded file is supported
            if(in_array($fileExt,$supportedExt)){
                $formattedFileName = "member_".$username."partner_"."$partnerName". date("dmY_His") .".".$fileExt;

                $saveDir = "../../uploads/receipt/".$formattedFileName;

                if(move_uploaded_file($fileTempPath, $saveDir)){

                    //calculations
                    $points_award = round($amt_spent, 1) * 10; //RM 1 = 10 Points

                    //insertQuery
                    $sql_insert_partnerTransaction = "INSERT INTO partner_point_transaction (user_id, partner_id, amount_spent, points_awarded, receipt_img, status) 
                                                      VALUES(?, ?, ?, ?, ?,'Pending') ";

                    $stmtInsert = mysqli_prepare($dbConn, $sql_insert_partnerTransaction);
                    mysqli_stmt_bind_param($stmtInsert,"iidss", $member_id, $partner_id, $amt_spent, $points_award, $saveDir);

                    if(mysqli_stmt_execute($stmtInsert)){
                        mysqli_stmt_close($stmtInsert);
                        $_SESSION['popupMessage']=[
                            'title' => 'Success',
                            'text' => 'Request Sent for Approval!',
                            'icon' => 'success'
                        ];
                        header("Location: partnerPointAward.php");
                        exit();
                    } 
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
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/partner.css">
    <?php include "../include/libraries.php";?>
    <link rel="icon" href="../../favicon.ico">
    <title>Point Award</title>
</head>
<body>
    <script src="../../js/customSwal.js"></script>
    <?php
        include "../include/header.php";
    ?>
    <div class="background">
        <div class="centeredContainer">
            <div class="formCard">
                <h2>Award Points</h2>
                <p class="subtitle">Enter customer details below</p>
                
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-inputs">
                        <label for="username">Username</label>
                        <input type="text" name="username" placeholder="Customer Username" required>

                        <label for="amt_spent">Amount Spent (RM)</label>
                        <input type="number" name="amt_spent" step="0.01" placeholder="0.00" required>

                        <label>Upload Receipt</label>
                        <input type="file" name="receipt_img" required accept="image/*">
                    </div>

                    <button type="submit" class="submitBtn">Confirm Transaction</button>
                </form> 
            </div>
        </div>
    </div>

    <?php include "../include/showSWAL.php"?>
</body>
</html>
<?php mysqli_close($dbConn)?>