<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Help.php - Controller
* This software is copy righted by Kobo 2013
* Writen by Dylan Gillespie <dylan@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-08-21
*************************************************************/

class Controller_Help extends Controller_Main {

	
	/**
	 Set stuff up, mainly just check if the user is an admin or not
	 */
	public function before()
	{
		parent::before();
	
	}
	
	/**
	 * Main page for the Help section
	 */
	public function action_index()
	{
		/***** initialize stuff****/
		//The title to show on the browser
		$this->template->html_head->title = __("Help");
		//make messages roll up when done
		$this->template->html_head->messages_roll_up = true;
		//the name in the menu
		$this->template->header->menu_page = "help";
		$this->template->content = view::factory("help/main");
		//$this->template->content->errors = array();
		//$this->template->content->messages = array();
		//$this->template->html_head->script_files[] = 'media/js/jquery.tools.min.js';
		//set the JS
		//$js = view::factory('mymaps/mymaps_js');
		//$this->template->html_head->script_views[] = $js;
		//$this->template->html_head->script_views[] = view::factory('js/messages');
		//$this->template->html_head->script_views[] = view::factory('js/shareEdit');
		//$this->template->html_head->script_views[] = view::factory('js/facebook');
		
		//$this->template->content->maps = $maps;
		
		
	}//end action_index
	
	
	
