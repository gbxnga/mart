<?php

require 'includes/config.inc.php';
require MYSQLI;
include 'includes/header.php';

?>
<div class="container">
<div class="row">
<div class="col-lg-12">

<h4 class="center-block">Error: </h4>
<p class="center-block">The page you have requested was not found.</p>
<p class="center-block"><?php echo $_SESSION['err-msg']; ?></p>
</div>
</div>
</div>

<?php

include 'includes/footer.php';
