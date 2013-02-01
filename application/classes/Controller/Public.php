<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Public.php - Controller
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* This handles thing for the public to see
* Started on 2012-11-08
*************************************************************/

class Controller_Public extends Controller_Main {


  	
	
	/**
	 where users go to change their profiel
	 */
	public function action_maps()
	{
		/***** initialize stuff****/
		//The title to show on the browser
		$this->template->html_head->title = __("Public Maps");
		//make messages roll up when done
		$this->template->html_head->messages_roll_up = true;
		//the name in the menu
		$this->template->header->menu_page = "publicmaps";
		$this->template->content = view::factory("public/maps");
		$this->template->content->errors = array();
		$this->template->content->messages = array();
		$this->template->html_head->script_files[] = 'media/js/jquery.tools.min.js';
		$this->template->html_head->script_files[] = 'media/js/jquery-ui.min.js';
		$this->template->html_head->styles['all'] = 'media/css/jquery-ui.css';
		//set the JS
		$js = view::factory('public/maps_js');
		$this->template->html_head->script_views[] = $js;
		$this->template->html_head->script_views[] = view::factory('js/messages');
		$this->template->html_head->script_views[] = view::factory('js/facebook');
	
		//figure out if the current user is logged in.
		$auth = Auth::instance();
		//is the user logged in?
		$this->template->content->user = $this->user;
		
	
		
		/*****Render the forms****/
	
		//get the forms that belong to this user
		$maps = ORM::factory("Map");
		//if we're doing a search then throw in the template table
		if(isset($_GET['q']) AND $_GET['q'] != "")
		{
			$query = '%'.$_GET['q'].'%';
			$maps = $maps->join('templates')
				->on('templates.id','=','map.template_id')
				->or_where('map.title', 'LIKE', $query)
				->or_where('map.description', 'LIKE', $query)
				->or_where('templates.title', 'LIKE', $query);
		}
		$maps = $maps->where('is_private', '=', '0')
		->order_by('title', 'ASC')
		->find_all();
	
		$this->template->content->maps = $maps;
	
	
	
	}//end action_index
	
	
	/**
	 * Used to make the auto complete work on the public maps page
	 * expets there to be a GET param of 'term' of type String
	 */
	public function action_search()
	{
		$this->auto_render = false;
		$this->response->headers('Content-Type','application/json');
		
		//if there's no term return an empty dataset
		if(!isset($_GET['term']))
		{
			echo '[]';
			return;
		}
		
		$maps = ORM::factory("Map");
		$query = '%'.$_GET['term'].'%';
		$maps = $maps->join('templates')
			->on('templates.id','=','map.template_id')
			->or_where('map.title', 'LIKE', $query)
			->or_where('map.description', 'LIKE', $query)
			->or_where('templates.title', 'LIKE', $query)		
			->order_by('title', 'ASC')
			->limit(10,0)
			->find_all();
		
		echo '[';
		$i = 0;
		foreach($maps as $map)
		{
			$i++;
			if($i > 1){echo ',';}
			$title_encoded = json_encode($map->title);
			echo '{"id":"'.$map->id.'","label":'.$title_encoded.',"value":'.$title_encoded.'}';
		}
		echo ']';
		
	}
	
	
	
	
	
	 
	 /**
	  * Called to view a map
	  */
	 public function action_view()
	 {
	 	//get the id
	 	$map_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	 	//something when wrong, kick them back to add1
	 	if($map_id == 0)
	 	{
	 		HTTP::redirect('mymaps');
	 	}
	 	
	 		
	 	$user = null;
	 	
	 	//pull the map object from the DB
	 	$map = ORM::factory('Map', $map_id);
	 	 
	 	//if the map isn't ready send it back to where it needs to go
	 	if(intval($map->map_creation_progress) != 5)
	 	{
	 		HTTP::redirect('mymaps/add'.$map->map_creation_progress.'?id='.$map->id);
	 	}
	 
	 	$auth = Auth::instance();
	 	//is the user logged in?
	 	if($auth->logged_in())
	 	{
	 		$user = ORM::factory('user',$auth->get_user());
	 		if($user->id != $map->user_id)
	 		{
	 			if($map->is_private)
	 			{
	 				if(!(isset($_POST['private_password']) AND $_POST['private_password'] == $map->private_password))
	 				{
	 					$this->template->content = view::factory('addmap/private_password');
	 					return;
	 				}
	 				
	 			}
	 		}
	 	}
	 	else
	 	{
	 		if($map->is_private)
	 		{
	 			if(!(isset($_POST['private_password']) AND $_POST['private_password'] == $map->private_password))
	 				{
	 					$this->template->content = view::factory('addmap/private_password');
	 					return;
	 				}
	 		}
	 	}
	 	//checking if this is where the increment_visits should be included
	 	if(!($user != null AND $user->id == $map->user_id)){
	 		Model_Usagestatistics::increment_visit($map->id);
	 	}
	 	$map_template = ORM::factory('Template', $map->template_id);
	 	
	 	$this->template = false;
	 	$this->auto_render = false;

	 	$view = view::factory("mapview");
	 	$view->map_id = $map_id;
	 	$view->map = $map;	
	 	$js =  view::factory("mapview_js");
	 	$js->map = $map;
	 	$js->template = $map_template;
	 	$view->template = $map_template;
	 	$view->html_head = $js;
	 	
	 	$view->menu_page = 'mapview';
	 	$view->user = $user;
	 	
	 	echo $view;
	 	
	 	 
	 	 
	 		 	
	 }//end action_view()
	 
	 
	 /**
	  * Saves a file from the temp upload area to the hard disk
	  * @param array $upload_file the $_FILES['<name>'] array for the given file
	  * @param obj $map Kohana ORM object for a map, this is used in naming the file 
	  */
	 protected function _save_file($upload_file, $map)
	 {
	 	if (
	 			! Upload::valid($upload_file) OR
	 			! Upload::not_empty($upload_file) OR
	 			! Upload::type($upload_file, array('xlsx', 'xls')))
	 	{
	 		return FALSE;
	 	}
	 
	 	$directory = DOCROOT.'uploads/data/';
	 
	 	$extention = $this->get_file_extension($upload_file['name']);
	 	$filename = $map->user_id.'-'.$map->id.'.'.$extention;
	 	
	 	if ($file = Upload::save($upload_file, $filename, $directory))
	 	{	 			 
	 		return $filename;
	 	}
	 
	 	return FALSE;
	 }
	 
