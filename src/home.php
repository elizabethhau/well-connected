<!-- 
     WellConnected

     home.php

     This page creates the homepage. The homepage contains information about the web app
     and links to login or sign up for a new account.
-->

<?php
session_start();
// Storing Session
$log = "";

if(isset($_SESSION['login_user'])){

  $log = "Logout";
}
else 
    $log = "Login";

?>
<!doctype html>
<html lang='en'>
<head> 
    <meta charset='utf-8'>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name=author content="Elizabeth Hau">
    <link href="css/WellConnected.css" rel="stylesheet" type="text/css">
    <!-- Custom CSS -->
    <link href="css/stylish-portfolio.css" rel="stylesheet">

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

    <!-- Latest compiled JavaScript -->
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    

    <title>WellConnected Home Page</title>

</head>

<body>

<!-- Creating the navigation bar on the top -->
<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="home.php">WellConnected</a>
    </div>
    <div>
    <ul class="nav navbar-nav">
        <li class="active"><a href="#">Home</a></li>
        <li><a href="search.php">Search</a></li>
        <li><a href="profile.php">Profile</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
        <li><a href="profile.php"><span class="glyphicon glyphicon-user"></span> Sign Up</a></li>
        <?php
            global $log;
            if($log == "Login")
                echo"<li><a href='login.php'><span class='glyphicon glyphicon-log-in'></span> ".$log."</a></li>";
            else
                echo"<li><a href='logout.php'><span class='glyphicon glyphicon-log-in'></span> ".$log."</a></li>";
        ?>
    </ul>
    </div>
  </div>
</nav>
  <!-- Header -->
    <header id="top" class="header">
        <div class="text-vertical-center">
            <h1>Welcome to <span><img id = "logo" src = "wellesleycollege_logo.png"
        width = "10%" height = "10%"></span>ellConnected</h1>
            <h3><font color="white">This is a web application that helps current Wellesley students to connect with alumnae through their field(s) of interests. 
        <p>To find people to connect with, please <span ><a href = "login.php" style = "color:black">login </a>or 
                        <a href = "profile.php" style = "color:black">sign up </a></span>for an account.</font></h3>

        </div>
    </header>

</body>


</html>
