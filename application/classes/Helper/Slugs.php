<?php defined('SYSPATH') or die('No direct access allowed.');
/***********************************************************
* Excel.php - Helper
* Used to open Excel files for speady reading
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/



class Helper_Slug
{

	/**
	 * Used to open files for reading data only. This reduces memory usage,
	 * but means we can't write or read formatting
	 * @param string $file_name fully qualified path to the Excel file to be read
	 * @return PHPExcel object for the Excel file
	 */
	public static function check_slug($slug, $db_obj)
	{
		$this->auto_render = false;
		$this->response->headers('Content-Type','application/json');
		
		if(!isset($_POST['slug'])){
			echo '{}';
			exit;
		}
		 
		 
		$slug = $_POST['slug'];
		$slug_original = $slug;
		 
		$slug = Model_Map::clean_slug($slug);
		
		if($slug == $db_obj->slug){
			echo '{"status": "true", "slug" :'.$slug.'"}';
			exit;
		}
		 
		//also check if the slug is a controller name
		$controllers_array =  Kohana::$config->load('config')->get('controllers');
		foreach($controllers_array as $controller)
		{
			if(strtolower($slug) == strtolower($controller))
			{
				echo '{"status": "notUnique"}';
				exit;
			}
		}
		 
		$slug_ids = ORM::factory('Map')->
		where('slug', '=', $slug)->
		find_all();
		
		if(count($slug_ids) > 0){
			echo '{"status": "notUnique"}';
			exit;
		}
		 	
		$slug_ids = ORM::factory('Custompage')->
		where('slug', '=', $slug)->
		find_all();
		
		if(count($slug_ids) > 0){
			echo '{"status": "notUnique"}';
			exit;
		}
		 
		//return the json specifying if the slug is legal
		if(strlen($slug_original) != strlen($slug)){
			echo '{"status":"false", "slug":"'.$slug.'"}';
			exit;
		}
		else{
			echo '{"status":"true", "slug":"'.$slug.'"}';
			exit;
		}	
		
	}
}//end class
