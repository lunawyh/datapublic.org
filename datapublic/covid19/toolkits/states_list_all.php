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

if(strpos($_SESSION["groups_in"], "admin") !== false){
    
} else {
    //header("location: ../welcome.php");
    //exit;
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
// suppliers
$suppliers_id_arr = $suppliers_h_company_arr = array();
$suppliers_total = 0;
// step 0, it's jumping from other pages
if(isset($_GET['query_h_id'])){
    $h_id = $_GET['query_h_id'];   
    if(isset($_GET['h_id_suppliers'])){
        $h_id_suppliers = $_GET['h_id_suppliers']; 
    }
    if(isset($_GET['h_name'])){
        $h_name = $_GET['h_name']; 
    }
}else {  // from POST
    // save important parameters
    if(isset($_POST['h_id'])) $h_id = trim($_POST["h_id"]);    
    if(isset($_POST['h_name'])) $h_name = trim($_POST["h_name"]);    
    $h_param22 = trim($_POST["h_param22"]);   // filtering
}

// step 3, identify which ID is used
$h_param01_arr = $h_param02_arr = $h_param03_arr = array();
$h_param04_arr = $h_param05_arr = $h_param06_arr = $h_param07_arr = array();
$h_param08_arr = $h_param09_arr = $h_param10_arr = array();
{  // or read database
    if(empty(trim($mysql_err))){
        // read house from tb_houses_global
        // Prepare a select statement
        $sql = "SELECT id, s_state, s_postalcode, s_region, s_team, s_finished FROM tb_states_global WHERE s_country = ? ORDER BY s_postalcode ASC";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_h_param00);            
            //
            {
                // Set parameters
                $param_h_param00 = 'USA';
            
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    /* store result */
                    mysqli_stmt_store_result($stmt);
                    
                    if(mysqli_stmt_num_rows($stmt) < 0){
                        $mysqli_err = "Oops! There is no record. Please create one.";
                    } else {
                        mysqli_stmt_bind_result($stmt, $h_param00, 
                            $h_param03, $h_param05,
                            $h_param06, $h_param07, $h_param08
                        );
                        $h_total = 0;                
                        while(mysqli_stmt_fetch($stmt)){      
                            if($h_param22 <= 0){                                
                            } else if($h_param22 == 1){
                                if($h_param06 == 'Northeast'){
                                } else { continue; }
                            } else if($h_param22 == 2){
                                if($h_param06 == 'Midwest'){
                                } else { continue; }
                            } else if($h_param22 == 3){
                                if($h_param06 == 'South'){
                                } else { continue; }
                            } else if($h_param22 == 4){
                                if($h_param06 == 'West'){
                                } else { continue; }
                            }
                            array_push($h_param00_arr, $h_param00);                           
                            array_push($h_param05_arr, $h_param05);   
                            array_push($h_param03_arr, $h_param03);                        
                            array_push($h_param06_arr, $h_param06);                       
                            array_push($h_param07_arr, $h_param07); 
                            array_push($h_param08_arr, $h_param08);  
                            $h_total ++;                  
                        }    
                        //$h_total = mysqli_stmt_num_rows($stmt); 
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
    if(isset($_POST['Edit'])) { 
        $selHouses = "";
        if(isset($_POST['formHouses'])) $selHouses = $_POST['formHouses'];
  
        if(empty($selHouses)) 
        {
            $mysql_err = "You didn't select any state!";
        } 
        else 
        {
            $nMembers = count($selHouses);            
            for($ii=0; $ii < $nMembers; $ii++)
            {
                $h_id_show = $selHouses[$ii];
                
                // only one will be selected
                header("location: states_manage.php?query_h_id=".$h_id_show."&h_name=".$h_name.
                    "&h_function=".$h_function);
                exit;  
            }
        }
    } 
    else if(isset($_POST['Import'])) {
        $mysql_err = "";             
        exit;
        // Prepare an insert statement
        $sql = "INSERT INTO tb_states_global (s_country, s_state, s_abbreviation, s_postalcode, s_region, s_team, s_finished) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // file('./usa_states.csv');
            if (($handle = fopen("./usa_states.csv", "r")) !== FALSE) {
                $row = 0;
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $row++;
                    if($row == 1) continue;

                    $num = count($data);
                    if($num < 8) continue;
                    //echo "<p> $num fields in line $row: <br /></p>\n";                
                    //for ($c=0; $c < $num; $c++) {
                    //    echo $data[$c] . "<br />\n";
                    //}                

                    // add to db
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "ssssssd", $param_h_param01, 
                        $param_h_param03, $param_h_param04, $param_h_param05, $param_h_param06, 
                        $param_h_param07, $param_h_param08 );
                    
                    // Set parameters
                    $ii = 0;
                    $param_h_param05 = $data[$ii++];
                    $param_h_param01 = $data[$ii++];
                    $param_h_param03 = $data[$ii++];
                    $param_h_param04 = $data[$ii++];
                    $ii++;
                    $param_h_param06 = $data[$ii++];
                    $param_h_param07 = $data[$ii++];
                    $param_h_param08 = $data[$ii++];
                    
                    // Attempt to execute the prepared statement
                    if(mysqli_stmt_execute($stmt)){
                        // silent
                    } else{
                        $mysql_err = "Something went wrong. Please create a supplier again later.";
                    }

                }
                fclose($handle);
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
    <title>COVIZ toolkits</title>
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
        echo "<td>"."<a href=../>DataPublic.org</a>"."<br>"."COVID-19"."<br>"."<a href=../../login>$account_info</a>"."</td>";
        echo "</table>";
    ?>
    <div class="wrapper">
        <p>Create -> Review -> Manage -> Close -> Reopen.</p>
        <h2>COVIZ toolkits</h2>
        <p>States in USA<?php echo $h_name.' in total of '.$h_total; ?> :</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($h_param12_err)) ? 'has-error' : ''; ?>">
                    <label for='formIdSub[]'>Filtered by region</label><br>                
                    <select id="h_param22" name="h_param22">
                        <?php
                            // subcategory for filtering only
                            echo '<option value=0 '; 
                            if($h_param22 == 0) echo "selected"; 
                            echo ' >Show all</option>';
                            for ($x = 1; $x <= 4; $x++) {                                
                                echo '<option value='.$x.' '; 
                                if($h_param22 == $x) echo "selected";
                                 if($x == 1) echo ' >'.'Northeast.'.'</option>';
                                elseif($x == 2) echo ' >'.'Midwest.'.'</option>';
                                elseif($x == 3) echo ' >'.'South.'.'</option>';
                                elseif($x == 4) echo ' >'.'West.'.'</option>';
                                else echo ' >'.'Show all.'.'</option>';
                            }
                        ?>
                    </select>  
                    <input type="submit" name="Filter" value="Filter"/>             
                    <span class="help-block"><?php echo $h_param12_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($h_param00_err)) ? 'has-error' : ''; ?>">                
                <input type="hidden" name="h_id" class="form-control" value="<?php echo $h_id; ?>" readonly="readonly">
                <input type="hidden" name="h_name" class="form-control" value="<?php echo $h_name; ?>" readonly="readonly">
                <input type="hidden" name="h_id_suppliers" class="form-control" value="<?php echo $h_id_suppliers; ?>" readonly="readonly">
                <input type="hidden" name="h_id_lease" class="form-control" value="<?php echo $h_id_lease; ?>" readonly="readonly">
                <span class="help-block"><?php echo $h_param00_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($houses_list_err)) ? 'has-error' : ''; ?>">
                <label for='formHouses[]'>Select one state that you want to view:</label><br>
                
                <?php
                    echo '<table border="0" width="960" align="center">';
                    //
                    $id_no = 0;                    
                    for($aa = 0; $aa <= $h_total - 1; $aa++){
                        if($id_no == 0){
                            echo '<td>'.' '.'</td>';
                            echo '<td>'.' '.'</td>';
                            echo '<td>'.'State'.'</td>';
                            echo '<td>'.'Full Name'.'</td>';
                            echo '<td>'.'Region'.'</td>';
                            echo '<td>'.'Team'.'</td>';
                            echo '<td>'.'Finished'.'</td>';
                            echo "</tr>";
                        }else if($id_no > 0 && ($id_no%10)==0 ){ // add group seperation
                            echo '<td>'.' '.'</td>';
                            echo '<td>'.' '.'</td>';
                            echo '<td>'.'<img src="../../img/icon_blue_line.png" alt="icon" />'.'</td>';
                            echo '<td>'.'<img src="../../img/icon_blue_line.png" alt="icon" />'.'</td>';
                            echo '<td>'.'<img src="../../img/icon_blue_line.png" alt="icon" />'.'</td>';
                            echo '<td>'.'<img src="../../img/icon_blue_line.png" alt="icon" />'.'</td>';
                            echo '<td>'.'<img src="../../img/icon_blue_line.png" alt="icon" />'.'</td>';
                            echo "</tr>";
                        }
                        // add items
                        echo '<td><input type="radio" name="formHouses[]" value='.$h_param00_arr[$aa].'><label>'.$h_param00_arr[$aa].'</label><br/></td>';
                        //
                        $f_name = str_replace(' ', '_', $h_param03_arr[$aa]);
                        echo '<td>'.'<img src="'.'../img/Seal_of_'.$f_name.'.svg.png'.'" alt="icon" />'.'</td>';  
                        echo '<td>'.' '.$h_param05_arr[$aa].'</td>';
                        //echo '<td>'.' '.$h_param03_arr[$aa].'</td>';
                        echo '<td>'.' '.'<a href="./states_manage.php?query_h_id='.$h_param00_arr[$aa].
                            "&h_name=".$h_param05_arr[$aa]."&h_function=".$h_function.'">'.
                            $h_param03_arr[$aa].'</a>'.'</td>';
                        echo '<td>'.' '.$h_param06_arr[$aa].'</td>';
                        echo '<td>'.' '.$h_param07_arr[$aa].'</td>';
                        echo '<td>'.' '.$h_param08_arr[$aa].'</td>';
                        echo "</tr>";
                        // 
                        $id_no ++;
                    }
                    echo "</table>";
                ?>
               
                <span class="help-block"><?php echo $houses_list_err; ?></span>
            </div>


            <div class="form-group">
                <input type="submit" class="btn btn-primary" name="Import" value="Import">
                <input type="submit" class="btn btn-primary" name="Edit" value="Edit">
            </div>
            <p><?php echo '<span style="color:#F00;text-align:center;">'.$mysql_err.'</span>'; ?></p>
            
            <p></p>
            <p>Create -> Review -> Manage -> Close -> Reopen.</p>
            <p><img src="../../img/ox_rent_qr100.png" alt="icon" />Copyright @2020 <a href="../">Data Public Organization</a>.</p>
        </form>
    </div>    
</body>
</html>