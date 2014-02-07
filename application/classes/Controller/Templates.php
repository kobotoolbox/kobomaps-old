<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Templates.php - Controller
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/

class Controller_Templates extends Controller_Loggedin {

	/** Used to store the regions that are lost when uploading a new KML file**/
	protected $deleted_regions = null;
	
	
	/**
	 Set stuff up, mainly just check if the user is an admin or not
	 */
	public function before()
	{
		parent::before();
		$this->is_admin = false;
		
	}


  	
	/**
	where users go to add templates
	*/
	public function action_index()
	{

		/***** initialize stuff****/
		//The title to show on the browser
		$this->template->html_head->title = __("Templates");
		//make messages roll up when done
		$this->template->html_head->messages_roll_up = true;
		//the name in the menu
		$this->template->header->menu_page = "templates";
		$this->template->content = view::factory("templates/templates");
		$this->template->content->errors = array();
		$this->template->content->messages = array();
		//set the JS
		$js = view::factory('templates/templates_js');
		$this->template->html_head->script_views[] = $js;
		$this->template->html_head->script_views[] = view::factory('js/messages');
		$this->template->html_head->script_files[] = 'media/js/jquery-ui.min.js';
		$this->template->html_head->styles['all'] = 'media/css/jquery-ui.css';
		$this->template->content->is_admin = $this->is_admin;
		
		/********Check if we're supposed to do something ******/
		if(!empty($_POST)) // They've submitted the form to update his/her wish
		{
			try
			{	
				if($_POST['action'] == 'delete')
				{
					//make make sure this user has the rights to do this
					$template = ORM::factory('Template',$_POST['template_id']);
					if($this->is_admin OR $template->user_id = $this->user->id)
					{
						//check for maps that use this template
						$maps_use_template = ORM::factory('Map')
							->where('template_id','=',$template->id)
							->find_all();
						Model_Template::delete_template($_POST['template_id']);
						$this->template->content->messages[] = __('Template Deleted') . ' - ' . $template->title;
						//create errors for maps that have lost their template
						foreach($maps_use_template as $m)
						{
							$this->template->content->errors[] = '<a href="'.URL::base().'mymaps/add4?id='.$m->id.'">'.$m->title.'</a> '.__('is now missing its template.');
							//create messages for the owners of maps that lost their templates
							$message = __('The template your map used has been deleted. Please fix', array(':title'=>$m->title, ':id'=>$m->id));
							$share = ORM::factory('Sharing')
								->where('map_id','=',$m->id)
								->where('permission','=', Model_Sharing::$owner)
								->find();
							Model_Message::add_alert($share->user_id, $message, __('KoboMaps System'), 'noreply@kobomaps.org');
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
		
		//if you're an admin and you can do whatever you want then you see all templates
		$templates = ORM::factory("Template")
			->select('users.username')
			->join('users')
			->on('users.id','=','template.user_id');
		//if there is a search term
		if(isset($_GET['q']) AND $_GET['q'] != "")
		{
			$query = '%'.$_GET['q'].'%';
			$templates = $templates
				->where_open()
				->or_where('template.title', 'LIKE', $query)
				->or_where('template.description', 'LIKE', $query)
				->where_close();
		}
		//if we only want to see my templates
		if(Request::initial()->action() == 'mine')
		{
			$templates = $templates->where('user_id','=', $this->user->id);
		}
		else
		{
			if($this->is_admin)
			{							
			}
			else //you're a regular user and can only see your own templates
			{
				$templates = $templates->where_open()
					->where_open()
		 			->where('is_official','=',1)
		 			->where('is_private','=', '0')
		 			->where_close()
		 			->or_where('user_id','=', $this->user->id)
		 			->or_where('is_private','=', '0')
					->where_close();
			}
		}
		$templates = $templates->order_by('title', 'ASC')
			->find_all();
		
		$this->template->content->user = $this->user;
		$this->template->content->is_admin = $this->is_admin;
		$this->template->content->templates = $templates;
		
		
	}//end action_index
	
	
	/**
	 * Shows just the user's own templates.
	 */
	public function action_mine()
	{
		$this->action_index();
	}
	
	
	/**
	 * the function for editing a Template
	 * super exciting
	 */
	 public function action_edit()
	 {
		//initialize data
		$data = array(
			'id'=>'0',
			'title'=>'',
			'description'=>'',
			'is_private'=>0,
			'is_official'=>0,
			'file'=>'',
			'admin_level'=>0,
			'decimals'=>-1,
			'lat'=>'',
			'lon'=>'',
			'zoom'=>4,
			'regions'=>array(),
			'kml_file'=>'',
			'marker_coordinates' => ''
		);
		
		$template = null;
		
		//check if there's a id
		if(isset($_GET['id']) AND intval($_GET['id']) != 0)
		{
			$template = ORM::factory('Template', $_GET['id']);		
		}
		else
		{
			//it's a new template
			if(!$this->check_max_items())
		 	{
		 		return;
		 	}
		}
		
		//make sure the user is allowed to look at this template
		if($template != null AND $template->user_id != $this->user->id AND !$this->is_admin)
		{
			HTTP::redirect('templates');
		}
		
		$map_count = -1;
		if($template != null)
		{
			$map_count = ORM::factory('Map')
				->where('template_id','=',$template->id)
				->count_all();
		}
		 
		
		/***Now that we have the form, lets initialize the UI***/
		//The title to show on the browser
		
		$header =  $data['id'] == 0 ? __("Add a Template") : __("Edit Template") ;
		$this->template->html_head->title = $header;		
		//make messages roll up when done
		$this->template->html_head->messages_roll_up = true;
		//the name in the menu
		$this->template->header->menu_page = "templates";
		$this->template->content = view::factory("templates/template_add");
		$this->template->content->map_count = $map_count;
		$this->template->content->errors = array();
		$this->template->content->messages = array();
		$this->template->content->header = $header;		
		$this->template->content->is_admin = $this->is_admin;
		$this->template->html_head->script_views[] = view::factory('js/messages');
		$js = view::factory('templates/template_add_js');
				
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
				
				
				$first_time = false; //used to know if we should blow away everything if there's an error
				$utferror = false; //small error with utf not catching and telling
				$ormerror = false; //just in case it doesn't catch these errors either
				//Should we use the old file
				if($_FILES['file']['size'] == '0' AND $template != null)
				{
					$_POST['file'] = $template->file;
				}
				else
				{
					$_POST['file'] = $_FILES['file']['name'];
				}
				//should we make a new template?
				if($template == null)
				{				
					$template = ORM::factory('Template');
					$first_time = true;
				}
				else
				{
					//we shouldn't make a new template, but we should update regions
					foreach($_POST['regions'] as $r_id => $r_title)
					{
						$region = ORM::factory('Templateregion', $r_id);
						$region->title = $r_title;
						$region->save();
					}
				}
				//only set the user when the template is edited for the first time
				if($first_time)
				{
					$_POST['user_id'] = $this->user->id;
				}
				else
				{
					$_POST['user_id'] = $template->user_id;
				}
				//if they aren't an admin, then they can't set officialness
				if(!$this->is_admin)
				{
					$_POST['is_official'] = 0;
				}
				else if(!isset($_POST['is_official']))
				{
					$_POST['is_official'] = 0;
				}
				else
				{
					$_POST['is_official'] = 1;
				}
				$template->update_template($_POST);
				
			
				if($_FILES['file']['size'] != '0')
				{
					
					//handle the kml file
					$filename = $this->_save_file($_FILES['file'], $template);
					if(is_array($filename))
					{					
						if($first_time)
						{
							Model_Template::delete_template($template->id);
						}
						throw new UTF_Character_Exception($filename['error']);
					}
					
					$template->file = $filename;					
				}
				else //we're editing an existing template and not changing the base file
				{
					$filename = $this->_save_file(null, $template);
				}
				if($_POST['edit'] != 'Add'){
					if($_POST['markerBool'] == 'true'){
						$template->marker_coordinates = $_POST['markers'];
					}
				}
				$template->save();
				$this->template->content->messages[] = __('Template Saved');	
				//check if we deleted any regions
				if($this->deleted_regions != null)
				{
					foreach($this->deleted_regions as $dr)
					{
						//if we did delete a region list the maps this affected
						$maps_affected = ORM::factory('Map')
							->join('mapsheets')
							->on('mapsheets.map_id','=','map.id')
							->join('columns')
							->on('columns.mapsheet_id','=','mapsheets.id')
							->where('columns.template_region_id','=',$dr->id)
							->group_by('map.id')
							->find_all();
						foreach($maps_affected as $ma)
						{
							$this->template->content->errors[] = '<a href="'.URL::base().'mymaps/add5?id='.
								$ma->id.'">'.$ma->title.'</a> '.__('is now missing the region'). ' '. $dr->title;
						}
						 
					}
				}
				
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors_temp = $e->errors('register');
				$ormerror = true;
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
				$data['id'] =  $_POST['id'];
				$data['title'] =  $_POST['title'];
				$data['description'] =  $_POST['description'];
				$data['admin_level'] =  $_POST['admin_level'];
				$data['decimals'] =  $_POST['decimals'];
				$data['zoom'] =  $_POST['zoom'];
				$data['lat'] =  $_POST['lat'];
				$data['lon'] =  $_POST['lon'];
				$data['marker_coordinates'] = $template->marker_coordinates;
			}
			catch (UTF_Character_Exception $e)
			{
				$this->template->content->errors[] = $e->getMessage();
				$utferror = TRUE;
				$data['id'] =  $_POST['id'];
				$data['title'] =  $_POST['title'];
				$data['description'] =  $_POST['description'];
				$data['admin_level'] =  $_POST['admin_level'];
				$data['decimals'] =  $_POST['decimals'];
				$data['zoom'] =  $_POST['zoom'];
				$data['lat'] =  $_POST['lat'];
				$data['lon'] =  $_POST['lon'];
				$data['marker_coordinates'] = $template->marker_coordinates;
			}
			//if we just created a new template, go to that page
			//checks if errors occured, this was overwritting the errors before
			if(!isset($_GET['id']) AND !$utferror AND !$ormerror)
			{
				HTTP::redirect('templates/edit?id='.$template->id);				
			}
		}
		if(isset($_GET['id']) AND intval($_GET['id']) != 0)
		{
			$data['id'] =  $template->id;
			$data['title'] =  $template->title;
			$data['description'] =  $template->description;
			$data['is_official'] = $template->is_official;
			$data['is_private'] = $template->is_private;
			$data['file'] =  $template->file;
			$data['admin_level'] =  $template->admin_level;
			$data['decimals'] =  $template->decimals;
			$data['zoom'] =  $template->zoom;
			$data['lat'] =  $template->lat;
			$data['lon'] =  $template->lon;
			$data['kml_file'] = $template->kml_file;
			$data['marker_coordinates'] = $template->marker_coordinates;
			
			$regions = ORM::factory('Templateregion')
				->where('template_id', '=', $template->id)
				->find_all();
			foreach($regions as $r)
			{
				$data['regions'][$r->id] = $r->title;
			}
		}
		$this->template->content->data = $data;
		$js->template = $template;			
		$this->template->html_head->script_views[] = $js;
	 }//end action_edit
	 
	 
	 
	 
	 /**
	  * the function to view a template
	  * super exciting
	  */
	 public function action_view()
	 {
	 	//initialize data
	 	$data = array(
	 			'id'=>'0',
	 			'title'=>'',
	 			'description'=>'',
	 			'is_private'=>0,
	 			'is_official'=>0,
	 			'file'=>'',
	 			'admin_level'=>0,
	 			'decimals'=>-1,
	 			'lat'=>'',
	 			'lon'=>'',
	 			'zoom'=>4,
	 			'regions'=>array(),
	 			'kml_file'=>'');
	 
	 	$template = null;
	 
	 	//check if there's a id
	 	if(isset($_GET['id']) AND intval($_GET['id']) != 0)
	 	{
	 		$template = ORM::factory('Template', $_GET['id']);
	 	}
	 	else //get out of here
	 	{
	 		HTTP::redirect('templates');
	 	}
	 
	 	//make sure the user is allowed to look at this template
	 	if($template != null AND $template->is_private == 1 AND !$this->is_admin)
	 	{
	 		HTTP::redirect('templates');
	 	}
	 
	 	$map_count = -1;
	 	if($template != null)
	 	{
	 		$map_count = ORM::factory('Map')
	 		->where('template_id','=',$template->id)
	 		->count_all();
	 	}
	 		
	 
	 	/***Now that we have the form, lets initialize the UI***/
	 	//The title to show on the browser
	 
	 	$header =  $data['id'] == 0 ? __("Add a Template") : __("Edit Template") ;
	 	$this->template->html_head->title = $header;
	 	//make messages roll up when done
	 	$this->template->html_head->messages_roll_up = true;
	 	//the name in the menu
	 	$this->template->header->menu_page = "templates";
	 	$this->template->content = view::factory("templates/template_view");
	 	$this->template->content->map_count = $map_count;
	 	$this->template->content->errors = array();
	 	$this->template->content->messages = array();
	 	$this->template->content->header = $header;
	 	$this->template->content->is_admin = $this->is_admin;
	 	$this->template->html_head->script_views[] = view::factory('js/messages');
	 	$js = view::factory('templates/template_add_js');
	 
	 	//get the status
	 	$status = isset($_GET['status']) ? $_GET['status'] : null;
	 	if($status == 'saved')
	 	{
	 		$this->template->content->messages[] = __('changes saved');
	 	}
	 

 		$data['id'] =  $template->id;
 		$data['title'] =  $template->title;
 		$data['description'] =  $template->description;
 		$data['is_official'] = $template->is_official;
 		$data['is_private'] = $template->is_private;
 		$data['file'] =  $template->file;
 		$data['admin_level'] =  $template->admin_level;
 		$data['decimals'] =  $template->decimals;
 		$data['zoom'] =  $template->zoom;
 		$data['lat'] =  $template->lat;
 		$data['lon'] =  $template->lon;
 		$data['kml_file'] = $template->kml_file;
 			
 		$regions = ORM::factory('Templateregion')
	 		->where('template_id', '=', $template->id)
 			->find_all();
 		foreach($regions as $r)
 		{
 			$data['regions'][$r->id] = $r->title;
 		}
 		
	 	$this->template->content->data = $data;
	 	$js->template = $template;
	 	$this->template->html_head->script_views[] = $js;
	 }//end action_view
	 
	 
	 
	 /**
	  * This function copies an existing template
	  * The template to copy is referenced via $_GET['id']
	  */
	 public function action_copy()
	 {
	 
	 	$template = null;
	 
	 	//check if there's a id
	 	if(isset($_GET['id']) AND intval($_GET['id']) != 0)
	 	{
	 		$template = ORM::factory('Template', $_GET['id']);
	 	}
	 	else //get out of here
	 	{
	 		HTTP::redirect('templates');
	 	}
	 
	 	//make sure the user is allowed to look at this template
	 	if($template != null AND ($template->is_private == 1 AND $template->user_id != $this->user->id) AND !$this->is_admin)
	 	{
	 		HTTP::redirect('templates');
	 	}
	 	
	 	//make sure the user hasn't exceed their template limit
	 	if(!$this->check_max_items())
		{
			return;
		}
	 
		$new_template = $template->copy($this->user->id);
		
		HTTP::redirect('templates/edit?id='.$new_template->id);
	 }//end action_view
	 

	 
	 
	 /**
	  * Used to make the auto complete work on the templates page
	  * expects there to be $_GET['term'] of type String
	  * there could also be $_GET['mine']
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
	 
	 	//if you're an admin and you can do whatever you want then you see all templates
		$templates = ORM::factory("Template");		
		//if there is a search term
		if(isset($_GET['term']) AND $_GET['term'] != "")
		{
			$query = '%'.$_GET['term'].'%';
			$templates = $templates
				->where_open()
				->or_where('template.title', 'LIKE', $query)
				->or_where('template.description', 'LIKE', $query)
				->where_close();
		}
		//if we only want to see my templates
		if(isset($_GET['mine']))
		{
			$templates = $templates->where('user_id','=', $this->user->id);
		}
		else
		{
			if(!$this->is_admin) //you're a regular user and can only see your own templates
			{
				$templates = $templates->where_open()
					->where_open()
		 			->where('is_official','=',1)
		 			->where('is_private','=', '0')
		 			->where_close()
		 			->or_where('user_id','=', $this->user->id)
		 			->or_where('is_private','=', '0')
					->where_close();
			}
		}
		$templates = $templates
			->order_by('title', 'ASC')
			->limit(10,0)
			->find_all();
	 
	 	echo '[';
	 	$i = 0;
	 	foreach($templates as $template)
	 	{
	 		$i++;
	 		if($i > 1){
	 			echo ',';
	 		}
	 		$title_encoded = json_encode($template->title);
	 		echo '{"id":"'.$template->id.'","label":'.$title_encoded.',"value":'.$title_encoded.'}';
	 	}
	 	echo ']';
	 
	 }
	 
	 
	 /**
	  * helper function that stores the uploaded file
	  * @param array $upload_file data from $_FILES
	  * @param db_obj $template ORM object of the template that this file is going to be saved for
	  */
	 protected function _save_file($upload_file, $template)
	 {
	 	//if we're working with a file that's already been uploaded.
	 	//Happens when a user is editing an existing template
	 	if($upload_file == null AND $template->kml_file != null)
	 	{
	 		$filename = $template->kml_file;
	 		$kml_converter = new Helper_Kml2json();
	 		$json_file = $kml_converter->convert($filename, $template);
	 		$this->deleted_regions = $kml_converter->deleted_regions;	 		
	 		return $json_file;
	 	}
	 	//Now deal with the case whe we're creating a new template and just uploaded a file
	 	else
	 	{	 		
		 	if (! Upload::valid($upload_file) OR ! Upload::not_empty($upload_file))
		 	{
		 		return array('error'=>__('uploaded file is not valid'));
		 	}
		 	if(! Upload::type($upload_file, array('kml', 'kmz')))
		 	{
		 		return array('error'=>__('This is not a .kml or .kmz file'));
		 	}
		 
		 
		 	$directory = DOCROOT.'uploads/templates/';
		 
		 	$extention = $this->get_file_extension($upload_file['name']);
		 	$filename = $template->id.'.'.$extention;
		 	$template->kml_file = $filename;
		 	if ($file = Upload::save($upload_file, $filename, $directory))
		 	{	 			 
		 		$kml_converter = new Helper_Kml2json();
		 		$json_file = $kml_converter->convert($filename, $template);
		 		$this->deleted_regions = $kml_converter->deleted_regions;
		 		return $json_file;
		 	}
		 
		 	return array('error'=>__('Something has gone wrong processing your template map file'));
	 	}
	 }
	 
	 function get_file_extension($file_name) {
	 	return substr(strrchr($file_name,'.'),1);
	 }
	 
	 
	 /**
	  * Checks to see if the user has exceeded thier
	  * maximum number of maps and if so
	  * sends them to an error page
	  * @return boolean if user can add another map
	  */
	 protected function check_max_items()
	 {
	 	if($this->user_max_items == -1) //they can add whatever they want
	 	{
	 		return true;
	 	}
	 		
	 	//figure out how many maps you the current user has
	 	$map_count = ORM::factory('Template')
	 	->where('user_id','=',$this->user->id)
	 	->count_all();
	 	if($map_count >= $this->user_max_items)
	 	{
	 		$this->template->header->menu_page = "templates";
	 		$this->template->content = new View('templates/exceeded_limit');
	 		$this->template->content->user_max_items = $this->user_max_items;
	 		$this->template->content->current_items = $map_count;
	 		return false;
	 	}
	 		
	 	return true;
	 		
	 }
	
	
}//end of class



class UTF_Character_Exception extends Exception
{
	

}