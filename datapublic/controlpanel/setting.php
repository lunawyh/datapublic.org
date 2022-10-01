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
$u_name = $u_alias = $u_birthday = $u_school = $u_address_h = $u_phone = $u_description = "";
$u_grade = $user_id = 0;
$u_name_err = $u_alias_err = $u_birthday_err = $u_school_err = $u_address_h_err = $u_phone_err = $u_description_err = "";
$u_grade_err = $item_existing_err = "";
$info_hint = "After change items, click the button save now.";
// query existing
if(true){
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
                $info_hint = "After fill all items, click the button add now.";
            } else{
                mysqli_stmt_bind_result($stmt, 
                    $u_name,
                    $u_alias, $u_birthday_mdy, $u_school, $u_grade, 
                    $u_address_h, $u_phone, 
                    $u_description
                );
                while(mysqli_stmt_fetch($stmt)){
                    // there is only one
                }               
                $u_birthday = date('Y-m', strtotime($u_birthday_mdy));  // convert to month year
            }
        } else{
            $info_hint =  "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    //echo "REQUEST_METHOD is POST";
    
    if(isset($_POST['Previous'])) { 
        header("location: activities_register.php");
        
    } elseif(isset($_POST['Next'])) { 
        header("location: activities_list.php");
        
    } elseif(isset($_POST['Query'])) { 
        //act_query_all();
    } else {
        $info_hint = "Is checking data.";
        // Check if name is empty
        if(strlen(trim($_POST["u_name"])) < 5){
            $u_name_err = "Please enter your full legal name.";
        } else{
            $u_name = trim($_POST["u_name"]);
        }
        // Check if alias is empty
        if(empty(trim($_POST["u_alias"]))){
            $u_alias_err = "Please enter your lias name.";
        } else{
            $u_alias = trim($_POST["u_alias"]);
        }
        // Check if birthday is empty
        if(empty(trim($_POST["u_birthday"]))){
            $u_birthday_err = "Please enter your born date.";
        } else{
            $u_birthday = trim($_POST["u_birthday"]);
        }
        // Check if school is empty
        if(strlen(trim($_POST["u_school"])) < 5){
            $u_school_err = "Please enter your school name.";
        } else{
            $u_school = trim($_POST["u_school"]);
        }
        // Check if grade is empty
        if($_POST["u_grade"] < 0 || $_POST["u_grade"] > 30){
            $u_grade_err = "Please enter your grade.";
        } else{
            $u_grade = trim($_POST["u_grade"]);
        }
        // Check if home is empty
        if(strlen(trim($_POST["u_address_h"]) < 0)){
            $u_address_h_err = "Please enter right home address.";
        } else{
            $u_address_h = trim($_POST["u_address_h"]);
        }
        // Check if phone is empty
        if(strlen(trim($_POST["u_phone"]) < 10)){
            $u_phone_err = "Please enter your telephone number.";
        } else{
            $u_phone = trim($_POST["u_phone"]);
        }
        // Check if description is empty
        if(strlen(trim($_POST["u_description"])) < 16){
            $u_description_err = "Please enter your awesome description longer than 16 letters.";
        } else{
            $u_description = trim($_POST["u_description"]);
        }

        // Check input errors before inserting in database
        if(empty($u_name_err) && empty($u_alias_err) && empty($u_birthday_err) && empty($u_school_err) && empty($u_grade_err) && 
            empty($u_address_h_err) && empty($u_phone_err) && empty($u_description_err) ){            
            // update item in database
            if(empty($item_existing_err) ){
                $info_hint = "It is updating data.";
                // Prepare an update statement
                $sql = "UPDATE tb_users_global SET u_name = ?, u_alias = ?, u_birthday = ?, u_school = ?, u_grade = ?, u_address_h = ?, u_phone = ?, u_description = ? WHERE u_id = ?";
                
                if($stmt = mysqli_prepare($link, $sql)){
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "ssssdsssd", $param_u_name, $param_u_alias, $param_u_birthday, $param_u_school, $param_u_grade, 
                        $param_u_address_h, $param_u_phone, $param_u_description, $param_user_id );
                    
                    // Set parameters
                    $param_u_name = $u_name;
                    $param_u_alias = $u_alias;
                    $param_u_birthday = date('Y-m-d', strtotime($u_birthday));  // convert to year month day ;
                    $param_u_school = $u_school;
                    $param_u_grade = $u_grade;
                    $param_u_address_h = $u_address_h;
                    $param_u_phone = $u_phone;
                    $param_u_description = $u_description;
                    $param_user_id = $_SESSION["id"];
                            
                    // Attempt to execute the prepared statement
                    if(mysqli_stmt_execute($stmt)){
                        // Redirect to activity list page
                        //header("location: setting.php");
                        $info_hint = "Data is updated.";
                    } else{
                        echo "Something went wrong. Please try again later.";
                        $info_hint = "Item is not updated, check and update again";
                    }  

                    // Close statement
                    mysqli_stmt_close($stmt);
                }else {
                    $info_hint = "updating is not prepared.";
                }
            } else {   // create item in database
                $info_hint = "It is creating data.";
                // Prepare an insert statement
                $sql = "INSERT INTO tb_users_global (u_id, username, u_name, u_alias, u_birthday, u_school, u_grade, u_address_h, u_phone, u_description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                if($stmt = mysqli_prepare($link, $sql)){
                    $info_hint = "It is uploading data.";
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "dsssssdsss", $param_user_id, $param_username, $param_u_name, $param_u_alias, $param_u_birthday, 
                        $param_u_school, $param_u_grade, $param_u_address_h, $param_u_phone, $param_u_description );
                    
                    // Set parameters
                    $param_user_id = $_SESSION["id"];
                    $param_username = trim($_SESSION["username"]);
                    $param_u_name = $u_name;
                    $param_u_alias = $u_alias;
                    $param_u_birthday = $u_birthday;
                    $param_u_school = $u_school;
                    $param_u_grade = $u_grade;
                    $param_u_address_h = $u_address_h;
                    $param_u_phone = $u_phone;
                    $param_u_description = $u_description;
                    
                    // Attempt to execute the prepared statement
                    if(mysqli_stmt_execute($stmt)){
                        // Redirect to activity list page
                        //header("location: setting.php");
                        $info_hint = "Item is created and saved.";
                    } else{
                        echo "Something went wrong. Please try again later.";
                        $info_hint = "Item is not created and saved, check and add again";
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);
                } else {
                    $info_hint = "creating is not prepared.";
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
    <title>Setting in Data Public Organization</title>
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
        <h2>My account in Data Public Organization</h2>
        <p><?php echo $info_hint; ?></p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($u_name_err)) ? 'has-error' : ''; ?>">
                <label>Full name</label>
                <input type="text" name="u_name" class="form-control" value="<?php echo $u_name; ?>">
                <span class="help-block"><?php echo $u_name_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($u_alias_err)) ? 'has-error' : ''; ?>">
                <label>Alias</label>
                <input type="text" name="u_alias" class="form-control" value="<?php echo $u_alias; ?>">
                <span class="help-block"><?php echo $u_alias_err; ?></span>
            </div>    
            <div class="form-group <?php echo (!empty($u_birthday_err)) ? 'has-error' : ''; ?>">
                <label>Birthday (month and year)</label>
                <input type="month" name="u_birthday" class="form-control" value="<?php echo $u_birthday; ?>">
                <span class="help-block"><?php echo $u_birthday_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($u_school_err)) ? 'has-error' : ''; ?>">
                <label>School</label>
                <input type="text" name="u_school" class="form-control" value="<?php echo $u_school; ?>">
                <span class="help-block"><?php echo $u_school_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($u_grade_err)) ? 'has-error' : ''; ?>">
                <label>Grade</label>
                <input type="number" name="u_grade" class="form-control" value="<?php echo $u_grade; ?>">
                <span class="help-block"><?php echo $u_grade_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($u_address_h_err)) ? 'has-error' : ''; ?>">
                <label>Home address (optional)</label>
                <input type="text" name="u_address_h" class="form-control" value="<?php echo $u_address_h; ?>">
                <span class="help-block"><?php echo $u_address_h_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($u_phone_err)) ? 'has-error' : ''; ?>">
                <label>Telephone</label>
                <input type="text" name="u_phone" class="form-control" value="<?php echo $u_phone; ?>">
                <span class="help-block"><?php echo $u_phone_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($u_description_err)) ? 'has-error' : ''; ?>">
                <label>Personal Description</label>
                <textarea name="u_description" cols="100" rows="10"><?php echo $u_description; ?></textarea>
                <span class="help-block"><?php echo $u_description_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-default" name="Previous" value="Register activity">
                <input type="submit" class="btn btn-primary" name="Apply" value="Save now">
                <input type="submit" class="btn btn-default" name="Next" value="My activities">
            </div>
            <p> </p>
            <p>Created -> <a href="./activities_register.php">Register</a> -> Admitted -> <a href="./activities_list.php">Join</a> -> Finished. <a href="./activities_list.php">[My Activities]</a> <a href="./setting.php">[My Setting]</a></p>
            <p>Copyright @2020 <a href="../../">Data Public Organization</a>.</p>
        </form>
    </div>    
</body>
</html>