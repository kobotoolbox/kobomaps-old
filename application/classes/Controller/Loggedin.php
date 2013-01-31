<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Loggedin.php - Controller
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/

class Controller_Loggedin extends Controller_Main {

	/**
	Set stuff up
	*/
	public function before()
	{
		parent::before();


		//is no one logged in
		if($this->user == null)
		{		
			//record where the user was trying to go
			$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			Session::instance()->set('returnUrl',$url);			
			HTTP::redirect('/login');
		}
		
	}
	
	
  	
	
} // End Welcome
