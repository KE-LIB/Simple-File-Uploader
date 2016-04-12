<?php
//start session for login 
session_start();

//include function class from functions.php
require_once(dirname(__FILE__) . '/lib/functions.php');

//new Object
$page = new Functions();

//cinfig file for database and path options
require_once(dirname(__FILE__) . '/lib/config.php');

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo PAGE_HTITLE; ?></title>
		<meta charset="utf-8" />
		<link rel="shortcut icon" href="./lib/icon/favicon.ico"/>
		<script src="./lib/js/jquery.min.js"></script>
		<script src="./lib/js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	</head>
 <body>
 <div class="container">
	 <div class="jumbotron">
	 <h1><a href="./"><?php echo PAGE_JTITLE; ?></a></h1>      
		<p><?php echo PAGE_JTEXT; ?></p>
	 </div>
<?php

//handle login with user data
if((isset($_POST['username']) && isset($_POST['password'])))
{
	//LDAP authentication
	$page->authenticateUser($_POST['username'] , $_POST['password']);
}
else
{
	//show appropriate form
	$page->showForms();
}

//log out
if((isset($_GET['destroy'])))
{
	//log out - destroy session data
	$page->destroySession();
}
?> 
</div>
</body>
</html>