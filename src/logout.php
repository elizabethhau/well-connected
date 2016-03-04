<!-- 
	 WellConnected

	 logout.php

	 This page implements the process of logging a user out.
-->

<!-- This file contains code for logging a user out of the web app -->
<?php
session_start();
// If the user is logged in, log the user out
if(isset($_SESSION['login_user'])) {
	session_destroy();// Destroying All Sessions
}
?>

<!doctype html>
<head>
	<meta http-equiv "refresh" content="10;URL='home.php'"/>
</head>
<body>

	<h1>You have been logged out</h1>
	<p>To login again, <a href = "home.php">click here</a></p>
	<p>You will be automatically redirected in <span id="counter">10</span>  second(s)</p>
	
<script type="text/javascript">
	/* The countdown function allows the user to see the countdown before getting redirected 
	 * back to the home page to then log in again.
	 */
	function countdown() {
	    var i = document.getElementById('counter');
	    if (parseInt(i.innerHTML)<=1) {
	        location.href = 'home.php';
	    }
	    i.innerHTML = parseInt(i.innerHTML)-1;
	}
	setInterval(function(){ countdown(); },1000);
</script>

</body>
