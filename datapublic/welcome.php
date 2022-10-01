<?php
// welcome.php
// Initialize the session
session_start();
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // last request was more than 5 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
}
$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp

 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
if(strpos($_SESSION["groups_in"], "admin") !== false){
    header("location: ./welcome_admin.php");
    exit;
} else {
    
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="icon" type="image/png" href="../img/icon_appstorego.png" sizes="32x32" />
    <link rel="stylesheet" href="../css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
    <?php
        echo '<table border="0" width="300" align="center">';
        echo "<td>".'<img src="../img/icon_appstorego.png" alt="icon" />'."</td>";
        echo "<td>"."<a href=../>DataPublic.org</a>"."<br>"."Data For Public Healthy"."<br>".htmlspecialchars($_SESSION["username"])."</td>";
        echo "</table>";
    ?>
    <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to Data Public Organization.</h1>
    </div>
    <p>Created -> <a href="./controlpanel/activities_register.php">Register</a> -> Admitted -> <a href="./controlpanel/activities_list.php">Join</a> -> Finished. <a href="./controlpanel/activities_list.php">[My Activities]</a> <a href="./controlpanel/setting.php">[My Setting]</a></p>
    <p>
        <a href="./controlpanel/activities_register.php" class="btn btn-warning">Register For A New Activity</a>
        <a href="./controlpanel/activities_register_list.php" class="btn btn-danger">All New Activities</a>
    </p>
    <p>
        <a href="./controlpanel/activities_list.php" class="btn btn-warning">My Activity</a>
        <a href="./controlpanel/activities_list_all.php" class="btn btn-danger">All My Activities</a>
    </p>
    <p>
        <a href="reset-password.php" class="btn btn-warning">Reset My Password</a>
        <a href="./controlpanel/setting.php" class="btn btn-warning">My Settings</a>
        <a href="logout.php" class="btn btn-danger">Sign Out</a>
    </p>

    <?php
        echo '<table border="0" width="600" align="center">';
        echo "<td>".'<img src="../img/icon_item.png" alt="icon" />'."</td>";
        echo "<td>"."1. "."<a href=./controlpanel/activities_register.php>Register For A New Activity</a>"." by clicking on the button, then click the Register Now button"."</td>";
        echo "</tr>";
        echo "<td>".'<img src="../img/icon_item.png" alt="icon" />'."</td>";
        echo "<td>"."2. Click "."<a href=./controlpanel/activities_list.php>My Activities</a>"." button to check it's admitted by organizers"."</td>";
        echo "</tr>";
        echo "<td>".'<img src="../img/icon_item.png" alt="icon" />'."</td>";
        echo "<td>"."3. Click Join Now button to start the activity once the activity is active."."</td>";
        echo "</tr>";
    ?>
</body>
</html>