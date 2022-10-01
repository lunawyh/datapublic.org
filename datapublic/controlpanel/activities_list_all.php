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
$l_activites = "";
$l_activites_err = "";
$act_id_arr = $act_name_arr = $act_date_arr = $act_time_start_arr = $act_duration_arr = $act_place_arr = $act_description_arr = $act_state_arr = array();
$act_index = 0;
$act_total = 0;
$act_state = 0;
$act_state_str = $activities_list_err = "";
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
                ////
            }
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }
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
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    //echo "REQUEST_METHOD is POST";
    
    if(isset($_POST['Report'])) { 
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
        $selActivities = $_POST['formActivities'];
  
        if(empty($selActivities)) 
        {
            $activities_list_err = "You didn't select any activities!";
        } else {
            $act_id = $selActivities[0];
            for ($x = 0; $x < $act_total; $x++) {
                if($act_id == $act_id_arr[$x]){
                    $act_state = $act_state_arr[$x];
                    $act_state_str = get_state_name($act_state); 
                    break;
                }
            }
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
                        $activities_list_err = "No activite in the database.";
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
            $pdf->Cell(120,10, 'This is to certify that Mr./Miss '.$u_name.' born on '.$u_birthday.' '.$act_state_str.' working with',0,1);
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
    } elseif(isset($_POST['JoinNow'])) { 
        $selActivities = $_POST['formActivities'];
  
        if(empty($selActivities)) 
        {
            $activities_list_err = "You didn't select any activities!";
        } 
        else 
        {
            $nMembers = count($selActivities);            
            for($ii=0; $ii < $nMembers; $ii++)
            {
                $act_id = $selActivities[$ii];
                $i_index = 0;
                for ($x = 0; $x < $act_total; $x++) {
                    if($act_id == $act_id_arr[$x]){
                        $i_index = $x;
                        $act_state = $act_state_arr[$x];
                        break;
                    }
                }     
                // check $act_date is within today
                date_default_timezone_set('America/New_York');
                $delta = strtotime(date('Y-m-d')) - strtotime($act_date_arr[$i_index]);
                if($delta == 0){  // only permitted on the same day
                } else {
                    $activities_list_err = "Today is ".date('Y-m-d').". The activity is scheduled on ".$act_date_arr[$i_index];
                    //$act_state = 0;
                }   
                
                if($act_state == 20 && empty($activities_list_err)){
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
                            $activities_list_err = "Something went wrong. Please join again later.";
                        }

                        // Close statement
                        mysqli_stmt_close($stmt);
                    } else {
                        $activities_list_err = "mysqli went wrong. Please join again later.";
                    }
                } elseif(empty($activities_list_err)){
                    if($act_state == 10)
                        $activities_list_err = "You are not admitted yet. Please contact organizor.";
                    elseif($act_state == 30)
                        $activities_list_err = "You have joined. Enjoy a happy ativity";
                    elseif($act_state == 40)
                        $activities_list_err = "You have finished. Welcome to register a new ativity";    
                }
                break;  // only the 1st will be used
            }
        }
        if(empty($activities_list_err) ){
                // read back the state of the 1st activity, update act_state_str
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
        // after it's approved by admin, you can confirm this activity is finished, so the value is changed to 30

    } elseif(isset($_POST['Unregister'])) {  // Unregister now
        $selActivities = $_POST['formActivities'];
  
        if(empty($selActivities)) 
        {
            $activities_list_err = "You didn't select any activities!";
        } 
        else 
        {
            $nMembers = count($selActivities);            
            for($ii=0; $ii < $nMembers; $ii++)
            {
                $act_id = $selActivities[$ii];
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
                        
                    } else{
                        $activities_list_err = "Something went wrong. Please try again later.";
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);
                }
                break;  // only the 1st will be used
            }
        }
        if(empty($activities_list_err) ){
            // Redirect to activity list page again
            header("location: activities_list_all.php");        
        }    
    } elseif(isset($_POST['ViewSingle'])) { 
        header("location: activities_list.php");
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
            <div class="form-group <?php echo (!empty($activities_list_err)) ? 'has-error' : ''; ?>">
                <label for='formActivities[]'>Select one activity that you want to change:</label><br>
                
                <?php
                    echo '<table border="0" width="960" align="center">';
                    // $act_id_arr = $act_name_arr = $act_date_arr = $act_time_start_arr = $act_duration_arr =
                    // $act_place_arr = $act_description_arr = $act_state_arr
                    for($aa = 0; $aa <= $act_total - 1; $aa++){
                        
                        $act_state = $act_state_arr[$aa];
                        $act_state_str = get_state_name($act_state);                          
                        //
                        echo '<td><input type="radio" name="formActivities[]" value='.$act_id_arr[$aa].'><label>'.$act_id_arr[$aa].'</label><br/></td>';
                        echo '<td>'.'<img src="../../img/icon_mission.png" alt="icon" />'.'<a href="./activities_list.php?query_act_id='.$act_id_arr[$aa].'">'.$act_name_arr[$aa].'</a>'.'</td>';
                        echo '<td>'.$act_date_arr[$aa].'</td>';
                        echo '<td>'.$act_time_start_arr[$aa].'</td>';
                        echo '<td>'.$act_duration_arr[$aa].' minutes'.'</td>';
                        echo '<td>'.$act_place_arr[$aa].'</td>';
                        echo '<td>'.$act_state_str.'</td>';
                        echo "</tr>";
                    }
                    echo "</table>";
                ?>
               
                <span class="help-block"><?php echo $activities_list_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-default" name="Report" value="Print report">
                <input type="submit" class="btn btn-primary" name="JoinNow" value="Join Now">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-default" name="Unregister" value="Unregister now">
                <input type="submit" class="btn btn-default" name="ViewSingle" value="View single">
            </div>
            <p> </p>
            <p>Created -> <a href="./activities_register.php">Register</a> -> Admitted -> <a href="./activities_list.php">Join</a> -> Finished. <a href="./activities_list.php">[My Activities]</a> <a href="./setting.php">[My Setting]</a></p>
            <p>Copyright @2020 <a href="../../">Data Public Organization</a>.</p>
        </form>
    </div>    
</body>
</html>