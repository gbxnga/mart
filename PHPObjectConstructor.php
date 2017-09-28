<?php
	class Employee {
		public $name;
		public function __construct($name="Anonymous", $position="Not Set", $salary=0){
			$this->name = $name;
			$this->position = $position;
			$this->salary = $salary;
		}
		public function describeEmployee(){
			return "The Employee, $this->name, holds the position of a $this->position, and earns #$this->salary per annum.";
		}
	}
 ?>