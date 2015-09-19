<?php
require_once(__DIR__.'/../helpers/View.php');
require_once(__DIR__.'/../models/UserModel.php');
class MadeoController {

	public static function home($args = array()) {
		if(isset($_SESSION['user_id']) && $_SESSION['user_id']) {
			$uri = explode('/', $_SERVER['REQUEST_URI']);
	    	$URL = $uri[0].'/madeo/user/profile';
	    	header('Location:'.$URL);
		}
		$token = md5(uniqid(time(), true));
		$_SESSION['token'] = $token;
		return View::Show('home', ['token'=>$token]);
	}
	
}