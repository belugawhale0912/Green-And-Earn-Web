<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green&Earn</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/layout.css">
    <link rel="stylesheet" href="../../css/landing.css">
    <link rel="stylesheet" href="../../css/footer.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="icon" href="../../icon/favicon.ico">
</head>
<body>
    <!-- global header element -->
     <?php
        include("../include/header.php");
        include("../include/libraries.php");
     ?>

    <section id="hero">
        <h1>Welcome To Green&Earn, Where Your Gamified Eco Quest Begin</h1>
        <img id="topBanner" src="../../assets/placeholder.jpg" alt="hero_img">
    </section>


    <main>
        <section id="how-to-use">
            <h2>Start Your Eco Adventure With Three Steps</h2>
            <div class="container">
                <div class="cards">
                    <p>Complete Eco Friendly Task Given</p>
                </div>
                <div class="cards">
                    <p>Submit Proof and Earn Points</p>
                </div>
                <div class="cards">
                    <p>Redeem Rewards To Your Liking</p>
                </div>
            </div>
        </section>

        <section id="impacts">
            <div id="impact_stats">
                <h2>Our Collective Impact</h2>
                <p>Turning green choices into real-world results.</p>
                <p>Since 2020</p>       
                <div class="cards">
                    <h3>12k+</h3>
                    <p>Trees Planted</p>
                </div>
                <div class="cards">
                    <h3>5k kg</h3>
                    <p>Waste Recycled</p>
                </div>
                <div class="cards">
                    <h3>$15k</h3>
                    <p>Rewards Earned</p>
                </div>
            </div>
        </section>

        <section id="rewards_partners">
            <h2>Featured Rewards (Our Partners)</h2>
            <div class="container">
                <div class="partner_card">
                    <h3>BlaBla Mart</h3>
                </div>
                <div class="partner_card">
                    <h3>Riot Games</h3>
                </div>
                <div class="partner_card">
                    <h3>Steam</h3>
                </div>
            </div>
        </section>
    </main>
    <!--global footer element-->
    <?php
        include("../include/footer.php");
    ?>
</body>
</html>