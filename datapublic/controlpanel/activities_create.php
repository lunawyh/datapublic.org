<?php
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
    header("location: ../login.php");
    exit;
}

if(strpos($_SESSION["groups_in"], "admin") !== false){
    
} else {
    header("location: ../welcome.php");
    exit;
}
?>

<?php
// Include config file
require_once "../config.php";
 
// Define variables and initialize with empty values
$act_name = $act_date = $act_time_start = $act_place = $act_description = "";
$act_duration = 60;
$act_iteration = 1;
$act_name_err = $act_date_err = $act_time_start_err = $act_duration_err = $act_place_err = $act_description_err = "";
$sql_error = ""; 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate act_name
    if(empty(trim($_POST["act_name"]))){
        $act_name_err = "Please enter a activity name.";
    } elseif(strlen(trim($_POST["act_name"])) < 6){
        // invalid name
        $act_name_err = "Please enter a longer name.";    
    } else{
        // check the same name is existing
        // Prepare a select statement
        $sql = "SELECT id FROM tb_activities_global WHERE act_name = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_act_name);
            
            // Set parameters
            $param_act_name = trim($_POST["act_name"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $act_name_err = "This act_name is already taken.";
                } else{
                    $act_name = trim($_POST["act_name"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate act_date
    if(empty(trim($_POST["act_date"]))){
        $act_date_err = "Please enter a validate date of activity.";     
    } elseif(strtotime(trim($_POST["act_date"])) == false){
        $act_date_err = "Date must be validate format.";
    } else{
        $act_date = trim($_POST["act_date"]);
    }
    
    // Validate act_time_start
    if(empty(trim($_POST["act_time_start"]))){
        $act_time_start_err = "Please enter a validate start time of activity.";     
    } elseif(strtotime(trim($_POST["act_time_start"])) == false){
        $act_time_start_err = "time must be validate format.";
    } else{
        $act_time_start = trim($_POST["act_time_start"]);
    }
    
    // Validate act_duration
    if(empty(trim($_POST["act_duration"]))){
        $act_duration_err = "Please enter a validate duration of activity in minute.";     
    } elseif(strlen(trim($_POST["act_duration"])) < 0){
        $act_duration_err = "time must be more than 0 minute.";
    } else{
        $act_duration = trim($_POST["act_duration"]);
    }
    // Validate act_iteration
    if(empty(trim($_POST["act_iteration"]))){
        $act_iteration_err = "Please enter a validate iteration of activits.";     
    } elseif(strlen(trim($_POST["act_iteration"])) < 1){
        $act_iteration_err = "iteration must be more than 1 times.";
    } else{
        $act_iteration = trim($_POST["act_iteration"]);
    }
    
    // Validate act_place
    if(empty(trim($_POST["act_place"]))){
        $act_place_err = "Please enter a validate place.";     
    } elseif(strlen(trim($_POST["act_place"])) < 6){
        $act_place_err = "activity place must be existing, longer than 6 letters.";
    } else{
        $act_place = trim($_POST["act_place"]);
    }
    
    // Validate act_description
    if(empty(trim($_POST["act_description"]))){
        $act_description_err = "Please enter a detailed descrition of activity.";     
    } elseif(strlen(trim($_POST["act_description"])) < 16){
        $act_description_err = "activity description must be detailed, longer than 16 letters.";
    } else{
        $act_description = trim($_POST["act_description"]);
    }
    
    // Check input errors before inserting in database
    if(empty($act_name_err) && empty($act_date_err) && empty($act_time_start_err) && empty($act_duration_err) && empty($act_place_err) && empty($act_description_err)){
        $n_date = strtotime($act_date);
        // $n_total = 52; // for one year
        // Prepare an insert statement
        $sql = "INSERT INTO tb_activities_global (act_name, t_act_date, t_act_start, act_duration, act_place, act_description, act_state) VALUES (?, ?, ?, ?, ?, ?, ?)";
        for ($ii = 0; $ii < $act_iteration; $ii++) {            
            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "sssdssd", $param_act_name, $param_act_date, $param_act_start, $param_act_duration, $param_act_place, $param_act_description, $param_act_state );
                
                // Set parameters                
                if($act_iteration <= 1) $param_act_name = $act_name;
                else {
                    $param_act_name = $act_name . " session " . strval($ii+1);
                }
                // add every week                  
                $param_act_date = date('Y-m-d', $n_date);
                $param_act_start = $act_time_start;
                //$tmp_time = strtotime($act_time_start);
                //$act_time_end = date("H:i", strtotime($act_duration.' minutes', $tmp_time));
                $param_act_duration = $act_duration;
                $param_act_place = $act_place;
                $param_act_description = $param_act_name . "\n" . $act_description;
                $param_act_state = 0;
                
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Redirect to activity list page
                    
                } else{
                    $sql_error = "Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
            $n_date = strtotime("+7 day", $n_date);
        }
    }
    
    // jump or not
    if( empty($sql_error) ){
        // Redirect to activity list page
        header("location: activities_list.php");
    }
}
// Close connection
mysqli_close($link);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create an activity in Data Public Organization</title>
    <link rel="icon" type="image/png" href="../../img/icon_appstorego.png" sizes="32x32" />
    <link rel="stylesheet" href="../../css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 640px; padding: 20px; }
    </style>
</head>
<body>
    <?php
        $account_info = "";
        if(isset($_SESSION['username'])) $account_info = htmlspecialchars($_SESSION["username"]);
        echo '<table border="0" width="300" align="center">';
        echo "<td>".'<img src="../../img/icon_appstorego.png" alt="icon" />'."</td>";
        echo "<td>"."<a href=../../>DataPublic.org</a>"."<br>"."Data For Public Healthy"."<br>"."<a href=../welcome.php>$account_info</a>"."</td>";
        echo "</table>";
    ?>
    <div class="wrapper">
        <p><a href="./activities_create.php">Create</a> -> Register -> <a href="./activities_manage.php">Admit</a> -> Join -> <a href="./activities_manage.php">Finish</a>.</p>
        <h2>Create an activity in Data Public Organization</h2>
        <p>To add an activity, please fill all boxes and click the button Create now.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($act_name_err)) ? 'has-error' : ''; ?>">
                <label>Name of activity</label>
                <input type="text" name="act_name" class="form-control" value="<?php echo $act_name; ?>">
                <span class="help-block"><?php echo $act_name_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($act_date_err)) ? 'has-error' : ''; ?>">
                <label>Date of Activity</label>
                <input type="date" name="act_date" class="form-control" value="<?php echo $act_date; ?>">
                <span class="help-block"><?php echo $act_date_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($act_time_start_err)) ? 'has-error' : ''; ?>">
                <label>Start Time</label>
                <input type="time" name="act_time_start" class="form-control" value="<?php echo $act_time_start; ?>">
                <span class="help-block"><?php echo $act_time_start_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($act_duration_err)) ? 'has-error' : ''; ?>">
                <label>Duration in minutes</label>
                <input type="number" name="act_duration" class="form-control" value="<?php echo $act_duration; ?>">
                <span class="help-block"><?php echo $act_duration_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($act_iteration_err)) ? 'has-error' : ''; ?>">
                <label>Iterations weekly</label>
                <input type="number" name="act_iteration" class="form-control" value="<?php echo $act_iteration; ?>">
                <span class="help-block"><?php echo $act_iteration_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($act_place_err)) ? 'has-error' : ''; ?>">
                <label>Place</label>
                <input type="text" name="act_place" class="form-control" value="<?php echo $act_place; ?>">
                <span class="help-block"><?php echo $act_place_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($act_description_err)) ? 'has-error' : ''; ?>">
                <label>Task Description</label>
                <textarea name="act_description" cols="100" rows="10"><?php echo $act_description; ?></textarea>
                <span class="help-block"><?php echo $act_description_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Create now">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p><a href="./activities_manage.php">Go to manage activities</a>.</p>
            <p><a href="./activities_create.php">Create</a> -> Register -> <a href="./activities_manage.php">Admit</a> -> Join -> <a href="./activities_manage.php">Finish</a>.</p>
            <p>Copyright @2020 <a href="../../">Data Public Organization</a>.</p>
        </form>
    </div>    
</body>
</html>