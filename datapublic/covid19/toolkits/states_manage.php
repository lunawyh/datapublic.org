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
require_once "../../house/config.php";
 
// Define variables and initialize with empty values
// h_company, h_contact, h_telephone, h_address, h_email, h_website, h_note
$h_param00 = $h_param01 = $h_param02 = $h_param03 = $h_param04 = $h_param05 = "";
$h_param06 = $h_param07 = $h_param08 = "";
$h_param00_err = $h_param01_err = $h_param02_err = $h_param03_err = $h_param04_err = $h_param05_err = "";
$h_param06_err = $h_param07_err = $h_param08_err = "";
$h_total = 0;
$h_index = 0;
$h_param00_arr = array();
$mysql_err = "";

// step 0, it's jumping from other pages
if(isset($_GET['query_h_id'])){
    $h_id = $_GET['query_h_id'];   
      
    if(isset($_GET['h_name'])){
        $h_name = $_GET['h_name']; 
    }
}else {  // from POST
    // save important parameters
    if(isset($_POST['h_id'])) $h_id = trim($_POST["h_id"]);    
    if(isset($_POST['h_name'])) $h_name = trim($_POST["h_name"]);
}
// step 1, query all houses
if($h_total < 1){
    // Prepare a select statement DESC or ASC
    $sql = "SELECT id FROM tb_states_global WHERE s_country = ? ORDER BY s_postalcode ASC";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters $_SESSION["id"]
        mysqli_stmt_bind_param($stmt, "s", $param_un_id);
        
        // Set parameters
        $param_un_id = 'USA';
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            /* store result */
            mysqli_stmt_store_result($stmt);
            
            if(mysqli_stmt_num_rows($stmt) < 1){
                $mysql_err = "Nothing in the database.";
            } else{
                mysqli_stmt_bind_result($stmt, 
                    $h_param00
                );
                $h_index = 0;   
                $h_total = 0;              
                while(mysqli_stmt_fetch($stmt)){
                    array_push($h_param00_arr, $h_param00);
                    if($h_param00 == $h_id){
                        $h_index = $h_total;
                    }
                    $h_total ++;
                }        
                //$h_total = mysqli_stmt_num_rows($stmt); 
                $h_param00 = $h_param00_arr[$h_index];
            }
        } else{
            $mysql_err = "Oops! Something went wrong. Please query houses again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

}
// something is wrong
if(empty(trim($mysql_err))){
} else {
    // jump to create one
    //header("location: ./house_tax_create.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function);
    //exit;
}

