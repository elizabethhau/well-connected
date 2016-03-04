<!-- 
    WellConnected

    search.php

    This page creates the search page. This page allows users to search through the
    database and find other Wellesley people to connect to.
-->

<?php
require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once('../../cs304/hhau-dsn.inc');
$dbh = db_connect($hhau_dsn);

// Start the session

session_start();
// Storing Session
$user_check=$_SESSION['login_user'];

if(!isset($user_check)){

    header('Location: login.php'); // Redirecting To Login Page

}

?>

<!doctype html>
<html lang='en'>
<head> 
    <meta charset='utf-8'>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name=author content="Elizabeth Hau">
    
    <title>WellConnected Search Page</title>

    <!-- Bootstrap -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
<div class = "container">
<!-- Creating the navigation bar on the top -->
<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="home.php">WellConnected</a>
    </div>
    <div>
    <ul class="nav navbar-nav">
        <li><a href="home.php">Home</a></li>
        <li class="active"><a href="#">Search</a></li>
        <li><a href="profile.php">Profile</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
        <li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>
        <li><a href="profile.php"><span class="glyphicon glyphicon-user"></span> Update Profile</a></li>
    </ul>
    </div>
  </div>
</nav>

 <div class = "container"> 
    <div>
    <?php 
        // Welcome message after the user logs in
        $sql = "SELECT name, email FROM user WHERE email = ?";
        $values = array($user_check);
        $resultset = prepared_query($dbh,$sql,$values);

        while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
                    $name = $row['name'];
                    echo "<p><br><br><br>Welcome, $name";
                }
    ?>
    </div>
<center>

<div class = "page-header">
   <h1><span class = 'glyphicon glyphicon-search'></span> for <span class = 'glyphicon glyphicon-user'></span></h1>
</div>

<div id = "resultDiv">

<?php

// Here is where most of the work happens

$dbh = db_connect($hhau_dsn);

// search array will contain the fields that have been entered and the values that the user 
// enters
$search = array();

add2Search('major');
add2Search('gradschool');
add2Search('company');
add2Search('gradyear');
add2Search('location');

/* The add2Search function populates the search array depending on whether a search field 
 * has been filled in. This function takes in a parameter, $key, which is the categories 
 * that the users can search for. The parameter will be the key of the associative array 
 * $search and the value will be the data entered by the users.
 */
function add2Search($key) {
    global $search;
    if(isset($_GET[$key]) && $_GET[$key] != "") {
        $search[$key] = htmlspecialchars($_GET[$key],ENT_COMPAT,"utf-8");
    }
    else
        $_GET[$key] = "";
}

/* Print the form along with data last searched for in the text boxes. */
echo ' <div class = "panel panel-info">
    <form role = "form" method="GET" action = "'.$_SERVER['PHP_SELF'].'">
        <div class = "panel-heading">
         <fieldset>
            <legend>Enter information into any of the boxes below</legend> </div>
            <div class = "panel-content">
            <table class = "table table-hover table-condensed">
                <div class = "form-group">
                  <tr><td><label for="major" accesskey="m">(m) Major/ Minor:</label></td>
                      <td><input class = "form-control "type="text" name="major" id="major" value = '.$_GET['major'].'></td>
                      <td>Ex: Math, Computer Science, Economics</td></tr>
                </div>
                <div class = "form-group">
                  <tr><td><label for="gradyear" accesskey="y">(y) Graduate Year (from Wellesley):</label></td>
                      <td><input class = "form-control" type="text" name="gradyear" id="gradyear" value = '.$_GET['gradyear'].'></td>
                      <td>Ex: 2015</td></tr>
                </div>
                <div class = "form-group">
                  <tr><td><label for="gradschool" accesskey="g">(g) Graduate School:</label></td>
                      <td><input class = "form-control" type="text" name="gradschool" id="gradschool" value = '.$_GET['gradschool'].'></td>
                      <td>Ex: Harvard</td></tr>
                </div>
                <div class = "form-group">
                  <tr><td><label for="company" accesskey="c">(c) Company:</label></td>
                      <td><input class = "form-control" type="text" name="company" id="company" value = '.$_GET['company'].'></td>
                      <td>Ex: JP Morgan</td></tr>
                </div>
                <div class = "form-group">
                  <tr><td><label for="location" accesskey="l">(l) Location (Graduate school and/ or Company):</label></td>
                      <td><input class = "form-control" type="text" name="location" id="location" value = '.$_GET['location'].'></td>
                      <td>Ex: Boston</td></tr>
                </div>
            </table>
            </div> 
            <input type="submit" value="Search" name = "submit" class = "btn btn-info">

          </fieldset>
    </form>
    </div>
