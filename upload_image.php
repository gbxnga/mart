<?php
require 'includes/config.inc.php';
require MYSQLI ;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {	

	if (is_uploaded_file($_FILES['img']['tmp_name']) && ($_FILES['img']['error'] === UPLOAD_ERR_OK)) {
		$errors = array();
		$file_name = $_FILES['img']['name'];
		$file_size = $_FILES['img']['size'];
		$file_tmp = $_FILES['img']['tmp_name'];
		$file_type = $_FILES['img']['type'];
		
		// explode file name delimited by fullstop, 
		// and extract the file extension
		$file_ext = strtolower(end(explode('.',$_FILES['img']['name'])));
		
		// declare accepted file extensions
		$extensions = array("jpeg", "jpg", "png");
		
		// check if file extension matches accepted extensions
		if (!in_array($file_ext, $extensions)) {
			
			$errors[] = "Extension not allowed";
		}
		
		// if file size is larger than 2MB,
		// declare and error
		if ($file_size > 2097152) {
			
			$errors[] = "File size exceeded 2MB";
		}
		
		// if there are no errors, 
		// move uploaded files 
		if (empty($errors)) {
			
			// Create a tmp_name for the file:
			//$tmp_name = sha1($_FILES['img']['name']) . uniqid('',true);
			$tmp_name = $_FILES['img']['name'];
	
			// Move the file to its proper folder but add _tmp, just in case:
			//$dest =  IMGS_DIR . $tmp_name . '.'.$file_ext;
			$dest =  IMGS_DIR . $tmp_name ;

			if (move_uploaded_file($file_tmp, $dest)){
				//echo "File successfully uploaded";
				$_SESSION['img_name'] = $tmp_name;
				echo 'http://localhost/mart/images/'. $tmp_name;
			}else {
				echo "File couldn't be moved, please try again";
			}
			
		}else { // if there are errors, display them
		
			print_r($errors);
		}
	}else {
		echo "Error occured during upload, please try again";
	}
}