<?php
/**
* Clover Sample Class For Web Request
* Copyright 2015 EJSE
* Under MIT License
* Version 0.2 Updated on Dec 26 2015
*/
class CloverInfoTest extends CloverController
{
	
	function __construct()
	{
		# code...
	}

	/**
	 * It is routed as CLOVER_ROOT/CloverInfoTest
	 * It displays request information
	 */
	function index(){
		$assignment=array(
			'title'=>'INDEX',
			'get'=>Clover::getQuery(),
			'post'=>Clover::getData(),
			'request'=>Clover::getRequest(),
			'server'=>Clover::getServer(),
			'controller_index'=>Clover::getControllerIndex(),
			'controller'=>Clover::getController(),
		);

		Clover::display('CloverInfoTest.htm',$assignment);
	}

	/**
	 * It is routed as CLOVER_ROOT/CloverInfoTest/sample0(/v1(/v2))
	 * It shows the controller and options with correct route
	 */
	function sample0($v1='default_value_1',$v2='default_value_2'){
		echo "This is CloverInfoTest->sample0([$v1],[$v2])";
	}

	/**
	 * It is routed as CLOVER_ROOT/CloverInfoTest/sample2
	 * It shows the usage of Clover Storage
	 */
	function sample1(){
		var_dump( Clover::getStore('A') );

		Clover::setStore('A','aaa');

		var_dump( Clover::getStore('A') );
	}
}
?>