';

echo "<h4>The results from the search will be displayed here</h4>";

    // the everyone array contains every user who matched any search
    $everyone = array();

    // the result array contains only the user(s) who match all the search criteria
    $result = array();

    // if graduate schools is the only field that the user searches for, display information about the school(s)
    // Otherwise, search for people related to the gradschools and add them to $everyone
    if (isset($search['gradschool']) && count($search) == 1){
        
        $gradschools = fetchGradSchools($_GET['gradschool']);

    } else if (isset($search['gradschool'])) {

        $gradschools = fetchGradSchools($_GET['gradschool']); // list of schools w/ $gid => $name

        // for each school, fetch the students
        foreach($gradschools as $key => $value) { // in gradschools, $key: gid, $value: name of school

            $students = fetchStudents($key);

            $everyone = incrementKey($students,$everyone);

        }
    }

    // if company is the only category the user searches for, display information about the company
    // Otherwise, get a list of people associated with the company and add them to $everyone
    if(isset($search['company']) && count($search) == 1) {

        $companies = fetchCompanies($_GET['company']);

    } elseif (isset($search['company']) && count($search) > 1) {

        $companies = fetchCompanies($_GET['company']);
        foreach($companies as $key => $value) { // list of company ids
            $employees = fetchEmployees($key);

            $everyone = incrementKey($employees,$everyone);

        }
    } 


    /* For all the remaining cases, get the list of people who match the search criteria
     * and add them to $everyone
     */
    if(isset($search['major'])){
        
        $major = fetchmajor($_GET['major']);
        $everyone = incrementKey($major,$everyone);

    } 
    if (isset($search['gradyear'])) {

        $gradyear = fetchGradYear($_GET['gradyear']);
        $everyone = incrementKey($gradyear,$everyone);
        
    } 


    if (isset($search['location'])) {

        $list = fetchLocation($_GET['location']);
        $everyone = incrementKey($list,$everyone);
    }
    if (isset($_GET['uid'])){

         fetchUser($_GET['uid']);

    } 
    if (isset($_GET['gid'])) {

        fetchOneSchool($_GET['gid']);

    } 
    if (isset($_GET['cid'])) {

        fetchOneCompany($_GET['cid']);

    } 
    if(!empty($search) && count($search) > 1) {

        foreach($everyone as $key => $value){
            if($value == count($search)) 
                array_push($result,$key);
        }
        fetchResults($result);
    }

/* The incrementKey function takes in two parameters, an input array and an output array.
 * This function then counts the number of times a key appears in the output array and 
 * returns the output array. If a key already exists in the output array, increase the value by one
 * Otherwise, add the key to the output array and set the value to 1, meaning
 * the key appeared for the first time in the output array.
 */
function incrementKey($input,$output){
    foreach($input as $key => $value) {
        if(!array_key_exists($key, $output))
            $output[$key] = 1;
        else
            $output[$key] += 1;
    }
    return $output;
}

/* The fetchResults function takes in a parameter, $result, and prints out 
 * the list of people who match all the search criteria.
 * The function then returns the result.
 */
function fetchResults($result) {
    global $dbh;
    $len = count($result);

    echo "<div class = 'jumbotron well well-lg'><h2>Here's a list of people who match all the search criteria:</h2>";
    if($len==0) {
        echo "<p>Sorry, no results found.";
    } elseif($len == 1) {
        fetchUser($result[0]);
    } else {
        foreach($result as $key => $value) {
            $sql = "SELECT uid, name, classyear FROM user WHERE uid = ?";
            $values = array($value);
            $resultset = prepared_query($dbh,$sql,$values);

            while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
                $uid = $row['uid'];
                $name = $row['name'];
                $classyear = $row['classyear'];

                echo "<p><a href=".$_SERVER['PHP_SELF']."?uid=$uid>$name ($classyear)</a>";
            }
        }
    }
    echo "</div>";
    return $result;
}