// step 3, identify which ID is used
$is_update = 0;
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    //echo "REQUEST_METHOD is POST";
    $i_index = 0;
    for ($x = 0; $x < $h_total; $x++) {
        if($_POST["h_param00"] == $h_param00_arr[$x]){
            $i_index = $x;
            break;
        }
    }
    if(isset($_POST['Previous'])) { 
        //act_show_previous();        
        $i_index --;
        if($i_index < 0) $i_index = 0;
        
        if($i_index < $h_total){        
            $h_param00 = $h_param00_arr[$i_index];
            //
            $h_index = $i_index;
        }
        
    } elseif(isset($_POST['Next'])) { 
        //act_show_next();
        $i_index ++;
        if($i_index < 0) $i_index = 0;
        if($i_index > $h_total-1) $i_index = $h_total-1;

        if($i_index < $h_total){        
            $h_param00 = $h_param00_arr[$i_index];
            //
            $h_index = $i_index;
        }
    } elseif(isset($_POST['Apply'])) { 
        // looking for member in the array        
        if($i_index < $h_total){        
            $h_param00 = $h_param00_arr[$i_index];
            //
            $h_index = $i_index;
        }
        // Check input errors before inserting in database
        $is_update = 1;
    } else {
        // looking for member in the array        
        if($i_index < $h_total){        
            $h_param00 = $h_param00_arr[$i_index];
            //
            $h_index = $i_index;
        }
    }
}
// step 4, Processing form data when form is submitted
if($is_update == 1){
    // Validate h_param03
    if(empty(trim($_POST["h_param03"]))){
        $h_param03_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param03"])) < 2){
        $h_param03_err = "The param is too short.";
    } else{
        $h_param03 = trim($_POST["h_param03"]);
    }
    // Validate h_param05
    if(empty(trim($_POST["h_param05"]))){
        $h_param05_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param05"])) < 2){
        $h_param05_err = "The param is too short.";
    } else{
        $h_param05 = trim($_POST["h_param05"]);
    }
    // Validate h_param06
    if(empty(trim($_POST["h_param06"]))){
        $h_param06_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param06"])) < 2){
        $h_param06_err = "The param is too short.";
    } else{
        $h_param06 = trim($_POST["h_param06"]);
    }
    // Validate h_param07
    if(empty(trim($_POST["h_param07"]))){
        $h_param07_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param07"])) < 2){
        $h_param07_err = "time must be validate format.";
    } else{
        $h_param07 = trim($_POST["h_param07"]);
    }
    // Validate h_param08
    $h_param08 = trim($_POST["h_param08"]);
    
    // Check input errors before inserting in database
    if( empty($h_param03_err) && 
        empty($h_param05_err) && empty($h_param06_err) && 
        empty($h_param07_err) && empty($h_param08_err) ){
        // Prepare a update statement
        $sql = "UPDATE tb_states_global SET s_state = ?, s_postalcode = ?, s_region = ?, s_team = ?, s_finished = ? WHERE id = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters 
            mysqli_stmt_bind_param($stmt, "ssssdd", 
                $param_h_param03, 
                $param_h_param05, $param_h_param06, 
                $param_h_param07, $param_h_param08, $param_h_param00 
                );
            
            // Set parameters
            $param_h_param00 = $h_param00;
            $param_h_param03 = $h_param03;
            $param_h_param05 = $h_param05;
            $param_h_param06 = $h_param06;
            $param_h_param07 = $h_param07;
            $param_h_param08 = $h_param08;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
            } else{
                $mysql_err = "Oops! Something went wrong. Please update again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }        
    }
} else {  // or read database
    if(empty(trim($mysql_err))){
        // read house from tb_houses_global
        // Prepare a select statement
        $sql = "SELECT s_state, s_postalcode, s_region, s_team, s_finished FROM tb_states_global WHERE id = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "d", $param_h_param00);
            
            // Set parameters
            $param_h_param00 = $h_param00;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt, 
                        $h_param03, $h_param05, $h_param06,
                        $h_param07, $h_param08
                    );
                                    
                    if(mysqli_stmt_fetch($stmt)){                        
                    }    
                    //  date , not datetime
                }
            } else{
                $mysqli_err = "Oops! Something went wrong. Please query houses again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }        
    }
}
// Close connection
mysqli_close($link);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage a state of COVIZ</title>
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
        echo "<td>"."<a href=../>DataPublic.org</a>"."<br>"."OX Rent Cloud"."<br>"."<a href=../../login>$account_info</a>"."</td>";
        echo "</table>";
    ?>
    <div class="wrapper">
        <p>Create -> Review -> <?php 
                echo '<a href="'."./states_manage.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function.'">'        
                ?>Manage</a> -> Close -> Reopen.</p>
        <h2>Manage a state of COVIZ</h2>
        <p>State No. <?php echo $h_index+1; ?> / <?php echo $h_total; ?> :</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($h_param00_err)) ? 'has-error' : ''; ?>">                
                <input type="hidden" name="h_param00" class="form-control" value="<?php echo $h_param00; ?>" readonly="readonly">
                <input type="hidden" name="h_id" class="form-control" value="<?php echo $h_id; ?>" readonly="readonly">
                <input type="hidden" name="h_name" class="form-control" value="<?php echo $h_name; ?>">
                <span class="help-block"><?php echo $h_param00_err; ?></span>
            </div>    
                        
            <div class="form-group <?php echo (!empty($h_param05_err)) ? 'has-error' : ''; ?>">
                <label>Postal Code</label>
                <input type="text" name="h_param05" class="form-control" value="<?php echo $h_param05; ?>" readonly="readonly">
                <span class="help-block"><?php echo $h_param05_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param03_err)) ? 'has-error' : ''; ?>">
                <label>State name</label>
                <input type="text" name="h_param03" class="form-control" value="<?php echo $h_param03; ?>" readonly="readonly">
                <span class="help-block"><?php echo $h_param03_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param06_err)) ? 'has-error' : ''; ?>">
                <label>Region</label>
                <input type="text" name="h_param06" class="form-control" value="<?php echo $h_param06; ?>" readonly="readonly">
                <span class="help-block"><?php echo $h_param06_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param07_err)) ? 'has-error' : ''; ?>">
                <label>Team</label>
                <input type="text" name="h_param07" class="form-control" value="<?php echo $h_param07; ?>">
                <span class="help-block"><?php echo $h_param07_err; ?></span>
            </div> 
            <div class="form-group <?php echo (!empty($h_param08_err)) ? 'has-error' : ''; ?>">
                <label>Finished</label>
                <input type="number" name="h_param08" min="-10" max="10" step="1" value="<?php echo $h_param08; ?>" />
                <span class="help-block"><?php echo $h_param08_err; ?></span>
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-default" name="Previous" value="Previous">
                <input type="submit" class="btn btn-primary" name="Apply" value="Save now">
                <input type="submit" class="btn btn-default" name="Next" value="Next">
            </div>
            <p><?php echo '<span style="color:#F00;text-align:center;">'.$mysql_err.'</span>'; ?></p>
            <p><?php 
                echo '<td>'.'<a href="./index.php?query_h_id='.$h_id."&h_name=".$h_name."&h_function=".$h_function.'">'.
                            'USA all states'.'</a>'.'</td>';
                ?></p>
            <p>Create -> Review -> <?php 
                echo '<a href="'."./states_manage.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function.'">'        
                ?>Manage</a> -> Close -> Reopen.</p>
            <p><img src="../../img/ox_rent_qr100.png" alt="icon" />Copyright @2020 <a href="../">Data Public Organization</a>.</p>
        </form>
    </div>    
</body>
</html>