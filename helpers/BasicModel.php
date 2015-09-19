<?php
abstract class BasicModel {
	protected static $connection;
	protected $table;
	public static $dbconfig;
	protected static $connect_function;
	protected static $connect_function_error;
	protected static $db_select_function;
	protected static $db_query_function;
	protected static $db_query_num_rows_function;
	protected static $query_fetch_assoc_function;
	protected static $query_inserted_id;
	static function init() {
		
		BasicModel::$connect_function = BasicModel::$dbconfig['engine'].'i_connect';

		BasicModel::$connect_function_error = BasicModel::$dbconfig['engine'].'i_connect_error';
		BasicModel::$db_select_function = BasicModel::$dbconfig['engine'].'i_select_db';
		BasicModel::$db_query_function = BasicModel::$dbconfig['engine'].'i_query';
		BasicModel::$query_inserted_id = BasicModel::$dbconfig['engine'].'i_insert_id';
		BasicModel::$db_query_num_rows_function = BasicModel::$dbconfig['engine'].'i_num_rows';
		BasicModel::$query_fetch_assoc_function = BasicModel::$dbconfig['engine'].'i_fetch_assoc';
		$connect_function = BasicModel::$connect_function;
		BasicModel::$connection = $connect_function(BasicModel::$dbconfig['host'], BasicModel::$dbconfig['username'], BasicModel::$dbconfig['password']);
		if(!BasicModel::$connection) {
			die("Connection failed: " . $connect_function_error());
		}
		$db_select_function = BasicModel::$db_select_function;
		$db_select_function(BasicModel::$connection, BasicModel::$dbconfig['database']) or die("Unable to select database");
	}
	function __construct() {
		
	}
	function check() {
		if(!$this->table || empty($this->table) || $this->table==""){
			die("Table not defined");
		}
		$object_class = get_class($this);
		
	}
	function get_all() {
		$this->check();
		$sql = "SELECT * FROM $this->table";
		$query_func = BasicModel::$db_query_function;
		$db_query_num_rows_function = BasicModel::$db_query_num_rows_function;
		// here is the problem
		//print $this->db_query_function($sql);
		$result = $query_func(BasicModel::$connection, $sql);

		if($db_query_num_rows_function($result) > 0) {

			$object_class = get_class($this);
			$objects = array();
			$query_fetch_assoc_function = BasicModel::$query_fetch_assoc_function;
			while($row = $query_fetch_assoc_function($result)){
				try {
					$record = new $object_class();
					$keys = array_keys($row);
					foreach($keys as $key) {
						$record->$key = $row[$key];	
					}
					$objects[] = $record;
				} catch(Exception $e) {
					die('Could not map object to this class, may be there is a missing properties not defined');
				}
			}
			return $objects;
		}
	}