/* This function searches for people's names in the database that are majoring in something that 
 * matches that of the user's input and returns the list of people who match the search.
 * The function takes in a parameter, $result, that is the user's input into the textbox.
 */
function fetchmajor($result){
   global $dbh;
   global $search;
   $people = array();
   $sql = "SELECT uid, name, email, classyear, major FROM user WHERE major like ? or minor like ?";
   $values=array("%".$result."%", "%".$result."%");

   $resultset = prepared_query($dbh,$sql,$values);
   $num_names = $resultset -> numRows();

   if(count($search) == 1) {
    echo "<div class = 'jumbotron well well-lg'><h3>".$num_names." name(s) found that match the search '$result'</h3>"; // displays the number of matches found
   }

    if($num_names == 0) {
        echo "<p>Sorry, there are no names that match the search '$result' ";
    }

    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $name = $row['name'];
        $email = $row['email'];
        $classyear = $row['classyear'];
        $major = $row['major'];
        $uid=$row['uid'];

        $people[$uid]=$uid;

        if(count($search) == 1) {
            if($num_names == 1) {
                fetchUser($uid); // displays the information
            } else {

                echo "<p><a href=".$_SERVER['PHP_SELF']."?uid=$uid>$name ($classyear)</a>";
            }
        }

    }
    if(count($search) == 1)
        echo "</div>";
    return $people;
}

/* This function searches for graduate schools in the database that are like that of the user's input.
 * The function takes in a parameter, $result, that is the user's input into the textbox.
 * This function also returns a list of graduate schools that match the search.
 */
function fetchGradSchools($result){
    global $dbh;
    global $search;
    $students = array();
    $schools = array();
    $sql = "SELECT gid,name,location FROM gradschool WHERE name like ?";
    $values=array("%".$result."%");

    $resultset = prepared_query($dbh,$sql,$values);
    $num_schools = $resultset -> numRows();

    if(count($search) == 1) {
        echo "<div class = 'jumbotron well well-lg'><h3>".$num_schools." school(s) match the search for '$result' as a graduate school</h3>";
    }
    
    if($num_schools == 0) {
          echo "<p>Sorry, there are no schools that match '$result'";
    }

    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $name=$row['name'];
        $location=$row['location'];
        $gid=$row['gid'];
        $schools[$gid] = $name;
        
        if(count($search) == 1) {
            if($num_schools == 1) {
                $students[$gid] = fetchOneSchool($gid); // displays the school information
            } else {
                echo "<p><a href=".$_SERVER['PHP_SELF']."?gid=$gid>$name </a>";
            }
        }
    }
    if(count($search) == 1)
        echo "</div";

    return $schools;
}



/* This function searches for companies in the database that are like that of the user's input.
 * The function takes in a parameter, $result, that is the user's input into the textbox.
 * This function returns a list of companies that match the search result.
 */
function fetchCompanies($result){
    global $dbh;
    global $search;
    $people = array();
    $companies = array();
    $sql = "SELECT cid,name FROM company WHERE name like ?";
    $values=array("%".$result."%");

    $resultset = prepared_query($dbh,$sql,$values);
    $num_companies = $resultset -> numRows();

    if(count($search) == 1) {
        echo "<div class = 'jumbotron well well-lg'><h3>".$num_companies." company/ companies found for companies like '$result'</h3>";
    }

    if($num_companies == 0) {
        echo "<p>Sorry, there are no companies that match '$result'";
    }

    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $name=$row['name'];
        $cid=$row['cid'];
        $companies[$cid] = $name;
        if(count($search) == 1){
            if($num_companies == 1) {
                $people[$cid] = fetchOneCompany($cid); // displays the company information
            } else {
                echo "<p><a href=".$_SERVER['PHP_SELF']."?cid=$cid>$name </a>";
            }

        }

    }
    if(count($search) == 1)
        echo "</div>";
    return $companies;
}

/* This function searches for people in the database who are of a certain classyear 
 * provided by the user and returns the people that match the searches.
 * The function takes in a parameter, $result, that is the user's input into the textbox.
 */
