<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Dynamic.php - Controller
* This software is copy righted by Kobo 2013
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* This handles requests for maps by name
* Started on 2013-02-15
*************************************************************/

class Controller_Dynamic extends Controller_Main {


  	
	
	/**
	 handles all requests
	 */
	public function action_index()
	{
		$this->auto_render = false;
		
		$slug = $this->request->param('slug');
		//see if this correlates to a map
		$map = ORM::factory('Map')
			->where('slug','=',$slug)
			->find();
		
		//if we coudln't find it bounce.
		if(!$map->loaded())
		{
			throw new HTTP_Exception_404();
		}
		
		$_GET['id'] = $map->id;
		$response = Request::factory('public/view')->execute()->response;
		echo $response;
		
	}//end action_index
		
}//end of class
