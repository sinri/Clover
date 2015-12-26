<?php
/**
* Clover Core Class For Web Request
* Copyright 2015 EJSE
* Under MIT License
* Version 0.2 Updated on Dec 26 2015
*/
class CloverController
{
	
	function __construct()
	{
		# code...
	}

	public function index(){
		Clover::display('CloverIndex.htm');
	}
}
