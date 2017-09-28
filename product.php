<?php
require 'includes/config.inc.php';
require (MYSQLI);

// cache script
//include 'includes/product-top-cache.php';


// validate if returned value is an integer
if($category_id = filter_var($_GET['c'], FILTER_VALIDATE_INT)){
	// getting the category ID
	$category_id = escape_data($category_id, $dbc);
	$category_id = xss_clean($category_id);
}else {
	// take user to error page
	//$_SESSION['err-msg'] = 'You entered and invalid category ID';
	//header("Location: http://".BASE_URL."/404.php");
}

include 'includes/header.php';

/*$p_name = $_GET['p_name'];
$p_name = str_replace(array('-'), ' ', $p_name);*/
if (isset( $_GET['id']) && $product_id = filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
	$product_id = escape_data($product_id, $dbc);
	$product_id = xss_clean($_GET['id']);
}else {
?>
<div class="col-lg-8 col-lg-offset-2 alert alert-danger"> You have not selected a product to be viewed. </div>
<?php
include 'includes/footer.php';
exit();
}

$sql = "SELECT  b.id AS brand_id, brand, os.id AS os_id, os, c.id AS category_id, category,
		p.id AS product_id, name, p.description AS description, p.image, price, sale, stock FROM product p
INNER JOIN categories c ON p.categories_id = c.id
INNER JOIN os ON p.os_id = os.id
INNER JOIN brands b ON p.brands_id = b.id
WHERE p.id = $product_id";
/*$stmt = $dbc->prepare($sql);
$stmt->execute(array($product_id));
$row = $stmt->fetch();*/

