<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<?php
$thearray = array();
while ( sizeof($thearray) < 5){ // while array lenght isnt 3 
	$in = rand(1, 10);
	// echo $in;
	if(!array_key_exists($in, $thearray)) $thearray[$in] = $in ;
}
 
foreach ($thearray as $key => $value){
	echo $value . ' ';
}
//print_r($thearray);

?>
</body>
</html>