<!-- 
profile.php

This creates the profile page. Here, users will fill out their personal information,
information about their first job out of Wellesley and/or where they attend(ed) graduate school.
Users who are logged in will see what is currently entered into the database. They can update these

-->

<?php

session_start();// Starting Session

// initialiazing variables to what we want if the user is not logged in
$login = 'login'; //variable to determine login/logout button type at top of page
// Storing Session
$user_check = "";
$button = "Insert"; // variable to change the value of the submit button
// statement to instruct the users on how to use the page
$statement = "Please enter the following information and click 'Insert Data' below";
$disabled = 'required'; // variable to change the ability to edit the email text box

// editing variables to be what we want when the user is logged in
if(isset($_SESSION['login_user'])) {
    $user_check=$_SESSION['login_user'];
    $login = 'logout';
    $button = "Update";
    $statement = 'If you would like to update your information, please make any changes and click "Update Data" below';
    $disabled = 'disabled';
}
//echo "<p>User check is: ".$user_check;

echo '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<HTML lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name=author content="Elizabeth Hau">
    <meta name=description content="">
    <meta name=keywords content="">
    <link href="css/WellConnected.css" rel="stylesheet" type="text/css">
    <!-- <link rel="stylesheet" type="text/css" href="http://cs.wellesley.edu/~anderson/sda-style.css"> -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet" type="text/css">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet" type="text/css">
    

    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

    <!-- JavaScript file for password validation -->
    <script src="js/profile.js"></script>

    <style>
    #profile_div {

        padding-right: 50px;
        padding-bottom: 25px;
        padding-left: 50px;
    }

    .form-group {
        clear: both;
    }

    </style>


<title>Personal Profile</title>

</head>
<body>

<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="home.php">WellConnected</a>
    </div>
    <div>
<ul class="nav navbar-nav">
    <li><a href="home.php">Home</a></li>
    <li><a href="search.php">Search</a></li>
    <li class="active"><a href="profile.php">Profile</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
        <li><a href="'.$login.'.php"><span class="glyphicon glyphicon-log-in"></span> '.$login.'</a></li>
      </ul>
    </div>
  </div>
</nav>

<div id = "profile_div">
<div class = "page-header">
  <h1>Personal Profile</h1>
</div>
<p>'.$statement.'</p>';

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once('../../cs304/hhau-dsn.inc');
$dbh = db_connect($hhau_dsn);

// Initializing id of new items entered to database for later use
$userid = 0;
$jobid = 0;
$companyid = 0;
$gradschoolid = 0;

$array = array('name','email','password','confirm_password', 'classyear','major','minor','title',
    'company','field','location','startDate','endDate','gradschool','location2',
    'gradYear','degree','program'); // array of values required in the post array
checkPost($array);


/*This statement does the main work of the page. It calls the functions that enter information
for a new user or update existing data in the database.
*/ 
if(isset($_POST['submit'])){
    if($user_check == "") { // inserts a new user and their information into the database
        editUser(true);
        editJob(true);
        editCompany(true);
        editEmployment(true);
        editGradSchool(true);
    } else { // updates a new user and their information into the database
        editUser(false);
        editJob(false);
        editCompany(false);
        editEmployment(false);
        editGradSchool(false);
    }
}

/* This function checks to see whether keys exist in the post array. For each key passed in, if it is not in the
post array, it is initialized to the empty string.
*/
function setEmpty($array) {
    foreach ($array as $key) {
        if (!isset($_POST[$key])) {
            $_POST[$key] = "";
        }
    }
}

