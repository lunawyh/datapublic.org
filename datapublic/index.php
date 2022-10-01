<?php
// about.php
// Initialize the session
session_start();
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // last request was more than 5 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    //header("location: welcome_en.php");
    //exit;
} else {
    //header("location: ../house/login.php");
    //exit;
}
if(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'zh'){
    //header("location: ../house_cn/about.php");
    //exit;
} 
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage House in Cloud</title>
    <link rel="icon" type="image/png" href="../house_en/images/logo.png" sizes="32x32" />
    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../house_en/css/style.css">

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
        <img src="../img/icon_appstorego.png" alt="">
    </a>

    <nav class="navbar">
        <a href="./">home</a>
        <a href="#about1">Our Mission</a>
        <a href="#about2">Our Activities</a>
        <a href="#about3">Our Histroy</a>
        <a href="#about4">Our Projects</a>
        <a href="#about5">Our Links</a>
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

</header>
<!-- header section ends -->
<!-- about section starts  -->

<section class="about" id="about1">

    <h1 class="heading"> <span>OUR </span> MISSION </h1>

    <div class="row">

        <div class="image">
            <img src="../house_en/images/blog-8.png" alt="">
        </div>

        <div class="content">
            <h3>DataPublic Service: Data for public health</h3>
            <p>Welcome to <a href="./login.php">join us</a>. We focus on making the maximum positive effort for our community by utilizing data for public health.</p>
            <p>It includes and is not limited to data mining, storing, sorting, analyzing, pruning, merging, predicting of public data resources relating with public health.</p>
            <p>1) Learn mathematics used in computer programming</p>
            <p>2) Get familiar with computer software and programming.</p>
            <p>3) Volunteer in our community.</p>
            <p>4) Create a data-driven computer application to domostrate your programming skills and leadership.</p>
            <a href="./login.php" class="btn">Join us Now</a>
        </div>

    </div>

</section>

<!-- about section ends -->
<!-- about section starts  -->

<section class="about" id="about2">

    <h1 class="heading"> <span>OUR</span> ACTIVITIES </h1>

    <div class="row">

        <div class="image">
            <img src="../house_en/images/about-img.jpeg" alt="">
        </div>

        <div class="content">
            <h3>Register to join activities of DataPublic service</h3>
            <p>Welcome to <a href="./login.php">register our activities</a>. Some activities are not listed here.</p>
            <p>1) Computer coding in the open source project of <a href="./covid19">COVID19VIZ</a> on every weekend.</p>
            <p>2) Learning the math of <a href="https://github.com/lunawyh/covid19viz/wiki">math4computer</a> used in programming.</p>
            <p>3) Volunteering events such as cleaning up our parks.</p>
            <p>4) Visit a public health organization and donate every half year.</p>
            <a href="./login.php" class="btn">Register Now</a>
        </div>

    </div>

</section>

<!-- about section ends -->
<!-- about section starts  -->

<section class="about" id="about3">

    <h1 class="heading"> <span>OUR</span> HISTORY </h1>

    <div class="row">

        <div class="image">
            <img src="../house_en/images/blog-7.png" alt="">
        </div>

        <div class="content">
            <h3>We set up this NPO for public health</h3>
            <p>This Nonprofit Organization, DataPublic Service was founded in April, 2020 by:</p>
            <p>-    Dennis Yang, student in Grade 11, Troy High School, MI USA.</p>
            <p>-    Yihan Wang (Luna), student in Grade 10, Troy High School, MI USA.</p>
            <p>DataPublic Service is registerred with ID <a href="https://www.michigan.gov/lara">802873818</a> in Michigan USA.</p>
        </div>

    </div>

</section>

<!-- about section ends -->
<!-- about section starts  -->

<section class="about" id="about4">

    <h1 class="heading"> <span>Our</span> PROJECTS </h1>

    <div class="row">

        <div class="image">
            <img src="../house_en/images/blog-6.png" alt="">
        </div>

        <div class="content">
            <h3>Join our projects and contribute for public health</h3>
            <p>#1: AI Project Provided by University of Michigan: <a href="./projectAI">Teen Distracted Driver Detection</a></p>
            <p>#2: Open Source Project <a href="./covid19">COVID19VIZ</a></p>
            <p>#3: math for computer, <a href="https://github.com/lunawyh/covid19viz/wiki">math4computer</a></p>
            <p>#4: python for <a href="https://www.youtube.com/watch?v=iqQgED9vV7k&list=PLeo1K3hjS3uvCeTYTeyfe0-rN5r8zn9rw&index=45">Machine Learning</a></p>
            <p>#5: <a href="./projectsummer/">summer project</a></p>
        </div>

    </div>

</section>

<!-- about section ends -->
<!-- about section starts  -->

<section class="about" id="about5">

    <h1 class="heading"> <span>OUR</span> Links </h1>

    <div class="row">

        <div class="image">
            <img src="../luna/img/luna_draw_1901a.JPG" alt="">
        </div>

        <div class="content">
            <h3>Specially friendly Links</h3>
            <p><a href="../luna">Luna Skills</a>: Art, Music and Coding</p>
            <p><a href="../judy">Best Judy</a>: Art, Music and Coding</p>
            <p><a href="https://github.com/cd6oy">Larry</a>: Art, Music and Coding</p>
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
        <a href="./">home</a>
        <a href="#about1">Our Mission</a>
        <a href="#about2">Our Activities</a>
        <a href="#about3">Our Histroy</a>
        <a href="#about4">Our Projects</a>
        <a href="#about5">Our Links</a>
    </div>

    <div class="credit">created by <span>DataPublic Service</span> | Registerred ID <a href="https://www.michigan.gov/lara">802873818</a> in MI USA</div>
    <div class="credit">@2021 all rights reserved</div>            
</section>

<!-- footer section ends -->

<!-- custom js file link  -->
<script src="../house_en/js/script.js"></script>
</body>
</html>	