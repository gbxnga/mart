<?php
require 'includes/config.inc.php';
require (MYSQLI);
include 'includes/header.php';
$os_id = $_GET['id'];
$category_id = $_GET['c'];
$os_name = $_GET['os_name'];

//if page value is set, append to $p variable
if (isset($_GET['p'])) {
$p = $_GET['p'];
}else { // else set page value to zero - first-page
	$p=0;
}

$show_os_nav = true;
include 'views/navigation.php';
?>

<div style="margin-top:15px" class="container">
<div class="row">
<?php include 'views/shopping_options_aside.html'; ?>
<div style="border:1px solid #CCCCCC; background-color:white; padding-top:15px;" class="col-lg-9">
<div class="row">
<?php
$p = $p * 6;
$q2 = "SELECT * FROM `product` WHERE product.os_id = $os_id
AND product.price > 0 LIMIT $p, 6";
$result = $dbc->query($q2);
$row_count = $result->rowCount();
if ($row_count > 0) {
	while ($row = $result->fetch()){
		include 'views/product_view.html';
	}
}else {
	echo '<div class="col-lg-12">';
	echo '<div class="alert alert-danger">';
	echo 'There are no products available for selected OS.';
	echo '</div>';
	echo '</div>';
}
?>

</div>
</div>
</div>
</div>


<!-- Container for pagination -->
<div class="container">
<div class="row">
<div style="borde:1px solid red; height:100px" class=" col-xs-12 col-lg-4 col-lg-offset-8">
<ul class="pagination">
 <?php
   echo '<li><a href="'.$pre.'/OS/'.$os_id.'-'.$category_id.'/'.$os_name.'/page-0">&laquo;</a></li>';
  $q = "SELECT count(*) AS number FROM product p WHERE p.os_id = $os_id";
  $result = $dbc->query($q);
  $row = $result->fetch();
  $number = $row['number'];
  $pages = ceil($number / 6);
  $page = 1;
  $pp = 0;
  $p = $_GET['p'];
  $p=$p+1;
	  
  while ($page <= $pages) {
	  // initializing the $active variable as an empty string
	  $active = "";
	  // If current page, append the value "active" to $active variable
	  if ($page === $p) {$active = 'active';}
	  echo '<li><a class="'.$active.'" href="'.$pre.'/OS/'.$os_id.'-'.$category_id.'/'.$os_name.'/page-'.$pp++.'">'. $page++ . '</a></li>';
  }
  $last_page = $pages - 1;
  echo '<li><a href="'.$pre.'/OS/'.$os_id.'-'.$category_id.'/'.$os_name.'/page-'.$last_page.'">&raquo;</a></li>';

?>
</ul>
</div>
</div>
</div>
<!-- pagination ends here -->

<?php
include 'includes/footer.php';
?>