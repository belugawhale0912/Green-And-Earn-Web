<header>
    <img id="logo" src="../../assets/Page Title.png" alt="Green&Earn">
    <nav id="header_links">
        <?php if(isset($_SESSION['role_id'])): $loggedInRole=$_SESSION['role_id']?>
            <?php if($loggedInRole==1):?>
                <a href="../pages/.php">Dashboard</a>
                <a href="../pages/.php">User Management</a>
                <a href="../pages/.php">System Data Management</a>
                <a href="../pages/.php">Profile</a>
            <?php elseif($loggedInRole==2):?>
                <a href="../pages/memberDashboard.php">Dashboard</a>
                <a href="../pages/memberTaskList.php">Task List</a>
                <a href="../pages/memberRewardList.php">Rewards</a>
                <a href="../pages/memberTransactionHistory.php">Point Transactions</a>
                <a href="../pages/memberLeaderBoard.php">Leaderboard</a>
                <a href="../pages/memberFriends.php">Friends</a>
                <a href="../pages/profile.php">Profile</a>
            <?php elseif($loggedInRole==3):?>
                <a href="../pages/.php">Dashboard</a>
                <a href="../pages/.php">Submissions</a>
                <a href="../pages/.php">Profile</a>
            <?php elseif($loggedInRole==4):?>
                <a href="../pages/.php">Dashboard</a>
                <a href="../pages/.php">Profile</a>
            <?php endif;?>
        <?php else:?>
            <a href="../pages/aboutUs.php">About Us</a>
            <a href="../pages/contactUs.php">Contact Us</a>
            <a href="../pages/login.php">Login</a>
        <?php endif;?>
    </nav>
</header>