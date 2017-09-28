<?php
require 'includes/config.inc.php';
require (MYSQLI);

$category_id = $_GET['id'];
$cat_name = $_GET['cat_name'];
include 'includes/header.php';

//if page value is set, append to $p variable
if (isset($_GET['p'])) {
$p = $_GET['p'];
}else { // else set page value to zero - first-page
	$p=0;
}

include 'views/navigation.php';
?>



<div style="margin-top:15px" class="container">
<div class="row">
<?php include 'views/shopping_options_aside.html'; ?>
<div style="border:1px solid #CCCCCC; background-color:white; padding-top:15px;" class="col-lg-9">
<div class="row">
<?php
$p = $p * 12;
$q2 = "SELECT * FROM `product` WHERE product.categories_id = $category_id
AND product.price > 0 LIMIT $p, 12";
$q = "CALL select_products_by_category('$category_id', '12')";
$result = $dbc->query($q2);
while ($row = $result->fetch()){
	include 'views/product_view.html';
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
  echo '<li><a href="'.$pre.'/category/'.$category_id.'/'.$cat_name.'/page-0">&laquo;</a></li>';
  $q = "SELECT count(*) AS number FROM product p WHERE p.categories_id = $category_id";
  $result = $dbc->query($q);
  $row = $result->fetch();
  $number = $row['number'];
  $pages = ceil($number / 12);
  $page = 1;
  $pp = 0;
  $p = $_GET['p'];
  $p=$p+1;
	  
  while ($page <= $pages) {
	  // initializing the $active variable as an empty string
	  $active = "";
	  // If current page, append the value "active" to $active variable
	  if ($page === $p) {$active = 'active';}
	  echo '<li><a class="'.$active.'" href="'.$pre.'/category/'.$category_id.'/'.$cat_name.'/page-'.$pp++.'">'. $page++ . '</a></li>';
  }
  $last_page = $pages - 1;
  echo '<li><a href="'.$pre.'/category/'.$category_id.'/'.$cat_name.'/page-'.$last_page.'">&raquo;</a></li>';

?>
</ul>
</div>
</div>
</div>
<!-- pagination ends here -->

<?php
include 'includes/footer.php';
?>