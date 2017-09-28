<?php
function setExpires($expires) {
	header('Expires: '.gmdate('D, d M Y H:i:s', time()+$expires).'GMT');
}
setExpires(10);
echo ( 'This page will self destruct in 10 seconds<br />');
echo ('The GMT is now' .gmdate('H:i:s').'<br />' );
echo ('<a href="'.$_SERVER['PHP_SELF'].'">View Again</a><br />' );
?>