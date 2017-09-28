<?php

// This file is the final page in the checkout process. 
// This script is begun in Chapter 10.

// Require the configuration before any PHP code:
require('./includes/config.inc.php');

// Start the session:
session_start();

// The session ID is the user's cart ID:
$uid = session_id();

// Check that this is valid:
if (!isset($_SESSION['customer_id'])) { // Redirect the user.
	$location = 'https://' . BASE_URL . 'checkout.php';
	header("Location: $location");
	exit();
} elseif (!isset($_SESSION['response_code']) || ($_SESSION['response_code'] != 1)) {
	$location = 'https://' . BASE_URL . 'billing.php';
	header("Location: $location");
	exit();
}

// Require the database connection:
require(MYSQL);

// Clear out the shopping cart:
$r = mysqli_query($dbc, "CALL clear_cart('$uid')");

// Send the email:
// Added in Chapter 13.
include('./includes/email_receipt.php');

// Include the header file:
$page_title = 'Coffee - Checkout - Your Order is Complete';
include('./includes/checkout_header.html');

// Include the view:
include('./views/final.html');

// Clear the session:
$_SESSION = array(); // Destroy the variables.
session_destroy(); // Destroy the session itself.

// Include the footer file:
include('./includes/footer.html');
?>