function fetchGradYear($result){
    global $dbh;
    global $search;
    $people = array();
    $sql = "SELECT uid, name, email, classyear, major FROM user WHERE classyear = ?";
    $values=array($result);

    $resultset = prepared_query($dbh,$sql,$values);
    $num_names = $resultset -> numRows();

    if($num_names == 0) {
        echo "<p>Sorry, there are no names that match the search '$result' ";
    }

    if(count($search) == 1) {
        echo "<div class = 'jumbotron well well-lg'><h3>".$num_names." name(s) found that match the search '$result'</h3>"; // displays the number of matches found
    }

    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $name = $row['name'];
        $email = $row['email'];
        $classyear = $row['classyear'];
        $major = $row['major'];
        $uid=$row['uid'];

        $people[$uid]=$uid;
        if(count($search) == 1) {
            if($num_names == 1) {
                fetchUser($uid); // displays the information
            } else {
                // if there is more than one match, display with hyperlinks that lead to full display
                echo "<p><a href=".$_SERVER['PHP_SELF']."?uid=$uid>$name ($classyear)</a>";
            }
        }
        
    }
    if(count($search) == 1)
        echo "</div>";
    return $people;
}

/* The fetchLocation function takes in a parameter that is the location entered by the user
 * and finds jobs and graduate schools that match the location. 
 * The function returns a list of people who match the search.
 */
function fetchLocation($input){
    global $dbh;
    global $search;
    $result = array(); 

    // find graduate schools in the location
    $sql = "SELECT gid, name, location FROM gradschool WHERE location like ?";
    $values=array("%".$input."%");

    $resultset = prepared_query($dbh,$sql,$values);
    $num_schools = $resultset -> numRows();

    if(count($search) == 1) {
        echo "<div class = 'jumbotron well well-lg'><h3>".$num_schools." school(s) found that match the location '$input'</h3>"; // displays the number of matches found
    }

    if($num_schools == 0) {
        echo "<p>Sorry, there are no names that match the location '$input' ";
    }

    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $name = $row['name'];
        $location = $row['location'];
        $gid=$row['gid'];
        
        if(count($search) == 1) {
            echo "<p><a href=".$_SERVER['PHP_SELF']."?gid=$gid>$name ($location)</a>";
        }
        
        $sql_user = "SELECT uid, name FROM user WHERE uid IN 
                                        (SELECT uid FROM education WHERE gid = ?)";
        $values_user = array($gid);
        $resultset_user = prepared_query($dbh,$sql_user,$values_user);
        $uid;
        $user_name;

        while($row = $resultset_user->fetchRow(MDB2_FETCHMODE_ASSOC)) {
            $uid = $row['uid'];
            $user_name = $row['name'];
            $result[$uid] = $uid;

        }

    }
    
    // find jobs in the location
    $sql_job = "SELECT jid, title, field, location FROM job WHERE location like ?";
    
    $values_job = array("%".$input."%");

    $resultset_job = prepared_query($dbh,$sql_job,$values_job);
    $num_jobs = $resultset_job -> numRows();

    if(count($search) == 1){
        echo "<h3>".$num_jobs." job(s) found that match the location '$input'</h3>";
    }


    if($num_jobs == 0) {
        echo "<p>Sorry, there are no jobs that match the location '$input' ";
    }
    
    while($row = $resultset_job->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $title = $row['title'];
        $location = $row['location'];
        $jid=$row['jid'];
        
        $sql_user = "SELECT uid, name FROM user WHERE uid IN 
                                        (SELECT uid FROM employment WHERE jid = ?)";
        $values_user = array($jid);
        $resultset_user = prepared_query($dbh,$sql_user,$values_user);
        $uid;
        $user_name;

        while($row = $resultset_user->fetchRow(MDB2_FETCHMODE_ASSOC)) {
            $uid = $row['uid'];
            $puser_name = $row['name'];
            $result[$uid] = $uid;

        }
        if(count($search) == 1)
            echo "<p><a href=".$_SERVER['PHP_SELF']."?uid=$uid>$title ($location)</a>";

    }
    if (count($search) == 1)
        echo "</div>";
    return $result;
}



