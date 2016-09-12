<?php

class Application_Model_EncryptUtility
{ 
        
    public function Application_Model_EncryptUtility() 
    { 
    } 
        
	//creates a random string
	public function genRandomString() 
	{
    	$length = 10;
    	$characters = "0123456789abcdefghijklmnopqrstuvwxyz";
    	$string = "";    

	    for ($p = 0; $p < $length; $p++) {
    	    $string .= $characters[mt_rand(0, strlen($characters)-1)];
    	}

    	return $string;
	}

	//Adds a salt to the end of the md5 string
	public function md5_me($input)
	{
   		$salt = $this->getStaticSalt();
   		$pass = md5($input.$salt);
   		$pass = str_ireplace(array("a","c","e"),"",$pass);
   		return md5($pass);
	}

	// String EnCrypt + DeCrypt function
	// Author: halojoy, July 2006
	public function encryptdecrypt($str,$ky='')
	{
		if($ky=='')return $str;
		$ky=str_replace(chr(32),'',$ky);
		if(strlen($ky)<8)exit('key error');
		$kl=strlen($ky)<32?strlen($ky):32;
		$k=array();for($i=0;$i<$kl;$i++){
		$k[$i]=ord($ky{$i})&0x1F;}
		$j=0;for($i=0;$i<strlen($str);$i++){
		$e=ord($str{$i});
		$str{$i}=$e&0xE0?chr($e^$k[$j]):chr($e);
		$j++;$j=$j==$kl?0:$j;}
		return $str;
	}
	
	public function getStaticSalt()
	{
		return "tsD3lUUbTGiDzHbU17UPsMQoicNExdET";
	}

}