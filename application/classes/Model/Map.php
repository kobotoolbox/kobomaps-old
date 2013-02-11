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
						array('max_length', array(':value', 254)),
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
				'file' => array(
						array('not_empty'),
						array('max_length', array(':value', 254)),
						array('min_length', array(':value', 1))
				),
				'json_file' => array(
						array('not_empty'),
						array('max_length', array(':value', 254)),
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
	
		$expected = array('title', 'description', 'user_id', 'file', 'map_style', 'CSS', 'lat', 'lon', 'zoom', 
				'template_id','json_file', 'is_private', 'private_password', 'map_creation_progress', 
				'show_empty_name', 'label_zoom_level', 'region_label_font', 'value_label_font');
	
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
		$copy_array['title'] = $copy_array['title'] .'('.__('Copy').')';
		$new_map->update_map($copy_array);
		
		//copy the map files
		$path = DOCROOT.'uploads/data/'; 
		
		//copy the json file
		$new_json = $new_map->id.'.json';
		copy($path.$this->json_file,$path.$new_json);
		$new_map->json_file = $new_json;
		
		//copy the .xls file
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
		
		return $new_map;
	}
	
} // End User Model