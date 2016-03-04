<!-- 
     WellConnected

     login.php

     This page creates the login page. This page implements the login feature
     and the form for the users to login.
-->


<?php
/* Session stuff in this section */
    require_once("MDB2.php");
    require_once("/home/cs304/public_html/php/MDB2-functions.php");
    require_once('../../cs304/hhau-dsn.inc');
    session_start(); // Starting Session
    global $error;
    $error =''; // Variable To Store Error Message

    if (isset($_POST['submit'])) {

        if ($_POST['email'] == "" || $_POST['password'] == "") {
            $error = "<p>please enter email or password";
        }
        
        else
        {
            // Define $username and $password
            $username=$_POST['email'];
            $password=$_POST['password'];
            
            $connection = db_connect($hhau_dsn);

            // SQL query to fetch information of registerd users and finds user match.
            $sql = "SELECT * from user where email = ? AND password = ? ";
            $values = array($username,$password);
            $result = prepared_query($connection, $sql, $values);
            $num_rows = $result -> numRows();

            if ($num_rows == 1) {
                $_SESSION['login_user']=$username; // Initializing Session
                echo "<p>Session login user is: ".$_SESSION['login_user'];
                header("location: search.php"); // Redirecting To Other Page
                die();
            } else {
                $error = "Email or Password is invalid";
            }
        }
    }


    // if the user is already logged in, redirect to the search page
    if(isset($_SESSION['login_user'])){
    header("location: search.php");
    }

?>




<!doctype html>
<html lang='en'>
<head> 
    <meta charset='utf-8'>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name=author content="Elizabeth Hau">
    <link href="css/WellConnected.css" rel="stylesheet" type="text/css">
    <!-- <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet" type="text/css"> -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    
    <title>WellConnected Home Page</title>
    <!--<link rel="stylesheet" href="../../css/webdb-style.css">-->

</head>

<body>

<!-- Creating the navigation bar on the top -->
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="home.php">WellConnected</a>
    </div>
    <div>
    <ul class="nav navbar-nav">
        <li><a href="home.php">Home</a></li>
        <li><a href="search.php">Search</a></li>
        <li><a href="profile.php">Profile</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
        <li><a href="profile.php"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
        <!--<li><a href="logout.php"><span class="glyphicon glyphicon-log-in"></span> Logout</a></li>-->
    </ul>
    </div>
  </div>
</nav>

<div class = "container">
<center>
<div id = "heading">
   <h1>Welcome to WellConnected!</h1>
</div>
<div>
   <img id = "logo" src = "wellesleycollege_logo.png"
        width = "10%" height = "10%">
</div>

<div class = "panel panel-info">
<form role = "form" method="post" id = "form">
    <div class = "panel-heading">
        <fieldset>
            <legend>Please Log in or Sign up for a New Account</legend>
                <div class = "panel-content">
                    <table>
                    <div class = "form-group">
                      <tr class = "spaceUnder"><td><label for="email" accesskey="e">(e) Email:</label></td>
                          <td><input class = "form-control" type="text" name="email" id="email" placeholder = "email address"></td>
                          <td>Ex: username@wellesley.edu</td></tr>
                    </div>
                    <div class = "form-group">
                      <tr class = "spaceUnder"><td><label for="password" accesskey="p">(p) Password:</label></td>
                          <td><input class = "form-control" type="password" name="password" id="password" placeholder = "************"></td>
                          <td>  Ex: 1234</td></tr>
                    </div>
                    <tr class = "spaceUnder"><td><td><center><input type="submit" value="Log in" name = "submit" class = "btn btn-success"></center></td></td></tr>

                    </table>
                </div>

                <!--Create new account links to the profile page-->
                <a href = "profile.php">Click here to create a new account</a> 
        </fieldset>
    </div>
    <span><?php echo $error; ?></span> 
</form>
</center>
</div>
</body>


</html>