$r = mysqli_query($con, $sql);
if (mysqli_num_rows($r) === 1) {
	$row = mysqli_fetch_array($r, MYSQLI_ASSOC);



// product page navigation
echo '<div id="productNav" class="container-fluid"><div class="row"><div class="col-lg-5 col-lg-offset-1" ><a href="http://'.BASE_URL.'">Home</a> »
		<a href="'.$pre.'/category.php?id=' . $row['category_id'] . '">' . $row['category'] . '</a> »
		<a href="'.$pre.'/brand.php?id=' . $row['brand_id'] . '&c='.$row['category_id'].'">' . $row['brand'] . '</a> » ' . $row['name'] . ' </div></div></div>';
?>
<div class="container">

<div style="margin-top:15px; color: "  class="row">
<div class="col-lg-5">
<div class="ro">
<div id="product-image-inner-div" class="col-lg-12">
    <div id="jssor_1" style="position: relative; margin:15px auto; margin-left:-10px;top: 0px; left: 0px; width: 1300px; height: 1000px; overflow: hidden; visibility: hidden;">
        <!-- Loading Screen -->
        <div data-u="loading" style="position: absolute; top: 0px; left: 0px;">
            <div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block; top: 0px; left: 0px; width: 100%; height: 100%;"></div>
            <div style="position:absolute;display:block;background:url('http://<?php  echo $pre;  ?>/img/loading.gif') no-repeat center center;top:0px;left:0px;width:100%;height:100%;"></div>
        </div>
        <div data-u="slides" style="cursor: default; position: relative; top: 0px; left:25%; width: 600px; height: 530px; overflow: hidden;">
            <div data-p="112.50">

                <img data-u="image" src="<?php echo $pre ; ?>/images/<?php echo $row['image']; ?>" />
                <img data-u="thumb" src="<?php echo $pre ; ?>/images/<?php echo $row['image']; ?>" />
            </div>
            <a data-u="any" href="http://www.jssor.com" style="display:none">Image Slider 2</a>
            <div data-p="112.50" style="display: none;">
                <img data-u="image" src="<?php echo $pre ; ?>/images/infinix-hot-3.jpg" />
                <img data-u="thumb" src="<?php echo $pre ; ?>/images/infinix-hot-3.jpg" />
            </div>
            <div data-p="112.50" style="display: none;">
                <img data-u="image" src="<?php echo $pre ; ?>/images/infinix-hot-4.jpg" />
                <img data-u="thumb" src="<?php echo $pre ; ?>/images/infinix-hot-4.jpg" />
            </div>
            <div data-p="112.50" style="display: none;">
                <img data-u="image" src="<?php echo $pre ; ?>/images/infinix-hot-s.jpg" />
                <img data-u="thumb" src="<?php echo $pre ; ?>/images/infinix-hot-s.jpg" />
            </div>
        </div>
        <!-- Thumbnail Navigator -->
        <div data-u="thumbnavigator" class="jssort03" style="position:absolute;left:0px;bottom:0px;width:700px;height:100px;" data-autocenter="1">
            <div style="position: absolute; top: 0; left: 0; width: 100%; height:100%; background-color: #000; filter:alpha(opacity=30.0); opacity:0.3;"></div>
            <!-- Thumbnail Item Skin Begin -->
            <div data-u="slides" style="cursor: default;">
                <div data-u="prototype" class="p">
                    <div class="w">
                        <div data-u="thumbnailtemplate" class="t"></div>
                    </div>
                    <div class="c"></div>
                </div>
            </div>
            <!-- Thumbnail Item Skin End -->
        </div>
        <!-- Arrow Navigator -->
        <span data-u="arrowleft" class="jssora02l glyphicon glyphicon-chevron-left" style=" font-size:32px;top:0px;left:8px;width:55px;height:55px;" data-autocenter="2"></span>
        <span data-u="arrowright" class="jssora02r glyphicon glyphicon-chevron-right" style=" font-size:32px;top:0px;right:8px;width:55px;height:55px;" data-autocenter="2"></span>
    </div>
    <!-- #endregion Jssor Slider End -->

</div>
</div>
</div>
<div  class="col-lg-7">
<div id="product-details-div" class="col-lg-12">
<h4><b><?php echo $row['name']; ?></b></h4>
<p>
<span style="color: #FF9900" class="glyphicon glyphicon-star"></span>
<span style="color: #FF9900" class="glyphicon glyphicon-star"></span>
<span style="color: #FF9900" class="glyphicon glyphicon-star"></span>
<span style="color:" class="glyphicon glyphicon-star-empty"></span>
<span class="glyphicon glyphicon-star-empty"></span>
</p>
<p>
<span style=" font-size:18px"><?php echo '#'.$row['sale']; ?> <span style="text-decoration:line-through; color: #666666"><?php echo $row['price']; ?></span></span>
</p>
<p>Availability: <b style="color:#FF9900"><?php if ($row['stock'] >0) {echo 'IN STOCK';}else {echo 'OUT OF STOCK';} ?></b></p>
<hr/>
<p>
You smart, you loyal, you a genius. The key is to enjoy life, because they don't want you to enjoy life.
I promise you, they don't want you to jetski !.You smart, you loyal, you a genius.
The key is to enjoy life, because they don't want you to enjoy life. I promise you, they don't want you to jetski !.
You smart, you loyal, you a genius. The key is to enjoy life, because they don't want you to enjoy life.
I promise you, they don't want you to jetski !. I promise you, they don't want you to jetski !.You smart, you loyal, you a genius.
The key is to enjoy life, because they don't want you to enjoy life. I promise you, they don't want you to jetski !.
</p>
<!-- Select quantity form goes here -->
<form method="get" action="<?php echo $pre; ?>/cart.php" id="addToCartForm">
Quantity:
<?php  echo '<input name="id" hidden="hidden" type="text" value="'.$product_id.'">' ; ?>
<input name="qty" style="width:45px; height:30px; padding-left:15px" type="number" value="1">
<input name="action" hidden="hidden" type="text" value="add">
<button name="butt" value="cart" type="submit">
<span class="	glyphicon glyphicon-shopping-cart"> </span>  ADD TO CART</button>
<button name="butt" value="wishlist"  type="submit">
<span class="glyphicon glyphicon-calendar"> </span>  ADD TO WISHLIST</button>
<?php
if (isset($_COOKIE['SESSION_ADMIN'])) {
	print '<a href="'.$pre.'/inventory.php?id='.$product_id.'&action=edit">
<span class="glyphicon glyphicon-edit"> </span>EDIT INVENTORY</a/>';
	print '<a href="'.$pre.'/inventory.php?id='.$product_id.'&action=delete">
<span class="glyphicon glyphicon-edit"> </span>DELETE INVENTORY</a/>';
}
?>
</form>

</div>
</div>
</div>
</div>

<!-- Product Details and reviews container -->

<div class="container">
<div class="row">
<div style=" margin-top:15px" class="col-lg-12">
<div style="border:1px solid #CCCCCC; background-color:white; transition:0.3s; heigh:500px;" class="col-g-12">

<div id="tabs-container">

	<ul class="tabs">
		<li class="tab-link current" data-tab="tab-1">Details</li>
		<li class="tab-link" data-tab="tab-2">Reviews</li>
	</ul>

	<div id="tab-1" class="tab-content current">
    The sporty Joust Duffle Bag can't be beat - not in the gym, not on the luggage carousel, not anywhere. Big enough to haul a basketball or soccer ball and some sneakers with plenty of room to spare, it's ideal for athletes with places to go.

    <ul>
    <li>Dual top handles.</li>
    <li>Adjustable shoulder strap.</li>
    <li>Full-length zipper.</li>
    <li>L 29" x W 13" x H 11".</li>
    </ul>
	</div>
	<div id="tab-2" class="tab-content">
       <p> Newest First | Oldest First</p>
       <!--<div id="aReview">
        TO -
        <b>Taslim O</b>
       <span style="color:#999999"> November 2, 2016</span>
       <p> Mac delivered in less than 24 hours. And a surprise (bigger RAM) was included by the seller. Thanks for that!</p>
        </div>
        <div id="aReview">
        AL -
        <b>Ali L</b>
        <span style="color:#999999"> March 8, 2016</span>
        <p>The item was delivered within 24hrs by the seller.</p>
        </div>-->
        <?php
		$q = "SELECT nickname, surname, review, date_format(date_created, '%M %d, %Y') AS date FROM reviews WHERE product_id = $product_id";
		$r = mysqli_query($con, $q);
		if (mysqli_num_rows($r) > 0) {
			while ($row= mysqli_fetch_array($r,MYSQLI_ASSOC)) {
				echo '<div id="aReview">';
				echo $row['nickname']. ' - ';
				echo '<b>'. $row['surname'] . ' </b>';
				echo '<span style="color:#999999">' .$row['date'].'</span>';
				echo '<p>'.$row['review'].'</p>';
				echo '</div>';
			}
		}else {
			echo '<span style="color:#999999">No reviews yet.</span>';
		}
		?>

        <hr style="background-color:#333333"/>

       <p> <b>WRITE A REVIEW :</b></p>

        <div>
        <form id="review_form" method="post" action="review.php">
        <p style="height:auto; color: #006600" id="review_feedback"></p>
        <label>Surname <span style="color:red">*</span></label><input name="surname" type="text"/>
        <label>Nickname <span style="color:red">*</span></label><input name="nickname" type="text"/>
        <label>Review <span style="color:red">*</span></label><textarea name="review" type="text"></textarea>
        <input hidden="hidden" name="product_id" type="number" value="<?php echo $product_id ;?>" />
        <button type="submit">SUBMIT REVIEW</button>
        </form>
        </div>
	</div>

</div><!-- container -->

</div>
</div>
</div>
</div>

<!-- End of Product Details and reviews container -->

<!-- Related products container -->
<?php
}else {
	?>
<div class="container">
<div class="row">
<div class="col-lg-12">
    <?php
	$notification = 'Product doesnt exist. '. $product_id . $category_id . $p_name. ' ' . $_GET['p_name'];
		echo '<div class="center-block" style="width:100%; margin-top:30px; margin-bottom:15px; height:40px; padding:10px 30px; background-color: #C1FFC1; border-left:4px solid #006600; color:#006600">'
		 . $notification . '</div>';
}
if (!empty($row['brand_id'])){
	$brands_id = $row['brand_id'];
}else {
	$brands_id = rand(1,4);
}
?>
</div>
</div>
</div>
<div class="container">
<div class="row">
<div style=" margin-top:15px" class="col-lg-12">
<div style="border:1px solid #CCCCCC; background-color:white; transition:0.3s; heigh:300px;" class="col-lg-12">
<h4>Related items</h4>

<?php
// code to get related items to selected product
$sql = "SELECT id, name, price, image FROM product p WHERE p.brands_id = $brands_id  ORDER BY RAND() LIMIT 4";
$request = $dbc->query($sql);
while ($row = $request->fetch()) {
	$col = 3;
	include 'views/product_view.html';
}
 ?>


</div>
</div>
</div>
</div>

<!-- End of Related products container -->

<?php
include 'includes/footer.php';
//include 'includes/bottom-cache.php';
?>