	function get_records($conditions=array()) {
		$this->check();
		$conditions_string = " WHERE";
		if(empty($conditions)){
			die("Conditions is not provided to get the record");
		}
		$stmt_init_function = BasicModel::$dbconfig['engine'].'i_stmt_init';
		$stmt = $stmt_init_function(BasicModel::$connection);
		$i = 0;
		foreach ($conditions as $key => $value) {
			$conditions_string.=" $key = ?";
			if($i<count($conditions)-1){
				$conditions_string.=" AND";
			}
			$i++;
		}

		$sql = "SELECT * FROM $this->table".$conditions_string;
		$prepare_function = BasicModel::$dbconfig['engine'].'i_stmt_prepare';
		//$prepare_function($stmt, $sql);
		$conn = BasicModel::$connection;
		$stmt = $conn->prepare($sql);
		$bind_params_function = BasicModel::$dbconfig['engine'].'i_stmt_bind_param';
		$stmt_execute = BasicModel::$dbconfig['engine'].'i_stmt_execute';
		$stmt_get_result = BasicModel::$dbconfig['engine'].'i_stmt_get_result';
		$types = "";
		$params = array();
		
		foreach ($conditions as $key => $value) {
			$types .= "s";
			//$bind_params_function($stmt, "s", $value);
		}
		$params[] =  $types;
		foreach ($conditions as $key => $value) {
			$params[] = $value;
			//$bind_params_function($stmt, "s", $value);
		}
		//$bind_params_function($stmt, "ss", $conditions['user_email'], $conditions['user_email']);
		//$email = "ahmedhosny.exe@gmail.com";
		//$password = "e10adc3949ba59abbe56e057f20f883e";
		//$stmt->bind_param($types, $email, $password);
		call_user_func_array(array($stmt, "bind_param"), $this->makeValuesReferenced($params));
		$result = $stmt->execute();
		//$result = $stmt_get_result($stmt);
		//var_dump($result);
		$result = $stmt->get_result();
		$object_class = get_class($this);
		$objects = array();

		$query_fetch_assoc_function = BasicModel::$query_fetch_assoc_function;
		while($row = $result->fetch_assoc()){
			try {
				$record = new $object_class();
				$keys = array_keys($row);
				foreach($keys as $key) {
					$record->$key = $row[$key];	
				}
				$objects[] = $record;
			} catch(Exception $e) {
				die('Could not map object to this class, may be there is a missing properties not defined');
			}
		}
		return $objects;
		
		/*
		$query_func = BasicModel::$db_query_function;
		$db_query_num_rows_function = BasicModel::$db_query_num_rows_function;
		$result = $query_func(BasicModel::$connection, $sql);
		if($db_query_num_rows_function($result) > 0) {

			$object_class = get_class($this);
			$objects = array();
			$query_fetch_assoc_function = BasicModel::$query_fetch_assoc_function;
			while($row = $query_fetch_assoc_function($result)){
				try {
					$record = new $object_class();
					$keys = array_keys($row);
					foreach($keys as $key) {
						$record->$key = $row[$key];	
					}
					$objects[] = $record;
				} catch(Exception $e) {
					die('Could not map object to this class, may be there is a missing properties not defined');
				}
			}
			return $objects;
		}*/
	}
	private function makeValuesReferenced($arr){ 
        $refs = array(); 
        foreach($arr as $key => $value) 
        $refs[$key] = &$arr[$key]; 
        return $refs; 

    }
	function insert_record($values=array()) {
		$this->check();
		if(empty($values)){
			die("Values is not provided to insert the record");
		}
		$sql = "INSERT INTO $this->table";
		$values_string = " VALUES (";
		$columns_string = " (";
		$i = 0;
		foreach ($values as $key => $value) {
			$columns_string .= " `$key`";
			$values_string .=" '$value'"; 
			if($i<count($values)-1){
				$columns_string.=" ,";
				$values_string.= " ,";
			}
			$i++;
		}
		$values_string.=")";
		$columns_string.=")";
		$sql .= $columns_string.$values_string;
		$query_func = BasicModel::$db_query_function;
		$db_query_num_rows_function = BasicModel::$db_query_num_rows_function;
		$result = $query_func(BasicModel::$connection, $sql);
		if(!$result) {
			return false;
		}
		$inserted_id = BasicModel::$query_inserted_id;
		$last_id = $inserted_id(BasicModel::$connection);
		return $this->get_records(array("id"=>$last_id));
	}

	function update_record($values, $condition) {
		if(empty($values) || empty($condition)){
			die("Values and conditions should be provided");
		}
		$update_query = "UPDATE $this->table SET ";
		$i = 0;
		foreach ($values as $key => $value) {
			$update_query .= " `$key`=";
			$update_query .=" '$value'"; 
			if($i<count($values)-1){
				$update_query.=" ,";
			}
			$i++;
		}
		$update_query.= " WHERE ";
		$i = 0;
		foreach ($condition as $key => $value) {
			$update_query .= " `$key`=";
			$update_query .=" '$value'"; 
			if($i<count($condition)-1){
				$update_query.=" AND";
			}
			$i++;
		}
		$query_func = BasicModel::$db_query_function;
		$result = $query_func(BasicModel::$connection, $update_query);
		if($result){
			return true;
		}
		return false;
	}

	function encrypt_password($pass_string) {
		if(!$pass_string || empty($pass_string)) {
			die;
		} else {
			return md5($pass_string);
		}
	}


}

$host = $_SERVER['HTTP_HOST'];
$config_included = false;

if($host=="localhost" || $host=="127.0.0.1" || strrpos($host,"localhost") >= 0 || strrpos($host,"127.0.0.1") >= 0){
	if(file_exists(__DIR__.'/../config/local') && file_exists(__DIR__.'/../config/local/dbconfig.php')){
		$config_included = true;
		require_once(__DIR__.'/../config/local/dbconfig.php');

	}
} else if(strrpos($host,".")) {
	if(file_exists(__DIR__.'/../config/production') && file_exists(__DIR__.'/../config/production/dbconfig.php')){
		$config_included = true;
		require_once(__DIR__.'/../config/production/dbconfig.php');
	}
}
if(!$config_included) {
	if(file_exists(__DIR__.'/../config') && file_exists(__DIR__.'/../config/dbconfig.php')){
		require_once(__DIR__.'/../config/dbconfig.php');
	}
	else {
		echo "dbconfig file is missing";
		die;
	}
}
BasicModel::$dbconfig = $dbconfig;
BasicModel::init();