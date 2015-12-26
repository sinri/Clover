<?php
/**
* Clover Sample Class For Command Test
* Copyright 2015 EJSE
* Under MIT License
* Version 0.2 Updated on Dec 26 2015
*/
class CLITest extends CloverCommand
{
	
	function defaultAction()
	{
		echo "CLITest->defaultAction".PHP_EOL;
	}

	function testAction($p='P',$q='Q')
	{
		echo "CLITest->testAction($p,$q)".PHP_EOL;
	}
}
