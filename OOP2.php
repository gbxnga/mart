<?php

abstract class Node {  
  	private $debugMessages;
  	public function __construct() {    
		$this->debugMessages = array();    
		$this->debug(__CLASS__." constructor called.");
  }
  public function __destruct() {    
  	$this->debug(__CLASS__." destructor called.");    
	$this->dumpDebug();  
  }
  protected function debug( $msg ) {    
  	$this->debugMessages[] = $msg;  
  }
  private function dumpDebug( ) {    
  	echo implode( "\n", $this->debugMessages);  
  }
  public abstract function getView(); 
} 