/* This function adds keys to the post array. If the user is not logged in, the setEmpty method is called. If the 
user is logged in, existing data in the database about the user is pulled and is entered into the post array if the
key is not set, i.e. when the form has not been submitted.
*/
function checkPost($keyArray) {
    global $dbh;
    global $user_check;
    global $userid;
    global $jobid;
    global $companyid;
    global $gradschoolid;
    global $addjob;
    if ($user_check == ""){
        setEmpty($keyArray);
    } else {
        // getting user's personal information
        $sqlUser = "SELECT * FROM user WHERE email = ?";
        $valuesUser = array($user_check);
        $resultUser = prepared_query($dbh,$sqlUser,$valuesUser);
        if(($resultUser -> numRows())==1) {
            $resultUser = $resultUser -> fetchRow(MDB2_FETCHMODE_ASSOC);
            $userid = $resultUser['uid'];
            foreach ($resultUser as $key => $value) {
                if (!isset($_POST[$key])) {
                    $_POST[$key] = $resultUser[$key];
                }
            }
            // if there was a password in the database, set the text box for 'confirm password' to be the 
            // same as the password
            if (isset($_POST['password']) && $_POST['password'] != "") {
                $_POST['confirm_password'] = $_POST['password'];
            }
            // getting user's job, company, employment information
            $sqlJob = "SELECT * FROM job, company, employment WHERE job.jid=(SELECT jid FROM employment WHERE uid=?) 
                and company.cid=(SELECT cid FROM employment WHERE uid=?) and employment.uid=?";
            $valuesJob = array($userid,$userid,$userid);
            $resultJob = prepared_query($dbh,$sqlJob,$valuesJob);
            if(($resultJob -> numRows())==1) {
                $resultJob = $resultJob -> fetchRow(MDB2_FETCHMODE_ASSOC);
                // altering key to match the value in the form
                $resultJob['company'] = $resultJob['name'];
                unset($resultJob['name']);
                $jobid = $resultJob['jid'];
                $companyid = $resultJob['cid'];
                foreach ($resultJob as $key => $value) {
                    if (!isset($_POST[$key])) {
                        $_POST[$key] = $resultJob[$key];
                    }
                }
            } else {
                // setting post array keys to be empty because user is logged in but does not have data on 
                // job/company/employment
                $array = array('title','field','location','company','startDate','endDate');
                setEmpty($array);
            }
            // getting education, gradschool information 
            $sqlGrad = "SELECT * FROM gradschool, education WHERE gradschool.gid=(SELECT gid FROM education WHERE uid=?) 
            and education.uid=?";
            $valuesGrad = array($userid,$userid);
            $resultGrad = prepared_query($dbh,$sqlGrad,$valuesGrad);
            if(($resultGrad -> numRows())==1) {
                $resultGrad = $resultGrad -> fetchRow(MDB2_FETCHMODE_ASSOC);
                // altering keys to match the values in the form
                $resultGrad['gradschool'] = $resultGrad['name'];
                $resultGrad['location2'] = $resultGrad['location'];
                unset($resultGrad['name']);
                unset($resultGrad['location']);
                $gradschoolid = $resultGrad['gid'];
                foreach ($resultGrad as $key => $value) {
                    if (!isset($_POST[$key])) {
                        $_POST[$key] = $resultGrad[$key];
                    }
                }
            } else {
                // setting post array keys to be empty because user is logged in but does not have data on 
                // gradschool/education
                $array = array('gradschool','location2','gradYear','degree','program');
                setEmpty($array);
            }

        } else {
            echo "<p style='color:red'>Error: this database has two users with the same email. Please contact an administrator.";
        }
        
    }
}


