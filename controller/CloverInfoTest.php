<?php
/**
* Clover Core Class
* Copyright 2015 EJSE
* Under MIT License
* Version 0.1 Updated on Dec 11 2015
*/
class CloverInfoTest extends CloverController
{
	
	function __construct()
	{
		# code...
	}

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

	function sample0($v1='d1',$v2='d2'){
		echo "this is CloverInfoTest->sample0([$v1],[$v2])";
	}

	function sample1(){
		var_dump( Clover::getStore('A') );

		Clover::setStore('A','aaa');

		var_dump( Clover::getStore('A') );
	}
}
?>