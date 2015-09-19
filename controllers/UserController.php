<?php
require_once(__DIR__.'/../helpers/mandrillconfig.php');
class UserController {
	
	static function register($args=null) {
		if(isset($_SESSION['user_id']) && $_SESSION['user_id']) {
			$uri = explode('/', $_SERVER['REQUEST_URI']);
	    	$URL = $uri[0].'/madeo/user/profile';
	    	header('Location:'.$URL);
		}
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			die("Request Method is not permitted");
		}
		$err = array();
		$token = $_POST['token'];
		
		if($token != $_SESSION['token']) {
			$err[] = "Your try is not permitted";
		}
		$username = $_POST['username'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		if(!$password || strlen($password) <= 5) {
			$err[] = "Password should be more than 5 characters";
		}

		$phone = $_POST['phone'];
		if(!$phone || !is_numeric($phone)) {
			$err[] = "Phone is required and should be numbers only";
		}
		$dob = $_POST['dob'];
		if(!$dob){
			$err[] = "Date of birth is required";
		}
		$birth_date = strtotime($dob.' +18 year');
		$birth_date = DateTime::createFromFormat('m-d-Y', $birth_date);
		$current_date = new DateTime('now');
		$today = DateTime::createFromFormat('m-d-Y', $current_date->format('m-d-Y'));
		if($birth_date > $today){
			$err[] = "You should be +18";
		}
		$users = new UserModel();
		$password = $users->encrypt_password($password);
		
		if (!$email || !preg_match('/^\S+@\S+\.\S+$/', $email)) {
      		$err[] = "Invalid email format";
    	}
    	$check_existance = $users->get_records(array("user_name"=>$username));
    	if($check_existance) {
    		$err[] = "User name exists"; 	
    	}
    	$check_existance = $users->get_records(array("user_email"=>$email));
    	if($check_existance) {
    		$err[] = "Email exists";
    	}
    	$email_domain = explode("@", $email);
    	if(!$email_domain || count($email_domain)>2){
    		$err[] = "Invalid email format";
    	}
    	try {
    		$mandrill = $GLOBALS['mandrill'];
    		$result = $mandrill->senders->checkDomain($email_domain[1]);
    		if(!$result) {
    			$err[] = "Invalid email domain";	
    		}
    	} catch(Exception $e) {
    		$err[] = "Invalid email domain";
    	}
    	if(!empty($err)){
    		return View::Show('error', ["errors"=>$err]);
    	}
		$result = $users->insert_record(
			array("user_name"=>$username,
				  "user_email"=>$email,
				  "user_phone"=>$phone,
				  "user_password"=>$password,
				  "user_dob"=>$dob));
		if(!$result || empty($result)) {
			$err[] = "Error in Database";
			return View::Show('error', ["errors"=>$err]);
		} else {
			try{
				$host = $_SERVER['HTTP_HOST'].'/madeo/user/verify/'.$result[0]->user_invitation_token;
				$verfication_link = " ".$host;
				$message = array(
			        'html' => "<p> Thanks for registeration </p><p>Verfication Email From Madeo</p><br/> Please visit this".$verfication_link,
			        'subject' => 'Verfication Email From Madeo',
			        'from_email' => 'me@ahmedhosnycs.com',
			        'from_name' => 'Madeo Co',
			        'to' => array(
			            array(
			                'email' => $email,
			                'name' => $username,
			                'type' => 'to'
			            )
			        ),
			        'headers' => array('Reply-To' => 'me@ahmedhosnycs.com'),
			    );
				$async = false;
			    $ip_pool = 'Main Pool';
			    $date = new DateTime('2000-01-01');
			    $send_at = $date->format('Y-m-d H:i:s');
			    $result = $mandrill->messages->send($message, $async, null, null);
			    if($result) {
			    	return View::Show('verification');
			    }
		    } catch (Exception $e) {
		    	return View::Show('error', ["errors"=>$e->getMessage()]);
		    }
		}
	}

	static function verify($args=null){
		if($args==null || empty($args)) {
			return View::Show('error', ["errors"=>"No Args are provided"]);
		}
		$users = new UserModel();
		$check_existance = $users->get_records(array("user_invitation_token"=>$args[0]));
		if(!$check_existance) {
    		return View::Show('error', ["errors"=>"Invitation Key is invalid"]);
    	}
    	$result = $users->update_record(array("user_isactive"=>"1","user_invitation_token"=>""), array('id' =>$check_existance[0]->id ));
    	if($result) {
    		return View::Show('error', ["errors"=>"You successfuly verified your account. Go to <a href='/madeo'>Login</a>"]);
    	}
    	return View::Show('error', ["errors"=>"Something went wrong"]);
	}

	static function login($args=null){
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			die("Request Method is not permitted");
		}
		if(isset($_SESSION['user_id']) && $_SESSION['user_id']) {
			$uri = explode('/', $_SERVER['REQUEST_URI']);
	    	$URL = $uri[0].'/madeo/user/profile';
	    	header('Location:'.$URL);
		}
		$users = new UserModel();
		if(!isset($_POST['password']) || !isset($_POST['email'])) {
			$uri = explode('/', $_SERVER['REQUEST_URI']);
	    	$URL = $uri[0].'/madeo';
	    	header('Location:'.$URL);
		}
		$password = $_POST['password'];

		$email = $_POST['email'];
		$err = array();
		if(!$password){
			$err[] = "Password required";
		}
		if(!$email){
			$err[] = "Email is required";
		}
		if(!empty($err)){
    		return View::Show('error', ["errors"=>$err]);
    	}
		$password = $users->encrypt_password($password);
		$check_existance = $users->get_records(
			array("user_email"=>$email,
				  "user_password" => $password));
		if(empty($check_existance)){
			$err[] = "Email or password are wrong please try again";
			return View::Show('error', ["errors"=>$err]);
		}
		$err = array();
		if(!$check_existance[0]->user_isactive) {
			$err[] = "User is not activated";
		}
		if($check_existance[0]->user_invitation_token) {
			$err[] = "User is should activate the profile from his email";
		}
		if(!empty($err)){
    		return View::Show('error', ["errors"=>$err]);
    	}
    	$_SESSION['user_id'] = $check_existance[0]->id;
    	$_SESSION['user_name'] = $check_existance[0]->user_name;
    	$_SESSION['user_login'] = time();
    	$uri = explode('/', $_SERVER['REQUEST_URI']);
    	$URL = $uri[0].'/madeo/user/profile';
    	header('Location:'.$URL);

	}
	static function profile($args=null) {
		if(isset($_SESSION['user_login']) && time()-$_SESSION['user_login']>60){
			UserController::logout();
		}
		if(isset($_SESSION['user_id']) && $_SESSION['user_id']) {
			return View::Show('msg', ["name"=>$_SESSION['user_name']]);	
		}
		else {
	    	UserController::logout();	
		}
	}
	static function logout($args=null) {
		session_destroy();
		$uri = explode('/', $_SERVER['REQUEST_URI']);
	    $URL = $uri[0].'/madeo';
	    header('Location:'.$URL);
	}
}