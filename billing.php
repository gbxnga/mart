<?php

// This file is the second step in the checkout process.
// It takes and validates the billing information.
// This script is begun in Chapter 10.

// Require the configuration before any PHP code:
require('includes/config.inc.php');

// Start the session:
session_start();

// The session ID is the user's cart ID:
/*if(!empty(session_id())) {
$uid = session_id();
}else {*/
	$uid = $_COOKIE['SESSION_CART'];
$uid2 = session_id();


// Check that this is valid:
if (!isset($_SESSION['customer_id'])) { // Redirect the user.
	$location = 'http://' . BASE_URL . 'checkout.php';
	header("Location: $location");
	exit();
}

// Require the database connection:
require(MYSQLI);

// Validate the billing form...

// For storing errors:
$billing_errors = array();

// Check for a form submission:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	if (get_magic_quotes_gpc()) {
		$_POST['cc_first_name'] = stripslashes($_POST['cc_first_name']);
		// Repeat for other variables that could be affected.
	}

	// Check for a first name:
	if (preg_match ('/^[A-Z \'.-]{2,20}$/i', $_POST['cc_first_name'])) {
		$cc_first_name = $_POST['cc_first_name'];
	} else {
		$billing_errors['cc_first_name'] = 'Please enter your first name!';
	}

	// Check for a last name:
	if (preg_match ('/^[A-Z \'.-]{2,40}$/i', $_POST['cc_last_name'])) {
		$cc_last_name  = $_POST['cc_last_name'];
	} else {
		$billing_errors['cc_last_name'] = 'Please enter your last name!';
	}
	
	// Check for a valid credit card number...
	// Strip out spaces or hyphens:
	$cc_number = str_replace(array(' ', '-'), '', $_POST['cc_number']);
	
	// Validate the card number against allowed types:
	if (!preg_match ('/^4[0-9]{12}(?:[0-9]{3})?$/', $cc_number) // Visa
	&& !preg_match ('/^5[1-5][0-9]{14}$/', $cc_number) // MasterCard
	&& !preg_match ('/^3[47][0-9]{13}$/', $cc_number) // American Express
	&& !preg_match ('/^6(?:011|5[0-9]{2})[0-9]{12}$/', $cc_number) // Discover
	) {
		$billing_errors['cc_number'] = 'Please enter your credit card number!';
	}
	
	// Check for an expiration date:
	if ( ($_POST['cc_exp_month'] < 1 || $_POST['cc_exp_month'] > 12)) {
		$billing_errors['cc_exp_month'] = 'Please enter your expiration month!';		
	}

	if ($_POST['cc_exp_year'] < date('Y')) {
		$billing_errors['cc_exp_year'] = 'Please enter your expiration year!';
	}
	
	// Check for a CVV:
	if (preg_match ('/^[0-9]{3,4}$/', $_POST['cc_cvv'])) {
		$cc_cvv = $_POST['cc_cvv'];
	} else {
		$billing_errors['cc_cvv'] = 'Please enter your CVV!';
	}
	
	// Check for a street address:
	if (preg_match ('/^[A-Z0-9 \',.#-]{2,160}$/i', $_POST['cc_address'])) {
		$cc_address  = $_POST['cc_address'];
	} else {
		$billing_errors['cc_address'] = 'Please enter your street address!';
	}
		
	// Check for a city:
	if (preg_match ('/^[A-Z \'.-]{2,60}$/i', $_POST['cc_city'])) {
		$cc_city = $_POST['cc_city'];
	} else {
		$billing_errors['cc_city'] = 'Please enter your city!';
	}

	// Check for a state:
	if (preg_match ('/^[A-Z]{2}$/', $_POST['cc_state'])) {
		$cc_state = $_POST['cc_state'];
	} else {
		$billing_errors['cc_state'] = 'Please enter your state!';
	}

	// Check for a zip code:
	if (preg_match ('/^(\d{5}$)|(^\d{5}-\d{4})$/', $_POST['cc_zip'])) {
		$cc_zip = $_POST['cc_zip'];
	} else {
		$billing_errors['cc_zip'] = 'Please enter your zip code!';
	}
	
	if (empty($billing_errors)) { // If everything's OK...

		// Convert the expiration date to the right format:
		$cc_exp = sprintf('%02d%d', $_POST['cc_exp_month'], $_POST['cc_exp_year']);

		// Check for an existing order ID:
		if (isset($_SESSION['order_id'])) { // Use existing order info:
			$order_id = $_SESSION['order_id'];
			$order_total = $_SESSION['order_total'];
		} else { // Create a new order record:


			// Get the last four digits of the credit card number:
			$cc_last_four = substr($cc_number, -4);

			// Call the stored procedure:
			$_SESSION['shipping'] = 200;
			$shipping = $_SESSION['shipping'] * 100;
			$customer_id = $_SESSION['customer_id'];
			$r = mysqli_query($con, "CALL add_order('$uid', $customer_id,  $shipping, $cc_last_four, @total, @oid)");

			// Confirm that it worked:
			if ($r) {

				// Retrieve the order ID and total:
				$r = mysqli_query($con, 'SELECT @total, @oid');
				if (mysqli_num_rows($r) == 1) {
					list($order_total, $order_id) = mysqli_fetch_array($r);
					
					// Store the information in the session:
					$_SESSION['order_total'] = $order_total;
					$_SESSION['order_id'] = $order_id;
					
				} else { // Could not retrieve the order ID and total.
					unset($cc_number, $cc_cvv, $_POST['cc_number'], $_POST['cc_cvv']);
					trigger_error('Your order could not be processed due to a system error. We apologize for the inconvenience.');
				}
			} else { // The add_order() procedure failed.
				unset($cc_number, $cc_cvv, $_POST['cc_number'], $_POST['cc_cvv']);
				trigger_error('Your order could not be processed due to a system error. We apologize for the inconvenience.');
			}
			
		} // End of isset($_SESSION['order_id']) IF-ELSE.
		
		// ------------------------
		// Process the payment!
		if (isset($order_id, $order_total)) {


				// Make the request to the payment gateway:
				require('includes/vendor/AuthorizeNet.php');
				$aim = new AuthorizeNetAIM(API_LOGIN_ID, TRANSACTION_KEY);

				// Are we testing?
				//$aim->setSandbox(true);

				// Set the amount (in dollars):
				$aim->amount = $order_total/320;

				// Set the invoice number:
				$aim->invoice_num = $order_id;

				// Set the customer ID:
				$aim->cust_id = $_SESSION['customer_id'];

				// Set the customer's CC info:
				$aim->card_num = $cc_number;
				$aim->exp_date = $cc_exp;
				$aim->card_code = $cc_cvv;

				// Set the customer's information:
				$aim->first_name = $cc_first_name;
				$aim->last_name = $cc_last_name;
				$aim->address = $cc_address;
				$aim->state = $cc_state;
				$aim->city = $cc_city;
				$aim->zip = $cc_zip;
				$aim->email = $_SESSION['email'];

				// $aim->addLineItem();
				// $aim->setCustomField('thing', 'value');
				// $aim->phone;
				// $aim->tax
				// $aim->freight
				// $aim->description

				$response = $aim->authorizeOnly();

				// Add slashes to two text values:
				$reason = addslashes($response->response_reason_text);
				$full_response = addslashes($response->response);

				// Record the transaction:
				$r = mysqli_query($con, "CALL add_transaction($order_id, '{$response->transaction_type}', $order_total, {$response->response_code}, '$reason', {$response->transaction_id}, '$full_response')");				
			
				// Upon success, redirect:
				if ($response->approved) {
					
					// Add the transaction info to the session:
					$_SESSION['response_code'] = $response->response_code;
					
					// Redirect to the next page:
					$location = 'https://' . BASE_URL . 'final.php';
					header("Location: $location");
					exit();

				} else { // Do different things based upon the response:

					switch ($response->response_code) {
						case '2': // Declined	
							$message = $response->response_reason_text . ' Please fix the error or try another card.';	
							break;
						case '3': // Error	
							$message = $response->response_reason_text . '  Please fix the error or try another card.';	
							break;
						case '4': // Held for review	
							$message = "The transaction is being held for review. You will be contacted ASAP about your order. We apologize for any inconvenience.";			
							break;
					}
									
				} // End of $response_array[0] IF-ELSE.

		} // End of isset($order_id, $order_total) IF.
		// Above code added as part of payment processing.
		// ------------------------

	} // Errors occurred IF

} // End of REQUEST_METHOD IF.
							
