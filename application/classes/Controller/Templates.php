<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Templates.php - Controller
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/

class Controller_Templates extends Controller_Loggedin {


  	
	/**
	where users go to change their profiel
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
		
		/********Check if we're supposed to do something ******/
		if(!empty($_POST)) // They've submitted the form to update his/her wish
		{
			try
			{	
				if($_POST['action'] == 'delete')
				{
					Model_Template::delete_template($_POST['template_id']);
					$this->template->content->messages[] = __('Template Deleted');
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
		
		//get the forms that belong to this user
		$maps = ORM::factory("Template")
			->order_by('title', 'ASC')
			->find_all();
		
		$this->template->content->maps = $maps;
		
		
	}//end action_index
	
	
	
	/**
	 * the function for editing a form
	 * super exciting
	 */
	 public function action_edit()
	 {
		//initialize data
		$data = array(
			'id'=>'0',
			'title'=>'',
			'description'=>'',
			'file'=>'',
			'admin_level'=>0,
			'decimals'=>-1,
			'lat'=>'',
			'lon'=>'',
			'zoom'=>4,
			'regions'=>array());
		
		$template = null;
		
		//check if there's a id
		if(isset($_GET['id']) AND intval($_GET['id']) != 0)
		{
			$template = ORM::factory('Template', $_GET['id']);		
		}
		
		//TODO write code to hanlde a user editing this once it's been set		
		 
		
		/***Now that we have the form, lets initialize the UI***/
		//The title to show on the browser
		
		$header =  $data['id'] == 0 ? __("Add a Template") : __("Edit Template") ;
		$this->template->html_head->title = $header;		
		//make messages roll up when done
		$this->template->html_head->messages_roll_up = true;
		//the name in the menu
		$this->template->header->menu_page = "templates";
		$this->template->content = view::factory("templates/template_add");
		
		$this->template->content->errors = array();
		$this->template->content->messages = array();
		$this->template->content->header = $header;		
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
				
				$template->update_template($_POST);
				
			
				if($_FILES['file']['size'] != '0')
				{
					
					//handle the kml file
					$filename = $this->_save_file($_FILES['file'], $template);
					$template->file = $filename;					
				}
				else //we're editing an existing template and not changing the base file
				{
					$filename = $this->_save_file(null, $template);
				}
				$template->save();
				
				HTTP::redirect('/templates?status=saved');				
				
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
		if(isset($_GET['id']) AND intval($_GET['id']) != 0)
		{
			$data['id'] =  $template->id;
			$data['title'] =  $template->title;
			$data['description'] =  $template->description;
			$data['file'] =  $template->file;
			$data['admin_level'] =  $template->admin_level;
			$data['decimals'] =  $template->decimals;
			$data['zoom'] =  $template->zoom;
			$data['lat'] =  $template->lat;
			$data['lon'] =  $template->lon;
			
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
	 }//end action_add1
	 
	 

	 protected function _save_file($upload_file, $template)
	 {
	 	//if we're working with a file that's already been uploaded.
	 	//Happens when a user is editing an existing template
	 	if($upload_file == null AND $template->kml_file != null)
	 	{
	 		$filename = $template->kml_file;
	 		$json_file = Helper_Kml2json::convert($filename, $template);
	 		return $json_file;
	 	}
	 	//Now deal with the case whe we're creating a new templae and just uploaded a file
	 	else
	 	{	 		
		 	if (
		 			! Upload::valid($upload_file) OR
		 			! Upload::not_empty($upload_file) OR
		 			! Upload::type($upload_file, array('kml', 'kmz')))
		 	{
		 		return FALSE;
		 	}
		 
		 
		 	$directory = DOCROOT.'uploads/templates/';
		 
		 	$extention = $this->get_file_extension($upload_file['name']);
		 	$filename = $template->id.'.'.$extention;
		 	$template->kml_file = $filename;
		 	if ($file = Upload::save($upload_file, $filename, $directory))
		 	{	 			 
		 		$json_file = Helper_Kml2json::convert($filename, $template);
		 		return $json_file;
		 	}
		 
		 	return FALSE;
	 	}
	 }
	 
	 function get_file_extension($file_name) {
	 	return substr(strrchr($file_name,'.'),1);
	 }
	
	
}//end of class
