<?php
/**
* Clover Core Class
* Copyright 2015 EJSE
* Under MIT License
* Version 0.2 Updated on Dec 26 2015
*/
class Clover
{
	static $root_path='/';

	static $default_controller_name='CloverController';

	static $command_dir='command';
	static $controller_dir='controller';
	static $model_dir='model';
	static $view_dir='view';

	static $log_dir='/var/log/Clover';

	static $error_view_file='error.htm';
	static $code_404_view_file='page_not_found.htm';

	static $current_task_uuid='';

	static $logger=null;

	static $storage=array();
	
	public static function getQuery($name=null,$default=null){
		if($name===null){
			return $_GET;
		}else{
			if(isset($_GET[$name])){
				return $_GET[$name];
			}else{
				return $default;
			}
		}
	}

	public static function getRawRequestBody(){
		$post_raw = file_get_contents("php://input");
		return $post_raw;
	}

	public static function getData($name=null,$default=null){
		if(Clover::getServer('CONTENT_TYPE')=='application/json'){
			$post_raw=Clover::getRawRequestBody();
			$json=json_decode($post_raw,true);

			if($name===null){
				return $json;
			}else{
				if(isset($json[$name])){
					return $json[$name];
				}else{
					return $default;
				}
			}
		}else{
			if($name===null){
				return $_POST;
			}else{
				if(isset($_POST[$name])){
					return $_POST[$name];
				}else{
					return $default;
				}
			}
		}
	}

	public static function getRequest($name=null,$default=null){
		if($name===null){
			return $_REQUEST;
		}else{
			if(isset($_REQUEST[$name])){
				return $_REQUEST[$name];
			}else{
				return $default;
			}
		}
	}

	public static function getServer($name=null,$default=null){
		if($name===null){
			return $_SERVER;
		}else{
			if(isset($_SERVER[$name])){
				return $_SERVER[$name];
			}else{
				return $default;
			}
		}
	}

	public static function getControllerIndex(){
		$prefix=Clover::getServer('SCRIPT_NAME');
		if(strpos(Clover::getServer('REQUEST_URI'), $prefix)!==0){
			if(strrpos($prefix, '/index.php')+10==strlen($prefix)){
				$prefix=substr($prefix, 0, strlen($prefix)-10);
			}
		}
		return substr(Clover::getServer('REQUEST_URI'), strlen($prefix));
	}

	public static function getController(&$sub_paths=array()){
		global $argv;
		global $argc;

		$controller_name='';

		if(Clover::is_cli()){
			$sub_paths=array();
			for ($i=1; $i < $argc; $i++) { 
				if($i==1){
					$controller_name=$argv[$i];
				}else{
					$sub_paths[]=$argv[$i];
				}
			}
		}else{
			$controllerIndex = Clover::getControllerIndex();
			$pattern = '/^\/([^\?]*)(\?|$)/';
			$r=preg_match($pattern, $controllerIndex, $matches);
			$controller_array=explode('/', $matches[1]);
			if(count($controller_array)>0){
				$controller_name=$controller_array[0];
				if(count($controller_array)>1){
					unset($controller_array[0]);
					$sub_paths=array_values($controller_array);
				}
			}
		}
		return $controller_name;
	}

	public static function setRootPath($path){
		$last_char=substr($path, -1);
		if(!in_array($last_char, array('\\','/'))){
			$path.=DIRECTORY_SEPARATOR;
		}
		Clover::$root_path=$path;
	}

	public static function getLogger(){
		if(!Clover::$logger){
			$instance=new CloverLogger();
			$instance->setLogDir(Clover::$log_dir);
			Clover::$logger=$instance;
		}
		return Clover::$logger;
	}

	private static function LogCommand(){
		global $argv;
		global $argc;

		$cmd=implode(' ', $argv);
		Clover::getLogger()->log("[COMMAND] #".Clover::$current_task_uuid." ".$cmd);
	}

	private static function LogRequest(){
		$request_uri=Clover::getServer('REQUEST_URI');
		$query=Clover::getQuery();
		$data=Clover::getData();
		Clover::getLogger()->log("[REQUEST] #".Clover::$current_task_uuid." ".json_encode(array('request_uri'=>$request_uri,'query'=>$query,'data'=>$data)));
	}

	public static function getStore($name,$default=null){
		$store=Clover::$storage;
		if(isset($store[$name])){
			return $store[$name];
		}else{
			return $default;
		}
	}

	public static function setStore($name,$value){
		Clover::$storage[$name]=$value;
	}

	public static function is_cli() {
    	return (!isset($_SERVER['SERVER_SOFTWARE']) && (php_sapi_name() == 'cli' || (is_numeric($_SERVER['argc']) && $_SERVER['argc'] > 0)));
	}

	public static function start($root_path=null){
		if(Clover::is_cli()){
			Clover::startForCLI($root_path);
		}else{
			Clover::startForWebRequest($root_path);
		}
	}

