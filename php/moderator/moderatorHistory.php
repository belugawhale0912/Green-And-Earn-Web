<?php
    include "../include/conn.php";
    include "../include/session.php";

    $filter = isset($_GET['status']) ? $_GET['status'] : 'all';

    if ($filter == "Approved"){
        $sql = "SELECT s.*, u.username, a.title AS activity_title
        FROM activity_submission s
        JOIN user u ON s.user_id = u.user_id
        JOIN activity a ON s.activity_id = a.activity_id
        WHERE s.status = 'Approved'";
    }else if ($filter == "Rejected"){
        $sql = "SELECT s.*, u.username, a.title AS activity_title
        FROM activity_submission s
        JOIN user u ON s.user_id = u.user_id
        JOIN activity a ON s.activity_id = a.activity_id
        WHERE s.status = 'Rejected'";
    }else{
        $sql = "SELECT s.*, u.username, a.title AS activity_title
        FROM activity_submission s
        JOIN user u ON s.user_id = u.user_id
        JOIN activity a ON s.activity_id = a.activity_id
        WHERE s.status IN ('Approved', 'Rejected')";
    }

    $result = mysqli_query($dbConn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=2, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/moderator.css">
    <title>Document</title>
</head>
<body>
    <?php
        include "../include/header.php";
    ?>
    <h1>History</h1>
    <section id="History">
    
    <form method="GET" id="filterForm">
        <label for="statusFilter">Filter:</label>
        <select name="status" id="statusFilter">
            <option value="all" <?php if ($filter == "all") echo 'selected'; ?>>All</option>
            <option value="Approved" <?php if ($filter == "Approved") echo 'selected'; ?>>Approved</option>
            <option value="Rejected" <?php if ($filter == "Rejected") echo 'selected'; ?>>Rejected</option>
        </select>
    </form>

    <table>
        <thead>
            <tr>
                <th>Submission ID</th>
                <th>User</th>
                <th>Activity</th>
                <th>Status</th>
                <th>Submission Time</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['submission_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['activity_title']); ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td><?php echo $row['submission_time']; ?></td>
                </tr>
                <?php endwhile; ?>
        </tbody>
    </table>
</section>
    <script src="../../js/moderator.js" defer></script>
</body>
</html>