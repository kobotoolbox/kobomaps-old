<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Share.php - Controller
* This software is copy righted by Kobo 2013
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-01-21
*************************************************************/

class Controller_Share extends Controller_Main {

	
	/**
	 Set stuff up
	 */
	public function before()
	{
		//don't do any of this if it's a redirect
		if(strpos(Request::initial()->action(), 'redirect') !== false)
		{
			return;
		}
		
		$this->auth = Auth::instance();
		//is the user logged in?
		if($this->auth->logged_in())
		{
			$this->user = ORM::factory('user',$this->auth->get_user());
		}
		//if not send them to the login page
		else
		{
			//record where the user was trying to go
			$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			Session::instance()->set('returnUrl',$url);
			HTTP::redirect('/login');
		}
	}
	
	


	/**
	where users go to change their profiel
	*/
	public function action_window()
	{
		$this->auto_render = false;
		$this->template = null;				
		
		//grab the map ID
		//was an id given?
		$map_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		
		if($map_id == 0)
		{
			return;
		}
		
		//grab the map from the database
		$map = ORM::factory('Map',$map_id);
		
		if(!$map->loaded())
		{
			return;
		}
				
		$view = new View('share/window');
		$view->map = $map;
		echo $view;
		
	}//end action_index
	
	/**
	 * what's called when a user is finished posting to facebook
	 */
	public function action_fbredirect()
	{
		$this->auto_render = false;
		$this->template = null;
	
		echo "<html><head><script>close();</script></head></html>";
	}
	
	
		 
	
}//end of class
