<?php
require 'includes/config.inc.php';
require MYSQLI ;

// Checking user authorization
// redirect_invalid_user();
$username = $_COOKIE['SESSION_ADMIN'];
$q = "SELECT * FROM members WHERE username = '$username' AND type = 'admin'";
$r = mysqli_query($con, $q);
if (mysqli_num_rows($r) !== 1){
	      $protocol = 'http://';
		  $url = $protocol . BASE_URL ; // Define the URL.
		  header("Location: $url");
		  exit(); // Quit the script.
}

$page_title = "Inventory";
include 'includes/header_checkout.php';

// initializing product ID to zero
// so the add_product procedure should create a new inventory
// unless told otherwise
if (isset($_SESSION['product_id'])) {
	$product_id = $_SESSION['product_id'];
}else {
	$product_id = 0;
}

// if visiting page without any request
// clear all variables
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])  && !isset($_GET['id'])) {
	unset($_SESSION['product_id'], $_POST, $row, $value, $_SESSION['img_name']);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'delete' && isset($_GET['id'])){ 
	
	$product_id = $_GET['id'];
	$q = "DELETE FROM product WHERE  id = $product_id ";
	$r = mysqli_query($con, $q);
	if ($r ) {
		
		$notification = "Inventory has been successfully deleted.";
		unset($_SESSION['product_id'], $_POST, $row, $product_id, $value, $_SESSION['img_name']);
	}else {
		$notification = "Inventory could not be deleted at this time.".$product_id;
		unset($_SESSION['product_id'], $_POST, $row, $product_id, $value, $_SESSION['img_name']);
	}
	
}
// request to edit an inventory
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'edit' && isset($_GET['id'])){ 
	
	// setting a new value for product ID
	// the database will now edit the product with specified ID
	$product_id = $_GET['id'];
	$_SESSION['product_id'] = $product_id;
	$q = "SELECT * FROM product p WHERE p.id = $product_id";
	$r = $dbc->query($q);
	$row = $r->fetch();
	
	// setting input value to $row 
	// so values for specified product is appended to 
	// equivalent input boxes
	$value = $row ;
	
	// setting SESSION img_name to value from database
	// if user decides to upload another image,
	// the upload_image.php engine will changeSESSION img_name value to the new one
	$_SESSION['img_name'] = $row['image'];
}

$reg_errors = array();
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
	
	$value = $_POST ;
	
	$cid = $_POST['category'];
	$bid = $_POST['brand'];
	$osid = $_POST['os'];
	if (preg_match('/^[A-Za-z0-9 , \'.-]{2,40}$/i', $_POST['name'])){
		$nm = escape_data($_POST['name'], $con);
	}else {
		$reg_errors['name'] = 'Please enter a valid name! ';
	}
	if (preg_match('/^[A-Za-z0-9 , \'.-]{2,100}$/i', $_POST['description'])){
		$dsc = escape_data($_POST['description'], $con);
	}else {
		$reg_errors['description'] = 'Please enter a valid Description! ';
	}
	if (isset($_SESSION['img_name'])){
		$img = escape_data($_SESSION['img_name'], $con);
	}else {
		$reg_errors['image'] = 'Please upload item image ';
	}
	if (preg_match('/^\d{1,10}$/', $_POST['price'])){
		$pr = escape_data($_POST['price'], $con);
	}else {
		$reg_errors['price'] = 'Please enter a valid Integer as Price! ';
	}
	if (preg_match('/^\d{1,4}$/', $_POST['stock'])){
		$sk = escape_data($_POST['stock'], $con);
	}else {
		$reg_errors['stock'] = 'Please enter a valid Integer as stock! ';
	}
	
	if (empty($reg_errors)){
		
		$r = mysqli_query($con, "CALL add_product('$cid', '$bid', '$osid', '$nm', '$dsc', '$img', '$pr', '$sk', $product_id)");
		if (mysqli_affected_rows($con) === 1){
			$notification = 'Inventory Successfully added';
			unset($_SESSION['product_id'], $_POST, $row, $value, $_SESSION['img_name']);
			$product_id = 0;
		} else {
			$notification = 'Inventory could not be added. please try again later. ';
		}
	}
}

?>

<div style="margin-top:15px" id="AdminContainer" class="container">
<div class="row">
<div class="col-lg-12">
<?php
	if(isset($notification))  {
		echo '<div class="center-block" style="width:100%; margin-top:30px; margin-bottom:15px; height:40px; padding:10px 30px; background-color: #C1FFC1; border-left:4px solid #006600; color:#006600">'
		 . $notification . '</div>';
	}