// Include the header file:
$page_title = 'Coffee - Checkout - Your Billing Information';
include('includes/header_checkout.php');

// Get the cart contents:
$r = mysqli_query($con, "CALL show_cart_content('$uid')");

if (mysqli_num_rows($r) > 0) { // Products to show!
	if (isset($_SESSION['shipping_for_billing']) && ($_SERVER['REQUEST_METHOD'] !== 'POST')) {
		$values = 'SESSION';
	} else {
		$values = 'POST';
	}
?>

<div class="container">
<div class="row">
<div class="col-lg-6 col-lg-offset-3">
<div class="col-lg-12" style="background-color:white">
<h2>Your Billing Information</h2>
<p>Please enter your billing information below. Then click the button to complete your order. For your security, we will not store your billing information in any way. We accept Visa, MasterCard, American Express, and Discover.</p>';

<?php
if (isset($message)) echo "<p class=\"error\">$message</p>";

echo '<form action="billing.php" method="POST" id="billing_form">';

include('includes/form_functions.inc.php');
?>
<fieldset>

	<div class="field"><label for="cc_number"><strong>Card Number</strong></label><br /><?php create_form_input('cc_number', 'text', $billing_errors, 'POST', array('autocomplete' => 'off')); ?></div>

	<div class="field"><label for="exp_date"><strong>Expiration Date</strong></label><br /><?php create_form_input('cc_exp_month', 'select', $billing_errors); ?><?php create_form_input('cc_exp_year', 'select', $billing_errors); ?></div>

	<div class="field"><label for="cc_cvv"><strong>CVV</strong></label><br /><?php create_form_input('cc_cvv', 'text', $billing_errors, 'POST', array('autocomplete' => 'off')); ?></div>

	<div class="field"><label for="cc_first_name"><strong>First Name </strong></label><br /><?php create_form_input('cc_first_name', 'text', $billing_errors, $values); ?></div>

	<div class="field"><label for="cc_last_name"><strong>Last Name </strong></label><br /><?php create_form_input('cc_last_name', 'text', $billing_errors, $values); ?></div>
	
	<div class="field"><label for="cc_address"><strong>Street Address </strong></label><br /><?php create_form_input('cc_address', 'text', $billing_errors, $values); ?></div>

	<div class="field"><label for="cc_city"><strong>City </strong></label><br /><?php create_form_input('cc_city', 'text', $billing_errors, $values); ?></div>

	<div class="field"><label for="cc_state"><strong>State </strong></label><br /><?php create_form_input('cc_state', 'select', $billing_errors, $values); ?></div>

	<div class="field"><label for="cc_zip"><strong>Zip Code </strong></label><br /><?php create_form_input('cc_zip', 'text', $billing_errors, $values); ?></div>
		
	<br clear="all" />
	
<div align="center" id="submit_div"><input type="submit" value="Place Order" class="button" /></div></fieldset></form>
<div>By clicking this button, your order will be completed and your credit card will be charged.</div></div></div></div></div>
<?php echo $uid2 ?>
<?php echo '<br/>' . $uid  . '<br/>' . $customer_id ?>

<!--<script type="text/javascript">  
$(function() {

  $('#billing_form').submit(function() {
      $('input[type=submit]', this).attr('disabled', 'disabled');
     $('#submit_div').html('<p class="button">Processing...</p>');
     return false;
  });

});   
</script>-->

<?php
} else { // Empty cart!
	include('');
}

// Finish the page:
include('includes/footer.php');
?>