<header>
    <img id="logo" src="../../assets/Page Title.png" alt="Green&Earn">
    
    <button id="hamburger">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-menu-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 6l16 0" /><path d="M4 12l16 0" /><path d="M4 18l16 0" /></svg>
    </button>

    <!-- TODO move theme button at the end of the bar -->
    <nav id="header_links">
        <?php if(isset($_SESSION['role_id'])): $loggedInRole=$_SESSION['role_id']?>
            <?php if($loggedInRole==1):?>
                <a href="../admin/adminDashboard.php">Dashboard</a>
                <a href="../admin/adminUserManagement.php">Manage User</a>
                <a href="../admin/adminActivityManagement.php">Manage Activity</a>
                <a href="../admin/adminRewardManagement.php">Manage Rewards</a>
                <a href="../pages/profile.php">Profile</a>
            <?php elseif($loggedInRole==2):?>
                <a href="../member/memberDashboard.php">Dashboard</a>
                <a href="../member/memberTaskList.php">Task List</a>
                <a href="../member/memberRewardList.php">Rewards</a>
                <a href="../member/memberTransactionHistory.php">Point Transactions</a>
                <a href="../member/memberLeaderBoard.php">Leaderboard</a>
                <a href="../member/memberFriends.php">Friends</a>
                <a href="../member/memberProfile.php">Profile</a>
            <?php elseif($loggedInRole==3):?>
                <a href="../moderator/moderatorDashboard.php">Dashboard</a>
                <a href="../moderator/moderatorSubmissions.php">Submissions</a>
                <a href="../moderator/moderatorHistory.php">History</a>
                <a href="../pages/profile.php">Profile</a>
            <?php elseif($loggedInRole==4):?>
                <a href="../partner/partnerDashboard.php">Dashboard</a>
                <a href="../partner/partnerRewardList.php">Rewards</a>
                <a href="../partner/partnerGiveReward.php">Give Reward</a>
                <a href="../partner/partnerPointAward.php">Points Award</a>
                <a href="../pages/profile.php">Profile</a>
            <?php endif;?>
        <?php else:?>
            <a href="../pages/aboutUs.php">About Us</a>
            <a href="../pages/contactUs.php">Contact Us</a>
            <a href="../pages/login.php">Login</a>
        <?php endif;?>
        <button id="themeToggle">☀️</button>
    </nav>
    <script src="../../js/header.js"></script>
    
</header>