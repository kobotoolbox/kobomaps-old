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
		
		//get a list of users who are colaborators on this
		$colaborators = ORM::factory('Sharing')
			->select('users.*')
			->join('users')
			->on('sharing.user_id','=','users.id')
			->where('sharing.map_id','=',$map_id)
			->find_all();
		
		$permissions = array();
		foreach(Model_Sharing::$allowed_permissions as $p)
		{
			$permissions[$p] = __($p);
		}
				
		$view = new View('share/window');
		$view->map = $map;
		$view->user = $this->user;
		$view->colaborators = $colaborators;
		$view->permissions = $permissions;
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
	
		$map = $this->checkMapAndUser();
		if($map == false)
		{
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
	
	
	/**
	 * This function is used to add users as colaborators to a map.
	 * This requires 
	 * $_POST['id'] Int ID of the map you're adding the colaborator to
	 * $_POST['name'] String username or email address of the user in question
	 * $_POST['permission'] String level of access to the map the user will have 
	 */
	public function action_adduserajax()
	{
		$this->auto_render = false;
		$this->response->headers('Content-Type','application/json');
		
		$map = $this->checkMapAndUser();
		if($map == false)
		{
			return;
		}
		//first is the name not empty?
		$name = $_POST['name'];
		if(strlen($name) == 0)
		{
			echo '{"status":"error", "message":'.json_encode(__('The name field is empty')).'}';
			return;
		}
		
		//then figure out if the name given is email or not
		if(filter_var($name, FILTER_VALIDATE_EMAIL))
		{//it's an email
			$user_to_add = ORM::factory('User')
				->where('email','=',$name)
				->find();			
		}
		else
		{//it's not an email
			$user_to_add = ORM::factory('User')
			->where('username','=',$name)
			->find();
		}
		
		if(!$user_to_add->loaded())
		{
			echo '{"status":"error", "message":'.json_encode(__('We cannot find a user with a name or email address of'). ' ' . $name).'}';
			return;
		}
		
		//verify the permissions are valid
		$permission = strtolower($_POST['permission']);
		if(!in_array($permission, Model_Sharing::$allowed_permissions))
		{
			echo '{"status":"error", "message":'.json_encode(__('That permission level is invalid')).'}';
			return;
		}
		
		//finally, we can add the user as a colaborator.

		$share = ORM::factory('Sharing');
		$share->map_id = $map->id;
		$share->user_id = $user_to_add->id;
		$share->permission = $permission;
		$share->save();
		
		//get the current list of colaborators
		$colaborators = ORM::factory('Sharing')
			->select('users.*')
			->join('users')
			->on('sharing.user_id','=','users.id')
			->where('sharing.map_id','=',$map->id)
			->find_all();
		
		$permissions = array();
		foreach(Model_Sharing::$allowed_permissions as $p)
		{
			$permissions[$p] = __($p);
		}
		
		$view = new View('share/map_colaborators');
		$view->colaborators = $colaborators;
		$view->permissions = $permissions;
		
		echo '{"status":"success", "message":'.json_encode($user_to_add->username . ' '. __('can now'). ' '. __($permission). ' ' .__('this map.'));
		echo ',"html":';
		echo json_encode($view->render());
		echo '}';
		
		
	}
	
	
	/**
	 * Use this to check that the $_POST['id'] specified
	 * is a valid map and is owned by the current user.
	 * If all is good returns the map object. If all is not good it returns
	 * false and echos out a JSON error messages
	 */
	protected function checkMapAndUser()
	{
		
		//get the map id
		$map_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		if($map_id == 0)
		{
			echo '{"status":"error", "message":'.json_encode(__('Not a valid map.')).'}';
			return false;
		}
		//get the map
		$map = ORM::factory('Map', $map_id);
		//is this your map
		if($map->user_id != $this->user->id)
		{
			echo '{"status":"error", "message":'.json_encode(__('You dont have permission.')).'}';
			return false;
		}
		return $map;	
	}
	
	/**
	 * Use this to check that the $_POST['id'] specified
	 * is a valid share entry in the DB and that the map is owned by the current user.
	 * If all is good returns the share object. If all is not good it returns
	 * false and echos out a JSON error messages
	 */
	protected function checkShare()
	{
	
		//get the map id
		$share_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		if($share_id == 0)
		{
			echo '{"status":"error", "message":'.json_encode(__('Not a valid ID.')).'}';
			return false;
		}
		//get the share
		$share = ORM::factory('Sharing', $share_id);
		//get the map
		$map = ORM::factory('Map',$share->map_id);
		//is this your map
		if($map->user_id != $this->user->id)
		{
			echo '{"status":"error", "message":'.json_encode(__('You dont have permission.')).'}';
			return false;
		}
		return $share;
	}
	
	
	/**
	 * This function is used to update the permission of a colaborator via AJAX
	 * 
	 * $_POST['id'] Int ID of the sharing row in the DB
	 * $_POST['permission'] String level of access to the map the user will have
	 */
	public function action_updateuserajax()
	{
		$this->auto_render = false;
		$this->response->headers('Content-Type','application/json');
	
		$share = $this->checkShare();
		if($share == false)
		{
			return;
		}
		//is the permission set
		if(!isset($_POST['permission']))
		{
			echo '{"status":"error", "message":'.json_encode(__('That permission level is invalid')).'}';
			return;
		}
		//get the permission level
		$permission = strtolower($_POST['permission']);
		if(!in_array($permission, Model_Sharing::$allowed_permissions))
		{
			echo '{"status":"error", "message":'.json_encode(__('That permission level is invalid')).'}';
			return;
		}
		
		$share->permission = $permission;
		$share->save();
	
		echo '{"status":"success", "message":""';
	
		echo '}';
	
	
	}
	
	
	
	/**
	 * This function is used to remove users as colaborators from a map.
	 * This requires
	 * $_POST['id'] Int ID of the share column in the DB that we're about to remove	 
	 */
	public function action_deluserajax()
	{
		$this->auto_render = false;
		$this->response->headers('Content-Type','application/json');
	
		$share = $this->checkShare();
		if($share == false)
		{
			return;
		}
		
		$map_id = $share->map_id;
	
		//finally, we can remove the user as a colaborator.
	
		$share->delete();
	
		//get the current list of colaborators
		$colaborators = ORM::factory('Sharing')
		->select('users.*')
		->join('users')
		->on('sharing.user_id','=','users.id')
		->where('sharing.map_id','=',$map_id)
		->find_all();
	
		$permissions = array();
		foreach(Model_Sharing::$allowed_permissions as $p)
		{
			$permissions[$p] = __($p);
		}
	
		$view = new View('share/map_colaborators');
		$view->colaborators = $colaborators;
		$view->permissions = $permissions;
	
		echo '{"status":"success", "message":""';
		echo ',"html":';
		echo json_encode($view->render());
		echo '}';
	
	
	}
		 
	
}//end of class
