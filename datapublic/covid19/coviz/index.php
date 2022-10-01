<?php
// states_all_list.php.php
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
    //header("location: ../login.php");
    //exit;
}

if(isset($_SESSION["groups_in"]) && strpos($_SESSION["groups_in"], "house") !== false){
    
} else {
    //header("location: ../welcome.php");
    //exit;
}
?>

<?php
// Include config file
require_once "../../../house/config.php";
 
// Define variables and initialize with empty values
$h_param00 = $h_param01 = $h_param02 = $h_param03 = $h_param04 = $h_param05 = "";
$h_param06 = $h_param07 = $h_param08 = "";
$h_param00_err = $h_param03_err = "";

$h_total = array();
$h_param00_arr = array(array(), array(), array(), array());
$mysql_err = "";
$h_id_show = 0;
// step 0, it's jumping from other pages
if(isset($_GET['query_h_id'])){
    $h_id = $_GET['query_h_id']; 
    if(isset($_GET['h_name'])){
        $h_name = $_GET['h_name']; 
    }
    if(isset($_GET['h_id_show'])){
        $h_id_show = $_GET['h_id_show']; 
    }
}else {  // from POST
    // save important parameters
    if(isset($_POST['h_id'])) $h_id = trim($_POST["h_id"]);    
    if(isset($_POST['h_name'])) $h_name = trim($_POST["h_name"]);   
}

// step 3, identify which ID is used
$h_param03_arr = array(array(), array(), array(), array());
$h_param05_arr = $h_param06_arr = $h_param07_arr = array(array(), array(), array(), array());
$s_regions = array('Northeast', 'Midwest', 'South', 'West');
{  // or read database
    if(empty(trim($mysql_err))){
        // read house from tb_houses_global
        // Prepare a select statement
        $sql = "SELECT id, s_state, s_postalcode, s_region, s_finished FROM tb_states_global WHERE s_region = ? ORDER BY s_state ASC";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_h_param00);
            
            for($ii=0; $ii < 4; $ii++){
                // Set parameters
                $param_h_param00 = $s_regions[$ii];
            
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    /* store result */
                    mysqli_stmt_store_result($stmt);
                    
                    if(mysqli_stmt_num_rows($stmt) < 0){
                        $mysqli_err = "Oops! There is no record. Please create one.";
                    } else {
                        mysqli_stmt_bind_result($stmt, $h_param00, 
                            $h_param03, $h_param05,
                            $h_param06, $h_param07
                        );
                        $n_total = 0;                
                        while(mysqli_stmt_fetch($stmt)){      
                            if($h_id_show < 0){
                                if($h_param07 > 0) continue;  // unfinished 
                            } else {
                                if($h_param07 < 1) continue;  // finished 
                            }
                            array_push($h_param00_arr[$ii], $h_param00);                           
                            array_push($h_param03_arr[$ii], $h_param03);                        
                            array_push($h_param05_arr[$ii], $h_param05);   
                            array_push($h_param06_arr[$ii], $h_param06);                       
                            array_push($h_param07_arr[$ii], $h_param07);   
                            $n_total ++;               
                        }    
                        array_push($h_total, $n_total ); 
                    }
                } else{
                    $mysqli_err = "Oops! Something went wrong. Please query houses again later.";
                }
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }        
    }
    
}

// step 3, command to be called
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST['Close'])) { 
        $h_id_show = 1;
        // only one will be selected
        header("location: index.php?query_h_id=".$h_id."&h_name=".$h_name.
            "&h_function=".$h_function."&h_id_show=".$h_id_show);
        exit;
    } 
    if(isset($_POST['Unfinished'])) { 
        $h_id_show = -1;
        // only one will be selected
        header("location: index.php?query_h_id=".$h_id."&h_name=".$h_name.
            "&h_function=".$h_function."&h_id_show=".$h_id_show);
        exit;
    } 
} 
// Close connection
mysqli_close($link);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>COVID-19 cases in USA</title>
    <link rel="icon" type="image/png" href="../../../img/icon_appstorego.png" sizes="32x32" />
    <link rel="stylesheet" href="../../../css/bootstrap.css">
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
        echo "<td>".'<img src="../../../img/icon_appstorego.png" alt="icon" />'."</td>";
        echo "<td>"."<a href=../../>DataPublic.org</a>"."<br>"."COVID-19"."<br>".
            "<a href=./>In USA states</a>"."<br>".
            "<a href=../../../login>$account_info</a>"."</td>";
        echo "</table>";
    ?>
    <div class="wrapper">        
        <h2>COVID-19 cases in USA with <a href="https://github.com/lunawyh/covid19viz">source codes</a><?php 
            if($h_id_show < 0) echo '(unfinished)'; 
            ?></h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($h_param00_err)) ? 'has-error' : ''; ?>">                
                <input type="hidden" name="h_id" class="form-control" value="<?php echo $h_id; ?>" readonly="readonly">
                <input type="hidden" name="h_name" class="form-control" value="<?php echo $h_name; ?>" readonly="readonly">
                <span class="help-block"><?php echo $h_param00_err; ?></span>
            </div>    
            <?php
                for($jj = 0; $jj < 4; $jj++){
                    echo '<div class="form-group';
                    echo (!empty($h_param03_err)) ? 'has-error' : '';
                    echo '">';
                    echo '<label><h3>'.$s_regions[$jj].'</h3></label><br>';
                    // tables of states                                                         
                    echo '<table border="0" width="960" align="center">';
                    for($aa = 0; $aa < intval($h_total[$jj]); $aa++){
                        // image
                        $f_name = str_replace(' ', '_', $h_param03_arr[$jj][$aa]);
                        echo '<td>'.'<img src="'.'../img/Seal_of_'.$f_name.'.svg.png'.'" alt="icon" />'.'</td>';                        
                        // 	text
                        echo '<td>'.'<a href="./coviz_state.php?query_h_id='.$h_param00_arr[$jj][$aa].'">'.
                            'COVID-19 in '.$h_param03_arr[$jj][$aa].'</a><br>'.
                            'Visualization and Prediction<br>in '.$h_param05_arr[$jj][$aa].'</td>';
                        if( (($aa+1)%3) == 0) echo "</tr>"; // show 3 states in one row
                    }
                    echo "</table>";
                    echo '<span class="help-block">'.$h_param03_err.'</span>';
                    echo '</div>';
                }
            ?>               

            <div class="form-group">
                <input type="submit" class="btn btn-primary" name="Unfinished" value="Show Unfinished">
                <input type="submit" class="btn btn-primary" name="Finished" value="Show Finished">
            </div>
            <p>COVID-19 cases in USA with <a href="https://github.com/lunawyh/covid19viz">source codes</a><?php 
                if($h_id_show < 0) echo '(unfinished)'; 
            ?></p>
            <p><?php echo '<span style="color:#F00;text-align:center;">'.$mysql_err.'</span>'; ?></p>
            
            <p></p>
            
            <p><img src="../../../img/ox_rent_qr100.png" alt="icon" />Copyright @2020 <a href="../">Data Public Organization</a>.</p>
        </form>
    </div>    
</body>
</html>