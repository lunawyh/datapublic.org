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
$activities_list_err = "";
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
        $activities_list_err = 'All existing activities are shown here';
    } elseif(isset($_POST['Next'])) { 
        $activities_list_err = 'All existing activities are shown here';
    } elseif(isset($_POST['SingleView'])) { 
        header("location: activities_register.php");
    } elseif(isset($_POST['Register'])) {  // Register
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
                    $activities_list_err = "You need finish your setting at first.";
                    $info_hint = "Please set up your information and you can join it.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }    
        if(empty($activities_list_err)) {            
            // get current selected activities
            $selActivities = $_POST['formActivities'];
    
            if(empty($selActivities)) 
            {
                $activities_list_err = "You didn't select any activities!";
            } else {
                $n_finished = 0;
                $nAct = count($selActivities);            
                for($ii=0; $ii < $nAct; $ii++)
                {
                    $act_id = $selActivities[$ii];
                    // check whether you have joined this activity
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
                                $activities_list_err = "You have registered.";
                                $info_hint = "You have registered this activity already.";
                            } else{
                                
                            }
                        } else{
                            $activities_list_err = "Oops! Something went wrong. Please try checking again later.";
                        }

                        // Close statement
                        mysqli_stmt_close($stmt);
                    } else {
                        $activities_list_err = "No valid activity, please come later.";
                    }
            
                    // Check input errors before inserting in database, to join the activity
                    if(empty($activities_list_err) ){
                        
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
                                $n_finished ++;
                            } else{
                                $activities_list_err = "Something went wrong. Please try inserting again later.";
                            }

                            // Close statement
                            mysqli_stmt_close($stmt);
                        }
                    }
                }
                if( $n_finished > 0 ){
                    header("location: activities_register_list.php");
                }
            }
        }
    }
    
    // Close connection
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
        <p>In total <?php echo $act_total; ?> activities:</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($activities_list_err)) ? 'has-error' : ''; ?>">
                <label for='formActivities[]'>Select activities that you want to register:</label><br>
                
                <?php
                    echo '<table border="0" width="960" align="center">';
                    // $act_id_arr = $act_name_arr = $act_date_arr = $act_time_start_arr = $act_duration_arr =
                    // $act_place_arr = $act_description_arr = $act_state_arr
                    for($aa = 0; $aa <= $act_total - 1; $aa++){
                        //
                        echo '<td><input type="checkbox" name="formActivities[]" value='.$act_id_arr[$aa].'><label>'.$act_id_arr[$aa].'</label><br/></td>';
                        echo '<td>'.'<img src="../../img/icon_mission.png" alt="icon" />'.'<a href="./activities_register.php?query_act_id='.$act_id_arr[$aa].'">'.$act_name_arr[$aa].'</a>'.'</td>';
                        echo '<td>'.$act_date_arr[$aa].'</td>';
                        echo '<td>'.$act_time_start_arr[$aa].'</td>';
                        echo '<td>'.$act_duration_arr[$aa].' minutes'.'</td>';
                        echo '<td>'.$act_place_arr[$aa].'</td>';
                        echo '<td>'.substr($act_description_arr[$aa], 0, 40).' ...'.'</td>';
                        echo "</tr>";
                    }
                    echo "</table>";
                ?>
               
                <span class="help-block"><?php echo $activities_list_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-default" name="Previous" value="Previous Next">
                <input type="submit" class="btn btn-primary" name="Register" value="Register now">
                <input type="submit" class="btn btn-default" name="Next" value="Next Page">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-default" name="SingleView" value="Single view">
            </div>
            <p><?php echo $info_hint; ?></p>
            <p>Created -> <a href="./activities_register.php">Register</a> -> Admitted -> <a href="./activities_list.php">Join</a> -> Finished. <a href="./activities_list.php">[My Activities]</a> <a href="./setting.php">[My Setting]</a></p>
            <p>Copyright @2020 <a href="../../">Data Public Organization</a>.</p>
        </form>
    </div>    
</body>
</html>