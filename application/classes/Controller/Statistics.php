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
		$this->template->html_head->script_views[] = new View('statistics/main_js');
		$this->template->html_head->styles['all'] = 'media/css/jquery-ui.css';
		
		$maps_array = array();
		foreach($maps as $map){
			$maps_array[$map->id] = $map->title;
		}
		$this->template->content->maps = $maps_array;
		
	}//end action_index
	
	private function hash_colors($id)
	{
		$hash = 255 % $id;
		$r = 255;
		$g = 255;
		$b = 255;
		if($hash % 3 == 0){
			$b = $hash;
		}
		if($hash % 2 == 0){
			$g = $hash;
		}
		if($hash % 1 == 0){
			$r = $hash;
		}
		return 'rgb('.$r.','.$g.','.$b.')';
	}
	
	
	public function action_getData(){
		$this->auto_render = false;
		$this->response->headers('Content-Type','application/json');
		
		$data = $_POST;
		
		$startDate = date('Y-m-d',strtotime($data['start']));
		$endDate = date('Y-m-d',strtotime($data['end']));
		$map = $data['map'];
		echo '[';
		$j = 0;
		foreach($map as $id){
			$j++;
			$map_name = ORM::factory('Map',$id)->title;
			if($j>1){
				echo ',';
			}
			echo $map_name.':{data:[';
			
			$stat_obj = ORM::factory('Usagestatistics')->where('date', '>=', $startDate)->where('date', '<=', $endDate)
			->where('map_id', '=', $id)->find_all();
			
			$i = 0;
			foreach($stat_obj as $values){
				$i++;
				if($i>1)
				{
					echo ',';
				}
				echo '['.$values->date.','.$values->visits.']';
				
			}
			echo ']}';
		}
		echo ']';
	}
	
		 
	
}//end of class
