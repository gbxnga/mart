<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
<ol>
<?php
	//require_once("PHPObjectConstructor.php");
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

	$employee1 = new Employee("Oni Gbenga", "Manager", 2000000);
	$employee2 = new Employee();
	$employee2->name = "Olabode Thomas";
	$employee2->salary = 5554443;
	$employee2->position = "Supervisor";
	print "<li>" . $employee1->describeEmployee() .  "</li>";
	print "<li>" . $employee2->describeEmployee() .  "</li>";
	
	class FurtherEmployee extends Employee{
		public function employeeRemark(){
			return " Also, $this->name is a nice guy!";
		}
	}
	
	$employee3 = new FurtherEmployee("Oni Seun", "Digital Marketer", 2000000);
	print "<li>" . $employee3->describeEmployee() . $employee3->employeeRemark() . "</li>"; 

	
/**	$a = $employee1->describeEmployee();
	$b = $employee2->describeEmployee();
	$output = <<<HERE
	 	 $a
		 $b
	
HERE;
	
	$fc = fopen("contacts.txt", "a");
	fwrite($fc, $output);
	fclose($fc);
**/
?>
</ol>
</body>
</html>