/* This function edits the user table. If a name has been entered, the function executes. The form already 
requires most of the fields to be entered, so we do not check that all fields are not empty.
If the user is not logged into an account,
the information about the user is entered into the database. This handles the cases where a major and/or 
minor is or is not entered, i.e. no information is entered and the database automatically enters it as null. 
If the user is logged into their account, the information is updated. In this case, empty strings can be
inserted. We decided to do this in this version of the project to easily replace information the user wants
deleted (while not being tedious with many cases).
The variable that is passed in, $insert, indicates whether the information should be inserted or not (ie updated).
*/
function editUser($insert){
    global $userid;
    global $dbh;
    if(isset($_POST['name']) && ($_POST['name'] != "")) {
        $_POST['name'] = htmlspecialchars($_POST['name'],ENT_COMPAT,"UTF-8");
        $_POST['email'] = htmlspecialchars($_POST['email'],ENT_COMPAT,"UTF-8");
        $_POST['classyear'] = htmlspecialchars($_POST['classyear'],ENT_COMPAT,"UTF-8");
        $_POST['password'] = htmlspecialchars($_POST['password'],ENT_COMPAT,"UTF-8");
        $_POST['confirm_password'] = htmlspecialchars($_POST['confirm_password'],ENT_COMPAT,"UTF-8");
        $_POST['major'] = htmlspecialchars($_POST['major'],ENT_COMPAT,"UTF-8");
        $_POST['minor'] = htmlspecialchars($_POST['minor'],ENT_COMPAT,"UTF-8");
        if(!$insert) { // if the user is logged in, update their information
            $_POST['confirm_password'] = $_POST['password'];
            if ($_POST['password'] != $_POST['confirm_password']) {
                    echo "<p style = 'color:red'>Error: Passwords don't match.";
            } else {
                $sql = "UPDATE user SET password=?, name=?, classyear=?, major=?, minor=? WHERE uid = ?";
                $values = array($_POST['password'], $_POST['name'], $_POST['classyear'], $_POST['major'], $_POST['minor'],
                    $userid);
                prepared_statement($dbh,$sql,$values);
            }
        } else { // if the user is not logged in, submit their information
            if(strpos($_POST['email'], '@wellesley.edu') == false) { // determines that entered email is @wellesley.edu
                echo "<p style='color:red'>Error: please enter a valid email address.";
            } else {
                $sql = "SELECT email FROM user where email=?";
                $values = array($_POST['email']);
                $result = prepared_query($dbh,$sql,$values);
                $numrows = $result -> numRows();
                
                if (($result -> numRows()) != 0) {
                    echo "<p style = 'color:red'>Error: an account under this email account already exists.";
                } else if ($_POST['password'] != $_POST['confirm_password']) {
                    echo "<p style = 'color:red'>Error: Passwords don't match.";
                } else {
                    if($_POST['minor'] != "") { // User has entered a minor
                        if($_POST['major'] == "") { // but not a major
                            echo "<p style='color:red'>Error: please enter your first undergraduate major";
                        } else { // and a major
                            if($insert) { // if we are inserting
                                $sql = "INSERT INTO user (email, password, name, classyear, major, minor) 
                                  VALUES (?,?,?,?,?,?)";
                                $values = array($_POST['email'], $_POST['password'], $_POST['name'], $_POST['classyear'],
                                    $_POST['major'], $_POST['minor']);
                                prepared_statement($dbh,$sql,$values);
                                $userid = getId();
                                //echo "<p>Personal information has been inserted";
                                //echo "<p>User id is: ";
                                //echo $userid;
                                
                                // ends the current session and creates a new one using the given email now that 
                                // an account exists
                                session_destroy();
                                session_start();
                                $_SESSION['login_user']=$_POST['email'];
                            }
                        }
                    } else { // User has not entered a minor
                        if($_POST['major'] != "") {// and has not entered a major
                            if($insert){
                                $sql = "INSERT INTO user (email, password, name, classyear, major) 
                                    VALUES (?,?,?,?,?)";
                                $values = array($_POST['email'], $_POST['password'], $_POST['name'], $_POST['classyear'],
                                    $_POST['major']);
                                prepared_statement($dbh,$sql,$values);
                            }
                        } else { // but has entered a major
                            if($insert){
                                $sql = "INSERT INTO user (email, password, name, classyear) 
                                    VALUES (?,?,?,?)";
                                $values = array($_POST['email'], $_POST['password'], $_POST['name'], $_POST['classyear']);
                                prepared_statement($dbh,$sql,$values);
                            }
                        }
                        $userid = getId();
                        //echo "<p>Personal information has been inserted";
                        //echo "<p>User id is: ";
                        //echo $userid;

                        // ends the current session and creats a new one using the given email now that an account exists
                        session_destroy();
                        session_start();
                        $_SESSION['login_user']=$_POST['email'];
                    }
                }
            }
        }
    }
}