	 /**
	  * Grabs the file extention of a file
	  * @param string $file_name name of the file
	  * @return the exention of the file. So for 'about.txt' this function would return 'txt'
	  */
	 protected function get_file_extension($file_name) {
	 	return substr(strrchr($file_name,'.'),1);
	 }
	 
	 
	 /**
	  * 
	  * @param array[][] $sheet_data the PHPexcel array for a work sheet. Formatted Row->Column
	  * @param string $header_index the index of the header row 
	  * @param array[] $indicator_columns An array of ORM objects that tell which columns are indicators
	  * @param array[] $errors an array of String errors
	  * @param array[] $warnings an array of String warnings
	  */
	 protected function _build_indicators_html($sheet_data, $header_index, $data_rows, $indicator_columns, $errors, $warnings)
	 {
	 	//make sure we have data rows
	 	if(count($data_rows) == 0)
	 	{
	 		$errors[] = __("No rows were set as containing data. You need at least one data row");
	 		return "";
	 	}
	 	
	 	//make sure we have indicator columns
	 	if(count($indicator_columns) == 0)
	 	{
	 		$errors[] = __("No columns were st as containing incidcators. You need at least one incidator column");
	 	}
	 	
	 	//init the current placeholders
	 	$current_indicators = array();
	 	foreach($indicator_columns as $ic)
	 	{
	 		$current_indicators[$ic->id] = null;
	 	}
	 	
	 	$ret_val = "<ul>"; // the return value
	 	$active_level = 1;
	 	//now loop through the data and render the list
	 	foreach($data_rows as $data_row)
	 	{
	 		//now loop over the indicators
	 		$current_level = 1;	 		
	 		foreach($indicator_columns as $indicator_column)
	 		{
	 			$indicator_value = $sheet_data[$data_row->name][$indicator_column->name];
	 			//is it different than the current indicator
	 			if($indicator_value != $current_indicators[$indicator_column->id] AND $indicator_value != null)
	 			{
	 				//is this isn't the start of the list?
	 				if($current_indicators[$indicator_column->id] != null AND $current_level < $active_level)
	 				{	 				
		 				for($i = 0; $i < count($indicator_columns)-$current_level; $i++)
	 					{
	 						$ret_val .='</ul></li>'."\r\n";			
	 					}
	 				}
	 				
	 				$current_indicators[$indicator_column->id] = $indicator_value;
	 				$active_level = $current_level;
	 				$ret_val .= '<li>'.$indicator_value.'<ul>'."\r\n";
	 				if($current_level == count($indicator_columns))
	 				{
	 					$ret_val .= '</ul></li>'."\r\n";
	 				}
	 			}
	 			$current_level++;
	 		}
	 	}
	 	//tie up lose ends
	 	for($i = 1; $i < $current_level - 1; $i++)
	 	{
	 		$ret_val .='</ul></li>'."\r\n";
	 	}
		$ret_val .= '</ul>';
	 	return $ret_val;
	 }
	 
	 
	 /**
	  * Used to build a PHP array of indicators, then data across regions
	  * @param array $sheet The excel data in array form
	  * @param array $indicator_columns Array of database objects representing indicator columns
	  * @param array $data_rows  Array of database objects representing data rows in the excel data
	  * @param array $region_columns Array of database objects representing region columns in the excel data
	  * @param dbOject $header_row Database object representing the header column in the excel data
	  * @param array $indicators Array of indicators that will be turned into JSON
	  * @param dbOject $unit_column Database object representing the unit column in the excel data
	  * @param dbOject $src_column Database object representing the source column in the excel data
	  * @param dbOject $src_link_column Database object representing the source link column in the excel data
	  * @param dbOject $total_column Database object representing the total column in the excel data
	  * @return array Array of indicators that will be turned into JSON
	  */
	 protected function _build_indicators_array($sheet, $indicator_columns, $data_rows, $region_columns, $header_row, $indicators, 
	 		$unit_column, $src_column, $src_link_column, $total_column)
	 {
	 		//$_GET['debug'] = true;
	 	
	 	
	 	$current_indicators = array(); //used to track what the current indicators are	 	
	 	foreach($data_rows as $data_row)
	 	{

	 		
	 		$i = 0; //counter
	 		$num_indicators = count($indicator_columns); //how deep till we hit data
	 		$indicator_array_ptr = &$indicators; //the current array the indicator should go into
	 		if(isset($_GET['debug'])){echo "<br/>=============================================================================<br/>\r\nThe Indicator array: <br/>\r\n";
	 		print($indicators);
	 		}
	 		foreach	($indicator_columns as $indicator_column)
	 		{
	 			
	 			$i++;	 		
	 			//get the current indicator out of the excel data
	 			$indicator_name = $sheet[$data_row->name][$indicator_column->name];
	 			if(isset($_GET['debug'])){echo "<br/>--------------------------------------------------------------------<br/>\r\n";
	 			echo "Name: $indicator_name<br/>\r\n";
	 			echo "Level: $i<br/>\r\n";}
	 			
	 			//is this a different indicator than last time
	 			if(!isset($current_indicators[$i]) OR $current_indicators[$i]['name'] != $indicator_name AND $indicator_name != null)
	 			{
	 				
	 				$key = count($indicator_array_ptr);
	 				//set the current indicator
	 				$current_indicators[$i] = array('name'=>$indicator_name, 'id'=>$key);
	 				if(isset($_GET['debug'])){echo "Current Indicator:";
	 				print_r($current_indicators);}
	 				//clear out indicators below this one
	 				for($j = $i + 1; $j <= $num_indicators; $j++)
	 				{
	 					$current_indicators[$j] = null;
	 				}
	 				
	 				//create a new array for this indicator	 				
	 				$indicator_array_ptr[$key] = array('name'=>$indicator_name, 'indicators'=>array());
	 				if(isset($_GET['debug'])){echo "<br/>ptr: ";
	 				print_r($indicator_array_ptr);
	 				echo "<br/>Indicator Array: ";
	 				print_r($indicators);}
	 				
	 				if($i == $num_indicators)
	 				{
	 					$indicator_array_ptr = &$indicator_array_ptr[$key];
	 				}
	 				else
	 				{
	 					$indicator_array_ptr = &$indicator_array_ptr[$key]['indicators'];
	 				}
	 				
	 			}
	 			else //we've seen this before
	 			{
	 				if(isset($_GET['debug'])){echo "<br/>We've seen this guy before:";
	 				echo "<br/>Current Indicators: ";
	 				print_r($current_indicators);
	 				echo "<br/>Ptr: ";
	 				print_r($indicator_array_ptr);} 
	 				
	 				if($i == $num_indicators)
	 				{
	 					$indicator_array_ptr = &$indicator_array_ptr[$current_indicators[$i]['id']];
	 				}
	 				else
	 				{
	 					$indicator_array_ptr = &$indicator_array_ptr[$current_indicators[$i]['id']]['indicators'];
	 				}
	 				
	 			
	 			}
	 			
	 			//if this is the last level grab some data
	 			if($i == $num_indicators)
	 			{
	 				$data = array();
	 				foreach($region_columns as $region_column)
	 				{
	 					$region_name_xls = trim($sheet[$header_row->name][$region_column->name]);
	 					$region_name_kml = ORM::factory('Templateregion')
	 						->join('regionmapping')
	 						->on('regionmapping.template_region_id', '=', 'templateregion.id')
	 						->where('regionmapping.column_id', '=', $region_column->id)
	 						->find()
	 						->title;
	 					if($region_name_kml == null OR $region_name_kml == '')
	 					{
	 						continue;
	 					}
	 					$region_name_kml = trim($region_name_kml);
	 					$value = $sheet[$data_row->name][$region_column->name];
	 					$data[$region_name_kml] = array('name'=>$region_name_xls, 'value'=>$value);
	 					$data[$region_name_kml] = str_replace("%", "",$data[$region_name_kml]);
	 					$data[$region_name_kml] = str_replace("$", "",$data[$region_name_kml]);
	 					$data[$region_name_kml] = str_replace("#", "",$data[$region_name_kml]);
	 					
	 					//todo need a better way to know what's been ignored, both for the purpose
	 					//of showing ignored things in the UI to the user, and so we don't have to check for empty.
	 				}
	 				$indicator_array_ptr['data'] = $data;
	 				$indicator_array_ptr['unit'] = $sheet[$data_row->name][$unit_column->name];
	 				$indicator_array_ptr['total'] = $sheet[$data_row->name][$total_column->name];
	 				$indicator_array_ptr['total'] = str_replace("%", "",$indicator_array_ptr['total']);
	 				$indicator_array_ptr['total'] = str_replace("$", "",$indicator_array_ptr['total']);
	 				$indicator_array_ptr['total'] = str_replace("#", "",$indicator_array_ptr['total']);
	 				$indicator_array_ptr['src'] = $sheet[$data_row->name][$src_column->name];
	 				$indicator_array_ptr['src_link'] = $sheet[$data_row->name][$src_link_column->name];
	 			}
	 		}
	 	}
	 	return $indicators;
	 	
	 }//end function
	 
	 
	 /**
	  * the function for editing a form
	  * super exciting
	  *
	  * Redirect to set password for private maps
	  */
	 public function action_private_password()
	 {
	 	//get the id
	 	$map_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	 	//something when wrong, kick them back to add1
	 	if($map_id == 0)
	 	{
	 		HTTP::redirect('');
	 	}
	 	 
	 	if(!empty($_POST)) // They've submitted the form to update his/her wish
	 	{ 
	 	
		 	//pull the map object from the DB
		 	$map = ORM::factory('Map', $map_id);
		 	 
		 	if($_POST['private_password'] == $map->private_password)
		 	{
		 		
		 	}
	 	}
	 	else	 		
	 	{	
	 		$this->template->content = view::factory("addmap/private_password");
	 	}
	 	 
	 	 
	 	 
	 	//echo __("test");
	 	//$password =
	 }
	 
	
	
}//end of class