/* This function searches for one user in the database and displays information about the user.
 * The information displayed includes the person's name, graduation year from Wellesley, major while
 * at Wellesley, and contact information.
 * The function takes in a parameter, $uid, that is the user's id and returns the user's id.
 */
function fetchUser($uid){
    global $dbh;

    $sql = "SELECT uid,name,email,classyear,major,minor FROM user WHERE uid = ?";

    $values = array($uid);
    $resultset = prepared_query($dbh,$sql,$values);

    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $name=$row['name'];
        $email=$row['email'];
        $classyear = $row['classyear'];
        $major = $row['major'];
        $minor = $row['minor']; 
        $companies = fetchEmployers($uid);
        $schools = fetchAlmaMater($uid);

        echo "<div class = 'jumbotron well well-lg'>
              <h2>$name</h2>
              <p>Wellesley College Class of $classyear";

        // depending on whether the user has a minor
        // display the user's information differently
        if(!is_null($minor))
            echo "<p>$major | $minor";
        else
            echo "<p>$major";

        echo"<p>Contact information: <span class = 'glyphicon glyphicon-envelope'></span>  $email";

        // If the user is associated to a company, display the link to the company
        if(count($companies) > 0) {
            echo "<p><strong>Companies associated with $name:</strong></p>";
            foreach($companies as $key => $value) {
                echo '<p><a href='.$_SERVER['PHP_SELF'].'?cid='.$key.'>'.$value.'</a>';
            }
        }

        // if the user is associated to a graduate school, dsplay the link to the graduate school
        if(count($schools) > 0) {
            echo "<p><strong>Schools associated with $name:</strong></p>";
            foreach($schools as $key => $value) 
                echo "<p><a href=".$_SERVER['PHP_SELF']."?gid=".$key.">".$value."</a>";

        }
        echo"</div>";
    }
    return $uid;
}


/* This function searches for one graduate school and displays information about it.
 * The function takes in a parameter, $gid, that is a graduate school the user has clicked on or 
 * if there is only one graduate school that matched the search and returns a list of people
 * associated to the graduate school.
 * This function also calls the function fetchStudents that gets a list of students who are
 * associated with that school.
 * The function displays information about the graduate school(s) and a list of people
 * associated to the school (if any).
 */
function fetchOneSchool($gid){
    global $dbh;
    $sql = "SELECT gid,name,location FROM gradschool WHERE gid = ?";
    $people;
    $values = array($gid);
    $resultset = prepared_query($dbh,$sql,$values);

    while($row = $resultset->fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $name=$row['name'];
        $location=$row['location'];

        echo "<div class = 'jumbotron well well-lg'>
              <h2 class ='text-info'>$name</h2>
              <p><mark>$location</mark>
              <p class = 'lead'>People associated to $name:";
        $people = fetchStudents($gid);
        foreach($people as $key => $value) {
            echo "<p><a href=".$_SERVER['PHP_SELF']."?uid=$key>$value</a>";
        }
      }
      echo "</div>";
    return $people;
}


/* This function displays one company.
 * The function takes in a parameter, $cid, the company id and returns a list of people
 * who match the search.
 * This function also calls the fetchEmployees function that gets a list of people 
 * associated with the company.
 * The full display shows the company name and people associated to it (if any).
 */
function fetchOneCompany($cid){
    global $dbh;
    $people;
    $sql = "SELECT cid, name from company where cid = ?";
    $values = array($cid);
    $resultset = prepared_query($dbh,$sql,$values);

    while($row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC)) {
        
        $cid=$row['cid'];
        $name=$row['name'];
        
        echo "<div class = 'jumbotron well well-lg'><h2>$name</h2>
            <p>People associated to $name:";
        $people = fetchEmployees($cid);
        foreach($people as $key => $value) {
            //echo "<p>".$key. " => ".$value;
            $jobtitle = fetchJobTitle($key); // $key is title, value is location
            $jobloc = fetchJobLoc($key);
            
                if($jobtitle==""){
                echo "<p><a href=".$_SERVER['PHP_SELF']."?uid=$key>$value</a>";
                }
                else {
                    // foreach($jobtitle as $k => $v){
                        if($jobloc=="")
                            echo "<p><a href=".$_SERVER['PHP_SELF']."?uid=$key>$value ($jobtitle)</a>";
                        else
                            echo "<p><a href=".$_SERVER['PHP_SELF']."?uid=$key>$value ($jobtitle in $jobloc)</a>";
                    }
                }
            }
    echo "</div>";
    return $people;
}