/* This function edits the job table. If a job title, field, or location has been entered, the function executes. 
The function checks that all fields are not empty. If a field is empty, an error message is printed for the user.
If the user is not logged into an account, the information about the user's job is entered into the database. 
If the user is logged into their account, the information is updated. In this case, empty strings can be
inserted. We decided to do this in this version of the project to easily replace information the user wants
deleted (while not being tedious with many cases).
The variable that is passed in, $insert, indicates whether the information should be inserted or not (ie updated).
*/
function editJob($insert) {
    global $jobid;
    global $dbh;
    global $userid;
    if((isset($_POST['title']) && ($_POST['title'] != "")) || 
        (isset($_POST['field']) && ($_POST['field'] != "")) || 
        (isset($_POST['location']) && ($_POST['location'] != "")) ){
        $executed = true; // boolean to determine if should be inserted into table
        $_POST['title'] = htmlspecialchars($_POST['title'],ENT_COMPAT,"UTF-8");
        if ($_POST['title'] == "") {
            echo "<p style='color:red'>Error: Please enter a job title.";
            $executed = false;
        }
        $_POST['field'] = htmlspecialchars($_POST['field'],ENT_COMPAT,"UTF-8");
        if ($_POST['field'] == "") {
            echo "<p style='color:red'>Error: Please enter a field.";
            $executed = false;
        }
        $_POST['location'] = htmlspecialchars($_POST['location'],ENT_COMPAT,"UTF-8");
        if ($_POST['location'] == "") {
            echo "<p style='color:red'>Error: Please enter your job's location.";
            $executed = false;
        }
        if ($executed) {
            // if the user is logged in and they already have a job already entered in the database
            if($insert) { // we want to insert or add a job
                $sqlJob = "INSERT INTO job (title, field, location) VALUES (?,?,?)";
                $valuesJob = array($_POST['title'], $_POST['field'], $_POST['location']);
                prepared_statement($dbh, $sqlJob, $valuesJob);
                $jobid = getId();
                //echo "<p>Job information has been inserted";
                //echo "<p>Job id is: ";
                //echo $jobid;
            } else{ // if the user is not logged in or is logged in but has never entered a job before
                $sql = "UPDATE job SET title=?, field=?, location=? WHERE jid=(SELECT jid from employment WHERE uid=?)";
                $values = array($_POST['title'], $_POST['field'], $_POST['location'], $userid);
                prepared_statement($dbh,$sql,$values);
                //echo "<p>Job information has been updated.";
                
            }
        }  
    }
}

/* This function edits the company table. If a company has been entered, the function executes. 
If the user is not logged into an account, the information about the company is entered into the database. 
If the user is logged into their account, the information is updated. In this case, empty strings can be
inserted. We decided to do this in this version of the project to easily replace information the user wants
deleted (while not being tedious with many cases).
The variable that is passed in, $insert, indicates whether the information should be inserted or not (ie updated).
*/
function editCompany($insert){
    global $companyid;
    global $dbh;
    global $userid;

    if(isset($_POST['company']) && $_POST['company'] != ""){
        $_POST['company'] = htmlspecialchars($_POST['company'],ENT_COMPAT,"UTF-8");
        if($insert){
            $sqlComp = "INSERT INTO company (name) VALUES (?)";
            $valuesComp = array($_POST['company']);
            prepared_statement($dbh,$sqlComp,$valuesComp);
            $companyid = getId();
            //echo "<p>Company information has been inserted";
            //echo "<p>Company id is: ";
            //echo $companyid;
        } else {
            $sql = "UPDATE company SET name=? WHERE cid=(SELECT cid from employment WHERE uid=?)";
            $values = array($_POST['company'], $userid);
            prepared_statement($dbh,$sql,$values);
            //echo "<p>Company information has been updated.";
        }
    } 
}

