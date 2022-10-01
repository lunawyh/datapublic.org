<?php
// house_tax_create.php
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

if(isset($_SESSION["groups_in"]) && strpos($_SESSION["groups_in"], "house") !== false){
    
} else {
    header("location: ../welcome.php");
    exit;
}
?>

<?php
// Include config file
require_once "../config.php";
 
// Define variables and initialize with empty values
// h_company, h_contact, h_telephone, h_address, h_email, h_website, h_note
$h_param01 = $h_param02 = $h_param03 = $h_param04 = $h_param05 = "";
$h_param06 = $h_param07 = $h_param08 = "";
$h_param01_err = $h_param02_err = $h_param03_err = $h_param04_err = $h_param05_err = "";
$h_param06_err = $h_param07_err = $h_param08_err = "";
// suppliers
$suppliers_id_arr = $suppliers_h_company_arr = array();
$suppliers_total = 0;
// step 1, it's jumping from other pages
if(isset($_GET['query_h_id'])){
    $h_id = $_GET['query_h_id'];   
      
    if(isset($_GET['h_function'])){
        $h_function = $_GET['h_function']; 
    }
    if(isset($_GET['h_name'])){
        $h_name = $_GET['h_name']; 
    }
    $h_param01 = $h_id;
}else {  // from POST
    // save important parameters
    if(isset($_POST['h_id'])) $h_id = trim($_POST["h_id"]);    
    if(isset($_POST['h_name'])) $h_name = trim($_POST["h_name"]);
}

