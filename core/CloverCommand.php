<?php
/**
* Clover Core Class For Command Line
* Copyright 2015 EJSE
* Under MIT License
* Version 0.2 Updated on Dec 26 2015
*/
class CloverCommand
{
	
	function __construct()
	{
		# code...
	}

	public function defaultAction(){
		echo "Clover Command Mode, Copyright 2015 Sinri Edogawa, Licensed with MIT.".PHP_EOL;
	}

	public function beforeExecute($controller_name,$action_name,$parameter_list){
		// To be overridden
		echo "======".PHP_EOL;
		echo "CloverCommand before execute [".$controller_name."->".$action_name."] with parameter_list:".PHP_EOL;
		foreach ($parameter_list as $key => $value) {
			echo $key." = ".$value.PHP_EOL;
		}
		echo "======".PHP_EOL;
	}

	public function afterExecute($controller_name,$action_name,$parameter_list){
		// To be overridden
		echo "======".PHP_EOL;
		echo "CloverCommand after execute [".$controller_name."->".$action_name."] with parameter_list:".PHP_EOL;
		foreach ($parameter_list as $key => $value) {
			echo $key." = ".$value.PHP_EOL;
		}
		echo "======".PHP_EOL;
	}
}
