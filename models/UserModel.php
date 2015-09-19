<?php
require_once(__DIR__.'/../helpers/BasicModel.php');
class UserModel extends BasicModel {
	function __construct() {
		$this->table = "users";
	}

}