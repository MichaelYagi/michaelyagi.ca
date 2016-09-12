<?php

namespace App\Http\Middleware;

use DB;
use Closure;

class BasicAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) 
	{
		$user = Array();
		
		if (!empty($request->getUser())) 
		{
			$user = DB::select('CALL commonGetUserInfoByUserSP(?)',Array($request->getUser()));
		} else {
			$headers = array('WWW-Authenticate' => 'Basic');
            return response('Admin Login', 401, $headers);
		}

		if (array_key_exists("hash",$user) && $user["hash"] != crypt($request->getPassword(), $user["hash"]) && array_key_exists("suspended",$user) && !$user["suspended"]) 
		{
			$headers = array('WWW-Authenticate' => 'Basic');
            return response('Admin Login', 401, $headers);
		}	

        return $next($request);
    }
}