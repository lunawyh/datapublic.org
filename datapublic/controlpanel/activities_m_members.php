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

$un_id_arr = $username_arr = $act_state_arr = array();

$members_total = 0;
// query all
if($members_total < 1){
    // Prepare a select statement
    $sql = "SELECT un_id,username,act_state FROM tb_activities_users WHERE act_id = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_act_id);
        
        // Set parameters
        $param_act_id = $_SESSION["act_id"];
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            /* store result */
            mysqli_stmt_store_result($stmt);
            
            if(mysqli_stmt_num_rows($stmt) < 1){
                $l_activites_err = "No one has joined yet. Please invite.";
            } else{
                mysqli_stmt_bind_result($stmt, 
                    $un_id, $username, $act_state
                );
                while(mysqli_stmt_fetch($stmt)){
                    array_push($un_id_arr, $un_id);
                    array_push($username_arr, $username);
                    array_push($act_state_arr, $act_state);
                }               
                $members_total = mysqli_stmt_num_rows($stmt);
                ////
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }
}
//change state from to   
function changeActivityState($state_from, $state_to, $state_arr, $un_arr, $sql_link){
    $sql_list_err = '';
    $selMembers = $_POST['formMembers'];
  
    if(empty($selMembers)) 
    {
        $sql_list_err = "You didn't select any members!";
    } 
    else 
    {
        $nMembers = count($selMembers);            
        for($ii=0; $ii < $nMembers; $ii++)
        {
            // from current state
            if($state_arr[ $selMembers[$ii] ] != $state_from) continue;

            // Prepare a update statement to update
            $sql = "UPDATE tb_activities_users SET act_state = ? WHERE username = ? AND  act_id = ?";
            
            if($stmt = mysqli_prepare($sql_link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "dsd", $param_act_state, $param_username, $param_act_id);
                
                // Set parameters                    
                $param_act_state = $state_to;
                $param_username = $un_arr[ $selMembers[$ii] ];
                $param_act_id = $_SESSION["act_id"];
                
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    
                } else{
                    $sql_list_err = "Oops! Something went wrong. Please try again later.";
                }                    

                // Close statement
                mysqli_stmt_close($stmt);
            } else {
                $sql_list_err = "No valid activity to be approved, please come later.";
            }                    
        }  
        if( empty($sql_list_err) ){      
            header("location: ./activities_m_members.php"); 
        }   
    }   
    return $sql_list_err;
}
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    //echo "REQUEST_METHOD is POST";
    if(isset($_POST['Deny'])) { 
        //change state from 20 to 10   
        $members_list_err = changeActivityState(20, 10, $act_state_arr, $username_arr, $link);
    } elseif(isset($_POST['Admit'])) { 
        //change state from 10 to 20   
        $members_list_err = changeActivityState(10, 20, $act_state_arr, $username_arr, $link);
    } elseif(isset($_POST['Unjoin'])) { 
        //change state from 30 to 20 
        $members_list_err = changeActivityState(30, 20, $act_state_arr, $username_arr, $link);
    } elseif(isset($_POST['Join'])) { 
        //change state from 20 to 30 
        $members_list_err = changeActivityState(20, 30, $act_state_arr, $username_arr, $link);
    } elseif(isset($_POST['Unfinish'])) { 
        //change state from 40 to 30 
        $members_list_err = changeActivityState(40, 30, $act_state_arr, $username_arr, $link);
    } elseif(isset($_POST['Finish'])) { 
        //change state from 30 to 40 
        $members_list_err = changeActivityState(30, 40, $act_state_arr, $username_arr, $link);
    } else {

    }
    
    // Close connection
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage activities in Data Public Organization</title>
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
        <h2>Members in activity in Data Public Organization</h2>
        <p>Activity No. <?php echo $_SESSION["act_id"].": ".$_SESSION['act_name']." on ".$_SESSION['act_date']; ?> has <?php echo $members_total; ?> members:</p>
        <p>  </p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($members_list_err)) ? 'has-error' : ''; ?>">
                <label for='formMembers[]'>Select the members that you want to change:</label><br>
                
                <?php
                    echo '<table border="0" width="600" align="center">';
                    // 
                    for($aa = 0; $aa <= $members_total - 1; $aa++){
                        
                        $act_state = $act_state_arr[$aa];
                        if($act_state <= 10) $act_state_str = "Registered";
                        else if($act_state <= 20) $act_state_str = "Admitted";
                        else if($act_state <= 30) $act_state_str = "Joined";
                        else $act_state_str = "Finished";                            
                        //
                        echo '<td><input type="checkbox" name="formMembers[]" value='.$aa.'><label>'.$aa.'</label><br/></td>';
                        echo '<td>'.$username_arr[$aa].'</td>';
                        echo '<td>'.$act_state_str.'</td>';
                        echo "</tr>";
                    }
                    echo "</table>";
                ?>
               
                <span class="help-block"><?php echo $members_list_err; ?></span>
            </div>    
            <div class="form-group">
                <input type="submit" class="btn btn-default" name="Deny" value="Deny">
                <input type="submit" class="btn btn-primary" name="Admit" value="Admit">
                <input type="submit" class="btn btn-default" name="Unjoin" value="Unjoin">
                <input type="submit" class="btn btn-default" name="Join" value="Join">
                <input type="submit" class="btn btn-default" name="Unfinish" value="Unfinish">
                <input type="submit" class="btn btn-primary" name="Finish" value="Finish">
            </div>
            <p>  </p>
            <p><a href="./activities_manage.php">Go to manage activities</a>.</p>
            <p><a href="./activities_create.php">Create</a> -> Register -> <a href="./activities_manage.php">Admit</a> -> Join -> <a href="./activities_manage.php">Finish</a>.</p>
            <p>Copyright @2020 <a href="../../">Data Public Organization</a>.</p>
        </form>
    </div>    
</body>
</html>