<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
	/**
     * Retrieve user by Username. Returns a users userid and email if authorized to do so.
     *
     * @param  Request  $request
	 * @param  string  $username
     * @return Response
     */
	public function showUserInfoByUsername(Request $request, $username)
	{		

		$user = DB::select('CALL commonGetUserIdEmailSP(?)',Array($username));

		if (!$user || $request->user() != $user)
		{
			$user = config('result.unauthorized');
		}
		
        return $user;
	}
	
	/**
     * Add a new user. Returns a users userid.
     *
     * @param  Request  $request
     * @return Response
     */
	public function add(Request $request)
	{		
	
		$username = '';
		$password = '';
		$hash = '';
		$email = '';
		$user = '';
		
		if ($request->isJson()) 
		{
			$username = $request->json()->get('username');
			$password = $request->json()->get('password');
			$email = $request->json()->get('email');
		}		
		
		if (empty($username) || empty($password) || empty($email))
		{
			$user = config('result.incomplete');
		} else {
			$hash = $this->generateHash($password);
			
			$retval = DB::select('CALL commonCreateUserSP(?,?,?)',Array($username,$hash,$email));
			
			if ($retval > 0) 
			{
				$user = Array(Array("retval" => $retval,"message" => "Success"));
			} else {
				$user = config('result.fail');
			}
		}
		
        return $user;
	}
	
	/**
     * Update user by Username. Returns a users userid and email if authorized to do so.
     *
     * @param  Request  $request
	 * @param  string	$username
     * @return Response
     */
	public function update(Request $request,$username)
	{	
		$user = DB::select('CALL commonGetUserIdEmailSP(?)',Array($username));

		if (!$user || $request->user() != $user)
		{
			return config('result.unauthorized');
		}
			
		$password = '';
		$hash = '';
		$email = '';
		
		if ($request->isJson()) 
		{
			$password = $request->json()->get('password');
			$email = $request->json()->get('email');
		}		
		
		$uid = $user[0]->userid;
		
		// Change password
		if (!empty($password)) 
		{
			$hash = $this->generateHash($password);
			$retval = DB::select('CALL commonSetUserHashSP(?,?)',Array($uid,$hash));
			$retarr["password_changed"] = $retval[0]->retVal;
		} else {
			$retarr["password_changed"] = 0;
		}
		
		// Change email
		if (!empty($email)) 
		{
			$retval = DB::select('CALL commonSetUserEmailSP(?,?)',Array($uid,$email));
			$retarr["email_changed"] = $retval[0]->ret_val;
		} else {
			$retarr["email_changed"] = 0;
		}
		
		return Array(Array("retval" => 1,"message" => "Success",$retarr));
	}
	
	/**
     * Verify a user by Username.
     *
     * @param  Request  $request
	 * @param  string	$username
     * @return Response
     */
	public function verify(Request $request,$username)
	{	
		$password = '';
		
		if ($request->isJson()) 
		{
			$password = $request->json()->get('password');
		}	
								
		$user_info = DB::select('CALL commonGetUserInfoByUserSP(?)',Array($username));
		$user_info = $user_info[0];

		if ($user_info->hash != crypt($password, $user_info->hash)) 
		{
			$retarr["userid"] = 0;
			$retarr["email"] = null;
			$retarr["retval"] = 0;
			$retarr["message"] = "Incorrect Credentials";
		} else if ($user_info->suspended) {
			$retarr["userid"] = 0;
			$retarr["email"] = null;
			$retarr["retval"] = 0;
			$retarr["message"] = "Account Suspended";
		} else {
			$user = DB::select('CALL commonGetUserIdEmailSP(?)',Array($username));
			$user = $user[0];
			$retarr["userid"] = $user->userid;
			$retarr["email"] = $user->email;
			$retarr["retval"] = 1;
			$retarr["message"] = "User Verified";
		}
	
		return $retarr;
	}
	
	/**
     * Recover a password by email.
     *
     * @param  Request  $request
     * @return Response
     */
	public function recover(Request $request)
	{	
		$email = '';
		
		if ($request->isJson()) 
		{
			$email = $request->json()->get('email');
		}	
		
		$user_id = DB::select('CALL commonGetUserIdSP(?)',Array($email));
		$user_id = $user_id[0]->userid;
				
		if ($user_id > 0) {
			$password = $this->generatePassword();
			$hash = $this->generateHash($password);
		
			$hash_retval = DB::select('CALL commonSetUserHashSP(?,?)',Array($user_id,$hash)); 
			try {
				$to = $email;
				$subject = "'Password reset for Recipe Book'";
				$txt = 'Your password is: '.$password;
				$headers = "From: myagi.developer@gmail.com";

				mail($to,$subject,$txt,$headers);
                $retval = config('result.success');
			} catch(Exception $e) {
				$retval = config('result.fail');
			}

		} else {
			$retval = config('result.fail');
		}
		
		return $retval;
	}
	
	private function generateHash($password) 
	{
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
	
	private function generatePassword($length = 5) 
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}