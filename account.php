<?php
require 'includes/config.inc.php';
require MYSQLI ;

$page_title = 'Account: Sign in';

include 'includes/header_checkout.php';
?>

<div id="form_encompasser">
<form class="center-block" id="account_login_form">
<p> Please fill the form with your details to login</p>
<label for="username_email">Username/email: </label><br/><input type="text" name="username_email"/><br/>
<label for="pass">password: </label><br/><input name="pass" type="password"/><br/>
<button>Log me in</button>

<span style="float:right; width:30%"><a href="register.php">Not Registered?</a></span>
</form>
</div>

<?php
/*include 'includes/footer.php';*/