	/**
	 * the function for editing a form
	 * Creates the map edit/create form that is first seen upon clicking edit/create
	 */
	 public function action_add1()
	 {
	 	//initialize data
		//default values that the form displays
		$data = array(
			'id'=>'0',
			'title'=>'',
			'description'=>'',
			'slug'=>'',
			'file'=>'',
			'CSS'=>'',
			'lat'=>'0',
			'lon'=>'0',
			'zoom'=>'1',
			'border_color' => '06D40D',
			'region_color' => 'AAAAAA',
			'polygon_color' => 'FF0000 FFFFFF',
			'graph_bar_color' => '223953',
			'graph_select_color' => 'D71818',
			'map_style'=>Model_Map::$style_default,
			'user_id'=>$this->user->id,
			'is_private'=>0,
			'map_creation_progress'=>1,
			'show_names' => true,
			'label_zoom_level' => 0,
			'region_label_font' => 12,
			'value_label_font' => 12,
			'large_file'=> false,
			'gradient' => false,
			);
		
		$map = null;
		
		//was an id given?		
		$map_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		
			
		if($map_id != 0)
		{
			$map = ORM::factory('Map', $map_id);
			
			//make sure the map exists
			if(!$map->loaded())
			{
				HTTP::redirect('mymaps');
			}
			
			$this->check_map_permissions($map_id, $this->user->id);
				
			$data['id'] = $map_id;
			$data['title'] = $map->title;
			$data['description'] = $map->description;
			$data['slug']= $map->slug;
			$data['CSS'] = $map->CSS;
			$data['lat'] = $map->lat;
			$data['lon'] = $map->lon;
			$data['zoom'] = $map->zoom;
			$data['map_style'] = $map->map_style;
			$data['user_id'] = $map->user_id;
			$data['border_color'] = $map->border_color;
			$data['region_color'] = $map->region_color;
			$data['polygon_color'] = $map->polygon_color;
			$data['graph_bar_color'] = $map->graph_bar_color;
			$data['graph_select_color'] = $map->graph_select_color;
			$data['is_private'] = $map->is_private;
			$data['show_names'] = $map->show_empty_name;
			$data['label_zoom_level'] = $map->label_zoom_level;
			$data['region_label_font'] = $map->region_label_font;
			$data['value_label_font'] = $map->value_label_font;
			$data['large_file'] = $map->large_file;
			$data['gradient'] = $map->gradient;
		}
		else
		{
			//this is a new map, check that the user is allowed to have more maps
			if(!$this->check_max_items())
			{
				return;
			}
		}
		

		 
		
		/***Now that we have the form, lets initialize the UI***/
		//The title to show on the browser
		$header =  __('Add Map - Basic Setup') ;
		$this->template->html_head->title = $header;		
		//make messages roll up when done
		$this->template->html_head->messages_roll_up = true;
		//the name in the menu
		$this->template->content = view::factory("mymaps/add1");
		$this->template->content->data = $data;
		$this->template->content->errors = array();
		$this->template->content->messages = array();
		$this->template->content->header = $header;
		$this->template->header->menu_page = "createmap";
		//set the JS
		
		//$js = view::factory('add1_js/form_edit_js');
		$js = view::factory('mymaps/add1_js');
		$js->map_id = $map_id;
		//$js->is_add = $is_add;
		$this->template->html_head->script_views[] = $js;		
		$this->template->html_head->script_views[] = view::factory('js/messages');
		$this->template->html_head->script_views[] = view::factory('js/gspreadsheetselect');
		
		//get the status
		$status = isset($_GET['status']) ? $_GET['status'] : null;
		if($status == 'saved')
		{
				$this->template->content->messages[] = __('changes saved');
		}
		
		/******* Handle incoming data*****/
		if(!empty($_POST)) // They've submitted the form to update his/her wish
		{
			try
			{
				
				//save to the DB
				$first_time = false;
				if($map == null)
				{			
					$first_time = true;
					$map = ORM::factory('Map');
					
					//check if they're using Excel files or Google Docs
					if($_POST['filetype'] == 'excel')
					{
						$_POST['file'] = $_FILES['file']['name'];
					}
					else
					{
						//make sure they did select a google doc
						if(!isset($_POST['googleid']) OR $_POST['googleid'] == '')
						{
							$this->template->content->errors[] = __('You must specify an Excel file or a Google Doc to use as the data source.'); 
							$data = array_merge($data,$_POST);
							$this->template->content->data = $data;
							return;
						}
						$_POST['file'] = $_POST['googleid'];
					}
					$_POST['template_id'] = 0;
					$_POST['json_file'] = '0';
					//if this is the first time the map is created, set the progress to 1
					$_POST['map_creation_progress'] = 1;
				}
				else
				{
					if($_FILES['file']['name'] == '' AND $_POST['googleid'] == '')
					{
						$_POST['file'] = $map->file; 
					}
					$_POST['template_id'] = $map->template_id;
					$_POST['json_file'] = $map->json_file;
					//if the map already exists, keep the same map_creation_progress
					$_POST['map_creation_progress'] = 1;	//$map->map_creation_progress;
				}
				//this handles is private, show gradient, and show empty fields
				$_POST['is_private'] = isset($_POST['is_private']) ? 1 : 0;
				$_POST['show_empty_name'] = isset($_POST['show_empty_name']) ? 1 : 0;
				$_POST['gradient'] = isset($_POST['gradient']) ? 1 : 0;

				//if the gradient has been chosen, put the two color keys together seperated by a space
				if($_POST['gradient'] == 1){
					$_POST['polygon_color'] = $_POST['polygon_color'].' '.$_POST['regionTwo'];
					$data['polygon_color'] = $_POST['polygon_color'];
				}
				else {
				//default gradient is color to white
					$data['polygon_color'] = $_POST['polygon_color'].' FFFFFF';
				}

				//handle the status
				if($map->map_creation_progress != null)
				{
					$_POST['map_creation_progress'] = $map->map_creation_progress; 
				}		
				$map->update_map($_POST);
				if($first_time)
				{
					//create this as being owned by the current user
					$share = Model_Sharing::create_owner($map->id, $this->user->id);						
				}
				
				
				//handle the xls file, or Google Doc, if there's something to save
				if(($_FILES['file']['name'] != '' AND $_POST['filetype'] == 'excel')  OR ($_POST['googleid'] != '' AND $_POST['filetype'] == 'google'))
				{					
					//now if we need to save a file
					if($_FILES['file']['name'] != '' AND $_POST['filetype'] == 'excel')
					{
						$filename = $this->_save_file($_FILES['file'], $map);
					}
					//else we need to save a google doc
					else 
					{
						$filename = $this->_save_google_doc($_POST['googlelink'], $map);
					}
					//if the user is uploading a new data source reset the map creation progress;
					$_POST['map_creation_progress'] = 1;
					$map->file = $filename;
					$map->save();
				
					//blow away all existing map sheets for this map if there are any
					$map_sheets = ORM::factory('Mapsheet')
						->where('map_id', '=', $map->id)
						->find_all();
					foreach($map_sheets as $map_sheet)
					{
						$map_sheet->delete();
					}
				
					//now we need to figure out what sheets there are
					//read the xls file and parse it
					$file_path = DOCROOT.'uploads/data/'. $map->file;
					 
					//read in the excel file
					$excel = Helper_Excel::open_for_reading_data($file_path);
					
					//determine if spreadsheet is large or not
					$sheet_names = $excel->getSheetNames();
					$highestCol = 0;
					$highestRow = 0;
					
					$i = 0;
					foreach($sheet_names as $sheet_name)
					{				
						$sheet = $excel->getSheetByName($sheet_name);
						$map_sheet = ORM::factory('Mapsheet');
						$map_sheet->position = $i;
						$map_sheet->name = $sheet_name;
						$map_sheet->map_id = $map->id;
						$map_sheet->save(); 
						$i++;
						$colTemp = PHPExcel_Cell::columnIndexFromString($sheet->getHighestDataColumn());
						$highestCol = $colTemp > $highestCol ? $colTemp : $highestCol;  
						$highestRow = intval($sheet->getHighestDataRow()) > $highestRow ? intval($sheet->getHighestDataRow()) : $highestRow;						
					}
										
					//if there are more than 26 columns and 6 rows, would be approximately 200 datapoints
					if($i * $highestCol * $highestRow > 20000){
						$map->large_file = TRUE;
						$map->save();
					}
					else //clear the large map file if the size of the data source has dropped
					{
						$map->large_file = FALSE;
						$map->save();
					}
				}
				
				
				HTTP::redirect('mymaps/add2?id='.$map->id);	
							
				
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors_temp = $e->errors('register');
				if(isset($errors_temp["_external"]))
				{
					$this->template->content->errors = array_merge($errors_temp["_external"], $this->template->content->errors);
				}				
				else
				{
					foreach($errors_temp as $error)
					{
						if(is_string($error))
						{
							$this->template->content->errors[] = __($error);							
						}
					}
				}
				$data = array_merge($data,$_POST);
				$this->template->content->data = $data;
			}
		}
		
		
		
	 }//end action_add1
	 
	 
	 /**
	 *	Calls the helper slug checker and exits if the slug is not defined
	 */
	 public function action_checkslug(){
	 	$this->auto_render = false;
	 	$this->response->headers('Content-Type','application/json');
	 	 
	 	
	 	if(!isset($_POST['slug'])){
	 		echo '{}';
	 		exit;
	 	}
	 	if($_POST['id'] == 0){
	 		$db_obj = ORM::factory('Map');
	 	 }
	 	else {
	 		$db_obj = ORM::factory('Map')->where('slug', '=', $_POST['slug'])->find();
	 	}
		//the helper function returns JSON that tells the javascript what to do
	 	Helper_Slugs::check_slug($_POST['slug'], $db_obj);
	 }
	
}//end of class