/* The fetchEmployees function returns a list of people who are associated with a given company.
 * The function takes in one parameter, $cid, which is the company's id.
 * This function is used in the fetchOneComapny method.
 */
function fetchEmployees($cid) {
    global $dbh;
    global $search;
    $people = array();
    $sql = "SELECT uid, name from user where uid in (
            SELECT uid from employment where cid=?) 
            ORDER BY user.name asc";

    $values = array($cid);
    $resultset = prepared_query($dbh,$sql,$values);

    $num_names = $resultset -> numRows();
    if($num_names == 0 && count($search) == 1) {
        echo "<p>Sorry, no results found"; // if no one found, display message
    }

    while($row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $uid = $row['uid'];
        $name = $row['name'];
        $people[$uid]=$name;
    }
    return $people;
}


/* The fetchStudents function returns a list of people who are associated with a given graduate school.
 * This function takes in one parameter, $gid, which is the graduate school's id.
 * This function is used in the fetchOneSchool method.
 */
function fetchStudents($gid) {
    global $dbh;
    global $search;
    $people = array();
    $sql = "SELECT uid, name from user where uid in (
            SELECT uid from education where gid=?) 
            ORDER BY user.name asc";
    $values = array($gid);
    $resultset = prepared_query($dbh,$sql,$values);
    $num_names = $resultset -> numRows(); // get the number of people
    // if no one found, display message
    if($num_names == 0 && count($search) == 1) {
        echo "<p>Sorry, no results found";
    }
    while($row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $uid = $row['uid'];
        $name = $row['name'];
        $people[$uid]=$name;
    }
    return $people;
}

/* The fetchEmployers function takes in a parameter $uid, the user's id,
 * and returns a list of companies the user has worked at.
 */
function fetchEmployers($uid) {
    global $dbh;

    $companies = array();
    $sql = "SELECT cid, name from company where cid in (
            SELECT cid from employment where uid=?) 
            ORDER BY company.name asc";

    $values = array($uid);
    $resultset = prepared_query($dbh,$sql,$values);

    while($row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $cid = $row['cid'];
        $name = $row['name'];
        $companies[$cid]=$name;
    }
    return $companies;
}

/* The function fetchAlmaMater takes in a parameter $uid, the user's id,
 * and returns a list of schools the user has attended.
 */
function fetchAlmaMater($uid) {
    global $dbh;

    $schools = array();
    $sql = "SELECT gid, name from gradschool where gid in (
            SELECT gid from education where uid=?) 
            ORDER BY gradschool.name asc";
    $values = array($uid);
    $resultset = prepared_query($dbh,$sql,$values);

    while($row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $gid = $row['gid'];
        $name = $row['name'];
        $schools[$gid]=$name;
    }
    return $schools;
}

/* The function fetchJobTitle takes in a parameter $uid, the user's id, 
 * and returns the user's job title.
 */
function fetchJobTitle($uid) {
    global $dbh;
    $result = "";
    $sql = "SELECT jid, title from job where jid in (
            SELECT jid from employment where uid=?)";

    $values = array($uid);
    $resultset = prepared_query($dbh,$sql,$values);

    while($row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $jid = $row['jid'];
        $title = $row['title'];
        $result = $title;
    }
    return $result;
}
/* The function fetchJobLoc takes in a parameter $uid, the user's id, 
 * and returns the location of the user's job.
 */
function fetchJobLoc($uid) {
    global $dbh;

    $result = "";
    $sql = "SELECT jid, location from job where jid in (
            SELECT jid from employment where uid=?)";

    $values = array($uid);
    $resultset = prepared_query($dbh,$sql,$values);

    while($row = $resultset -> fetchRow(MDB2_FETCHMODE_ASSOC)) {
        $jid = $row['jid'];
        $location = $row['location'];
        $result = $location;
    }
    return $result;
}
?> 

</div> <!-- closing the resultDiv-->

</center>

</div> <!-- closing the container-->
</div>
</body>
</html>
