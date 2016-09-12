<?php

require_once('../wr/WR.class.php');

class User
{
	private $User;
	
	private $Hash;
	
	private $UserID;
	
	protected $WebService;
	
	public function __construct() {
		$this->WebService = new WebRequest();
	}
	
	public function createUser($username,$password,$email) {
		$this->Hash = $this->generateHash($password);
		$ws = $this->WebService;
		
		$create_user = $ws->setMethod('commonCreateUserSP')->setParam("n_user",$username)->setParam("n_hash",$this->Hash)->setParam("n_email",$email)->getData();
		
		return $create_user["ret_val"];
		
	}
	
	public function getUserInfoByUser($username) {
	
		$ws = $this->WebService;
		$user_info = $ws->setMethod('commonGetUserInfoByUserSP')->setParam("user_email",$username)->getData();
		
		$this->Hash = $user_info["hash"];
		$this->User = $username;
		
		return $user_info;
		
	}
	
	public function getUserInfoById($uid) {
	
		$ws = $this->WebService;
		$user_info = $ws->setMethod('commonGetUserInfoByIdSP')->setParam("uid",$uid)->getData();
		
		$this->Hash = $user_info["hash"];
		$this->User = $username;
		
		return $user_info;
		
	}
	
	public function getUserIdEmail($username) {
		$ws = $this->WebService;
		$user = $ws->setMethod('commonGetUserIdEmailSP')->setParam("user_email",$username)->getData();
		
		$this->UserID = $user["userid"];
		
		return $user;
	}
	
	public function getUserId($username) {
	
		$ws = $this->WebService;
		$user_id = $ws->setMethod('commonGetUserIdSP')->setParam("user_email",$username)->getData();
		
		$this->UserID = $user_id["userid"];
		
		return $user_id["userid"];
		
	}
	
	public function setUserHash($uid,$password) {
		$this->Hash = $this->generateHash($password);
		$ws = $this->WebService;
		$user_info = $ws->setMethod('commonSetUserHashSP')->setParam("uid",$uid)->setParam("u_hash",$this->Hash)->getData();
		
		return $user_info;
	}

	public function setUserEmail($uid,$email) {
		$ws = $this->WebService;
		$user_info = $ws->setMethod('commonSetUserEmailSP')->setParam("uid",$uid)->setParam("user_email",$email)->getData();
		
		return $user_info;
	}
	
	public function resetPassword($email) {
		
		$user_id = $this->getUserId($email);
		
		if ($user_id > 0) {
			$password = $this->generatePassword();
			$hash = $this->generateHash($password);
		
			$ws = $this->WebService;
			$hash_ret_val = $ws->setMethod('commonSetUserHashSP')->setParam("uid",$user_id)->setParam("u_hash",$hash)->getData();
			
			try {
				$to = $email;
				$subject = "'Password reset for Recipe Book'";
				$txt = 'Your password is: '.$password;
				$headers = "From: myagi.developer@gmail.com";

				mail($to,$subject,$txt,$headers);
                $retval = 1;
			} catch(Exception $e) {
				$retval = 0;
			}

		} else {
			$retval = 0;
		}
		
		return $retval;
	}
	
	private function generatePassword($length = 5) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	private function generateHash($password) {
		// A higher "cost" is more secure but consumes more processing power
		$cost = 10;
		
		// Create a random salt
		$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
		
		// Prefix information about the hash so PHP knows how to verify it later.
		// "$2a$" Means we're using the Blowfish algorithm. The following two digits are the cost parameter.
		$salt = sprintf("$2a$%02d$", $cost) . $salt;
		
		// Hash the password with the salt
		$hash = crypt($password, $salt);
		
		return $hash;
	}
	
}	