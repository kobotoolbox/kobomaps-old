<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Register.php - Controller
* This software is copy righted by Etherton Technologies Ltd. 2011
* Writen by John Etherton <john@ethertontech.com>
* Started on 8/20/2011
*************************************************************/

class Controller_Logout extends Controller_Main {

	
  	
	/**
	where users go to sign up
	*/
	public function action_index()
	{
		
		//you can't log out if you're not logged in
		$auth = Auth::instance();
		if( !($auth->logged_in() OR $auth->auto_login()))
		{
			$this->request->redirect('main');
		}
		
		$auth->logout();
		 
		$this->template->html_head->title = __("logout");
		$this->template->content = View::factory('logout');
		$this->template->content->errors = array();

	}
	
	
} // End Welcome