<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Map extends ORM {

	
	/**
	 * Set the name of the table
	 */
	protected  $_table_name = 'maps';
	
	/**
	 * A user has many tokens and roles
	 *
	 * @var array Relationhips
	 */
	protected $_has_many =  array(
	
	);
	
	protected $_has_one = array(
		'user' => array('model' => 'user'),
	);

	
	/**
	 * Rules function
	 * @see Kohana_ORM::rules()
	 */
	public function rules()
	{
		return array(
				'title' => array(
						array('not_empty'),
						array('max_length', array(':value', 255)),
						array('min_length', array(':value', 1))
				),
				'lat' => array(
						array('not_empty'),
						array('numeric'),
						array('range', array(':value',-90, 90)),
				),
				'lon' => array(
						array('not_empty'),
						array('numeric'),
						array('range', array(':value',-180, 180)),
				),
				'zoom' => array(
						array('not_empty'),
						array('numeric'),
						array('range', array(':value',-1,24)),
				),
				'description' => array(
						array('max_length', array(':value', 65533)),
						array('min_length', array(':value', 1))
				),
				'slug' => array(
						array('max_length', array(':value', 128)),
						array('min_length', array(':value', 1)),
						array(array($this, 'unique'), array('slug', ':value')),
						array(array('Model_Map', 'slug_no_controller')),
				),
				'file' => array(
						array('not_empty'),
						array('max_length', array(':value', 255)),
						array('min_length', array(':value', 1))
				),
				'json_file' => array(
						array('not_empty'),
						array('max_length', array(':value', 255)),
						array('min_length', array(':value', 1))
				),
				'CSS' => array(
						array('max_length', array(':value', 65533)),
						array('min_length', array(':value', 1))
				),
				'map_style' => array(
						array('max_length', array(':value', 65533)),
						array('min_length', array(':value', 1))
				),
				'user_id' => array(
						array('not_empty'),					
				),
				'template_id' => array(
						array('not_empty'),
				),
				'label_zoom_level' => array(
						array('not_empty'),
						array('numeric'),
						array('range', array(':value',-1,24)),
				),
				'region_label_font' => array(
						array('not_empty'),
						array('numeric'),
						array('range', array(':value',7,100)),
				),
				'value_label_font' => array(
						array('not_empty'),
						array('numeric'),
						array('range', array(':value',7,100)),
				),
		);
	}//end function
	
	
	/**
	 * Update an existing map
	 *
	 * Example usage:
	 * ~~~
	 * $form = ORM::factory('Map', $id)->update_map($_POST);
	 * ~~~
	 *
	 * @param array $values
	 * @throws ORM_Validation_Exception
	 */
	public function update_map($values)
	{
	
		$expected = array('title', 'description', 'slug', 'large_file', 'user_id', 'file', 'map_style', 'CSS', 'lat', 'lon', 'zoom', 
				'template_id','json_file', 'is_private', 'map_creation_progress', 'border_color', 'region_color', 'polygon_color',
				'graph_bar_color', 'graph_select_color', 'gradient', 'id',
				'show_empty_name', 'label_zoom_level', 'region_label_font', 'value_label_font');
		
		//if no slug is set
		if($values['slug'] == '')
		{
			$auth = $auth = Auth::instance();
			
			$hash = substr($auth->hash_password(microtime().$this->id), 0, 32);
			$values['slug'] = $hash;
			$map = ORM::factory('Map')->where('slug','=',$hash)->find();
			
			while($map->loaded()) //keep coming up with a new hash until we find a unique one.
			{
				$hash = substr($auth->hash_password(microtime().$this->id), 0, 32);
				$values['slug'] = $hash;
				$map = ORM::factory('Map')->where('slug','=',$hash)->find();
			}			
		}
		
		//print_r($values);
		//print_r($expected);
		//exit;
		$this->values($values, $expected);
		$this->check();
		$this->save();
	}//end function
	
	
	/**
	 * A helper function to remove maps from the database
	 * @param int $id the id of the map to be deleted
	 */
	public static function delete_map($id)
	{
		$map = ORM::factory('map',$id);
		
		$directory = DOCROOT.'uploads/data/';
		if(file_exists($directory.$map->file) AND !is_dir($directory.$map->file))
		{
			unlink($directory.$map->file);
		}
		if(file_exists($directory.$map->json_file) AND !is_dir($directory.$map->json_file))
		{
			unlink($directory.$map->json_file);
		}
		$map->delete();
	}

	
	public static  $style_default = '[
	  {
		featureType: "administrative.province",
		elementType: "all",
		stylers: [
		  { visibility: "off" }
		]
	  },{
		featureType: "poi",
		elementType: "all",
		stylers: [
		  { visibility: "off" }
		]
	  },{
		featureType: "road",
		elementType: "all",
		stylers: [
		  { visibility: "off" }
		]
	  },{
		featureType: "landscape",
		elementType: "geometry",
		stylers: [
		  { lightness: -60 },
		  { hue: "#91ff00" },
		  { visibility: "on" },
		  { saturation: -60 }
		]
	  },{
		featureType: "administrative.locality",
		elementType: "all",
		stylers: [
		  { saturation: -50 },
		  { invert_lightness: true },
		  { lightness: 52 }
		]
	  }
	]';
	
	
	
	/**
	 * Converts the default map style to a java friendly format
	 */
	public static function get_style_default_js()
	{
		return str_replace("\"", "\\\"", str_replace("\n", "\\n", self::$style_default));	
	}

	
	/**
	 * Make a full on copy of a map for a user
	 * @param int $user_id - The DB ID of the user who will own the copied map
	 */
	public function copy($user_id)
	{
		//copy the map database entry.
		$copy_array = $this->as_array();
		$new_map = ORM::factory('Map');
		$copy_array['user_id'] = $user_id;
		$copy_array['id'] = null;

		
		//these loops check to see if the title and slug contain __('Copy') already, if they do, take them out
		$unique = false;
		$count = 1;
		if(strrpos($copy_array['title'], '('.__('Copy')) !== false){
			$copy_array['title'] = substr($copy_array['title'], 0, strrpos($copy_array['title'], '('.__('Copy')));
		}
		if(strrpos($copy_array['slug'], '_'.__('Copy')) !== false){
			$copy_array['slug'] = substr($copy_array['slug'], 0, strrpos($copy_array['slug'], '_'.__('Copy')));
		}
		$original_title = $copy_array['title'];
		$original_slug = $copy_array['slug'];

		//check the database until a new copy(count) isn't loaded, and then make the map title and slug the title/slug.(copy)($count)
		while(!$unique){
			$checkSlug = ORM::factory('Map')->
			where('slug', '=', $copy_array['slug'])->
			find();
			if(!$checkSlug->loaded()){
				$copy_array['title'] = $original_title.'('.__('Copy').')('.$count.')';
				$copy_array['slug'] = $original_slug.'_'.__('Copy').'('.$count.')';
				$unique = true;
			}
			else{
				$count ++;
				$copy_array['title'] = $original_title.'('.__('Copy').')('.$count.')';
				$copy_array['slug'] = $original_slug.'_'.__('Copy').'('.$count.')';
			}
		}

		
		$new_map->update_map($copy_array);
		
		//copy the map files
		$path = DOCROOT.'uploads/data/'; 
		
		//copy the json file
		$new_json = $new_map->id.'.json';
		if(file_exists($path.$this->json_file))
		{
			copy($path.$this->json_file,$path.$new_json);
		}
		
		
		
		//copy the .xls file
		$new_map->json_file = $new_json;
		$extention = pathinfo($this->file, PATHINFO_EXTENSION);
		$new_xls = $new_map->user_id.'-'.$new_map->id.'.'.$extention;
		copy($path.$this->file, $path.$new_xls);
		$new_map->file = $new_xls;
		
		$new_map->save();
		
		//now copy each of the map sheets
		$map_sheets = ORM::factory('Mapsheet')
			->where('map_id','=',$this->id)
			->find_all();
		foreach($map_sheets as $map_sheet)
		{
			$map_sheet->copy($new_map->id);
		}
		
		$template = ORM::factory('Template')->
		where('id', '=', $copy_array['template_id'])->
		find();
		
		//if the template for this map cannot be used at all, create a copy for it
		if(!$template->is_official){
			$myTemplate = ORM::factory('Template');
			$myTemplate = $template->copy($user_id);
			$copy_array['template_id'] = $myTemplate->id;
		}
		
		//create a new owner entry
		Model_Sharing::create_owner($new_map->id, $user_id);
		
		return $new_map;
	}
	
	/**
	 * Custom function to make sure that the slug isn't a
	 * controller
	 * @param string $value Slug value as a string
	 */
	public static function slug_no_controller($value)
	{
		$temp_val = strtolower($value);
		$controllers_array =  Kohana::$config->load('config')->get('controllers');
		foreach($controllers_array as $controller)
		{
			if($temp_val == strtolower($controller))
			{
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Used to clean up a slug and remove unwanted characters
	 * @param string $slug the uncleaned slug
	 * @return string the clean slug
	 */
	public static function clean_slug($slug)
	{
		//illegal characters in a url
		$illegalChar = array(
				"+" => '+',
				"/" => '/',
				"?" => '?',
				"%" => '%',
				"#" => '#',
				"&" => '&',
				"<" => '<',
				">" => '>',
				'"' => '"',
				"\'" => '\'',
				"@" => '@',
				"\\" => '\\');
		
		//go through the illegal character array and remove any instances of them in the slug
		foreach($illegalChar as $char){
			$pos = strpos($slug, $char);
			if($pos !== false){
				$slug = str_replace($char, '', $slug);
			}
		}
		//replaces spaces with _
		$slug = str_replace(' ', '_', $slug);
		 
		return $slug;
	}
	
} // End User Model