	public static function startForCLI($root_path=null){
		global $argv;
		global $argc;
		try {
			Clover::$current_task_uuid=uniqid();
			Clover::LogCommand();

			if($root_path!==null){
				Clover::setRootPath($root_path);
			}
			$controller_name=Clover::getController($sub_paths);
			if(empty($controller_name)){
				$controller_name=Clover::$default_controller_name;
			}

			if(class_exists($controller_name)){
				$controller=new $controller_name();
				if(!is_a($controller, 'CloverCommand')){
					throw new Exception($controller_name, -404);
				}
			}else{
				throw new Exception($controller_name, -404);				
			}

			$parameter_list=array();
			if(!empty($sub_paths)){
				$action=$sub_paths[0].'Action';
				if(method_exists($controller, $action)){
					unset($sub_paths[0]);
					
					//some test
					
					$reflect = new ReflectionMethod($controller_name,$action);
					foreach($reflect->getParameters() as $param) {  
						$param_name=$param->getName();

						// set default values
					    if($param->isDefaultValueAvailable() && $param->isDefaultValueConstant()) {
					        $parameter_list[$param_name]=$param->getDefaultValueConstantName();
					    }else{
					    	$parameter_list[$param_name]="";
					    }
					}
					
					foreach ($sub_paths as $item) {
						$eq_index=strpos($item, '=');
						if($eq_index!==false){
							$p_name=substr($item, 2,$eq_index-2);
							$p_value=substr($item, $eq_index+1);
							if(isset($parameter_list[$p_name])){
								$parameter_list[$p_name]=$p_value;
							}
						}
					}
				}else{
					throw new Exception($controller_name."->".$action, -404);
				}
			}else{
				$action='defaultAction';
			}

			call_user_func_array(array($controller,'beforeExecute'), array($controller_name,$action,$parameter_list));
			call_user_func_array(array($controller,$action), $parameter_list);
			call_user_func_array(array($controller,'afterExecute'), array($controller_name,$action,$parameter_list));
		} catch (Exception $e) {
			if($e->getCode()==-404){
				//Command and action Not Found
				echo "Command and action missing: ".$e->getMessage().PHP_EOL;
			}else{
				echo "Clover Command Error: ".$e->getMessage().PHP_EOL;
			}
		}
	}

	public static function startForWebRequest($root_path=null){
		try {
			Clover::$current_task_uuid=uniqid();
			Clover::LogRequest();

			if($root_path!==null){
				Clover::setRootPath($root_path);
			}
			$controller_name=Clover::getController($sub_paths);
			if(empty($controller_name)){
				$controller_name=Clover::$default_controller_name;
			}
			if(class_exists($controller_name)){
				$controller=new $controller_name();
				if(!is_a($controller, 'CloverController')){
					throw new Exception($controller_name, -404);
				}
			}else{
				throw new Exception($controller_name, -404);				
			}
			if(!empty($sub_paths)){
				$func_name=$sub_paths[0];
				if(method_exists($controller, $func_name)){
					unset($sub_paths[0]);
					call_user_func_array(array($controller,$func_name), array_values($sub_paths));
				}else{
					throw new Exception($controller_name."->".$func_name, -404);
				}
			}else{
				$controller->index();
			}
		} catch (Exception $e) {
			if($e->getCode()==-404){
				//Page Not Found
				Clover::displayPageNotFound();
			}else{
				Clover::displayError($e);
			}
		}		
	}

	public static function displayPageNotFound(){
		Clover::display(Clover::$code_404_view_file);
	}

	public static function displayError(Exception $e){
		Clover::display(Clover::$error_view_file,array('error'=>$e));
	}

	public static function display($view_file,$assignment=array(),$isPart=false){
		extract($assignment);
		include Clover::$root_path.Clover::$view_dir.DIRECTORY_SEPARATOR.$view_file;
		if(!$isPart)exit();
	}

	public static function displayWithJSON($json_object,$code=200,$error=''){
		header('Content-type: application/json');
		$json=array(
			'code'=>$code,
			'result'=>$json_object,
			'error'=>$error,
		);
		$result=json_encode($json);
		echo $result;
		Clover::getLogger()->log("[RESPONSE] #".Clover::$current_task_uuid." ".$result);
		exit();
	}
}


function __autoload($classname) {
	$stack=array(
		Clover::$root_path.Clover::$command_dir,
		Clover::$root_path.Clover::$model_dir,
		Clover::$root_path.Clover::$controller_dir,
		__DIR__
	);
	while(!empty($stack)){
		$dir=array_pop($stack);

		if(file_exists($dir.DIRECTORY_SEPARATOR.$classname.".php")){
			include_once($dir.DIRECTORY_SEPARATOR.$classname.".php");
			return;
		}

		if ($handle = opendir($dir)) {
		    while (false !== ($file = readdir($handle))) {
		        if($file!='.' && $file!='..' && is_dir($dir.DIRECTORY_SEPARATOR.$file)){
		        	array_push($stack, $dir.DIRECTORY_SEPARATOR.$file);
		        }
		    }
		    closedir($handle);
		}
	}
}
