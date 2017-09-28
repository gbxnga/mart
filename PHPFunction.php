<?php
// This is a function to roll a die 5 times, 
// outputting the value for each time 
function rollDie() {
	$roll = rand(1,6);
	echo $roll . ' ';
}

for ($i = 0; $i < 5; $i++) {
	rollDie();
}
?>

