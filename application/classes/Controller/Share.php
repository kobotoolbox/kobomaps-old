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
		$view->user = $this->user;
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
	
	
	
	
	/**
	 * this function is used to process ajax requests
	 * to toggle the privacy state of a map. This method expects there to be
	 * a $_POST['id'] to know which map we're trying to change.
	 */
	public function action_changestateajax()
	{
		$this->auto_render = false;
		$this->response->headers('Content-Type','application/json');
	
		//get the map id
		$map_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		if($map_id == 0)
		{
			echo '{"status":"error", "message":'.json_encode(__('Not a valid map.')).'}';
			return;
		}
		//get the map
		$map = ORM::factory('Map', $map_id);
		//is this your map
		if($map->user_id != $this->user->id)
		{
			echo '{"status":"error", "message":'.json_encode(__('You dont have permission.')).'}';
			return;
		}
		
		//flip the status of the map
		$map->is_private = intval($map->is_private) == 1 ? 0 : 1;
		$map->save();
		
		echo '{"status":"success", "html":';
		$view = new View('share/map_state');
		$view->map = $map;
		echo json_encode($view->render());
		echo '}';
		return;
		
	}
	
	
		 
	
}//end of class
