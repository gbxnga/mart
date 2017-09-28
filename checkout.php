<?php

// This file is the first step in the checkout process.
// It takes and validates the shipping information.
// This script is begun in Chapter 10.

// Require the configuration before any PHP code:
require('includes/config.inc.php');

// Check for the user's session ID, to retrieve the cart contents:
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	if (isset($_COOKIE['SESSION_CART']) && (strlen($_COOKIE['SESSION_CART']) === 32)) {
		$uid = $_COOKIE['SESSION_CART'];
		// Start the session:
		session_start();
		// Use the existing user ID:
		session_id($uid);
	} else { // Redirect the user.
		$location = 'http://' . BASE_URL . 'cart.php';
		header("Location: $location");
		exit();
	}
} else { // POST request.
	session_start();
	$uid = session_id();
}

// Create an actual session for the checkout process...

// Require the database connection:
require(MYSQLI);

// Validate the checkout form...

// For storing errors:
$shipping_errors = array();

// Check for a form submission:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	// Check for Magic Quotes:
	if (get_magic_quotes_gpc()) {
		$_POST['first_name'] = stripslashes($_POST['first_name']);
		// Repeat for other variables that could be affected.
	}

	// Check for a first name:
	if (preg_match ('/^[A-Z \'.-]{2,20}$/i', $_POST['first_name'])) {
		$fn = addslashes($_POST['first_name']);
	} else {
		$shipping_errors['first_name'] = 'Please enter your first name!';
	}
	
	// Check for a last name:
	if (preg_match ('/^[A-Z \'.-]{2,40}$/i', $_POST['last_name'])) {
		$ln  = addslashes($_POST['last_name']);
	} else {
		$shipping_errors['last_name'] = 'Please enter your last name!';
	}
	
	// Check for a street address:
	if (preg_match ('/^[A-Z0-9 \',.#-]{2,80}$/i', $_POST['address1'])) {
		$a1  = addslashes($_POST['address1']);
	} else {
		$shipping_errors['address1'] = 'Please enter your street address!';
	}

	// Check for a second street address:
	if (empty($_POST['address2'])) {
		$a2 = NULL;
	} elseif (preg_match ('/^[A-Z0-9 \',.#-]{2,80}$/i', $_POST['address2'])) {
		$a2 = addslashes($_POST['address2']);
	} else {
		$shipping_errors['address2'] = 'Please enter your street address!';
	}
	
	// Check for a city:
	if (preg_match ('/^[A-Z \'.-]{2,60}$/i', $_POST['city'])) {
		$c = addslashes($_POST['city']);
	} else {
		$shipping_errors['city'] = 'Please enter your city!';
	}
	
	// Check for a state:
	if (preg_match ('/^[A-Z]{2}$/', $_POST['state'])) {
		$s = $_POST['state'];
	} else {
		$shipping_errors['state'] = 'Please enter your state!';
	}
	
	// Check for a zip code:
	if (preg_match ('/^(\d{5}$)|(^\d{5}-\d{4})$/', $_POST['zip'])) {
		$z = $_POST['zip'];
	} else {
		$shipping_errors['zip'] = 'Please enter your zip code!';
	}
	
	// Check for a phone number:
	// Strip out spaces, hyphens, and parentheses:
	$phone = str_replace(array(' ', '-', '(', ')'), '', $_POST['phone']);
	if (preg_match ('/^[0-9]{10}$/', $phone)) {
		$p  = $phone;
	} else {
		$shipping_errors['phone'] = 'Please enter your phone number!';
	}
	
	// Check for an email address:
	if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$e = $_POST['email'];
		$_SESSION['email'] = $_POST['email'];
	} else {
		$shipping_errors['email'] = 'Please enter a valid email address!';
	}
	
	// Check if the shipping address is the billing address:
	if (isset($_POST['use']) && ($_POST['use'] === 'Y')) {
		$_SESSION['shipping_for_billing'] = true;
		$_SESSION['cc_first_name']  = $_POST['first_name'];
		$_SESSION['cc_last_name']  = $_POST['last_name'];
		$_SESSION['cc_address']  = $_POST['address1'] . ' ' . $_POST['address2'];
		$_SESSION['cc_city'] = $_POST['city'];
		$_SESSION['cc_state'] = $_POST['state'];
		$_SESSION['cc_zip'] = $_POST['zip'];
	}

	if (empty($shipping_errors)) { // If everything's OK...
		
		// Add the user to the database...
		
		// Call the stored procedure:
		$r = mysqli_query($con, "CALL add_customer('$e', '$fn', '$ln', '$a1', '$a2', '$c', '$s', $z, $p, @cid)");

		// Confirm that it worked:
		if ($r) {

			// Retrieve the customer ID:
			$r = mysqli_query($con, 'SELECT @cid');
			if (mysqli_num_rows($r) == 1) {

				list($_SESSION['customer_id']) = mysqli_fetch_array($r);
					
				// Redirect to the next page:
				// billing.php will take all the $_POST values
				$location = 'http://' . BASE_URL . 'billing.php';
				header("Location: $location");
				exit();

			}else {
				die('couldnt select');
			}

		}

		// Log the error, send an email, panic!

		trigger_error('Your order could not be processed due to a system error. We apologize for the inconvenience.');

	} // Errors occurred IF.

} // End of REQUEST_METHOD IF.
							
// Include the header file:
$page_title = 'Checkout - Your Shipping Information';
$cart_color = 'white';
include('includes/header_checkout.php');

// Get the cart contents:
$r = $dbc->query("CALL show_cart_content('$uid')");

?>
<div class="container">
<div class="row">
<h4 style="font-size:36px; text-align:center; padding:15px">Checkout Portal</h4>


