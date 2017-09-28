<?php

require 'includes/config.inc.php';
require MYSQLI ;

$review_errors = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	
	$product_id = escape_data($_POST['product_id'], $con);
	if (preg_match('/^[A-Za-z0-9 , \'.-]{2,40}$/i', $_POST['surname'])){
		$sn = escape_data($_POST['surname'], $con);
	}else {
		$review_errors['surname'] = 'Please enter a valid surname! ';
	}
	if (preg_match('/^[A-Za-z0-9 , \'.-]{1,45}$/i', $_POST['nickname'])){
		$nn = escape_data($_POST['nickname'], $con);
	}else {
		$review_errors['nickname'] = 'Please enter a valid nickname! ';
	}
	if (preg_match('/^[A-Za-z0-9 , \'.-]{2,100}$/i', $_POST['review'])){
		$rv = escape_data($_POST['review'], $con);
	}else {
		$review_errors['review'] = 'Please enter a valid Review! ';
	}	
	
	// if incoming information is OK
	// proceed to store in database
	if (empty($review_errors)) {
		
		$q = "INSERT INTO reviews (product_id, surname, nickname, review) VALUES ($product_id, '$sn', '$nn', '$rv')";
		$r = mysqli_query($con, $q);
		if ($r) {
			echo 'Review successfully posted.';
		}else {
			echo 'Couldn\'t post review, Please try again..';
		}
	}else {
		print_r($review_errors);
	}
}