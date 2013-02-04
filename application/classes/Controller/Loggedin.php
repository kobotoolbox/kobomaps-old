<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Loggedin.php - Controller
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/

class Controller_Loggedin extends Controller_Main {

	/** Is the current user an admin?**/
	public $is_admin = null;
	/** How many items can the current user have? -1 is infinite**/
	public $user_max_items = null;
	
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
		
		$this->is_admin = false;
		
		//see if the given user is an admin, if so they can do super cool stuff
		$admin_role = ORM::factory('Role')->where("name", "=", "admin")->find();
		if($this->user->has('roles', $admin_role))
		{
			$this->is_admin = true;
		}
		
		//also figure out the max items
		$this->user_max_items = 0;
		//if they're an admin, then they get max items
		if($this->is_admin)
		{
			$this->user_max_items = -1;
		}
		else
		{
			//get the role with the highest max objects that this user has
			$role = ORM::factory('Role')
				->join('roles_users')
				->on('role.id','=','roles_users.role_id')
				->where('roles_users.user_id','=',$this->user->id)
				->order_by('max_items','DESC')
				->limit(1,0)
				->find();
			$this->user_max_items = $role->max_items; 
				
		}
	}
	
	
  	
	
} // End Welcome
