<?php
// howto_creat_lease.php
// Initialize the session
session_start();
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // last request was more than 5 minutes ago
    //session_unset();     // unset $_SESSION variable for the run-time 
    //session_destroy();   // destroy session data in storage
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){

}
 
// Include config file

?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI project</title>
    <link rel="icon" type="image/png" href="../../house_en/images/logo.png" sizes="32x32" />
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../../house_en/css/style.css">

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-KWTSFY2CH4"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-KWTSFY2CH4');
    </script>
</head>
<body>

<!-- header section starts  -->
<header class="header">
    <a href="#" class="logo">
        <img src="../../img/icon_appstorego.png" alt="">
    </a>

    <nav class="navbar">
        <a href="../#home">home</a>
        <a href="./index.php">AI project</a>
        <a href="#about1">Project</a>
        <a href="#about2">Knowledge</a>
        <a href="#about3">Discussion</a>
    </nav>

    <div class="icons">
        <div class="fas fa-search" id="search-btn"></div>
        <div class="fas fa-home" id="cart-btn"></div>
        <div class="fas fa-bars" id="menu-btn"></div>
    </div>

    <div class="search-form">
        <input type="search" id="search-box" placeholder="search here...">
        <label for="search-box" class="fas fa-search"></label>
    </div>

    <div class="cart-items-container">
        <a href="../controlpanel/setting.php" class="btn">My Setting</a>
    </div>
</header>
<!-- header section ends -->

<!-- about section starts  -->

<section class="about" id="about1">

    <h1 class="heading"> <span>AI</span> project </h1>

    <div class="row">

        <div class="image">
            <img src="./img/mh_step101.png" alt="">
        </div>

        <div class="content">
            <h3>Teen Distracted Driver Detection</h3>
            <p>By: <a href="https://umdearborn.edu/shan-bao">Shan Bao</a>, Ph.D., Associate professor, Industrial and Manufacturing Systems Engineering</p>
            <p>With: <a href="https://umdearborn.edu/zhen-hu">Zhen Hu</a>, Ph.D., Assistant Professor, Industrial and Manufacturing Systems Engineering</p>
            <p>With: <a href="https://sites.google.com/umich.edu/hfet-lab/people">Zifei Wang</a>, Ph.D. student in Industrial and Systems Engineering at the University of Michigan-Dearborn</p>
        </div>

    </div>

</section>

<!-- about section ends -->
<!-- about section starts  -->

<section class="about" id="about2">

    <h1 class="heading"> <span>Project</span> Base </h1>

    <div class="row">

        <div class="image">
            <img src="./img/mh_step105.jpg" alt="">
        </div>

        <div class="content">
            <h3>Prepare your dataset and algorithms</h3>
            <p>1. <a href="https://github.com/Abhinav1004/Distracted-Driver-Detection">Start from an open source project as reference</a></p>
            <p>2. <a href="https://www.kaggle.com/c/state-farm-distracted-driver-detection/data?select=imgs">Kaggle dataset</a></p>
            
        </div>

    </div>

</section>

<!-- about section ends -->
<!-- about section starts  -->

<section class="about" id="about3">

    <h1 class="heading"> <span>Discussion</span> meeting </h1>

    <div class="row">

        <div class="image">
            <img src="./img/mh_step111.webp" alt="">
        </div>

        <div class="content">
            <h3>Attend meeting and discussion</h3>
            <p>1. <a href="https://umich.zoom.us/j/95027422327">Join online meeting</a> every 2 weeks from 10:00 - 11:00 AM on Thursday from July 7 to Sep 1.</p>
            <p>2. Show your test results and personal statement</p>
            
        </div>

    </div>

</section>

<!-- about section ends -->

<!-- footer section starts  -->

<section class="footer">

    <div class="share">
        <a href="https://www.facebook.com/OXRentCloud" class="fab fa-facebook-f"></a>
        <a href="https://twitter.com/cloudh_org/status/1483138386441478156" class="fab fa-twitter"></a>
        <a href="https://www.youtube.com/channel/UC4ap0lCTYDgDibVa332mqbg" class="fab fa-youtube"></a>
        <a href="https://www.instagram.com/" class="fab fa-instagram"></a>
        <a href="https://www.tiktok.com/@jeff8888888888/video/7054226963233492271?is_copy_url=1&is_from_webapp=v1" class="fab fa-tiktok"></a>
        <a href="https://apps.apple.com/us/app/managehouse/id1603908999" class="fab fa-apple"></a>
    </div>

    <div class="links">
        <a href="../#home">home</a>
        <a href="./index.php">AI project</a>
        <a href="#about1">Project</a>
        <a href="#about2">Knowledge</a>
        <a href="#about3">Discussion</a>
    </div>

    <div class="credit">created by <span>Datapublic service</span> | @2021 all rights reserved</div>

</section>

<!-- footer section ends -->

<!-- custom js file link  -->
<script src="../../house_en/js/script.js"></script>
</body>
</html>	