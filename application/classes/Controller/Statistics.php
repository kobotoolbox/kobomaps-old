<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Statistis.php - Controller
* This software is copy righted by Kobo 2013
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-01-23
* Used to show the user if people are looking at their maps
*************************************************************/

class Controller_Statistics extends Controller_Loggedin {

	
		


	/**
	where users go to change their profiel
	*/
	public function action_index()
	{
		$this->template->header->menu_page = "statistics";
		$this->template->content = new View('statistics/main');
		
	}//end action_index
	
	
	
		 
	
}//end of class
