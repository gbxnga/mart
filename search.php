<?php
require 'includes/config.inc.php';
require (MYSQLI);

include 'includes/header.php';

//if page value is set, append to $p variable
if (isset($_GET['p'])) {
$p = $_GET['p'];
}else { // else set page value to zero - first-page
	$p=0;
}

// retrieve search term through GET method
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_term'])) {
	
	// filter should come after escape data for optimum effect
	/*$search_term = escape_data($_GET['search_term'], $con);
	$search_term = filter_var($search_term, FILTER_SANITIZE_STRING);
	$search_term = xss_clean($search_term);*/
	
	$search_term = $_GET['search_term'];
}
?>

<div id="category-banner" class="container-fluid">
<div class="row">
<div class="col-lg-12">
<div class="row">
<img id="category-banner-img" style="width:100%;" src="images/tech.png" />
<div id="category-banner-details" class="col-lg-12"><div class="col-lg-offset-1 col-lg-3">
<p> <a href="http://<?= BASE_URL;?>">HOME</a> » 
<?php
$q = "SELECT c.id, category FROM categories c WHERE c.id = ?  ";
$stmt = $dbc->prepare($q);
$stmt->execute(array($category_id));
$row = $stmt->fetch();
echo '<a href="category.php?id='.$row['id'].'">'.strtoupper($row['category']). '</a>';
?></p>
</div></div>
</div>
</div>
</div>
</div>


<div style="margin-top:15px" class="container">
<div class="row">
<?php include 'views/shopping_options_aside.html'; ?>
<div style="border:1px solid #CCCCCC; background-color:white; padding-top:15px;" class="col-lg-9">
<div class="row">
<?php
$p = $p * 6;

$query = "SELECT * FROM product WHERE MATCH(name) AGAINST ('$search_term' IN NATURAL LANGUAGE MODE ) LIMIT $p, 6";

// if there is a result
// if query returns one or more results
$request = mysqli_query($con, $query);
if (mysqli_num_rows($request) > 0){
	while ($row = mysqli_fetch_array($request, MYSQLI_ASSOC)){
		include 'views/product_view.html';
	}
}else {
	$notification =  'No result found for ' . $search_term;

	if(isset($notification))  {
		echo '<div class="center-block" style="width:100%; margin-top:30px; margin-bottom:15px; height:40px; padding:10px 30px; background-color: #C1FFC1; border-left:4px solid #006600; color:#006600">'
		 . $notification . '</div>';
	}

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
  echo '<li><a href="search.php?search_term='.$search_term.'&p=0">&laquo;</a></li>';
	$query = "SELECT count(*) AS count FROM product WHERE MATCH(name) AGAINST ('$search_term' IN NATURAL LANGUAGE MODE )";
	
	// if there is a result
	// if query returns one or more results
	$request = mysqli_query($con, $query);
	$row = mysqli_fetch_array($request, MYSQLI_ASSOC);
	$number = $row['count'];
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
	  echo '<li><a class="'.$active.'" href="search.php?search_term='.$search_term.'&p='.$pp++.'">'. $page++ . '</a></li>';
  }
  $last_page = $pages - 1;
  echo '<li><a href="search.php?search_term='.$search_term.'&p='.$last_page.'">&raquo;</a></li>';

?>
</ul>
</div>
</div>
</div>
<!-- pagination ends here -->

<?php
include 'includes/footer.php';
?>