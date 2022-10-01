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
// given state, get state name
function get_state_name($n_state=0){
    if($n_state <= 10) $state_str = "has registered to join";
    else if($n_state <= 20) $state_str = "has been admitted to join";
    else if($n_state <= 30) $state_str = "has joined";
    else $state_str = "has finished";
    return $state_str;
}

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
$act_state = 0;
$act_state_str = "";
// query all joined activities
if($act_total < 1){
    // Prepare a select statement
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
                $l_activites_err = "No activite is joined yet. Please join now.";
            } else{
                mysqli_stmt_bind_result($stmt, 
                    $act_id, $act_state
                );
                while(mysqli_stmt_fetch($stmt)){
                    array_push($act_id_arr, $act_id);
                    array_push($act_state_arr, $act_state);
                }               
                $act_total = mysqli_stmt_num_rows($stmt);
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
                $act_state = $act_state_arr[$i_index];
                $act_state_str = get_state_name($act_state);
                $act_index = $i_index;
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }
    // get all information of activities
    for ($i_index = 0; $i_index < $act_total; $i_index++){   
        $act_id = $act_id_arr[$i_index];

        // now get information of activity
        // Prepare a select statement
        $sql = "SELECT act_name,t_act_date,t_act_start,act_duration,act_place,act_description FROM tb_activities_global WHERE id = ?";
        
        if($stmt2 = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt2, "d", $param_act_id);
            
            // Set parameters
            $param_act_id = $act_id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt2)){
                // store result 
                mysqli_stmt_store_result($stmt2);
                
                if(mysqli_stmt_num_rows($stmt2) < 1){
                    $l_activites_err = "No activite in the database.";
                } else{
                    mysqli_stmt_bind_result($stmt2, 
                        $act_name, $act_date,
                        $act_time_start, $act_duration, $act_place, $act_description
                    );
                    // $act_id_arr = $act_name_arr = $act_date_arr = $act_time_start_arr = $act_duration_arr = 
                    // $act_place_arr = $act_description_arr = $act_state_arr
                    while(mysqli_stmt_fetch($stmt2)){
                        array_push($act_name_arr, $act_name);
                        array_push($act_date_arr, $act_date);
                        array_push($act_time_start_arr, $act_time_start);
                        array_push($act_duration_arr, $act_duration);
                        array_push($act_place_arr, $act_place);
                        array_push($act_description_arr, $act_description);                        
                    }        
                    //       
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt2);
        }
    }
    // set the current, after all are read
    $act_id = $act_id_arr[$act_index];
    $act_name = $act_name_arr[$act_index];
    $act_date = $act_date_arr[$act_index];
    $act_time_start = $act_time_start_arr[$act_index];
    $act_duration = $act_duration_arr[$act_index]; 
    $act_place = $act_place_arr[$act_index]; 
    $act_description = $act_description_arr[$act_index]; 
    $act_state = $act_state_arr[$act_index];

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
            $act_state = $act_state_arr[$i_index];
            $act_state_str = get_state_name($act_state);
        //
            $act_index = $i_index;
            $act_name_err = "";
        } else {
            $act_name_err = "No activity, please join now.";
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
            $act_state = $act_state_arr[$i_index];            
            $act_state_str = get_state_name($act_state);
            //
            $act_index = $i_index;
            $act_name_err = "";
        } else {
            $act_name_err = "No activity, please join at once.";
        }
        
    } elseif(isset($_POST['Report'])) { 
        // read user information
        {
            // Prepare a select statement
            $sql = "SELECT u_name,u_alias,u_birthday,u_school,u_grade,u_address_h,u_phone,u_description FROM tb_users_global WHERE username = ?";
            
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
                        $item_existing_err = "No detailed information is set yet. Please add now.";
                    } else{
                        mysqli_stmt_bind_result($stmt, 
                            $u_name,
                            $u_alias, $u_birthday, $u_school, $u_grade, 
                            $u_address_h, $u_phone, 
                            $u_description
                        );
                        while(mysqli_stmt_fetch($stmt)){
                            // there is only one
                        }               
                        
                    }
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }
        
                // Close statement
                mysqli_stmt_close($stmt);
            }
        }    
        // get current activity
        $i_index = 0;
        for ($x = 0; $x < $act_total; $x++) {
            if($_POST["act_id"] == $act_id_arr[$x]){
                $i_index = $x;
                break;
            }
        }
        
        if($i_index < $act_total){        
            $act_id = $act_id_arr[$i_index];
            $act_state = $act_state_arr[$i_index];
            $act_state_str = get_state_name($act_state);
            //
            $act_index = $i_index;
            $act_name_err = "";
        } else {
            $act_name_err = "No activity, please join today.";
        }
        if(empty($act_name_err) ){
            // now get information of activity
            // Prepare a select statement
            $sql = "SELECT id,act_name,t_act_date,t_act_start,act_duration,act_place,act_description,act_state FROM tb_activities_global WHERE id = ?";
            
            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "d", $param_act_id);
                
                // Set parameters
                $param_act_id = $act_id;
                
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    /* store result */
                    mysqli_stmt_store_result($stmt);
                    
                    if(mysqli_stmt_num_rows($stmt) < 1){
                        $act_name_err = "No activite in the database.";
                    } else{
                        mysqli_stmt_bind_result($stmt, 
                            $act_id, $act_name, $act_date,
                            $act_time_start, $act_duration, $act_place, $act_description, $act_state
                        );
                        while(mysqli_stmt_fetch($stmt)){
                        }               
                        // should be only one because id is unique 
                    }
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }
                // Close statement
                mysqli_stmt_close($stmt);
            }

            // create PDF
            require('../../plugins/fpdf.php');
            class volunteer_PDF extends FPDF
            {
                // Page header
                function Header()
                {
                    // Logo
                    $this->Image('../../img/icon_appstorego.png',10,6,30);
                    // Arial bold 15
                    $this->SetFont('Arial','B',15);
                    // Move to the right
                    $this->Cell(80);
                    // Title
                    $this->Cell(80,10,'Data Public Organization',1,0,'C');
                    // Line break
                    $this->Ln(20);
                }
                
                // Page footer
                function Footer()
                {
                    // Position at 2 cm from bottom
                    $this->SetY(-20);
                    // Arial italic 8
                    $this->SetFont('Arial','I',8);
                    // Page number
                    $this->Cell(0,5,'datapublic.org of page '.$this->PageNo(),0,1,'C');
                    $this->Cell(0,5,'datapublic.org@gmail.com',0,1,'C');
                    $this->Cell(0,5,'Troy MI USA 48098',0,1,'C');
                }
            }
            $pdf = new volunteer_PDF('L','mm','A4');
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',16);
            $pdf->Cell(80,10,' ', 0, 1);
            $pdf->SetFont('Arial','B',24);
            $pdf->Cell(0,10,'Volunteer Certificate',0,1,'C');  // title
            $pdf->SetFont('Arial','B',16);
            $pdf->Cell(80,10,' ', 0, 1);
            // content main
            //$act_name_err = $act_state." ".$act_state_str;  // for testing
            //if($act_state <= 10) $act_state_str = "registered to join";
            //else if($act_state <= 20) $act_state_str = "been admitted to join";
            //else if($act_state <= 30) $act_state_str = "joined";
            //else $act_state_str = "finished";
            $u_birthday_ym = date('Y-m', strtotime($u_birthday));  // convert to month year
            $pdf->Cell(120,10, 'This is to certify that Mr./Miss '.$u_name.' born in '.$u_birthday_ym.' '.$act_state_str.' working with',0,1);
            $pdf->Cell(80,10, 'Data Public Orgnization.', 0, 1);
            $pdf->Cell(80,10, 'As Student Volunteer, he/she works on '.$act_name.' on '.$act_date.' for',0,1);
            $pdf->Cell(80,10, 'public health. The activity starts at '.$act_time_start.' for '.strval($act_duration).' minutes at '.$act_place.'.',0,1);
            $pdf->Cell(80,10, 'During the service, we found him/her a hardworking and dedicated person. He/She would be an', 0, 1);
            $pdf->Cell(80,10, 'asset of any organization with whom he/she would be engaged in. Part of works are shown at', 0, 1);
            $pdf->Cell(80,10, 'http://www.DataPublic.org/covid19/.', 0, 1);
            $pdf->Cell(80,10, 'We wish him/her a brillant and successful career in his/her life.', 0, 1);
            $pdf->Cell(80,10, ' ', 0, 1);
            $pdf->Cell(80,10, ' ', 0, 1);
            $pdf->Cell(80,10, 'President:____Jeff Wang_____', 0, 1);
            $pdf->Cell(80,10, 'Data Public Orgnization', 0, 1);
            $pdf->Output();            
        }
    } elseif(isset($_POST['ViewAll'])) { 
        header("location: activities_list_all.php");
    } elseif(isset($_POST['JoinNow'])) { 
        // after it's approved by admin, you can confirm this activity is finished, so the value is changed to 30
        // looking for member in the array
        $i_index = 0;
        for ($x = 0; $x < $act_total; $x++) {
            if($_POST["act_id"] == $act_id_arr[$x]){
                $i_index = $x;
                break;
            }
        }
        
        if($i_index < $act_total){        
            $act_id = $act_id_arr[$i_index];
            $act_state = $act_state_arr[$i_index];
            $act_state_str = get_state_name($act_state);
            //
            $act_index = $i_index;
            $act_name_err = "";
        } else {
            $act_name_err = "No activity, please join today.";
        }
        // check $act_date is within today
        date_default_timezone_set('America/New_York');
        $delta = strtotime(date('Y-m-d')) - strtotime($act_date_arr[$i_index]);
        if($delta == 0){  // only permitted on the same day
        } else {
            $act_name_err = "Today is ".date('Y-m-d').". The activity is scheduled on ".$act_date_arr[$act_index];
        }
        //echo 'JoinNow '.$act_id;
        if($act_state == 20 && empty($act_name_err)){  
            // Prepare an update statement
            $sql = "UPDATE tb_activities_users SET act_state = ? WHERE username = ? and act_id = ?";
            
            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "dsd", $param_act_state, $param_username, $param_act_id);
            
                // Set parameters
                $param_act_state = $act_state + 10;
                $param_username = trim($_SESSION["username"]);
                $param_act_id = $act_id;
                        
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Redirect to activity list page again
                    //header("location: activities_list.php");
                } else{
                    $act_name_err = "Something went wrong. Please join again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            } else {
                $act_name_err = "mysqli went wrong. Please join again later.";
            }
            // read back the state of this activity, update act_state_str
            if(empty($act_name_err)){
                // Prepare a select statement
                $sql = "SELECT act_state FROM tb_activities_users WHERE username = ? and act_id = ?";
                
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
                        
                        if(mysqli_stmt_num_rows($stmt) < 1){
                            $l_activites_err = "No activite is joined yet. Please join now.";
                        } else{
                            mysqli_stmt_bind_result($stmt, 
                                $act_state
                            );
                            while(mysqli_stmt_fetch($stmt)){
                            }     // there is only one    
                            $act_state_str = get_state_name($act_state);  
                        }    
                    } else{
                        echo "Oops! Something went wrong. Please try again later.";
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);
                }
            }
        } elseif(empty($act_name_err)){
            if($act_state == 10)
                $act_name_err = "You are not admitted yet. Please contact organizor.";
            elseif($act_state == 30)
                $act_name_err = "You have joined. Enjoy a happy ativity";
            elseif($act_state == 40)
                $act_name_err = "You have finished. Welcome to register a new ativity";
        }
    } elseif(isset($_POST['Unregister'])) {  // Unregister now
        // looking for member in the array
        $i_index = 0;
        for ($x = 0; $x < $act_total; $x++) {
            if($_POST["act_id"] == $act_id_arr[$x]){
                $i_index = $x;
                break;
            }
        }
        
        if($i_index < $act_total){        
            $act_id = $act_id_arr[$i_index];
            $act_state = $act_state_arr[$i_index];
            $act_state_str = get_state_name($act_state);
            //
            $act_index = $i_index;
            $act_name_err = "";
        } else {
            $act_name_err = "No activity, please join today.";
        }
        // Check input errors before inserting in database
        if(empty($act_name_err) ){
            
            // Prepare an update statement
            $sql = "DELETE FROM tb_activities_users WHERE username = ? and act_id = ?";
            
            if($stmt = mysqli_prepare($link, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "sd", $param_username, $param_act_id);
            
                // Set parameters
                $param_username = trim($_SESSION["username"]);
                $param_act_id = $act_id;
                        
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Redirect to activity list page again
                    header("location: activities_list.php");
                } else{
                    echo "Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        }
    }
    if(empty($act_name_err && ((isset($_POST['Previous'])) || (isset($_POST['Next'])))) ){
        // now get information of activity
        // set the current, after all are read
        $act_id = $act_id_arr[$act_index];
        $act_name = $act_name_arr[$act_index];
        $act_date = $act_date_arr[$act_index];
        $act_time_start = $act_time_start_arr[$act_index];
        $act_duration = $act_duration_arr[$act_index]; 
        $act_place = $act_place_arr[$act_index]; 
        $act_description = $act_description_arr[$act_index]; 
        $act_state = $act_state_arr[$act_index];
    }
    // Close connection 
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activities in Data Public Organization</title>
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
        <h2>My activities in Data Public Organization</h2>
        <p>Activity No. <?php echo $act_index+1; ?> / <?php echo $act_total; ?> :</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($act_id_err)) ? 'has-error' : ''; ?>">
                
                <input type="hidden" name="act_id" class="form-control" value="<?php echo $act_id; ?>" readonly="readonly">
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
            <div class="form-group <?php echo (!empty($act_state_err)) ? 'has-error' : ''; ?>">
                <label>State</label>
                <input type="text" name="act_state_str" class="form-control" value="<?php echo $act_state_str; ?>" readonly="readonly">
                <span class="help-block"><?php echo $act_state_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-default" name="Previous" value="Previous">
                <input type="submit" class="btn btn-primary" name="Report" value="Print report">
                <input type="submit" class="btn btn-default" name="Next" value="Next">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-default" name="Unregister" value="Unregister now">
                <input type="submit" class="btn btn-primary" name="JoinNow" value="Join now">
                <input type="submit" class="btn btn-default" name="ViewAll" value="View all">
            </div>
            <p> </p>
            <p>Created -> <a href="./activities_register.php">Register</a> -> Admitted -> <a href="./activities_list.php">Join</a> -> Finished. <a href="./activities_list.php">[My Activities]</a> <a href="./setting.php">[My Setting]</a></p>
            <p>Copyright @2020 <a href="../../">Data Public Organization</a>.</p>
        </form>
    </div>    
</body>
</html>