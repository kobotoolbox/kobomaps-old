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
				'template_id','json_file', 'is_private', 'private_password');
	
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
		unlink($directory.$map->file);
		unlink($directory.$map->json_file);
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

	
} // End User Model