/* This function edits the employment table. If a job title and/or company has been entered, the function executes. 
The function checks whether a job, company, or both job and company have been entered (it does not require both).
We require that a start date be entered. End dates are not required as jobs may be current.
If the user is not logged into an account, the information about the user's employment is entered into the database. 
If the user is logged into their account, the information is updated. In this case, empty strings can be
inserted. We decided to do this in this version of the project to easily replace information the user wants
deleted (while not being tedious with many cases).
The variable that is passed in, $insert, indicates whether the information should be inserted or not (ie updated).
*/
function editEmployment($insert) {
    global $userid;
    global $jobid;
    global $companyid;
    global $dbh;
    if(($jobid != 0) || ($companyid != 0)){
        $sd = true; // boolean if start date was entered
        $_POST['startDate'] = htmlspecialchars($_POST['startDate'],ENT_COMPAT,"UTF-8");
        if ($_POST['startDate'] == "") {
            echo "<p style='color:red'>Error: Please enter a start date.";
            $sd = false;
        }
        $ed = true; // boolean if end date was entered
        $_POST['endDate'] = htmlspecialchars($_POST['endDate'],ENT_COMPAT,"UTF-8");
        if ($_POST['endDate'] == "") {
            $ed = false;
        }
        if($insert){
            if ($sd) { // if a start date was entered
                if($ed) { // but an end date was not
                    if($companyid == 0) { // no company was entered, but a job was
                        $sql = "INSERT INTO employment (uid, jid, startDate, endDate) VALUES (?,?,?,?)";
                        $values = array($userid, $jobid, $_POST['startDate'], $_POST['endDate']);
                        prepared_statement($dbh,$sql,$values);
                        //echo "<p style='color:blue'>Employment information has been inserted";
                    } else if($jobid == 0) { // no job was entered, but a company was
                        $sql = "INSERT INTO employment (uid, cid, startDate, endDate) VALUES (?,?,?,?)";
                        $values = array($userid, $companyid, $_POST['startDate'], $_POST['endDate']);
                        prepared_statement($dbh,$sql,$values);
                        //echo "<p style='color:blue'>Employment information has been inserted";
                    } else { // both job and company entered
                        $sql = "INSERT INTO employment (uid, cid, jid, startDate, endDate) VALUES (?,?,?,?,?)";
                        $values = array($userid, $companyid, $jobid, $_POST['startDate'], $_POST['endDate']);
                        prepared_statement($dbh,$sql,$values);
                        //echo "<p style='color:blue'>Employment information has been inserted";
                    }
                } else { // and an end date was entered
                    if($companyid == 0) { // no company was entered, but a job was
                        $sql = "INSERT INTO employment (uid, jid, startDate) VALUES (?,?,?)";
                        $values = array($userid, $jobid, $_POST['startDate']);
                        prepared_statement($dbh,$sql,$values);
                        //echo "<p style='color:blue'>Employment information has been inserted";
                    } else if($jobid == 0) { // no job was entered, but a company was
                        $sql = "INSERT INTO employment (uid, cid, startDate) VALUES (?,?,?)";
                        $values = array($userid, $companyid, $_POST['startDate']);
                        prepared_statement($dbh,$sql,$values);
                        //echo "<p style='color:blue'>Employment information has been inserted";
                    } else { // both job and company entered
                        $sql = "INSERT INTO employment (uid, cid, jid, startDate) VALUES (?,?,?,?)";
                        $values = array($userid, $companyid, $jobid, $_POST['startDate']);
                        prepared_statement($dbh,$sql,$values);
                        //echo "<p style='color:blue'>Employment information has been inserted";
                    }
                }
            }
        } else { // update entry
            $sql = "UPDATE employment SET startDate=?, endDate=? WHERE uid=?";
            $values = array($_POST['startDate'], $_POST['endDate'], $userid);
            prepared_statement($dbh,$sql,$values);
            //echo "<p>Employment information has been updated";
        }
    }
}


