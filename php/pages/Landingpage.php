<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green&Earn</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/landing.css">
    <link rel="stylesheet" href="../../css/footer.css">
    <link rel="stylesheet" href="../../css/header.css">
    <link rel="icon" href="/GreenAndEarnWeb/favicon.ico">
</head>
<body>
    <!-- global header element -->
     <?php
        include("../include/header.php");
        include("../include/libraries.php");
     ?>

    <section id="hero">
        <h1>Welcome To Green&Earn, Where Your Gamified Eco Quest Begin</h1>
    </section>


    <main>
        <section id="how-to-use">
            <h1>Start Your Eco Adventure With Three Steps</h1>
            <div class="stepContainer">
                <div class="stepCards step1">
                    <h1 class="stepNumber">1</h1>
                    <p>Complete Eco Friendly Task Given</p>
                </div>
                <div class="stepCards step2">
                    <h1 class="stepNumber">2</h1>
                    <p>Submit Proof and Earn Points</p>
                </div>
                <div class="stepCards step3">
                    <h1 class="stepNumber">3</h1>
                    <p>Redeem Rewards To Your Liking</p>
                </div>
            </div>
        </section>

        <section id="impacts">
            <h1>Our Collective Impact</h1>
            <p>Turning green choices into real-world results.</p>     
            <div class="impact_stats">
                <div class="impactCards">
                    <h3>12k+</h3>
                    <p>Trees Planted</p>
                </div>
                <div class="impactCards">
                    <h3>5k kg</h3>
                    <p>Waste Recycled</p>
                </div>
                <div class="impactCards">
                    <h3>2k</h3>
                    <p>Rewards Earned</p>
                </div>
            </div>
        </section>

        <section id="rewards_partners">
            <h2>Our Partners</h2>
            <p>Various partners providing amazing rewards for you to redeem. Shop at our partner's shop to earn points</p>
            <div class="partnercontainer">
                <div class="partner_card blabla">
                    <h3>BlaBla Mart</h3>
                </div>
                <div class="partner_card rito">
                    <h3>Rito Games</h3>
                </div>
                <div class="partner_card apu">
                    <h3>APU</h3>
                </div>
            </div>
            <h2>And More For You To Discover</h2>
        </section>
    </main>
    <!--global footer element-->
    <?php
        include("../include/footer.php");
    ?>
</body>
</html>