<?php
/*
 * Sinri Database Toolkit
 * =========================
 * The toolkit for database
 * Sinri Edogawa 2015-09-24
 */

/**
* Make the use of PDO easier
*/
class CloverPDO
{
	private $pdo=null;
	private $deploy_level='TEST';

	function __construct($deploy_level,$username,$password,$host,$port,$database,$charset='utf8')
	{
		try {
			$this->deploy_level=$deploy_level;
			$this->pdo = new PDO('mysql:host='.$host.';port='.$port.';dbname='.$database.';charset='.$charset,$username,$password,
				array(PDO::ATTR_EMULATE_PREPARES => false)
			);
			$this->pdo->query("set names ".$charset);
		} catch (PDOException $e) {
			// throw new Exception("Error Processing Request", 1);
			global $logger;
			$logger->log('データベース接続失敗。'.$e->getMessage());
			exit('データベース接続失敗。'.$e->getMessage());
		}
	}

	public function getDeployLevel(){
		return $this->deploy_level;
	}

	public function getAll($sql){
		$stmt=$this->pdo->query($sql);
		if($stmt===false){
			global $logger;
			$logger->log("STMT GET FALSE FOR SQL: ".$sql);
			throw new Exception("STMT GET FALSE FOR SQL: ".$sql, -1);
		}
		$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}

	public function getColumn($sql){
		$stmt=$this->pdo->query($sql);
		if($stmt===false){
			global $logger;
			$logger->log("STMT GET FALSE FOR SQL: ".$sql);
			throw new Exception("STMT GET FALSE FOR SQL: ".$sql, -1);
		}
		$rows=$stmt->fetchAll(PDO::FETCH_BOTH);
		$column=array();
		if($rows){
			foreach ($rows as $row) {
				$column[]=$row[0];
			}
		}
		return $column;
	}

	public function getRow($sql){
		$stmt=$this->pdo->query($sql);
		if($stmt===false){
			global $logger;
			$logger->log("STMT GET FALSE FOR SQL: ".$sql);
			throw new Exception("STMT GET FALSE FOR SQL: ".$sql, -1);
		}
		$rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
		if($rows)
			return $rows[0];
		else return false;
	}

	public function getOne($sql){
		//FETCH_BOTH
		$stmt=$this->pdo->query($sql);
		if($stmt===false){
			global $logger;
			$logger->log("STMT GET FALSE FOR SQL: ".$sql);
			throw new Exception("STMT GET FALSE FOR SQL: ".$sql, -1);
		}
		$rows=$stmt->fetchAll(PDO::FETCH_BOTH);
		if($rows){
			$row = $rows[0];
			if($row){
				return $row[0];
			}else{
				return false;
			}
		}
		else return false;
	}

	public function exec($sql){
		$rows=$this->pdo->exec($sql);
		return $rows;
	}

	public function insert($sql){
		$rows=$this->pdo->exec($sql);
		if($rows){
			return $this->pdo->lastInsertId();
		}else{
			return false;
		}
	}

	public function beginTransaction(){
		return $this->pdo->beginTransaction();
	}
	public function commit(){
		return $this->pdo->commit();
	}
	public function rollBack(){
		return $this->pdo->rollBack();
	}
	public function inTransaction(){
		return $this->pdo->inTransaction();
	}

	public function errorCode(){
		return $this->pdo->errorCode();
	}
	public function errorInfo(){
		return $this->pdo->errorInfo();
	}

	public function quote($string,$parameter_type = PDO::PARAM_STR ){
		if($parameter_type==PDO::PARAM_INT){
			return intval($string);
		}else{
			return $this->pdo->quote($string);
		}
	}
}
?>