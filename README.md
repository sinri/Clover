# Clover

An MVC Framework for Restful Web Project in PHP, under MIT License, now version 0.1.

## Introduction

Clover is designed after CodeIgniter but much more simpler. It provides Restful style HTTP request process resolution.

Clover uses Model-View-Controller (MVC) architecture. Clover would parse all requests to controller, method and its parameters, and call the very method of controller with those parameters to response.

In short, URL `clover.ng/CONTROLLER/METHOD/Param1/Param2` would be processed by `CONTROLLER()->METHOD(Param1,Param2)`. Of course, URL `clover.ng/CONTROLLER/METHOD/Param1`, `clover.ng/CONTROLLER/METHOD`, and `clover.ng/CONTROLLER` are also available if controller was designed to support.

For `GET` request, Clover would parse the query string. For `POST`, Clover accept encoded url data and form data format by default, and also request with JSON data as HTTP request body when declare `application/json` as the value of Header `Content-Type`.

## Install

Clover is based on rewrite modual of Apache. (Nginx is also supported, in theory.) Create and edit `.htaccess` file in the root directory with the following:

	RewriteEngine On
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule ^(.*)$ index.php [QSA,L]
	# For Apache2

## Usage

Just fill the controller classes in `controller` directory, model classes in `model` directory, and view html files in `view` directory.

Import Clover file into `index.php`:

	require __DIR__.'/core/Clover.php';

In `index.php`, first set the root path:

	Clover::setRootPath($path_of_root);

For some strange purposes, Clover supports customization on MVC directories, just modify the source code under MIT License.

Finally, start Clover:
	
	Clover::start();



### Controller

Create an php file with a class inside. File name should be the same with the class inside. The class should extend class `CloverController`.

The methods of the class would be the method of the controller and `index` would be the default. The third element and the next ones of the url, if exist, would be the parameters of the method.

Use method `display($view_file,$assignment=array(),$isPart=false)` to display with certain view. The second parameter is an array to carry the assigned parameters to view. For example, if pass `array('K'=>'V')` as `$assignment`, you can use `<?php echo $K; ?>` to display `V` in your response view. The third parameter accepts boolean value and FALSE by default to stop PHP script with this very function call. If TRUE were given, the PHP script would run continuely until the end of the code.


### Model

You can define model class in model directory in the php file with same name, which would be loaded automatically when called.

### View

For view, create html file in view directory. Within the html content, you can use PHP codes inside, as well as the assigned parameters.

## Clover Class Toolkits

The following are all static functions of Clover class.

### Get Query Method

	function getQuery($name=null,$default=null)

Return the whole `$_GET` when `$name` is null. When `$name` is not null, try to return `$_GET[$name]` if it is set, or `$default` would be returned.

### Get Raw HTTP Request Method

	function getRawRequestBody()

Return the raw body of the current HTTP request.

### Get Data Method

	function getData($name=null,$default=null)

If request comes in standard HTTP format, return the whole `$_POST` when `$name` is null. When `$name` is not null, try to return `$_POST[$name]` if it is set, or `$default` would be returned.

If request comes in JSON Object format and `application/json` delared in `Content-Type` header, Clover would parse the HTTP request to JSON Object and get its property with the name given, or return `$default` when not set.

## P.S.

I think that, CI is in good design, but it is too heavy for simple project. I want make an environment to work with free feel, from both requirements and frameworks. In short, I do not want to learn too much about a tool and then trap into the tool. Using Clover, you can get the nearly same result with CI, but you can use all pure PHP function instead of the completely capsulized toolkit of CI. You like extension? Well, just develop them as you like. Clover is only a base framework.

