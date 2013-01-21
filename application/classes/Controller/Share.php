<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Share.php - Controller
* This software is copy righted by Kobo 2013
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-01-21
*************************************************************/

class Controller_Share extends Controller_Loggedin {

	
	


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
	
	
		 
	
}//end of class
