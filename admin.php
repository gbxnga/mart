<?php
require 'includes/config.inc.php';
require MYSQLI ;

$page_title = 'Admin Page';
include 'includes/header_checkout.php';


if (isset($_COOKIE['SESSION_ADMIN'])) {
	
	if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		
		if ($_GET['action'] === 'add_category' && isset($_GET['category'])) {
			$category = escape_data($_GET['category'], $con);
			$q = "INSERT INTO categories (category) VALUES ('$category')";
			$r = mysqli_query($con, $q);
			if ($r) {
				
				$notification = "Category <strong>" . $category . "</strong> was successfully added";
			}else {
				$notification = "Couldnt add <strong>" . $category . "</strong> category. Please try again.";
			}
		}
		
		if ($_GET['action'] === 'add_brand' && isset($_GET['brand'])) {
			$brand = escape_data($_GET['brand'], $con);
			$q = "INSERT INTO brands (brand) VALUES ('$brand')";
			$r = mysqli_query($con, $q);
			if ($r) {
				
				$notification = "Brand <strong>" . $brand . "</strong> was successfully added";
			}else {
				$notification = "Couldnt add <strong>" . $brand . "</strong> brand. Please try again.";
			}
		}
		
		if ($_GET['action'] === 'add_OS' && isset($_GET['OS'])) {
			$OS = escape_data($_GET['OS'], $con);
			$q = "INSERT INTO os (os) VALUES ('$OS')";
			$r = mysqli_query($con, $q);
			if ($r) {
				
				$notification = "OS <strong>" . $OS . "</strong> was successfully added";
			}else {
				$notification = "Couldnt add <strong>" . $OS . "</strong> OS. Please try again.";
			}
		}
	}
	//$notification = 'You are logged in as : <strong>' . $_COOKIE['SESSION_ADMIN'] . '</strong>' ;		
?>
<div class="container">
<div class="row">
<div class="col-lg-12">
<?php
	if(isset($notification))  {
		echo '<div class="center-block" style="width:100%; margin-top:30px; margin-bottom:15px; height:40px; padding:10px 30px; background-color: #C1FFC1; border-left:4px solid #006600; color:#006600">'
		 . $notification . '</div>';
	}
?>
<div class="col-lg-12" style=" overflow:scroll;box-shadow:0px 0px 4px 0px #999999; border-top: 4px solid #FFBE58;background-color:white; height:300px;">
<p style="padding:4px; border-bottom:1px solid #CCCCCC"><strong>Orders: </strong></p>
<table>
            <thead>
                <tr bgcolor="#cccccc" >
                    <th width="200"  scope="col"><span>Name</span></th>
                    <th width="150"  scope="col"><span>City</span></th>
                    <th width="150"  scope="col"><span>State</span></th>
                    <th width="200"   scope="col"><span>Phone Number</span></th>
                    <th width="150"  scope="col"><span>Total
                    <th width="150"  scope="col"><span>Order date</span></th></span></th>
                    <th width="100"  scope="col"><span>View order</span></th></span></th>
                </tr>
            </thead>
            
            <tbody>
            <?php
			$q = "SELECT o.id AS id, CONCAT(first_name, ' ',last_name) AS name, city, state, phone, o.total, date_format(o.order_date, '%D %M') AS date FROM orders o
					INNER JOIN customers c ON c.id = o.customer_id";
			$r = mysqli_query ($con, $q);
			while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)){
				echo  '<a href="orders.php?id='.$row['id'].'"><tr>';
				echo  '<td>'.$row['name'].'</td>';
				echo  '<td>'.$row['city'].'</td>';
				echo  '<td>'.$row['state'].'</td>';
				echo  '<td>'.$row['phone'].'</td>';
				echo  '<td>#'.$row['total'].'</td>';
				echo  '<td>'.$row['date'].'</td>';
				echo  '<td><a href="orders.php?id='.$row['id'].'">More Details</a></td>';
				echo  '</tr></a>';
			}
			?>
            </tbody>
</table>
</div>
</div>
</div>
</div>

