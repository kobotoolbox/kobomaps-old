<?php defined('SYSPATH') or die('No direct access allowed.');
/***********************************************************
* Slugs.php - Helper
* Used to parse and check slugs entered and check against all other slugs in database
* This software is copy righted by Kobo 2012
* Writen by Dylan Gillespie <dylan@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-03-04
*************************************************************/



class Helper_Slugs
{

	/**
	 * Used to check if slugs
	 * @param string $slug to be the name of the slug, object $db_obj from which the slug is originating, currently a Map or Custompage
	 * @return JSON string that tells the javascript if the slug is valid or not
	 */
	public static function check_slug($slug, $db_obj)
	{		
		$slug = strtolower($slug);
		if(!isset($slug)){
			echo '{}';
			exit;
		}
		//used to compare the length of the original and end slug
		$slug_original = $slug;
		
		//parses illegal characters out of the slug
		$slug = Model_Map::clean_slug($slug);
		
		if(strlen($slug_original) != strlen($slug)){
			echo '{"status":"illegal", "slug":"'.$slug.'"}';
			exit;
		}
		
		
		//basically if the slug has been renamed to be the same as the original, return true that the slug is valid
		if($slug == $db_obj->slug){
			echo '{"status": "valid", "slug" :"'.$slug.'"}';
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
		
		if($slug == __('about') || $slug == __('help') || $slug == __('support') || $slug == __('main')){
			echo '{"status": "notUnique"}';
			exit;
		}
		 
		//create a map to compare the slug to, if there are any slugs in the map database is the same
		$slug_ids = ORM::factory('Map')->
		where('slug', '=', $slug)->
		find_all();
		
		if(count($slug_ids) > 0){
			echo '{"status": "notUnique"}';
			exit;
		}
		 	
		//create a Custompage to compare the slugs, if there are any similar
		$slug_page = ORM::factory('Custompage')->
		where('slug', '=', $slug)->
		find_all();
		
		if(count($slug_page) > 0){
			echo '{"status": "notUnique"}';
			exit;
		}
		 
		//return the json specifying if the slug is legal
		echo '{"status": "valid", "slug" :"'.$slug.'"}';
		exit;
		
	}
}//end class