// set default value
{
    date_default_timezone_set('America/New_York');
    $h_param04 = date('Y-m-d', time());     // today
    $h_param07 = (int)date('Y', time()); 
    $n_month = (int)date('m', time()); 
    if($n_month >= 2 && $n_month <= 8 ) $h_param06 = 1;
    else $h_param06 = 2;
}
// step 3, Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate h_param01
    if(empty(trim($_POST["h_param01"]))){
        $h_param01_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param01"])) < 1){
        $h_param01_err = "The param is too short.";
    } else{
        $h_param01 = trim($_POST["h_param01"]);
    }
    
    // Validate h_param03
    if(empty(trim($_POST["h_param03"]))){
        $h_param03_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param03"])) < 1){
        $h_param03_err = "The param is too short.";
    } else{
        $h_param03 = trim($_POST["h_param03"]);
    }
    // Validate h_param04
    if(empty(trim($_POST["h_param04"]))){
        $h_param04_err = "Please enter a validate param.";     
    } elseif(strtotime(trim($_POST["h_param04"])) == false){
        $h_param04_err = "time must be validate format.";
    } else{
        $h_param04 = trim($_POST["h_param04"]);
    }
    // Validate h_param05
    if(empty(trim($_POST["h_param05"]))){
        $h_param05_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param05"])) < 3){
        $h_param05_err = "The param is too short.";
    } else{
        $h_param05 = trim($_POST["h_param05"]);
    }
    // Validate h_param06
    if(empty(trim($_POST["h_param06"]))){
        $h_param06_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param06"])) < 1){
        $h_param06_err = "The param is too short.";
    } else{
        $h_param06 = trim($_POST["h_param06"]);
    }
    // Validate h_param07
    $h_param07 = trim($_POST["h_param07"]);
    if(empty(trim($_POST["h_param07"]))){
        $h_param07_err = "Please enter a validate param.";     
    } elseif(strlen(trim($_POST["h_param07"])) < 4){
        $h_param07_err = "time must be validate format.";
    } else{        
    }
    // Validate h_param08
    $h_param08 = trim($_POST["h_param08"]);

    $h_id = $h_param01;  // restore important parameters
    if($h_id < 1){
        $mysql_err = "No house is selected. Select house firstly.";
    }    
    // Check input errors before inserting in database
    elseif( empty($h_param01_err) && empty($h_param02_err) && empty($h_param03_err) && 
        empty($h_param04_err) && empty($h_param05_err) && empty($h_param06_err) && 
        empty($h_param07_err) && empty($h_param08_err) ){
            $mysql_err = "";             
        
            // Prepare an insert statement
            $sql = "INSERT INTO tb_states_global (s_country, s_state, s_abbreviation, s_postalcode, s_region, s_team, s_finished) VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ssssssd", $param_h_param01, 
                    $param_h_param03, $param_h_param04, $param_h_param05, $param_h_param06, 
                    $param_h_param07, $param_h_param08 );
                
                // Set parameters
                $param_h_param01 = $h_param01;
                $param_h_param03 = $h_param03;
                $param_h_param04 = $h_param04;
                $param_h_param05 = $h_param05;
                $param_h_param06 = $h_param06;
                $param_h_param07 = $h_param07;
                $param_h_param08 = $h_param08;
                
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // jump to manage one
                    header("location: ./states_manage.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function);
                    exit;
                } else{
                    $mysql_err = "Something went wrong. Please create a supplier again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
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
    <title>Create a house tax bill</title>
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
        <p><?php
                echo '<a href="'."./house_tax_create.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function.'">'        
                ?>Create</a> -> Review -> <?php 
                echo '<a href="'."./house_tax_manage.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function.'">'        
                ?>Manage</a> -> Close -> Reopen.</p>
        <h2>Create a house tax bill</h2>
        <p>To add a tax bill for your house, please fill all boxes and click the button Create now.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            
            <div class="form-group <?php echo (!empty($h_param01_err)) ? 'has-error' : ''; ?>">
                <label><?php 
                    echo '<td>'.'<a href="../controlpanel/houses_manage.php?query_h_id='.$h_id."&h_name=".$h_name."&h_function=".$h_function.'">'.
                    'House '.$h_name.'</a>'.'</td>'; 
                    ?></label>
                <input type="hidden" name="h_param01" class="form-control" value="<?php echo $h_param01; ?>">
                <input type="hidden" name="h_name" class="form-control" value="<?php echo $h_name; ?>">
                <span class="help-block"><?php echo $h_param01_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($h_param03_err)) ? 'has-error' : ''; ?>">
                <label>Tax receiver</label>
                <select id="h_param03" name="h_param03">
                    <?php
                        // suppliers
                        echo '<option value=0 '; 
                        if($h_param03 == 0) echo "selected"; 
                        echo ' >please select</option>';
                        for ($x = 0; $x < $suppliers_total; $x++) {
                            echo '<option value='.$suppliers_id_arr[$x].' '; 
                            if($h_param03 == $suppliers_id_arr[$x]) echo "selected";
                            echo ' >'.$suppliers_h_company_arr[$x].'</option>';
                        }
                    ?>
                </select>
                <span class="help-block"><?php echo $h_param03_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param04_err)) ? 'has-error' : ''; ?>">
                <label>Paid date</label>
                <input type="date" name="h_param04" class="form-control" value="<?php echo $h_param04; ?>">
                <span class="help-block"><?php echo $h_param04_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param05_err)) ? 'has-error' : ''; ?>">
                <label>Paid amount ($)</label>
                <input type="number" name="h_param05" class="form-control" value="<?php echo $h_param05; ?>">
                <span class="help-block"><?php echo $h_param05_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param06_err)) ? 'has-error' : ''; ?>">
                <label>Term</label>
                <select id="h_param06" name="h_param06">
                    <?php
                        // pay methods x7
                        echo '<option value=0 '; 
                        if($h_param06 == 0) echo "selected"; 
                        echo ' >please select</option>';
                        for ($x = 1; $x <= 7; $x++) {
                            echo '<option value='.$x.' '; 
                            if($h_param06 == $x) echo "selected";
                            if($x == 1) echo ' >'.'1. Summer'.'</option>';
                            if($x == 2) echo ' >'.'2. Winter'.'</option>';
                            if($x == 3) echo ' >'.'1. Quarter 1.'.'</option>';
                            if($x == 4) echo ' >'.'2. Quarter 2.'.'</option>';
                            if($x == 5) echo ' >'.'3. Quarter 3.'.'</option>';
                            if($x == 6) echo ' >'.'4. Quarter 4.'.'</option>';
                            if($x == 7) echo ' >'.'5. Quarter 5.'.'</option>';
                        }
                    ?>
                </select> 
                <span class="help-block"><?php echo $h_param06_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param07_err)) ? 'has-error' : ''; ?>">
                <label>Tax Year</label>
                <input type="number" name="h_param07" min="2000" max="2099" step="1" value="<?php echo $h_param07; ?>" />
                <span class="help-block"><?php echo $h_param07_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param08_err)) ? 'has-error' : ''; ?>">
                <label>Note</label>
                <input type="text" name="h_param08" class="form-control" value="<?php echo $h_param08; ?>">
                <span class="help-block"><?php echo $h_param08_err; ?></span>
            </div>            
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Create now">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p><?php echo '<span style="color:#F00;text-align:center;">'.$mysql_err.'</span>'; ?></p>
            <p><a href="../controlpanel/houses_list_all.php">Go to list all houses</a>.</p>
            <p><?php
                echo '<a href="'."./house_tax_create.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function.'">'        
                ?>Create</a> -> Review -> <?php 
                echo '<a href="'."./house_tax_manage.php?query_h_id=".$h_id."&h_name=".$h_name."&h_function=".$h_function.'">'        
                ?>Manage</a> -> Close -> Reopen.</p>
            <p>Copyright @2020 <a href="../">Data Public Organization</a>.</p>
        </form>
    </div>    
</body>
</html>