<?php
require "OOP2.php";

class ForumTopic extends Node {
	private $debugMessages ;

	public function __construct() {
		parent:: __construct ();
		$this->debug(__CLASS__." constructor called.");
	}
	
	public function __destruct() {
		$this->debug(__CLASS__. " destructor called.");
		parent:: __destruct();
	}
	
	public function myFunc () {
		$this->debug(__CLASS__. " destructor calledddd.");
	}
	
	public function getView() {
		return " This is  view into ". __CLASS__;
	}
}

$forum = new ForumTopic();
$forum->myFunc();
echo" Thiis is the class object 'Forum' was created with: <b>". get_class($forum). "</b><br/>";
//echo $forum->getView();

?>