<div class="col-lg-5 col-xs-12" style="margin-bottom: 15px">
<div class="col-lg-12"  style="background-color:#eee">
<h3 style="text-align:center">Your Shipping Information</h3>
<p>Please enter your shipping information. On the next page,  you'll be able to enter your billing information and complete the order. 
Please check the first box if your shipping and billing addresses are the same. <span class="required">*</span> Indicates a required field. </p>
<form id="checkoutForm" action="checkout.php" method="POST">
<?php // Need the form function:
include('includes/form_functions.inc.php');
?>
	<fieldset>
		<div class="field"><label for="use"><strong>Use Same Address for Billing?</strong></label><input type="checkbox" name="use" value="Y" id="use" <?php if (isset($_POST['use'])) echo 'checked="checked" ';?>/></div>

	<div class="field"><label for="first_name"><strong>First Name <span class="required">*</span></strong></label><br /><?php create_form_input('first_name', 'text', $shipping_errors); ?></div>
	
	<div class="field"><label for="last_name"><strong>Last Name <span class="required">*</span></strong></label><br /><?php create_form_input('last_name', 'text', $shipping_errors); ?></div>

	<div class="field"><label for="address1"><strong>Street Address <span class="required">*</span></strong></label><br /><?php create_form_input('address1', 'text', $shipping_errors); ?></div>

	<div class="field"><label for="address2"><strong>Street Address, Continued</strong></label><br /><?php create_form_input('address2', 'text', $shipping_errors); ?></div>

	<div class="field"><label for="city"><strong>City <span class="required">*</span></strong></label><br /><?php create_form_input('city', 'text', $shipping_errors); ?></div>
	
	<div class="field"><label for="state"><strong>State <span class="required">*</span></strong> </label><br /><?php create_form_input('state', 'select', $shipping_errors); ?></div>
	
	<div class="field"><label for="zip"><strong>Zip Code <span class="required">*</span></strong></label><br /><?php create_form_input('zip', 'text', $shipping_errors); ?></div>

	<div class="field"><label for="phone"><strong>Phone Number <span class="required">*</span></strong></label><br /><?php create_form_input('phone', 'text', $shipping_errors); ?></div>

	<div class="field"><label for="email"><strong>Email Address <span class="required">*</span></strong></label><br /><?php create_form_input('email', 'text', $shipping_errors); ?></div>
	
	<br clear="all" />
	
<div align="center"><input style="padding:10px; background-color:#FFBE58" type="submit" value="Continue onto Billing" class="button" /></div></fieldset></form>
</div>
</div>


<div class="col-lg-7 col-xs-12" style="margin-bottom: 15px">
<div style="border:1px solid #CCCCCC"  class="col-lg-12">
<table id="shopping-cart-table" class="cart items data table">
            <caption role="heading" aria-level="2" class="table-caption"><h4>Review Your Order</h4></caption>
            <thead>
                <tr>
                    <th class="col item" scope="col"><span>Item</span></th>
                    <th class="col price" scope="col"><span>Price</span></th>
                    <th class="col qty" scope="col" style="text-align:right" ><span>Qty</span></th>
                    <th class="col subtotal" scope="col"><span>Subtotal</span></th>
                </tr>
            </thead>
                            <tbody class="cart item">
                            
<?php

if ($r) { // Products to show!
	//include('./views/checkout.html');
$theSubtotal = 0;
$subtotal = 0;
while ($row=$r->fetch()) {
	
?>
    <tr id="theParent" class="item-info">
        <td data-th="Item" class="col item">
                            <?php echo '<a href="product.php?id='.$row['product_id'].'" title="Joust Duffle Bag" tabindex="-1" class="product-item-photo">'; ?>
                        
<span class="product-image-container" style=" float:left">
    <span class="product-image-wrapper" style="padding-bottom: 100%;">
        <img id="cart-item-img"  class="product-image-photo" src="images/<?php echo $row['image']; ?>" alt="Joust Duffle Bag"></span>
    </span>
                            </a>
                        <div style="float:left" class="product-item-details">
                <strong class="product-item-name">
                           <?php echo ' <a  href="product.php?id='.$row['product_id'].'">'. $row['name'] . '</a>'; ?>
                </strong>
                       </div>
        </td>

        <td class="col price" data-th="Price">
                        
            <span class="price-excluding-tax" data-label="Excl. Tax">
                            <span class="cart-price">
                                <span class="price"><?php echo '#' .$row['price']; ?></span></span>

          </span>
       </td>
       <td class="col qty" data-th="Qty"  align="right">
            <div class="field qty">

                <div id="cart-buttons" class="control qty">

                 <?php
                     echo  $row['quantity'];
				 ?>
                </div>
            </div>
            
      </td>

      <td class="col subtotal" data-th="Subtotal">
                            
            <span class="price-excluding-tax" data-label="Excl. Tax">
                    <span class="cart-price">
                        <span class="price"><?php $subtotal = $row['price'] * $row['quantity']; echo '#'. $subtotal; ?></span>            </span>

            </span>

    </td>
    </tr>
<?php
$theSubtotal = $theSubtotal + $subtotal;
}
?>
    
    
      <tr class="item-actions">
		<td></td><td></td><td width="250" align="right">Shipping and Handling: </td>	<td><strong><?php echo 1500 ; ?></strong></td>
    </tr>  
    
    <tr class="item-actions">
		<td></td><td></td><td width="250"  align="right">Order Total</td>	<td><strong><?php $total = $theSubtotal + 1500;  echo '#'. $total  ;?></strong></td>
    </tr>

</tbody>
</table>
</div>
</div>


</div>
</div>
<?php
} else { // Empty cart!
	include('./views/emptycart.html');
}

// Finish the page:
// include('includes/footer.php');
?>