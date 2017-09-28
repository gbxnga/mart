<?php
require 'includes/config.inc.php';
require MYSQLI ;

/**if (!isset($_COOKIE['SESSION_ADMIN'])) {
	$location = 'http://' . BASE_URL ;
	header("Location: $location");
	exit();
}**/

$page_title = 'Orders';
include 'includes/header_checkout.php';

// if ( ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) && (isset($_COOKIE['SESSION_ADMIN'])) ) {
if ( ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) ) {

	$order_id = escape_data($_GET['id'], $con) ;

	// carry out additional actions on orders
	if (isset($_GET['action']) && $_GET['action'] === 'confirm_dispatch') {

		$q = "UPDATE order_contents SET ship_date = NOW() WHERE order_id = $order_id ";
		$r = mysqli_query($con, $q);
		if (mysqli_affected_rows($r) === 1) {
		}
	}


	$q = "SELECT o.id AS id, CONCAT(first_name, ' ',last_name) AS name,email,phone, CONCAT(address1, ' ',address2) AS address, city, state, zip, o.total,
			o.shipping, o.credit_card_number AS cc, oc.ship_date, date_format(o.order_date, '%D %M, %Y') AS date FROM orders o
			INNER JOIN customers c ON c.id = o.customer_id
			INNER JOIN order_contents oc ON o.id = oc.order_id
			WHERE o.id = ? ";
	$stmt = $dbc->prepare($q);
	$stmt->execute(array($order_id));
	$row = $stmt->fetch();

?>

<div class="container" style="margin-top:15px;">
<div class="row">

<div class="col-lg-12" style=" margin-top:15px;">
<h4>Order Details</h4>
<div class="col-lg-12" style=" padding:0;background-color:white; border:1px solid #CCCCCC; height:80px; margin-bottom:30px; border-radius:4px">
<table>
            <thead>
                <tr bgcolor="#F0F0F0" height="40" >
                    <th width="300" align="center"  scope="col"><span>Order ID</span></th>
                    <th width="300"  scope="col"><span>Order Total</span></th>
                    <th width="250"  scope="col"><span>Shipping</span></th>
                    <th align="right" width="350"   scope="col"><span>Credit Card Digit</span></th>
                </tr>
            </thead>
            <tr height="40">
            <?php
			echo '<td>'.$row['id'].'</td>';
			echo '<td>#'.$row['total'].'</td>';
			echo '<td>#'.$row['shipping'].'</td>';
			echo '<td>'.$row['cc'].'</td>';
			?>
            </tr>
            <tbody>
            </tbody>
</table>
</div>
</div>


<div id="cust_details_orders_page" class="col-lg-6">
<div id="p" class="col-lg-12" style="background-color:white; border:1px solid #CCCCCC; height:400px; border-radius:4px">
<h4 style="border-bottom:1px solid #CCCCCC; padding:7px 3px">Customer Details</h4>
<p><label>Name: </label> <span><?php echo $row['name'] ?></span></p>
<p><label>Email Address: </label> <span><?php echo $row['email'] ?></span></p>
<p><label>Phone Number: </label> <span><?php echo $row['phone'] ?></span></p>
<p><label>Address: </label><span> <?php echo $row['address'] ?></span></p>
<p><label>City: </label> <span><?php echo $row['city'] ?></span></p>
<p><label>State: </label> <span><?php echo $row['state'] ?></span></p>
<p><label>Zip Code: </label> <span><?php echo $row['zip'] ?></span></p>
<p><label>Order Date: </label> <span><?php echo $row['date'] ?></span></p>
<p><label>Status: </label> <span>
<?php
if($row['ship_date'] !== NULL){
	 echo 'Shipped' ;
}else {
	echo 'Awaiting Shipping' ;
	if (isset($_COOKIE['SESSION_ADMIN'])) {
		echo '<p style="clear:both; margin-top:30px;"><a style=" background-color:#cccccc; padding:7px; color:black " href="orders.php?id='.$order_id.'&action=confirm_dispatch">CONFIRM ORDER DISPATCH</a></p>';
	}
}
?>
</span></p>


</div>
</div>


<div class="col-lg-6">
<div class="col-lg-12" style="background-color:white; border:1px solid #CCCCCC; height:400px; overflow:scroll; border-radius:4px">
<h4 style="border-bottom:1px solid #CCCCCC; padding:7px 3px">Items Ordered</h4>
<table>
            <thead>
                <tr bgcolor="#f0f0f0" height="40" >
                    <th width="200"  scope="col"><span>Item</span></th>
                    <th width="250"  scope="col"><span>Name</span></th>
                    <th width="100"  scope="col"><span>Quantity</span></th>
                    <th align="right" width="150"   scope="col"><span>Price</span></th>
                </tr>
            </thead>

            <tbody>
<?php
$q = "SELECT product_id AS id, quantity FROM `order_contents` WHERE order_id = $order_id";
$r = mysqli_query($con, $q);
while ($row2 = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
	$product_id = $row2['id'];
	$q2 = "SELECT id, categories_id, sale, name, image FROM product p WHERE p.id = $product_id";
	$stmt = $dbc->query($q2);
	$result = $stmt->fetch();

	echo '<tr style="border-bottom:1px solid #cccccc; padding:10px ">';
	echo '<td><a href="product.php?id='.$result['id'].'&c='.$result['categories_id'].'"><img width="100" height="100" src="images/'.$result['image']. '"/></a></td>';
	echo '<td>'.$result['name'] . '</td>';
	echo  '<td>' . $row2['quantity'] . '</td>';
	echo '<td> #' . $result['sale'] . '</td>';
	echo '</tr>';
}
?>
</tbody>
</table>
</div>
</div>




</div>
</div>

<?php
} else {
?>
<div>
	<form id="enter_order_id_form"   class="center-block" action="<?php echo $pre ; ?>/orders.php" method="get">
		<p> Please enter order id : </p>
		<input type="text" name="id"/>
		<button type="submit">VIEW ORDER</button>
	</form>
</div>
<?php
}
