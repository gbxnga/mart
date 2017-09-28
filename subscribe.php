<?php
require 'includes/config.inc.php';
require MYSQLI ;

$subscription_errors = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {	

	if (preg_match('/^[A-Za-z0-9]+@.+\..{2,4}$/i', $_POST['email'])) {
		$email = escape_data($_POST['email'], $con);
	}else {
		echo 'Invalid email.';
		exit();
	}
	
	$q = "INSERT INTO subscription (email) VALUES ('$email')";
	$r = mysqli_query($con, $q);
	if ($r) {
		echo 'Email successfully added, you will now receive updates from us.';
	}
}