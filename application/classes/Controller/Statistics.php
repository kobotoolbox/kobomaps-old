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
		$maps = ORM::factory('Map')->where('user_id', '=', $this->user->id)->find_all();
		
		$this->template->header->menu_page = "statistics";
		$this->template->content = new View('statistics/main');
		$this->template->html_head->script_files[] = 'media/js/jquery-ui.min.js';
		$this->template->html_head->script_files[] = 'media/js/flot/jquery.flot.js';
		$this->template->html_head->script_files[] = 'media/js/flot/jquery.flot.navigate.js';
		$this->template->html_head->script_views[] = new View('statistics/main_js');
		$this->template->html_head->styles['all'] = 'media/css/jquery-ui.css';
		
		$maps_array = array();
		foreach($maps as $map){
			$maps_array[$map->id] = $map->title;
		}
		$this->template->content->maps = $maps_array;
		
	}//end action_index
	

	

	/**
	 * AJAX call for getting JSON data for a specific set of maps and time frame
	 * POST variables for this are start, end, and map. 
	 */
	public function action_getData(){
		
		$this->auto_render = false;
		$this->response->headers('Content-Type','application/json');

		//make sure all the required parameters have been specified
		if(!isset($_POST['start']) OR !isset($_POST['end']) OR !isset($_POST['map']))
		{
			echo '{}';
			exit;
		}
		//make sure the map parameter is an array and has at least one map specified
		if(!is_array($_POST['map']) OR count($_POST['map']) == 0)
		{
			echo '{}';
			exit;
		}
		
		
		$startDate = date('Y-m-d',strtotime($_POST['start']));
		$endDate = date('Y-m-d',strtotime($_POST['end']));
		$map = $_POST['map'];
		echo '[';
		$j = 0;
		foreach($map as $id){
			$j++;
			$map_name = ORM::factory('Map',$id)->title;
			if($j>1){
				echo ',';
			}
			
			echo '{"label":'.json_encode($map_name).', "data":[';
			$stat_obj = ORM::factory('Usagestatistics')
				->where('date', '>=', $startDate)
				->where('date', '<=', $endDate)
				->where('map_id', '=', $id)
				->order_by('date', 'ASC')
				->find_all();
			$stats_array = array();
			$i = 0;
			foreach($stat_obj as $values){
				$i++;
				if($i>1)
				{
					echo ',';
				}
				echo '['.(strtotime($values->date) * 1000).','.$values->visits.']';
			}
			echo ']}';
		}
		echo ']';
	}
	
		 
	
}//end of class