<div class="container">
<div class="row">


<div class="col-lg-4" >
<div class="col-lg-12" style="height:200px; border:1px solid #cccccc; background-color:white; margin-top:15px">
<h4>Add Category</h4>
<form method="get" action="admin.php">
<input hidden="hidden" name="action" value="add_category">
<input type="text" name="category"><br/>
<button  type="submit">ADD CATEGORY</button>
</form>
</div>
</div>
<div class="col-lg-4">
<div class="col-lg-12" style="height:200px;border:1px solid #cccccc; background-color:white; margin-top:15px">
<h4>Add Brand</h4>
<form method="get" action="admin.php">
<input hidden="hidden" name="action" value="add_brand">
<input type="text" name="brand"><br/>
<button  type="submit">ADD BRAND</button>
</form>
</div>
</div>
<div class="col-lg-4">
<div class="col-lg-12" style="height:200px; border:1px solid #cccccc; background-color:white; margin-top:15px">
<h4>Add OS</h4>
<form method="get" action="admin.php">
<input hidden="hidden" name="action" value="add_OS">
<input type="text" name="OS"><br/>
<button  type="submit">ADD OS</button>
</form>
</div>
</div>


</div>
</div>


<?php
	exit();
}

// create admin for error array 
$admin_form_errors = array();

// if theres a POST request 
// and admin session isnt set
// then its a login request
if (($_SERVER['REQUEST_METHOD'] === 'POST') && (!isset($_COOKIE['SESSION_ADMIN']))  ) {
	
	// Check for a first name:
	if (preg_match ('/^[A-Z0-9]{7,20}$/i', $_POST['username_email'])) {
		$us = escape_data($_POST['username_email'], $con);
	} else {
		$admin_form_errors['username_email'] = 'Please enter your username!';
	}
	
	// Check for a last name:
	if (preg_match ('/^[A-Z .0-9-]{7,40}$/i', $_POST['pass'])) {
		$ps  = escape_data($_POST['pass'], $con);
	} else {
		$admin_form_errors['pass'] = 'Please enter password!';
	}
	
	// if incoming details meet server requirement
	// query the database
	if (empty($admin_form_errors)) {
		
		$q = "SELECT username, pass FROM members WHERE username = '$us' AND type = 'admin'";
		/*$stmt = $dbc->prepare($q);
		$stmt->execute(array($us));
		$row = $stmt->fetch();*/
		$row = mysqli_query($con, $q);
		 
		if (mysqli_num_rows($row) === 1) { // if username exists
			$r = mysqli_fetch_array($row, MYSQLI_ASSOC);
		
			// crosscheck given password with database's
			if ($ps === $r['pass']) { // if condition is true, log user in
			
				setcookie('SESSION_ADMIN', $us, time()+(60*60*24*30));
				$notification = "You have successfully logged in.";
				unset($_POST['username_email'], $_POST['pass']);
			}else {
				$notification = "username and password do not match ones in database.";
			}
		}else {
				$notification = "username and password do not match ones in databaseeee.";
		}
	}
}

?>
<?php if(isset($notification))  
echo '<div class="center-block" style="width:35%; margin-top:30px; height:40px; padding:10px 30px; background-color: #C1FFC1; border-left:4px solid #006600; color:#006600">
' . $notification . '</div>';
?>
<div id="form_encompasser">
<form method="post"  action="admin.php" class="center-block" id="account_login_form">
<p> Please fill the form with your details to login</p>
<label for="username_email">Username/email: </label>
<?php if (isset($admin_form_errors['username_email'])) echo $admin_form_errors['username_email']; ?>
<br/><input type="text" name="username_email" value="<?php if (isset($_POST['username_email'])) echo $_POST['username_email']; ?>"/><br/>
<label for="pass">password: </label>
<?php if (isset($admin_form_errors['pass'])) echo $admin_form_errors['pass']; ?>
<br/><input name="pass" type="password" autocomplete="off"/><br/>
<button type="submit">Log me in</button>
</form>
</div>



<?php
/*include 'includes/footer.php';*/