?>
<div class="col-lg-12" style="border:1px solid #cccccc; background-color:white;padding:40px 15px; border-top:4px solid #999999">
<h4>Add Inventory</h4>
<div class="col-lg-3">
<?php //echo $_SESSION['img_name']; ?>
<?php echo 'Product ID : ' . $product_id; ?>
<?php echo 'Product ID Sess: ' . $_SESSION['product_id']; ?>
<form id="upload_img_form" action="upload_image.php" method="post" enctype="multipart/form-data">
<?php  	if ($reg_errors['image']){
		echo $reg_errors['image'];
	}
?>
<input type="file" name="img" id="img"/>
<p id="feedback">
<?php 
if (isset($_SESSION['img_name'])) {
	 echo '<img height="100" width="100"  src="images/'. $_SESSION['img_name'].'"/>' ;
}
?>
</p>
<button type="submit">Upload</button>
</form>
</div>

<div class="col-lg-9">
<form id="add_inventory_form" action="inventory.php" method="post">
<label for="category">Category</label>
<select name="category">
<?php
$q = "SELECT id, category FROM categories ORDER BY id ASC";
$r = mysqli_query($con, $q);
if(mysqli_num_rows($r)>0){
		while($roww = mysqli_fetch_array($r, MYSQLI_ASSOC)){
		echo '<option value="' .$roww['id']. '"';
		if ($roww['id'] === $value['categories_id'] || $roww['id'] === $value['category'] ) echo 'selected="selected"';
		echo '>' .$roww['category']. '</option>';
		}
}
?>

</select>
<label for="brand">Brand</label>
<select name="brand">
<?php
$q = "SELECT id, brand FROM brands ORDER BY id ASC";
$r = mysqli_query($con, $q);
if(mysqli_num_rows($r)>0){
		while($roww = mysqli_fetch_array($r, MYSQLI_ASSOC)){
		echo '<option value="' .$roww['id']. '"';
		if ($roww['id'] === $value['brands_id'] || $roww['id'] === $value['brand'] ) echo 'selected="selected"';
		echo '>' .$roww['brand']. '</option>';
		}
}
?>
</select>
<label for="os">OS</label>
<select name="os">
<?php
$q = "SELECT id, os FROM os ORDER BY id ASC";
$r = mysqli_query($con, $q);
if(mysqli_num_rows($r)>0){
		while($roww = mysqli_fetch_array($r, MYSQLI_ASSOC)){
		echo '<option value="' .$roww['id']. '"';
		if ($roww['id'] === $value['os_id'] || $roww['id'] === $value['os'] ) echo 'selected="selected"';
		echo '>' .$roww['os']. '</option>';
		}

}
?>

</select>
<?php
    
	if ($reg_errors['name']){
		echo $reg_errors['name'];
	}
  echo '<label for="name">Name</label><input type="text" value="' . $value['name'] . '" name="name"  placeholder="*name"/>';
  	if ($reg_errors['description']){
		echo $reg_errors['description'];
	}
  echo '<label for="description">Description</label><input type="text" value="' . $value['description'] . '" name="description" placeholder="*description"/>';

  	if ($reg_errors['price']){
		echo $reg_errors['price'];
	}
  echo '<label for="price">Price</label><input type="text" value="' . $value['price'] . '" name="price" placeholder="*price"/>';
  	if ($reg_errors['stock']){
		echo $reg_errors['stock'];
	}
  echo '<label for="Stock">Stock</label><input type="text" name="stock" value="' . $value['stock'] . '" placeholder="*stock"/>';
?>
<button type="submit">GO</button>

</form>
</div>


</div>
</div>
</div>
</div>

<footer>
<script src="jquery.js"></script>
<script src="bootstrap.min.js"></script>
<script>
$(function(){
	
	$('#upload_img_form').on('submit', function(e) {
		e.preventDefault();

		$.ajax({
			
			url: "upload_image.php",
			type: "POST",
			data: new FormData(this),
			contentType: false,
			processData:false,
			success: function(data){
				//$('#upload_img_form #feedback').html(data);
				//$('#upload_img_form #feedback').fadeOut(5000);
				$('#upload_img_form #feedback').html('<img width="100" height="100" id="rtv" src=""/>').fadeIn(400);
				$('#rtv').attr('src', data);
			}
		});
		
	});
});
</script>

</footer>