<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Mymaps.php - Controller
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/

class Controller_Mymaps extends Controller_Loggedin {

	
	/**
	 Set stuff up, mainly just check if the user is an admin or not
	 */
	public function before()
	{
		parent::before();
	
	}
	


	/**
	where users go to change their profile
	*/
	public function action_index()
	{
		/***** initialize stuff****/
		//The title to show on the browser
		$this->template->html_head->title = __("My Maps");
		//make messages roll up when done
		$this->template->html_head->messages_roll_up = true;
		//the name in the menu
		$this->template->header->menu_page = "mymaps";
		$this->template->content = view::factory("mymaps/mymaps");
		$this->template->content->errors = array();
		$this->template->content->messages = array();
		$this->template->html_head->script_files[] = 'media/js/jquery.tools.min.js';
		//set the JS
		$js = view::factory('mymaps/mymaps_js');
		$this->template->html_head->script_views[] = $js;
		$this->template->html_head->script_views[] = view::factory('js/messages');
		$this->template->html_head->script_views[] = view::factory('js/shareEdit');
		$this->template->html_head->script_views[] = view::factory('js/facebook');
		
		
		
		
		/********Check if we're supposed to do something ******/
		if(!empty($_POST)) // They've submitted the form to update his/her wish
		{
			try
			{	
				if($_POST['action'] == 'delete')
				{
					$share = Model_Sharing::get_share($_POST['map_id'], $this->user);
					
					if($share->permission == Model_Sharing::$owner)
					{
						$map = ORM::factory('Map',$_POST['map_id']);
						$this->template->content->messages[] = __('Map Deleted').': '.$map->title;
						Model_Map::delete_map($_POST['map_id']);
					}
					else
					{
						$this->template->content->errors[] = 'Hey, You are trying to delete someone elses map. Shame on you.';
					}
					
				}
				
				if($_POST['action'] == 'delete_selected' AND isset($_POST['map_check']))
				{
					foreach($_POST['map_check'] as $map_id=>$value)
					{
						$share = Model_Sharing::get_share($map_id, $this->user);
						if($share->permission == Model_Sharing::$owner)
						{
							$map = ORM::factory('Map',$map_id);
							$this->template->content->messages[] = __('Map Deleted').': '.$map->title;
							Model_Map::delete_map($map_id);
						}
						else
						{
							$this->template->content->errors[] = 'Hey, You are trying to delete someone elses map. Shame on you.';
						}
						
					}
				}
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
							$this->template->content->errors[] = $error;							
						}
					}
				}
			}	
		}
		
		/*****Render the forms****/
		
		//get the forms that belong to this user or that they've been given permission
		$maps = ORM::factory("Map")
			->select('sharing.*')
			->join('sharing')
			->on('sharing.map_id','=','map.id')
			->where('sharing.user_id', '=', $this->user->id)
			->order_by('title', 'ASC')
			->find_all();
		
		$this->template->content->maps = $maps;
		
		
		
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
	
	 
	 
	 /**
	  * creates the page for checking the spreadsheet or file submitted and choosing options for the table
	  */
	 public function action_add2()
	 {  	
	 	//for memory usage debuging
	 	//echo "Just started - Memory used: ". number_format(memory_get_peak_usage(),0,'.',',')."<br/>";
	 	//echo "Just started - Memory used: ". number_format(memory_get_usage(),0,'.',',')."<br/>";

	 	//get the id
	 	$map_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	 	//something when wrong, kick them back to add1
	 	if($map_id == 0)
	 	{
	 		HTTP::redirect('mymaps/add1');
	 	}
	 	
	 	//get the sheet position
	 	$sheet_position = isset($_GET['sheet']) ? intval($_GET['sheet']) : 0;
	 	
	 	//pull the map object from the DB
	 	$map = ORM::factory('Map', $map_id);
	 	
	 	//check permissions of the user on this map
	 	$this->check_map_permissions($map_id, $this->user->id);
	 	
	 	if($map->map_creation_progress < 1)
	 	{
	 		HTTP::redirect('mymaps/add1/?id='.$map_id);
	 	}
	 	
	 	//grab all the sheet objects too
	 	$sheets = ORM::factory('Mapsheet')
	 		->where('map_id', '=', $map->id)
	 		->where('position', '=', $sheet_position)
	 		->order_by('position', 'ASC')
	 		->find_all();
	 	
	 	//create a look-up table for use later
	 	$sheet_name_to_db_id = array();
	 	foreach($sheets as $s)
	 	{
	 		$sheet_name_to_db_id[$s->name] = $s->id;
	 	}

	 	//initialize the data array for this form
	 	$data = array('row'=>array(), 'column'=>array());
	 	
	 	//now we need to figure out what is on the sheets
	 	//read the xls file and parse if
	 	$file_path = DOCROOT.'uploads/data/'. $map->file;
	 		
	 	$excel = Helper_Excel::open_for_reading_data($file_path);
	 	
	 	$sheet_names = $excel->getSheetNames();
	 	$sheet_data = array();
	 	//now all of this is to init the $data array for the form.
	 	foreach($sheet_names as $sheet_name)
	 	{
	 		//make sure that we're only looking at the sheet we care about
	 		if(!isset($sheet_name_to_db_id[$sheet_name]))
	 		{
	 			continue;
	 		}
	 		$sheet = $excel->getSheetByName($sheet_name);
	 		$sheet_data[$sheet_name] = $sheet->toArray(null, true, true, true);
	 		//we don't need the excel or sheet objects. Even though we're in a loop it should never run more than once.
	 		//probably shouldn't assume that, but whatever. We have bigger problems if it does run more than once.
	 		unset($sheet);
	 		unset($excel);
	 		gc_collect_cycles(); //excel files can be huge, so we force garabage collection here.
	 		
	 		//now use that look-up table to get the DB id of this sheet
	 		$sheet_db_id = $sheet_name_to_db_id[$sheet_name];
	 		$data['row'][$sheet_db_id] = array();
	 		$data['column'][$sheet_db_id] = array();
	 		foreach($sheet_data[$sheet_name] as $row_index=>$row_data)
	 		{
	 			if($row_index == 1)
	 			{
	 				//grab the columns
	 				foreach($row_data as $column_index=>$column)
	 				{
	 					$data['column'][$sheet_db_id][$column_index]=null;
	 				}
	 			}
	 			$data['row'][$sheet_db_id][$row_index]=null;
	 		} 	 			
	 	}
	 		
	 	//now that we've initizlied the $data array, lets see if there is any existing data we can throw in there	 	
	 	foreach($sheet_name_to_db_id as $sheet_name=>$sheet_id)
	 	{	 		
	 		//columns first
		 	$columns = ORM::factory('Column')
		 		->where('mapsheet_id', '=', $sheet_id)
		 		->find_all();
		 	foreach($columns as $col)
		 	{
		 		$data['column'][$sheet_id][$col->name] = $col->type;
		 	}
		 	//rows next
		 	$rows = ORM::factory('Row')
			 	->where('mapsheet_id', '=', $sheet_id)
			 	->find_all();
		 	foreach($rows as $row)
		 	{
		 		$data['row'][$sheet_id][$row->name] = $row->type;
		 	}
	 	}
	 	
	 	
	 	//echo "Finished parsing the excel file - Memory used: ". number_format(memory_get_peak_usage(),0,'.',',')."<br/>";
	 	//echo "Finished parsing the excel file - Memory used: ". number_format(memory_get_usage(),0,'.',',')."<br/>";
	 	//next the rows
	 
	 	/***Now that we have the form, lets initialize the UI***/
	 	//The title to show on the browser
	 	$this->template->html_head->title = __('Add Map - Data Structure');
	 	//make messages roll up when done
	 	$this->template->html_head->messages_roll_up = true;
	 	//the name in the menu
	 	$this->template->header->menu_page = "mymaps";
	 	$this->template->content = view::factory("mymaps/add2");
	 	$this->template->content->map_id = $map_id;
	 	$this->template->content->map = $map;
	 	$this->template->content->data = $data;
	 	$this->template->content->sheets = $sheets;
	 	$this->template->content->sheet_data = $sheet_data;
	 	$this->template->content->errors = array();
	 	$this->template->content->messages = array();	 	
	 	$this->template->content->sheet_position = $sheet_position;
	 	$this->template->header->menu_page = "createmap";
	 	
	 	$js = view::factory('mymaps/add2_js');
	 	$this->template->html_head->script_views[] = $js;
	 	
	 	//some contstants for the form
	 	$column_types = array('region'=>__('Region'),
	 			'indicator'=>__('Indicator'),	 			
	 			'total'=>__('Total'),
	 			'total_label'=>__('Total Label'),
	 			'unit'=>__('Unit'),
	 			'source'=>__('Source'),
	 			'source link'=>__('Source Link'),
	 			'ignore'=>__('Ignore')
	 			);
	 	$this->template->content->column_types = $column_types;
	 	
	 	$row_types = array('data'=>__('Data'),
	 			'header'=>__('Header'),
	 			'ignore'=>__('Ignore'),
	 	);
	 	$this->template->content->row_types = $row_types;
	 	

	 	$this->template->html_head->script_views[] = view::factory('js/messages');
	 
	 	//get the status
	 	$status = isset($_GET['status']) ? $_GET['status'] : null;
	 	if($status == 'saved')
	 	{
	 		$this->template->content->messages[] = __('changes saved');
	 	}
	 
	 	//echo "About to handle the POST - Memory used: ". number_format(memory_get_peak_usage(),0,'.',',')."<br/>";
	 	//echo "About to handle the POST - Memory used: ". number_format(memory_get_usage(),0,'.',',')."<br/>";
	 	
	 	/******* Handle incoming data*****/
	 	if(!empty($_POST)) // They've submitted the form to update his/her wish
	 	{
	 		try
	 		{
	 			//if we're editing things
	 			if($_POST['action'] == 'edit')
	 			{
	 				//handle the is_ignored value
	 				if(isset($_POST['is_ignored']))
	 				{
		 				foreach($_POST['is_ignored'] as $sheet_id=>$sheet)
		 				{
		 					$mapsheet = ORM::factory('Mapsheet')
		 						->where('id', '=', $sheet_id)
		 						->find();
		 					$mapsheet->is_ignored = 1;
		 					$mapsheet->save();
		 				}
	 				}
	 				else
	 				{
	 					$mapsheet = ORM::factory('Mapsheet')
		 						->where('map_id', '=', $map_id)
		 						->where('position', '=', $_POST['sheet_position'])
		 						->find();
		 					$mapsheet->is_ignored = 0;
		 					$mapsheet->save();
	 				}
	 			
	 				//handle the column 
	 				$sheet_column_data = null;
	 				foreach($_POST['column'] as $sheet_id=>$sheet)
	 				{
	 					$this->_process_column_data_structure($sheet_id,$sheet);
	 					$sheet_column_data = $sheet;
	 				}
			 				
	 				//now handle the rows
	 				$sheet_row_data = null;
	 				foreach($_POST['row'] as $sheet_id =>$sheet)
	 				{
	 					$this->_process_row_data_structure($sheet_id, $sheet);
	 					$sheet_row_data = $sheet;
	 				}
	 				
	 				//if they want to make this template the same for all other sheets do that here
	 				if(isset($_POST['same_structure']))
	 				{
	 					$sheets =  ORM::factory('Mapsheet')
								 		->where('map_id', '=', $map->id)
								 		->where('position', '>', $_POST['sheet_position'])
								 		->find_all();
	 					
						foreach($sheets as $sheet)
						{							
							$this->_process_column_data_structure($sheet->id,$sheet_column_data);
							$this->_process_row_data_structure($sheet->id, $sheet_row_data);
							set_time_limit(30);//because this could take a long freaking time.
						}
	 				}
			 				
	 				//send to next page if no errors
	 				if(count($this->template->content->errors) == 0)
	 				{
	 					//get the highest postion for a sheet of this map
	 					$max_sheet = ORM::factory('Mapsheet')
								 		->where('map_id', '=', $map->id)
								 		->order_by('position', 'DESC')
								 		->limit(1)
								 		->find()
								 		->position;
								 	
	 					//more debuging
	 					//echo "Finished processing the POST - Memory used: ". number_format(memory_get_peak_usage(),0,'.',',')."<br/>";
	 					//echo "Finished processing the POST - Memory used: ". number_format(memory_get_usage(),0,'.',',')."<br/>";
	 					
	 					//if we have more sheets to fiddle with AND they didn't select same structure then move on to that sheet
	 					if($sheet_position < $max_sheet AND !isset($_POST['same_structure']))
	 					{
	 						$next_sheet = $sheet_position + 1;
	 						HTTP::redirect('mymaps/add2?id='.$map->id.'&sheet='.$next_sheet);
	 					}
	 					//else move on to add3
	 					else
	 					{		 					
	 						//TODO: We need a way to know if they hide all their sheets, and tell them not to do that.
	 						
		 					//don't change the map creation progress if they've already gone past this point
		 					if($map->map_creation_progress < 2)
		 					{
		 						$map->map_creation_progress = 2;
		 					}
		 					$map->save();
		 				
		 					HTTP::redirect('mymaps/add3?id='.$map->id);
	 					}
	 				}	 						
		 					 				
	 			}//end if $_POST['action']==edit
	 	
	 			
	 		}//end try
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
	 						$this->template->content->errors[] = $error;
	 					}
	 				}
	 			}
	 		}
	 	}
	 }//end action_add2
	 

	 /**
	  * Helper function to set the data structure of a sheet's rows
	  * @param int $sheet_id DB id of a sheet
	  * @param array $sheet POST data for the sheet's rows
	  */
	 private function _process_row_data_structure($sheet_id, $sheet)
	 {
	 	$sheet_id = intval($sheet_id); //make sure we're only getting intergers
	 	
	 	//for the sake of speed we're going to interact directly with the database here
	 	//so go ahead and set that up
	 	
	 	$server = Kohana::$config->load('database.default.connection.hostname');
	 	$user_name = Kohana::$config->load('database.default.connection.username');
	 	$password = Kohana::$config->load('database.default.connection.password');
	 	$database = Kohana::$config->load('database.default.connection.database');
	 	
	 	$database = new mysqli($server, $user_name, $password, $database);
	 	
	 	
	 	if(mysqli_connect_errno())
	 	{
	 		return "Error - Couldn't connect to the database";
	 	}
	 	
 		//first we blow away any column data associated with this sheet
 		$sql = "DELETE FROM `rowss` WHERE `rowss`.`mapsheet_id` = ".$sheet_id;
 		$database->query($sql); 		
 			
 		$mapsheet = ORM::factory('Mapsheet')
 		->where('id', '=', $sheet_id)
 		->find();
 	
 		if($mapsheet->is_ignored == 0)
 		{
 	
 			$header_count = 0;
 			$data_count = 0;
 			
 			//create a bunch of insert statements
 			$sql = "INSERT INTO  `rowss` (`id` ,`mapsheet_id` ,`name` ,`type`) VALUES ";

 			$i = 0;
 			foreach($sheet as $row_name=>$row_type) //loop over the column data
 			{
 				//hanlde commas
 				$i++;
 				if($i > 1){$sql .= ',';}
 				
 				//make the sql
 				$sql .= "(NULL ,  '".$sheet_id."',  '".$database->real_escape_string($row_name)."',  '".$database->real_escape_string($row_type)."')"; 
 				 					
 				if($row_type == "header")
 				{
 					$header_count++;
 				}
 				if($row_type == "data")
 				{
 					$data_count++;
 				}
 					
 			}
 			
 			//dont' forget the semi-colon
 			$sql .= ";";
 	
 			$database->query($sql);
 			$database->close();
 			
 			$sheet_name = $mapsheet->name;
 	
 			//validate sheets by checking row counts
 			if($header_count != 1)
 			{
 				$this->template->content->errors[] = __('Sheet').' '.$sheet_name. ' '. __('needs exactly one row set as a header row.');
 			}
 	
 			if($data_count < 1)
 			{
 				$this->template->content->errors[] = __('Sheet').' '.$sheet_name. ' '. __('needs at least one row set as a data row.');
 			}
 				
 		}//end if not ignored statement
	 }
	 

	 /**
	  * Helper function to handle the data structure of a sheet's columns
	  * @param int $sheet_id DB id of a sheet
	  * @param array $sheet POST data for a sheet's columns
	  */
	 private function _process_column_data_structure($sheet_id, $sheet)
	 {
			
	 	$sheet_id = intval($sheet_id);
	 	
 		$mapsheet = ORM::factory('Mapsheet')
 		->where('id', '=', $sheet_id)
 		->find();
 	
 			
 		if($mapsheet->is_ignored == 0)
 		{
 	
 			$indicator_count = 0;
 			$region_count = 0;
 			$total_count = 0;
 			$total_label_count = 0;
 			$unit_count = 0;
 			$source_count = 0;
 			$source_link_count = 0;
 			
 			$server = Kohana::$config->load('database.default.connection.hostname');
 			$user_name = Kohana::$config->load('database.default.connection.username');
 			$password = Kohana::$config->load('database.default.connection.password');
 			$database = Kohana::$config->load('database.default.connection.database');
 			 
 			$database = new mysqli($server, $user_name, $password, $database);
 	
 			$delete_sql = "DELETE FROM columns WHERE mapsheet_id = ". $sheet_id. " AND NOT IN (";
 			
 			//so before doing anything get a list of the columns that already exist
 			$existing_columns_sql = "SELECT * FROM  `columns` WHERE  `mapsheet_id` = ".$sheet_id;
 			$existing_columns_result = $database->query($existing_columns_sql);
 			$existing_columns_name_to_id = array();
 			
 			for ($row_no = $existing_columns_result->num_rows - 1; $row_no >= 0; $row_no--)
 			{
	 			$existing_columns_result->data_seek($row_no);
	 			$row = $existing_columns_result->fetch_assoc();
	 			$existing_columns_name_to_id[$row['name']] = $row['id'];
 			}
 			$existing_columns_result->close();
 			
 			
 			$insert_sql = "";
 	
 			foreach($sheet as $column_name=>$column_type) //loop over the column data
 			{
 					
 				$column_name = $database->real_escape_string($column_name);
 				$column_type = $database->real_escape_string($column_type);
 				
 				//update the list of things NOT to delete
 				if(strlen($delete_sql) > 0){
 					$delete_sql .= ",";
 				}
 				$delete_sql .= "'".$column_name."'";
 				//if it's known get the column ID, else it's null
 				$column_id = isset($existing_columns_name_to_id[$column_name]) ? $existing_columns_name_to_id[$column_name] : "NULL";
 				//handle commas
 				if(strlen($insert_sql) > 0){$insert_sql .= ',';}
 				
 				$insert_sql .= "(".$column_id.",".$sheet_id.",'".$column_name."','".$column_type."')";
 				
 				//now keep track of how many types we have
 					
 				if($column_type == "indicator")
 				{
 					$indicator_count++;
 				}
 				if($column_type == "region")
 				{
 					$region_count++;
 				}
 				if($column_type == "total")
 				{
 					$total_count++;
 				}
 				if($column_type == "total_label")
 				{
 					$total_label_count++;
 				}
 				if($column_type == "unit")
 				{
 					$unit_count++;
 				}
 				if($column_type == "source")
 				{
 					$source_count++;
 				}
 				if($column_type == "source link")
 				{
 					$source_link_count++;
 				}
 			}
 			//run the insert/update query
 			$database->query("INSERT INTO `columns` (id, mapsheet_id, name, type) VALUES ".$insert_sql. " ON DUPLICATE KEY UPDATE type=VALUES(type)");
			
			
			//delete what's left over
			$database->query($delete_sql.');');
			$database->close();
 	 			
 				
 			//validate sheets by checking column counts
 			if($indicator_count < 1)
 			{
 				$sheet_name = ORM::factory('Mapsheet', $sheet_id)->name;
 				$this->template->content->errors[] = __('Sheet').' '.$sheet_name. ' '. __('needs at least one column set as an indicator');
 			}
 			if($region_count < 1)
 			{
 				$sheet_name = ORM::factory('Mapsheet', $sheet_id)->name;
 				$this->template->content->errors[] = __('Sheet').' '.$sheet_name. ' '. __('needs at least one column set for region.');
 			}
 			if($total_count > 1)
 			{
 				$sheet_name = ORM::factory('Mapsheet', $sheet_id)->name;
 				$this->template->content->errors[] = __('Sheet').' '.$sheet_name. ' '. __('cannot have more than one total column.');
 			}
 			if($total_label_count > 1)
 			{
 				$sheet_name = ORM::factory('Mapsheet', $sheet_id)->name;
 				$this->template->content->errors[] = __('Sheet').' '.$sheet_name. ' '. __('cannot have more than one total label column.');
 			}
 			if($unit_count > 1)
 			{
 				$sheet_name = ORM::factory('Mapsheet', $sheet_id)->name;
 				$this->template->content->errors[] = __('Sheet').' '.$sheet_name. ' '. __('cannot have more than one unit column.');
 			}
 			if($source_count > 1)
 			{
 				$sheet_name = ORM::factory('Mapsheet', $sheet_id)->name;
 				$this->template->content->errors[] = __('Sheet').' '.$sheet_name. ' '. __('cannot have more than one source column.');
 			}
 			if($source_link_count > 1)
 			{
 				$sheet_name = ORM::factory('Mapsheet', $sheet_id)->name;
 				$this->template->content->errors[] = __('Sheet').' '.$sheet_name. ' '. __('cannot have more than one source link column.');
 			}
 		}	//end if not ignored statement
	 }
	 
	 
	 
	 /**
	  * This after asking the user for the meaning of the 
	  * rows and columns in the previous controller,
	  * we'll now show them what the data base thinks things are
	  * and ask them to verify, or go back and change stuff around
	  */
	 public function action_add3()
	 {
	 	//get the id
	 	$map_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	 	//something when wrong, kick them back to add1
	 	if($map_id == 0)
	 	{
	 		HTTP::redirect('mymaps/add1');
	 	}
	 	 
	 	//pull the map object from the DB
	 	$map = ORM::factory('Map', $map_id);
	 	
	 	//check permissions of the user on this map
	 	$this->check_map_permissions($map_id, $this->user->id);
	 	
	 	if($map->map_creation_progress < 2)
	 	{
	 		$this->template->content->messages[] = __('Map stage missing. Complete this page first.');
	 		HTTP::redirect('mymaps/add2/?id='.$map_id);
	 	}
	 	
	 	//check if they've submitted this
	 	if(!empty($_POST)) // They've submitted the form to update his/her wish
	 	{
	 		
 			//if we're editing things
 			if($_POST['action'] == 'edit')
 			{
 				//don't change the map creation progress if they've already gone past this point
 				if($map->map_creation_progress < 3)
 				{
 					$map->map_creation_progress = 3;
 				}
 				$map->save();
 				HTTP::redirect('mymaps/add4?id='.$map->id);
 			}
	 	}
	 			

	 	//grab all the sheet objects too
	 	$sheets = ORM::factory('Mapsheet')
		 	->where('map_id', '=', $map->id)
		 	->where('is_ignored', '=', 0)
		 	->order_by('position', 'ASC')
		 	->find_all();
	 	
 	
	 	$columns = array();
	 	$rows = array();
	 	
	 	//now loop over the sheets and grab the column and row data for each 
	 	//grab all the columns
	 	foreach($sheets as $sheet_id=>$sheet)
	 	{
	 		if($sheet->is_ignored == 0)
	 		{
		 		$columns[$sheet->id] = array();
		 		
		 		$columns[$sheet->id]['region'] = ORM::factory('Column')
		 			->where('mapsheet_id', '=', $sheet->id)
		 			->where('type', '=', 'region')
		 			->find_all();
		 		
		 		$columns[$sheet->id]['indicator'] = ORM::factory('Column')
			 		->where('mapsheet_id', '=', $sheet->id)
			 		->where('type', '=', 'indicator')
			 		->find_all();
		 		
		 		$columns[$sheet->id]['ignore'] = ORM::factory('Column')
			 		->where('mapsheet_id', '=', $sheet->id)
			 		->where('type', '=', 'ignore')
			 		->find_all();
		 		
		 		$columns[$sheet->id]['total'] = ORM::factory('Column')
			 		->where('mapsheet_id', '=', $sheet->id)
			 		->where('type', '=', 'total')
			 		->find_all();
		 		
		 		$columns[$sheet->id]['unit'] = ORM::factory('Column')
			 		->where('mapsheet_id', '=', $sheet->id)
			 		->where('type', '=', 'unit')
			 		->find_all();
		 	
		 		$columns[$sheet->id]['source'] = ORM::factory('Column')
			 		->where('mapsheet_id', '=', $sheet->id)
			 		->where('type', '=', 'source')
			 		->find_all();
		 		
		 		$columns[$sheet->id]['source_link'] = ORM::factory('Column')
			 		->where('mapsheet_id', '=', $sheet->id)
			 		->where('type', '=', 'source link')
			 		->find_all();
		 		
		 		//now rows
		 		$rows[$sheet->id]['data'] = ORM::factory('Row')
			 		->where('mapsheet_id', '=', $sheet->id)
			 		->where('type', '=', 'data')
			 		->find_all();
		 		 
		 		$rows[$sheet->id]['header'] = ORM::factory('Row')
			 		->where('mapsheet_id', '=', $sheet->id)
			 		->where('type', '=', 'header')
			 		->find_all();
			 		
		 		$rows[$sheet->id]['ignore'] = ORM::factory('Row')
			 		->where('mapsheet_id', '=', $sheet->id)
			 		->where('type', '=', 'source link')
			 		->find_all();
	 		}
	 		
	 	}
	 	
	 	//Now we have all the data we need
	 	//so lets load in the excel file
	 	//now we need to figure out what is on the sheets
	 	//read the xls file and parse if
	 	$file_path = DOCROOT.'uploads/data/'. $map->file;
	 	
	 	$excel = Helper_Excel::open_for_reading_data($file_path);
	 	
	 	
	 	//so now lets figure out the names of all the regions  and indicators at play for each sheet
	 	$sheet_regions = array();
	 	$sheet_indicators = array();
	 	$errors = array();
	 	$warnings = array();
	 	$longUnits = array();
	 	foreach($sheets as $sheet)
	 	{
	 		$longUnits[$sheet->id] = array();
	 		if($sheet->is_ignored == 0)
	 		{	
	 			//get the index of the header
		 		if(count($rows[$sheet->id]['header']) == 0)
		 		{
		 			$errors[] = __('no header is defined for sheet ').$sheet->name;
		 			continue; 
		 		}
		 		if(count($rows[$sheet->id]['header']) > 1)
		 		{
		 			$warnings[] = __('More than one row has been defined as a header in sheet ').$sheet->name;
		 		}
		 		//we have a header row
		 		$header_index = $rows[$sheet->id]['header'][0]->name;
		 		
		 		//figure out what the regions are
		 		if(count($columns[$sheet->id]['region']) == 0)
		 		{
		 			$errors[] = __('no regions are defined for sheet ').$sheet->name;
		 			continue;
		 		}
		 		
		 		//grab the sheet data
		 		$sheet_data = $excel->getSheetByName($sheet->name)->toArray(null, true, true, true);;
		 		//setup our array
		 		$sheet_regions[$sheet->id] = array();
		 		//loop over the regions
		 		foreach($columns[$sheet->id]['region'] as $region)
		 		{
		 			$sheet_regions[$sheet->id][] = $sheet_data[$header_index][$region->name];
		 		}
	
		 		//TODO check indicators, total, units, source, and source link
		 		$sheet_indicators[$sheet->id] = $this->_build_indicators_html($sheet_data, $header_index, $rows[$sheet->id]['data'], $columns[$sheet->id]['indicator'], $errors, $warnings);
		 		
		 		//check if units are too long for a easily viewable map
		 		if(count($columns[$sheet->id]['unit']) > 0){
			 		$unitColumnName = '';
			 		foreach($columns[$sheet->id]['unit'] as $unitColumn){
			 			$unitColumnName = $unitColumn->name;
			 			break;
			 		}
			 		foreach($rows[$sheet->id]['data'] as $dataRow){
				 		$unit = $sheet_data[$dataRow->name][$unitColumnName];
				 		if(strlen($unit) > 12){
				 			$longUnits[$sheet->id][] = $unit;
				 		}
			 		}
		 		}
		 		
	 		}
	 	}
	 	
	 	
	 	
	 
	 	/***Now that we have the form, lets initialize the UI***/
	 	//The title to show on the browser
	 	$this->template->html_head->title = __('Add Map - Page 3');
	 	//make messages roll up when done
	 	$this->template->html_head->messages_roll_up = true;
	 	//the name in the menu
	 	$this->template->header->menu_page = "mymaps";
	 	$this->template->content = view::factory("mymaps/add3");
	 	$this->template->content->map_id = $map_id;
	 	$this->template->content->map = $map;
	 	$this->template->content->sheets = $sheets;
	 	$this->template->content->errors = $errors;
	 	$this->template->content->longUnit = $longUnits;
	 	$this->template->content->warnings = $warnings;
	 	$this->template->content->sheet_regions = $sheet_regions;
	 	$this->template->content->sheet_indicators = $sheet_indicators;
	 	$this->template->content->messages = array();
	 	$this->template->html_head->script_views[] = view::factory('js/messages');
	 	$this->template->header->menu_page = "createmap";
	 
	 	//get the status
	 	$status = isset($_GET['status']) ? $_GET['status'] : null;
	 	if($status == 'saved')
	 	{
	 		$this->template->content->messages[] = __('changes saved');
	 	}
	 
	 	
	 }//end action_add3
	 
	 
	 
	 /**
	  * This asks the user to pick a map for their map,
	  * or as I call it, a template
	  */
	 public function action_add4()
	 {
	 	
	 	//get the id
	 	$map_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	 	//something when wrong, kick them back to add1
	 	if($map_id == 0)
	 	{
	 		HTTP::redirect('mymaps/add1');
	 	}
	 		 
	 	//pull the map object from the DB
	 	$map = ORM::factory('Map', $map_id);
	 	
	 	//check permissions of the user on this map
	 	$this->check_map_permissions($map_id, $this->user->id);
	 	
	 	if($map->map_creation_progress < 2)
	 	{
	 		$this->template->content->messages[] = __('Map stage missing. Complete this page first.');
	 		HTTP::redirect('mymaps/add2/?id='.$map_id);
	 	}
	 	
	 	$data = array();
	 	$data['id'] = $map->id;
	 	$data['template_id'] = $map->template_id;
	 
	 	
	 	//grab all the templates
	 	$templates = ORM::factory('Template');	 		
	 	if(!$this->is_admin)
	 	{
	 		$templates = $templates->where_open()
	 			->where('is_official','=',1)
	 			->where('is_private','=', '0')
	 			->where_close()
	 			->or_where('user_id','=', $this->user->id);
	 	}
	 	$templates = $templates->order_by('title', 'ASC')
	 		->find_all();
	 	 
	 	 
	 	/***Now that we have the form, lets initialize the UI***/
	 	//The title to show on the browser
	 	$this->template->html_head->title = __('Add Map - Page 4');
	 	//make messages roll up when done
	 	$this->template->html_head->messages_roll_up = true;
	 	//the name in the menu
	 	$this->template->header->menu_page = "mymaps";
	 	$this->template->content = view::factory("mymaps/add4");
	 	$this->template->content->map_id = $map_id;
	 	$this->template->content->map = $map;
	 	$this->template->content->templates = $templates;	
	 	$this->template->content->data = $data;	
	 	$this->template->content->messages = array();
	 	$this->template->content->errors = array();
	 	$this->template->header->menu_page = "createmap";
	 	$this->template->html_head->styles['all'] = 'media/css/jquery-ui.css';
	 	$this->template->html_head->script_files[] = 'media/js/jquery-ui.min.js';
	 	$this->template->html_head->script_views[] = view::factory('js/messages');
	 	$js =  view::factory("mymaps/add4_js");
	 	$js->lat = $map->lat;
	 	$js->lon = $map->lon;
	 	$js->zoom = $map->zoom;
	 	//make sure the map template exists. If it's a user template they could have deleted it out from under themselves.
	 	$template = ORM::factory('Template', $map->template_id);
	 	if($template->loaded())
	 	{
	 		$js->template_id = $map->template_id;
	 	}
	 	else
	 	{
	 		$js->template_id = null;
	 	}
	 	$this->template->html_head->script_views[] = $js;
	 
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
	 			//if we're editing things
	 			if($_POST['action'] == 'edit')
	 			{
	 				//make sure they chose a template
	 				if(!isset($_POST['template_id']))
	 				{
	 					$this->template->content->errors[] = __('You have to choose a template.');
	 					return;
	 				}
	 				
	 				$template = ORM::factory('Template',$_POST['template_id']);
	 				if(($template->is_official == 0 AND $template->user_id != $this->user->id) OR 
	 						($template->is_official == 1 AND $template->is_private == 1))
	 				{
	 					throw new Cannot_Access_Template_Exception(__('You do not have access to this template'));
	 				}
	 				
					$map_array = $map->as_array();
					$map_array['template_id'] = $_POST['template_id'];
					
					$map_array['lat'] = $_POST['lat'];
					$map_array['lon'] = $_POST['lon'];
					$map_array['zoom'] = $_POST['zoom'];
					
					
					//update map creation progress tracker
					//don't change the map creation progress if they've already gone past this point
					if($map->map_creation_progress < 4)
					{
						$map_array['map_creation_progress'] = 4;
					}
					
					$map->update_map($map_array);
	 				HTTP::redirect('mymaps/mapstyle?id='.$map->id);
	 			}
	 	
	 		 	
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
	 						$this->template->content->errors[] = $error;
	 					}
	 				}
	 			}
	 		}
	 		catch(Cannot_Access_Template_Exception $e)
	 		{
	 			$this->template->content->errors[] = $e->getMessage();
	 		}
	 	}
	 	 
	 
	 	 
	 }//end action_add4
	 
	 
	
	 
	 /**
	  * This ask the user to map their regions to the regions of the template
	  */
	 public function action_add5()
	 {
	 	 
	 	//echo "Starting: ". microtime(true). "<br/>";
	 	//$time = microtime(true);
	 	
	 	//get the id
	 	$map_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	 	//something when wrong, kick them back to add1
	 	if($map_id == 0)
	 	{
	 		HTTP::redirect('mymaps/add1');
	 	}
	 	
	 	//get the sheet position
	 	$sheet_position = isset($_GET['sheet']) ? intval($_GET['sheet']) : 0;

	 	$data = array();
	 	
	 	//pull the map object from the DB
	 	$map = ORM::factory('Map', $map_id);
	 	
	 	//make sure the template is still there. If it isn't send them to add4
	 	if(!ORM::factory('Template',$map->template_id)->loaded())
	 	{
	 		HTTP::redirect('mymaps/add4?id='.$map_id);
	 	}
	 	
	 	//check permissions of the user on this map
	 	$this->check_map_permissions($map_id, $this->user->id);
	 	
	 	if($map->map_creation_progress < 4)
	 	{
	 		HTTP::redirect('mymaps/add4/?id='.$map_id);
	 	}
	 	 
	 	//grab the sheets
	 	$sheets = ORM::factory('Mapsheet')
	 		->where('map_id','=',$map->id)
	 		->where('is_ignored', '=', 0)
	 		->where('position', '>=', $sheet_position)
	 		->order_by('position', 'ASC')
	 		->limit(1)
	 		->find_all();

	 	//now grab the region columns for each sheet
	 	$region_columns = array();
	 	$header_rows = array();
	 	$region_columns_string = array();
	 	foreach($sheets as $sheet)
	 	{
	 		if($sheet->is_ignored == 0)
	 		{
	 			//in case we landed on a hidden sheet, update to the next non-hidden sheet
	 			$sheet_position = $sheet->position;
	 			
		 		//grab the header row
		 		$header_rows[$sheet->id] = ORM::factory('Row')
		 		->where('mapsheet_id', '=', $sheet->id)
		 		->where('type', '=', 'header')
		 		->find();
		 		
		 		$region_columns[$sheet->id] = ORM::factory('Column')
		 			->where('mapsheet_id', '=', $sheet->id)
		 			->where('type', '=', 'region')
		 			->find_all();
		 		
		 		$data[$sheet->id] = array();


		 		foreach($region_columns[$sheet->id] as $column)
		 		{
		 			$data[$sheet->id][$column->id] = $column->template_region_id;
		 		}		 		
	 		}
	 		
	 	}
	 	
	 	//grab the regions that the map comes with
	 	$map_regions_temp = ORM::factory('Templateregion')	 	
	 		->where('template_id', '=',$map->template_id)
	 		->order_by('title', 'ASC')
	 		->find_all();
	 	//setup the regions as an array
	 	$map_regions = array();
	 	foreach($map_regions_temp as $m)
	 	{
	 		$map_regions[$m->id] = $m->title;
	 	}
	 	$map_regions[0] = '--'.__('Ignore').'--';
	 	
	 	//finally grab the data in the file
	 	//now we need to figure out what is on the sheets
	 	//read the xls file and parse if
	 	$file_path = DOCROOT.'uploads/data/'. $map->file;
	 	
	 	
	 	//echo "Set up the database stuff: ". (microtime(true) - $time) . "<br/>";
	 	//$time = microtime(true);
	 	
	 	$excel = Helper_Excel::open_for_reading_data($file_path);
	 	
	 	$sheet_data = array();
	 	//now all of this is to init the $data array for the form.
	 	foreach($sheets as $sheet)
	 	{
	 		$sheet_excel = $excel->getSheetByName($sheet->name);
	 		$sheet_data[$sheet->id] = $sheet_excel->toArray(null, true, true, true);
	 	}
	 	
	 
	 	//echo "Finished loading the excel file into arrays: ". (microtime(true) - $time) . "<br/>";
	 	//$time = microtime(true);
	 	 
	 
	 	/***Now that we have the form, lets initialize the UI***/
	 	//The title to show on the browser
	 	$this->template->html_head->title = __('Add Map - Page 5');
	 	//make messages roll up when done
	 	$this->template->html_head->messages_roll_up = true;
	 	//the name in the menu
	 	$this->template->header->menu_page = "mymaps";
	 	$this->template->content = view::factory("mymaps/add5");
	 	$this->template->content->map_id = $map_id;
	 	$this->template->content->map = $map;
	 	$this->template->content->sheets = $sheets;
	 	$this->template->content->region_columns = $region_columns;
	 	$this->template->content->header_rows = $header_rows;
	 	$this->template->content->map_regions = $map_regions;	 	
	 	$this->template->content->sheet_data = $sheet_data;
	 	$this->template->content->data = $data;
	 	$this->template->content->sheet_position = $sheet_position;
	 	$this->template->content->messages = array();
	 	$this->template->content->errors = array();
	 	$this->template->html_head->script_views[] = view::factory('js/messages');
	 	$this->template->header->menu_page = "createmap";
	 		 
	 	//get the status
	 	$status = isset($_GET['status']) ? $_GET['status'] : null;
	 	if($status == 'saved')
	 	{
	 		$this->template->content->messages[] = __('changes saved');
	 	}
	 	
	 	
	 	//echo "Just finished the default stuff: ". (microtime(true) - $time) . "<br/>";
	 	//$time = microtime(true);
	 	
	
	 	/******* Handle incoming data*****/
	 	if(!empty($_POST)) // They've submitted the form to update his/her wish
	 	{
	 		try
	 		{
	 			$time = microtime(true);
	 			//if we're editing things
	 			if($_POST['action'] == 'edit')
	 			{	 	
	 				//that's right we're accessing the database in the raw. Woot.
	 				$server = Kohana::$config->load('database.default.connection.hostname');
	 				$user_name = Kohana::$config->load('database.default.connection.username');
	 				$password = Kohana::$config->load('database.default.connection.password');
	 				$database_name = Kohana::$config->load('database.default.connection.database');
	 				 
	 				$database = new mysqli($server, $user_name, $password, $database_name);
	 				$column_name_to_region = array();
	 				$dupe_region_array = array();
	 				
	 				foreach($_POST['region'] as $sheet_id=>$sheet)
	 				{
	 					
	 					$sheet_id = intval($sheet_id);
	 					$update_sql = "";
	 					$dupes = array();
	 					foreach($sheet as $column=>$region_id)
	 					{
	 						$ignore_region = ORM::factory('Templateregion')->where('title','=','ignore_region')->find();
	 						//make sure it's not the ignore region, we can have more than one of those
	 						if($region_id != $ignore_region->id AND !isset($dupes[$region_id])){
	 							$dupes[$region_id] = 0;
	 						}
	 						if(($region_id != $ignore_region->id) AND (++$dupes[$region_id] > 1)){
	 								$dupe_region_array[$region_id] = $dupes[$region_id];
	 						}
							else {$region_id = intval($region_id);
								$column = intval($column);
	 							$update_sql = "UPDATE  `columns` SET  `template_region_id` =  '".$region_id."' WHERE  `columns`.`id` = ".$column.";";
	 							$database->query($update_sql);
							}
	 					}
						
						
	 					
	 					
						//not sure why I have to do this, but it seems necessary
						$database->close();
						$database = new mysqli($server, $user_name, $password, $database_name);
	 					
	 					//echo "Handled the sheet on screen: ". (microtime(true) - $time) . "<br/>";
	 					//$time = microtime(true);
	 					
	 					//only do this if we need to
	 					if(isset($_POST['same_settings']))
	 					{	 						
		 					$select_sql = "SELECT name, template_region_id FROM columns WHERE mapsheet_id = $sheet_id AND type = 'region'";
		 					$result = $database->query($select_sql);
		 					for ($row_no = $result->num_rows - 1; $row_no >= 0; $row_no--)
		 					{
			 					$result->data_seek($row_no);
			 					$row = $result->fetch_assoc();
			 					$column_name_to_region[$row['name']] = $row['template_region_id'];
		 					}
		 					$result->close();
	 					}
	 					
	 					//echo "Just created the name to region mapping: ". (microtime(true) - $time) . "<br/>";
	 					//$time = microtime(true);
	 					
	 				}
	 				
	 				if(count($dupe_region_array) > 0){
	 					foreach($dupe_region_array as $region_id=>$num_dupes){
	 						$region = ORM::factory('Templateregion', $region_id);
	 						if($region->title != 'ignore_region'){
	 							$error_string = __('You have used region ').$region->title.' '.$num_dupes.__(' times.');
	 							$this->template->content->errors[] = $error_string;
	 							}
	 						else{
	 							unset($dupe_region_array[$region_id]);	
	 						}
	 					}
	 					if(count($dupe_region_array) > 0){
	 						$this->template->content->data = $_POST['region'];
	 						return;
	 					}
	 				}
	 				
	 				
	 				//if the settings we just set are to be repeated on all the other sheets
	 				if(isset($_POST['same_settings']))
	 				{
	 					//get the next sheets
	 					$next_sheets = ORM::factory('Mapsheet')
		 					->where('map_id', '=', $map->id)
		 					->where('is_ignored', '=', 0)
		 					->where('position', '>', $sheet_position)
		 					->find_all();
	 					foreach($next_sheets as $next_sheet)
	 					{
	 						$sheet_id = $next_sheet->id;
	 						$sheet_position = $next_sheet->position; // update this because now we're on this sheet position
	 						$update_sql = "";
	 						foreach($column_name_to_region as $name=>$region_id)
	 						{		 								 						
		 						$update_sql = "UPDATE  `columns` SET  `template_region_id` =  '".$region_id."' WHERE  `columns`.`mapsheet_id` = ".$sheet_id." AND `columns`.`name` = '".$name."';";
		 						$database->query($update_sql);
	 						}
	 						
	 						$database->close();	 						
	 						$database = new mysqli($server, $user_name, $password, $database_name);
	 					}
	 					
	 					//first we need to know the column names and the values they were given
	 				}
	 				
	 				//echo "Handled all the other sheets: ". (microtime(true) - $time) . "<br/>";
	 				//$time = microtime(true);
	 				
	 				//figure out the last sheet that's visible
 					$max_sheet = ORM::factory('Mapsheet')
							 		->where('map_id', '=', $map->id)
							 		->where('is_ignored', '=', 0)
							 		->order_by('position', 'DESC')
							 		->limit(1)
							 		->find()
							 		->position;
 					
 					if($sheet_position < $max_sheet)
 					{
 						//get the next sheet
 						$next_sheet = ORM::factory('Mapsheet')
 						->where('map_id', '=', $map->id)
 						->where('is_ignored', '=', 0)
 						->where('position', '>', $sheet_position)
 						->order_by('position', 'DESC')
 						->limit(1)
 						->find()
 						->position;
 						
 						$database->close();
 						HTTP::redirect('mymaps/add5?id='.$map->id.'&sheet='.$next_sheet);
 					}
 					
 					
 					
 					//now do the heavy lifting of creating the JSON.
 					
 					//pre-load the region names
 					$regions = ORM::factory('Templateregion')
 						->where('template_id', '=', $map->template_id)
 						->find_all();
 					$region_id_to_name = array();
 					foreach($regions as $region)
 					{
 						$region_id_to_name[$region->id] = $region->title;
 					}
 					unset($regions);
 					
 					
 					
 					//save the json to file
 					$file = DOCROOT.'uploads/data/'.$map_id.'.json';
 					//if the file exists delete it
 					if(file_exists($file))
 					{
 						unlink($file);
 					}
 					
 					$file_resource = fopen($file,"w");
 					
 					//grab ALL the sheets
 					$sheets = ORM::factory('Mapsheet')
 					->where('map_id','=',$map->id)
 					->where('is_ignored', '=', 0)
 					->order_by('position', 'ASC')
 					->find_all();
 					
 					

	 				//now create the json	 				 				
	 				$json_header =  '{"title":'.json_encode($map->title).','.
		 				'"description":'.json_encode($map->description).','.
		 				'"centerLat":'.json_encode($map->lat).','.
		 				'"centerLon":'.json_encode($map->lon).','.
		 				'"zoom":'.json_encode($map->zoom).','.
		 				'"sheets":{';
	 				
	 				fwrite($file_resource, $json_header);
	 				
	 				$i = 0;
	 				//now loop over the sheets
	 				foreach($sheets as $sheet)
	 				{
	 					
	 					$i++;
	 					
	 					//get a list of indcators
	 					$indicator_columns = ORM::factory('Column')
	 						->where('mapsheet_id', '=', $sheet->id)
	 						->where('type','=','indicator')
	 						->find_all();
	 					
	 					
	 					
	 					//get the list of data rows
	 					$data_rows = ORM::factory('Row')
	 						->where('mapsheet_id', '=', $sheet->id)
	 						->where('type','=','data')
	 						->find_all();	 					
	 					
	 					
	 					//get a list of regions
	 					$region_columns = ORM::factory('Column')
	 						->where('mapsheet_id', '=', $sheet->id)
	 						->where('type','=','region')
	 						->find_all();
	 					
	 					
	 					//get the list of data rows
	 					$header_row = ORM::factory('Row')
	 					->where('mapsheet_id', '=', $sheet->id)
	 					->where('type','=','header')
	 					->find();
	 					
	 						
	 					
	 					//get unit column
	 					$unit_column = ORM::factory('Column')
	 					->where('mapsheet_id', '=', $sheet->id)
	 					->where('type','=','unit')
	 					->find();
	 					
	 					//get src column
	 					$src_column = ORM::factory('Column')
	 					->where('mapsheet_id', '=', $sheet->id)
	 					->where('type','=','source')
	 					->find();
	 					
	 					//get src link column
	 					$src_link_column = ORM::factory('Column')
	 					->where('mapsheet_id', '=', $sheet->id)
	 					->where('type','=','source link')
	 					->find();
	 					
	 					//get total column
	 					$total_column = ORM::factory('Column')
	 					->where('mapsheet_id', '=', $sheet->id)
	 					->where('type','=','total')
	 					->find();

	 					//get total label column
	 					$total_label_column = ORM::factory('Column')
	 					->where('mapsheet_id', '=', $sheet->id)
	 					->where('type','=','total_label')
	 					->find();
	 						 					
	 					//get the sheet in array form
	 					$excel->garbageCollect();
	 					$sheet_excel = $excel->getSheetByName($sheet->name);	 					
	 					$sheet_array = $sheet_excel->toArray(null, true, true, true);
	 					
	 					$indicators = array();
	 					//echo "Prep work to call build indcators array: ". (microtime(true) - $time) . "<br/>";	 					
	 					//$time = microtime(true);
	 					//get a helper function in here
	 					$indicators = $this->_build_indicators_array($sheet_array, $indicator_columns, $data_rows, $region_columns, $header_row, $indicators,
	 							 $unit_column, $src_column, $src_link_column, $total_column, $total_label_column, $region_id_to_name);
	 						 						 					
	 					if($i > 1)
	 					{
	 						fwrite($file_resource, ',');
	 					}
	 					fwrite($file_resource, '"'.$sheet->id.'":'.json_encode(array('sheetName'=>$sheet->name, 'indicators'=>$indicators)));
	 					
	 	
	 					//echo "Just got back from build indicators array: ". (microtime(true) - $time) . "<br/>";
	 					//$time = microtime(true);
	 					
	 					//hopefully this'll reduce memory usage
	 					unset($indicator_columns);
	 					unset($data_rows);
	 					unset($region_columns);
	 					unset($indicator_columns);
	 					unset($header_row);
	 					unset($unit_column);
	 					unset($src_column);
	 					unset($src_link_column);
	 					unset($total_column);
	 					unset($indicators);
	 					unset($sheet_array);
	 					gc_collect_cycles();
	 					
	 				}
	 				$database->close();
	 				//convert to a string

	 				fwrite($file_resource, '}}');
	 				fclose($file_resource);
	 					
	 				
	 				
	 				$map->json_file = $map_id.'.json';
	 				$map->save();
	 				
	 				//update map creation progress tracker
	 				$map_array = $map->as_array();
	 				//don't change the map creation progress if they've already gone past this point
	 				if($map->map_creation_progress < 5)
	 				{
	 					$map_array['map_creation_progress'] = 5;
	 				}
	 				$map->update_map($map_array);
	 				HTTP::redirect('mymaps/add6?id='.$map->id);
	 			}
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
	 						$this->template->content->errors[] = $error;
	 					}
	 				}
	 			}
	 		}
	 	}
	 
	 
	 
	 }//end action_add5
	 
	 
	 /**
	  * Set the style of the map
	  */
	 public function action_add6(){
	 
	 	//get the id
	 	$map_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	 	//something when wrong, kick them back to add1
	 	if($map_id == 0)
	 	{
	 		HTTP::redirect('mymaps/add1');
	 	}
	 		
	 	//pull the map object from the DB
	 	$map = ORM::factory('Map', $map_id);
	 	 
	 	//check permissions of the user on this map
	 	$this->check_map_permissions($map_id, $this->user->id);
	 	 
	 	if($map->map_creation_progress < 2)
	 	{
	 		$this->template->content->messages[] = __('Map stage missing. Complete this page first.');
	 		HTTP::redirect('mymaps/add2/?id='.$map_id);
	 	}

	 	$s = $map->map_style;
	 	$s = json_encode($s);
	 	$data['style'] = json_decode($s);
	 	$data['id'] = $map->id;
	 	//$data['style'] = $map->map_style;
	 
	 
	 
	 	/***Now that we have the form, lets initialize the UI***/
	 	//The title to show on the browser
	 	$this->template->html_head->title = __('Add Map - Add 6');
	 	//make messages roll up when done
	 	$this->template->html_head->messages_roll_up = true;
	 	//the name in the menu
	 	$this->template->header->menu_page = "mymaps";
	 	$this->template->content = view::factory("mymaps/add6");
	 	$this->template->content->map_id = $map_id;
	 	$this->template->content->map = $map;
	 	$this->template->content->data = $data;
	 	$this->template->content->messages = array();
	 	$this->template->content->errors = array();
	 	$this->template->header->menu_page = "createmap";
	 	$this->template->html_head->styles['all'] = 'media/css/jquery-ui.css';
	 	$this->template->html_head->script_files[] = 'media/js/jquery-ui.min.js';
	 	$this->template->html_head->script_views[] = view::factory('js/messages');
	 	//$js =  view::factory("mymaps/add4_js");
	 	 
	 
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
	 			//if we're editing things
	 			if($_POST['action'] == 'edit')
	 			{
	 
	 				//update map creation progress tracker
	 				//don't change the map creation progress if they've already gone past this point
	 				if($map->map_creation_progress < 5)
	 				{
	 					$map_array['map_creation_progress'] = 5;
	 				}
	 					
	 				$map->update_map($map_array);
	 				HTTP::redirect($map->slug);
	 			}
	 			 
	 
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
	 						$this->template->content->errors[] = $error;
	 					}
	 				}
	 			}
	 		}
	 		catch(Cannot_Access_Template_Exception $e)
	 		{
	 			$this->template->content->errors[] = $e->getMessage();
	 		}
	 	}
	 
	 
	 }
	 
	 	 
	 
	 /**
	  * Saves a file from the temp upload area to the hard disk
	  * @param array $upload_file the $_FILES['<name>'] array for the given file
	  * @param obj $map Kohana ORM object for a map, this is used in naming the file 
	  * @return boolean or filename
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
	  * Saves the .xls implementation of a google doc
	  * @param string $link HTTP link to the .xls version of the google doc we're to use as our data source
	  * @param obj $map Kohana ORM object for a map, this is used in naming the file
	  * @return string filename
	  */
	 protected function _save_google_doc($link, $map)
	 {
	 	$directory = DOCROOT.'uploads/data/';
	 	$filename = $map->user_id.'-'.$map->id.'.xlsx';
	 	
	 	//we'll need this to get access
	 	$token = $_POST['googletoken'];
	 	
	 	//open up the file streams
	 	$input = fopen($link,"r");
	 	
	 	
	 	$ch = curl_init($link);
	 	$output = fopen($directory . $filename, "w");
	 	
	 	curl_setopt($ch, CURLOPT_FILE, $output);
	 	//curl_setopt($ch, CURLOPT_HEADER, 0);
	 	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	 			'authorization: Bearer '.$token,
	 	));
	 	
	 	curl_exec($ch);
	 	curl_close($ch);
	 	fclose($output);
	 
	 	return $filename;
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
	  * @param dbObject $total_label_column Database object representing the total label column in the data
	  * @param array $region_id_to_name Mapping of region IDs to their names. Speeds up writing out the names
	  * @return array Array of indicators that will be turned into JSON
	  */
	 protected function _build_indicators_array($sheet, $indicator_columns, $data_rows, $region_columns, $header_row, $indicators, 
	 		$unit_column, $src_column, $src_link_column, $total_column, $total_label_column, $region_id_to_name)
	 {
	 		//$_GET['debug'] = true;
	 	$max_execution_time = intval(ini_get('max_execution_time')) - 5;
	 	
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
	 					
	 					if($region_column->template_region_id == 0)
	 					{
	 						continue;
	 					}
	 					
 						
	 					$region_name_xls = trim($sheet[$header_row->name][$region_column->name]);
	 					$region_name_kml = $region_id_to_name[$region_column->template_region_id];
	 					if($region_name_kml == null OR $region_name_kml == '')
	 					{
	 						continue;
	 					}
	 					$region_name_kml = trim($region_name_kml);
	 					$value = $sheet[$data_row->name][$region_column->name];
	 					$value = str_replace("%", "",$value);
	 					$value = str_replace("$", "",$value);
	 					$value = str_replace("#", "",$value);
	 					$value = trim($value);
	 					

	 					$data[$region_name_kml] = array('name'=>$region_name_xls, 'value'=>$value);
	 					
	 					
	 					//todo need a better way to know what's been ignored, both for the purpose
	 					//of showing ignored things in the UI to the user, and so we don't have to check for empty.	 					
	 					
	 				}
	 				
	 				
	 				//TODO respond appropriately if data is not a number
	 				$indicator_array_ptr['data'] = $data;
	 				
	 				//checking for optional columns
	 				if($unit_column->loaded())
	 				{
	 					$indicator_array_ptr['unit'] = $sheet[$data_row->name][$unit_column->name];
	 				}
	 				else
	 				{
	 					$indicator_array_ptr['unit'] = "";
	 				}
	 				
	 				if($total_column->loaded())
	 				{
		 				$indicator_array_ptr['total'] = $sheet[$data_row->name][$total_column->name];
		 				$indicator_array_ptr['total'] = str_replace("%", "",$indicator_array_ptr['total']);
		 				$indicator_array_ptr['total'] = str_replace("$", "",$indicator_array_ptr['total']);
		 				$indicator_array_ptr['total'] = str_replace("#", "",$indicator_array_ptr['total']);
	 				}
	 				//js code checks if this is undefined
	 				
	 				if($src_column->loaded())
	 				{
	 					$indicator_array_ptr['src'] = $sheet[$data_row->name][$src_column->name];
	 				}
	 				else 
	 				{
	 					$indicator_array_ptr['src'] = "";	
	 				}
	 					 				
	 				if($src_link_column->loaded())
	 				{
	 					$indicator_array_ptr['src_link'] = $sheet[$data_row->name][$src_link_column->name];
	 				}
	 				else
	 				{
	 					$indicator_array_ptr['src_link'] = "";
	 				}
	 				
	 				
	 				if($total_label_column->loaded())
	 				{
	 					$indicator_array_ptr['total_label'] = $sheet[$data_row->name][$total_label_column->name];
	 				}
	 				
	 			}
	 			
	 			
	 			set_time_limit(30);
	 		}
	 	}
	 	return $indicators;
	 	
	 }//end function
	
	 
	 /**
	  * Checks to see if the user has exceeded thier
	  * maximum number of maps and if so
	  * sends them to an error page
	  * @return boolean, true if a new page is allowed
	  */
	 protected function check_max_items()
	 {
	 	if($this->user_max_items == -1) //they can add whatever they want
	 	{
	 		return true;
	 	}
	 	
	 	//figure out how many maps you the current user has
	 	$map_count = ORM::factory('Map')
	 		->where('user_id','=',$this->user->id)
	 		->count_all();
	 	if($map_count >= $this->user_max_items)
	 	{
	 		$this->template->header->menu_page = "mymaps";
	 		$this->template->content = new View('mymaps/exceeded_limit');
	 		$this->template->content->user_max_items = $this->user_max_items;
	 		$this->template->content->current_items = $map_count;
	 		return false;
	 	}
	 	
	 	return true;
	 	
	 }

	 /**
	  * Creates a copy of a given map
	  * $_GET['id'] int - ID of the map to copy
	  */
	 public function action_copy()
	 {
	 	
	 	
	 	//this is a new map, check that the user is allowed to have more maps
	 	if(!$this->check_max_items())
	 	{
	 		return;
	 	}
	 	
	 	//make sure there's an id
	 	$map_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	 	//something went wrong, kick them back home
	 	if($map_id == 0)
	 	{
	 		HTTP::redirect('mymaps');
	 	}
	 	$map = ORM::factory('Map',$map_id);
	 	//they aren't allowed to be here, or this isn't a valid id
	 	if(!$map->loaded() OR $map->user_id != $this->user->id)
	 	{
	 		$edit = ORM::factory('Sharing')->
	 		where('map_id', '=', $map->id)->
	 		where('user_id', '=', $this->user->id)->
	 		find();
	 		if(!$edit->loaded()){
	 			HTTP::redirect('mymaps');
	 		}
	 	}
	 	
	 	$new_map = $map->copy($this->user->id);
	 	
	 	HTTP::redirect('mymaps/add1?id='.$new_map->id);
	 }
	 
	 
	 /**
	  * Checks if a give user can access a give map. True if they can, if they can't
	  * then they get booted to /mymaps
	  * @param int $map_id DB ID of a map
	  * @param int $user_id DB ID of a user
	  * @return boolean if can't edit the map
	  */
	 protected function check_map_permissions($map_id, $user_id)
	 {
	 	$share = Model_Sharing::get_share($map_id, $user_id);
	 		
	 	//no record of this map belonging to this user
	 	if($share === false)
	 	{
	 		HTTP::redirect('mymaps');
	 	}
	 		
	 	//This user can't edit this map
	 	if($share->permission == Model_Sharing::$owner OR $share->permission == Model_Sharing::$edit )
	 	{	 		
	 		return true;
	 	}
	 	
	 	HTTP::redirect('mymaps');
	 	
	 }
	 
	 
	
}//end of class


class Cannot_Access_Template_Exception extends Exception
{


}