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
?>

<?php
// Include config file
require_once "../config.php";
 
// Define variables and initialize with empty values
$act_name = $act_date = $act_time_start = $act_place = $act_description = "";
$act_duration = 0;
$act_id = 0;
$act_name_err = $act_date_err = $act_time_start_err = $act_duration_err = $act_place_err = $act_description_err = $act_id_err = "";
$l_activites = "";
$l_activites_err = "";
$act_id_arr = $act_name_arr = $act_date_arr = $act_time_start_arr = $act_duration_arr = $act_place_arr = $act_description_arr = $act_state_arr = array();
$act_index = 0;
$act_total = 0;
$info_hint = "";
$act_joined_total = $act_id_joined = $act_state_joined = 0;
$act_id_joined_arr = $act_state_joined_arr = array();
// query all joined activities
if($act_joined_total < 1){
    // Prepare a select statement to query all joined
    $sql = "SELECT act_id,act_state FROM tb_activities_users WHERE username = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        
        // Set parameters
        $param_username = trim($_SESSION["username"]);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            /* store result */
            mysqli_stmt_store_result($stmt);
            
            if(mysqli_stmt_num_rows($stmt) < 1){
                $l_activites_err = "No activite is registered yet. Please register now.";
            } else{
                mysqli_stmt_bind_result($stmt, 
                    $act_id_joined, $act_state_joined
                );
                while(mysqli_stmt_fetch($stmt)){
                    array_push($act_id_joined_arr, $act_id_joined);
                    array_push($act_state_joined_arr, $act_state_joined);
                }               
                $act_joined_total = mysqli_stmt_num_rows($stmt);
                ////
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }
}
// query all existing activities to be chosen
if($act_total < 1){

    // Prepare a select statement to look up all activities after today for joining
    $sql = "SELECT id,act_name,t_act_date,t_act_start,act_duration,act_place,act_description,act_state FROM tb_activities_global WHERE act_state >= ? ORDER BY t_act_date ASC";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "d", $param_act_state);
        
        // Set parameters
        $param_act_state = 0;
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            /* store result */
            mysqli_stmt_store_result($stmt);
            
            if(mysqli_stmt_num_rows($stmt) < 1){
                $l_activites_err = "No activite in the database.";
            } else{
                mysqli_stmt_bind_result($stmt, 
                    $act_id, $act_name, $act_date,
                    $act_time_start, $act_duration, $act_place, $act_description, $act_state
                );
                $act_total = 0;
                date_default_timezone_set('America/New_York');
                while(mysqli_stmt_fetch($stmt)){
                    if(strtotime($act_date) - strtotime(date('Y-m-d')) >= 0){
                        // check this activity is registered already
                        $is_joined = 0;
                        for ($xx = 0; $xx < $act_joined_total; $xx++) {
                            if($act_id_joined_arr[$xx] == $act_id){
                                $is_joined ++;
                                break;
                            }
                        }
                        if($is_joined <= 0){     // is not joined yet
                            array_push($act_id_arr, $act_id);
                            array_push($act_name_arr, $act_name);
                            array_push($act_date_arr, $act_date);
                            array_push($act_time_start_arr, $act_time_start);
                            array_push($act_duration_arr, $act_duration);
                            array_push($act_place_arr, $act_place);
                            array_push($act_description_arr, $act_description);
                            array_push($act_state_arr, $act_state);
                            $act_total ++;
                        }
                    }
                }               
                //start from the earliest activity, because it's already sorted
                $i_index = 0;
                if(isset($_GET['query_act_id'])){
                    for ($x = 0; $x < $act_total; $x++) {
                        if($_GET['query_act_id'] == $act_id_arr[$x]){
                            $i_index = $x;
                            break;
                        }
                    }            
                }
                
                $act_id = $act_id_arr[$i_index];
                $act_name = $act_name_arr[$i_index];
                $act_date = $act_date_arr[$i_index];
                $act_time_start = $act_time_start_arr[$i_index];
                $act_duration = $act_duration_arr[$i_index];
                $act_place = $act_place_arr[$i_index];
                $act_description = $act_description_arr[$i_index];
                $act_state = $act_state_arr[$i_index];
                $act_index = $i_index;
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    //echo "REQUEST_METHOD is POST";
    if(isset($_POST['Previous'])) { 
        //act_show_previous();        
        $i_index = 0;
        for ($x = 0; $x < $act_total; $x++) {
            if($_POST["act_id"] == $act_id_arr[$x]){
                $i_index = $x;
                break;
            }
        }
        $i_index --;
        if($i_index < 0) $i_index = 0;
        
        if($i_index < $act_total){        
            $act_id = $act_id_arr[$i_index];
            $act_name = $act_name_arr[$i_index];
            $act_date = $act_date_arr[$i_index];
            $act_time_start = $act_time_start_arr[$i_index];
            $act_duration = $act_duration_arr[$i_index];
            $act_place = $act_place_arr[$i_index];
            $act_description = $act_description_arr[$i_index];
            $act_state = $act_state_arr[$i_index];
            //
            $act_index = $i_index;
        }
        
    } elseif(isset($_POST['Next'])) { 
        //act_show_next();
        $i_index = 0;
        for ($x = 0; $x < $act_total; $x++) {
            if($_POST["act_id"] == $act_id_arr[$x]){
                $i_index = $x;
                break;
            }
        }
        $i_index ++;
        if($i_index < 0) $i_index = 0;
        if($i_index > $act_total-1) $i_index = $act_total-1;

        if($i_index < $act_total){        
            $act_id = $act_id_arr[$i_index];
            $act_name = $act_name_arr[$i_index];
            $act_date = $act_date_arr[$i_index];
            $act_time_start = $act_time_start_arr[$i_index];
            $act_duration = $act_duration_arr[$i_index];
            $act_place = $act_place_arr[$i_index];
            $act_description = $act_description_arr[$i_index];
            $act_state = $act_state_arr[$i_index];
            //
            $act_index = $i_index;
        }
        
    } elseif(isset($_POST['ListView'])) { 
        header("location: activities_register_list.php");
    } elseif(isset($_POST['Register'])) {  // Register
        // looking for member in the array
        $i_index = 0;
        for ($x = 0; $x < $act_total; $x++) {
            if($_POST["act_id"] == $act_id_arr[$x]){
                $i_index = $x;
                break;
            }
        }
        // check you have set up all information in setting
        // Prepare a select statement
        $sql = "SELECT id FROM tb_users_global WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_SESSION["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    
                } else{
                    // Display an error message if username doesn't exist
                    $act_name_err = "You need finish your setting at first.";
                    $info_hint = "Please set up your information and you can join it.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }        
        // check whether you have joined this activity
        if($i_index < $act_total){        
            $act_id = $act_id_arr[$i_index];
            $act_name = $act_name_arr[$i_index];
            $act_date = $act_date_arr[$i_index];
            $act_time_start = $act_time_start_arr[$i_index];
            $act_duration = $act_duration_arr[$i_index];
            $act_place = $act_place_arr[$i_index];
            $act_description = $act_description_arr[$i_index];
            $act_state = $act_state_arr[$i_index];
            //
            $act_index = $i_index;
            // Prepare a select statement
            $sql = "SELECT id FROM tb_activities_users WHERE username = ? AND  act_id = ?";
            
            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "sd", $param_username, $param_act_id);
                
                // Set parameters
                $param_username = trim($_SESSION["username"]);
                $param_act_id = $act_id;
                
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    /* store result */
                    mysqli_stmt_store_result($stmt);
                    
                    if(mysqli_stmt_num_rows($stmt) == 1){
                        $act_name_err = "You have registered.";
                        $info_hint = "You have registered this activity already.";
                    } else{
                        
                    }
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            } else {
                $act_name_err = "No valid activity, please come later.";
            }
        }
        // Check input errors before inserting in database, to join the activity
        if(empty($act_name_err) ){
            
            // Prepare an insert statement
            $sql = "INSERT INTO tb_activities_users (un_id, username, act_id, act_state, t_created_at) VALUES (?, ?, ?, ?, ?)";
            
            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "dsdds", $param_un_id, $param_username, $param_act_id, $param_act_state, $param_t_created_at );
                
                // Set parameters
                $param_un_id = $_SESSION["id"];
                $param_username = trim($_SESSION["username"]);
                $param_act_id = $act_id;
                $param_act_state = 10;
                $param_t_created_at = date("Y-m-d h:i:sa");
                    
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Redirect to activity list page
                    header("location: activities_register.php");
                } else{
                    echo "Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        }

    }
    
    // Close connection finally
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Join an activity in Data Public Organization</title>
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
        <p>Created -> <a href="./activities_register.php">Register</a> -> Admitted -> <a href="./activities_list.php">Join</a> -> Finished. <a href="./activities_list.php">[My Activities]</a> <a href="./setting.php">[My Setting]</a></p>
        <h2>Register an activity in Data Public Organization</h2>
        <p>Activity No. <?php echo $act_index+1; ?> / <?php echo $act_total; ?> :</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($act_id_err)) ? 'has-error' : ''; ?>">
                <label>No.</label>
                <input type="number" name="act_id" class="form-control" value="<?php echo $act_id; ?>" readonly="readonly">
                <span class="help-block"><?php echo $act_id_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($act_name_err)) ? 'has-error' : ''; ?>">
                <label>Name of activity</label>
                <input type="text" name="act_name" class="form-control" value="<?php echo $act_name; ?>" readonly="readonly">
                <span class="help-block"><?php echo $act_name_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($act_date_err)) ? 'has-error' : ''; ?>">
                <label>Date of Activity</label>
                <input type="date" name="act_date" class="form-control" value="<?php echo $act_date; ?>" readonly="readonly">
                <span class="help-block"><?php echo $act_date_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($act_time_start_err)) ? 'has-error' : ''; ?>">
                <label>Start Time</label>
                <input type="time" name="act_time_start" class="form-control" value="<?php echo $act_time_start; ?>" readonly="readonly">
                <span class="help-block"><?php echo $act_time_start_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($act_duration_err)) ? 'has-error' : ''; ?>">
                <label>Duration in minutes</label>
                <input type="number" name="act_duration" class="form-control" value="<?php echo $act_duration; ?>" readonly="readonly">
                <span class="help-block"><?php echo $act_duration_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($act_place_err)) ? 'has-error' : ''; ?>">
                <label>Place</label>
                <input type="text" name="act_place" class="form-control" value="<?php echo $act_place; ?>" readonly="readonly">
                <span class="help-block"><?php echo $act_place_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($act_description_err)) ? 'has-error' : ''; ?>">
                <label>Task Description</label>
                <textarea name="act_description" cols="100" rows="10" readonly><?php echo $act_description; ?></textarea>
                <span class="help-block"><?php echo $act_description_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-default" name="Previous" value="Previous">
                <input type="submit" class="btn btn-primary" name="Register" value="Register now">
                <input type="submit" class="btn btn-default" name="Next" value="Next">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-default" name="ListView" value="List view">
            </div>
            <p><?php echo $info_hint; ?></p>
            <p>Created -> <a href="./activities_register.php">Register</a> -> Admitted -> <a href="./activities_list.php">Join</a> -> Finished. <a href="./activities_list.php">[My Activities]</a> <a href="./setting.php">[My Setting]</a></p>
            <p>Copyright @2020 <a href="../../">Data Public Organization</a>.</p>
        </form>
    </div>    
</body>
</html>