/* This function edits the gradschool and education table. If a grad school name has been entered, the function executes. 
If the user is not logged into an account, the information about the gradschool and education are entered into the database. 
If the user is logged into their account, the information is updated. The empty strings can be inserted for any of the fields
at any time. We decided to do this in this version of the project to easily replace information the user wants
deleted (while not being tedious with many cases).
The variable that is passed in, $insert, indicates whether the information should be inserted or not (ie updated).
*/
function editGradSchool($insert) {
    global $userid;
    global $gradschoolid;
    global $dbh;
    if(isset($_POST['gradschool']) && ($_POST['gradschool'] != "")) {
        $_POST['gradschool'] = htmlspecialchars($_POST['gradschool'],ENT_COMPAT,"UTF-8");
        $_POST['location2'] = htmlspecialchars($_POST['location2'],ENT_COMPAT,"UTF-8");
        $_POST['gradYear'] = htmlspecialchars($_POST['gradYear'],ENT_COMPAT,"UTF-8");
        $_POST['degree'] = htmlspecialchars($_POST['degree'],ENT_COMPAT,"UTF-8");
        $_POST['program'] = htmlspecialchars($_POST['program'],ENT_COMPAT,"UTF-8");
        if($insert){
            $sql = "INSERT INTO gradschool (name,location) VALUES (?,?)";
            $values = array($_POST['gradschool'], $_POST['location2']);
            prepared_statement($dbh,$sql,$values);
            $gradschoolid = getId();
            //echo "<p>Graduate School information has been inserted";
            //echo "<p>Graduate School id is: ";
            //echo $gradschoolid;

            $sqlEdu = "INSERT INTO education (uid, gid, gradYear, degree, program) VALUES (?,?,?,?,?)";
            $valuesEdu = array($userid, $gradschoolid, $_POST['gradYear'], $_POST['degree'], $_POST['program']);
            prepared_statement($dbh,$sqlEdu,$valuesEdu);
            //echo "<p>Education information has been inserted";
        } else {
            // updating gradschool table
            $sql = "UPDATE gradschool SET name=?, location=? WHERE gid=?";
            $values = array($_POST['gradschool'], $_POST['location2'], $gradschoolid);
            prepared_statement($dbh,$sql,$values);
            //echo "<p>Graduate School information has been updated";

            // updating education table
            $sql = "UPDATE education SET gradYear=?, degree=?, program=? WHERE gid=?";
            $values = array($_POST['gradYear'], $_POST['degree'], $_POST['program'], $gradschoolid);
            prepared_statement($dbh,$sql,$values);
            //echo "<p>Education information has been updated";
        }
    }
}

