<?php
require 'includes/config.inc.php';
require MYSQLI ;

$page_title = 'Cart - Mart';
$cart_color = 'white';

if(isset($_COOKIE['SESSION_CART']) && (strlen($_COOKIE['SESSION_CART']) === 32)){
	$user_id = $_COOKIE['SESSION_CART'] ;
} else {
	$user_id = openssl_random_pseudo_bytes(16);
	$user_id = bin2hex($user_id);
}
setcookie('SESSION_CART', $user_id, time()+(60*60*24*30));
include 'includes/header.php';

//
if (($_SERVER['REQUEST_METHOD'] === 'GET') && isset($_GET['qty']) & isset($_GET['butt'])) {
}
//check for product id and validate
if (isset($_GET['id'])){
	
	// remove potential XSS attacks
	$product_id = xss_clean($_GET['id']);
	
	// validate if returned value is an integer
	if ($product_id = filter_var($product_id, FILTER_VALIDATE_INT)){
	}else {
		die('Invalid_product_id');
	}
}

// takin appropriate actions 
if (isset($user_id, $_GET['action']) && ($_GET['action'] === 'clear')){
	$q = "DELETE FROM cart WHERE user_session_id='$user_id' ";
	$r =$dbc->query($q);
}

if (isset($product_id, $_GET['action']) && ($_GET['action'] === 'add')){
	
	// intializing qty to 1
	$qty = 1;
	// checking if qty is preset from product.php
	if (isset($_GET['qty'])) $qty = $_GET['qty'];
	
	// updating the database
	$r = $dbc->query("CALL add_to_cart('$user_id', $product_id, $qty)");
	//if (!$r) echo mysqli_error($dbc);
}elseif (isset($product_id, $_GET['action']) && ($_GET['action'] === 'remove')){
	$q = "DELETE FROM cart WHERE user_session_id='$user_id' AND product_id=$product_id ";
	$r =$dbc->query($q);
	//if (!$r) echo mysqli_error($dbc);
}elseif (isset($_POST['quantity'], $_POST['product_id'])){
	//if ($_SESSION['form_token_update'] != $_POST['form_token_update']) die();
	$qty = filter_var($_POST['quantity'], FILTER_VALIDATE_INT);
	$id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
	$tr = $dbc->query("CALL update_cart('$user_id', $id, $qty)");
	
}

//$result = $dbc->query("CALL show_cart_content('$user_id')");
$result = mysqli_query($con,"CALL show_cart_content('$user_id')");
include 'views/cart_items.php';
?>




<?php
include 'includes/footer.php';