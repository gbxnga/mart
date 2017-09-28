<?php




if ($live) {	
	define('DB_HOST', 'mart-db.mysql.database.azure.com');
	define('DB_NAME', 'phonemart');
	define('DB_USER', 'onigbenga1@mart-db');
	define('DB_PASSWORD', 'Revelation1');
}else {
	define('DB_HOST', 'localhost');
	define('DB_NAME', 'phonemart');
	if ($theadmin = true) {
		define('DB_USER', 'app_rw');
	}else {
		define('DB_USER', 'app_readonly');
	}
	define('DB_PASSWORD', 'revelation1');
}

// set database connection for php PDO
$host_db = 'mysql:host='.DB_HOST.';dbname='. DB_NAME;
$dbc = new PDO($host_db, DB_USER, DB_PASSWORD);
$dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// set database connection for mysqli
$con = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
mysqli_set_charset($con, 'utf8');