// This function returns the id of the last inputted item into the database
function getId(){
    global $dbh;
    $sqlid = "SELECT LAST_INSERT_ID()";
    $rs = query($dbh,$sqlid);
    $row = $rs->fetchRow();
    return $row[0];
}

 
// Print lines to print the form, insertting the profile information the user has entered if the form was submitted
echo '<p><form role = "form" action="'.$_SERVER['PHP_SELF'].'" method="post">
    <fieldset>
        <legend>Personal Information:</legend>
        <div class = "form-group">
            <div class="col-xs-2">
                <label for = "name">Name: </label>
                <input class = "form-control" type="text" name = "name" id="name" value="'.$_POST['name'].'" required><br>
            </div>
        </div>

        <div class = "form-group" display = "block">
            <div class="col-xs-2" display = "block">
                <label for = "email">Email: (@wellesley.edu) </label>
                <input class = "form-control" type="text" name = "email" id="email" value="'.$_POST['email'].'" '
                .$disabled.'><br>
            </div>
        </div>

        <div class = "form-group">
            <div class="col-xs-2">
                <label for = "password">Password: </label>
                <input class = "form-control" type="password" name = "password" id="password" value="'.$_POST['password'].'" 
                required><br>
            </div>
        </div>

        <div class = "form-group">
            <div class="col-xs-2">
                <label for = "password">Confirm Password: </label>
                <input class = "form-control" type="password" name = "confirm_password" id="confirm_password" value="'.$_POST['confirm_password'].'" 
                required>
                <p id= "validate-status"></p><br>
            </div>
        </div>

        <div class = "form-group">
            <div class="col-xs-2">
                <label for = "classyear">Wellesley Class Year: </label>
                <input class = "form-control" type="text" name = "classyear" id="classyear" value="'.$_POST['classyear'].'" 
                required><br>
            </div>
        </div>

        <div class = "form-group">
            <div class = "col-xs-2">
                <label for = "major">Undergraduate Major: </label>
                <input class = "form-control" type="text" name = "major" id="major" value="'.$_POST['major'].'"><br>
            </div>
        </div>

        <div class = "form-group">
            <div class="col-xs-2">
                <label for = "major2">Second Undergraduate Major/Minor: </label>
                <input class = "form-control" type="text" name = "minor" id="minor" value="'.$_POST['minor'].'"><br>
            </div>
        </div>
    </fieldset>
    <p>
    <fieldset>
        <legend>Information About Your First Job Out of Wellesley:</legend>
        <div class = "form-group">
            <div class="col-xs-2">
                <label for = "job">Job Title: </label>
                <input class = "form-control" type="text" name = "title" id="title" value="'.$_POST['title'].'">
            </div>
        </div>
        <div class = "form-group">
            <div class="col-xs-2">
                <label for = "company">Company: </label>
                <input class = "form-control" type="text" name = "company" id="company" value="'.$_POST['company'].'">
            </div>
        </div>
        <div class = "form-group">
            <div class="col-xs-2">
                <label for = "field">Field of Work: </label>
                <input class = "form-control" type="text" name = "field" id="field" value="'.$_POST['field'].'">
            </div>
        </div>
        <div class = "form-group">
            <div class="col-xs-2">
                <label for = "location">Location (City, State): </label>
                <input class = "form-control" type="text" name = "location" id="location" value="'.$_POST['location'].'">
            </div>
        </div>
        <div class = "form-group">
            <div class="col-xs-2">
                <label for = "start-date">Start Date (MM/YYYY): </label>
                <input class = "form-control" type="text" name = "startDate" id="startDate" value="'.$_POST['startDate'].'">
            </div>
        </div>
        <div class = "form-group">
            <div class="col-xs-2">
                <label for = "end-date">End Date (MM/YYYY): </label>
                <input class = "form-control" type="text" name = "endDate" id="endDate" value="'.$_POST['endDate'].'">
            </div>
        </div>
    </fieldset>
    <p>
    <fieldset>
        <legend>Information About the Graduate School You Attended/Are Attending:</legend>
        <div class = "form-group">
            <div class="col-xs-2">
                <label for "gradschool">Graduate School: </label>
                <input class = "form-control" type="text" name = "gradschool" id="gradschool" value="'.$_POST['gradschool'].'">
            </div>
        </div>
        <div class = "form-group">
            <div class="col-xs-2">
                <label for = "degree">Degree: </label>
                <input class = "form-control" type="text" name = "degree" id="degree" value="'.$_POST['degree'].'">
                <span class="help-block">Ex: Masters, PhD</span>
            </div>
        </div>
        <div class = "form-group">
            <div class="col-xs-2">
                <label for = "program">Program: </label>
                <input class = "form-control" type="text" name = "program" id="program" value="'.$_POST['program'].'">
                <span class="help-block">Ex: English, Law, Business</span>
            </div>
        </div>
        <div class = "form-group">
            <div class="col-xs-2">
                <label for = "location">Location (City, State): </label>
                <input class = "form-control" type="text" name = "location2" id="location2" value="'.$_POST['location2'].'">
            </div>
        </div>
        <div class = "form-group">
            <div class="col-xs-2">
                <label for "gradYear">Graduation Year: </label>
                <input class = "form-control" type="text" name = "gradYear" id="gradYear" value="'.$_POST['gradYear'].'">
            </div>
        </div>
    </fieldset>
    <p><br>
<!-- Making the buttons -->
<button type="button" onclick=reset() class = "btn btn-danger">Reset</button>
<input type = "submit" value="'.$button.' Data" name = "submit" class = "btn btn-success"></input>  
</form>';


?>

</div> <!-- close profile div -->  

</body>
</html>
