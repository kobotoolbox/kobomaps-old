<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Template extends ORM {

	
	
	/**
	 * Set the name of the table
	 */
	protected  $_table_name = 'templates';
	
	/**
	 * A user has many tokens and roles
	 *
	 * @var array Relationhips
	 */
	protected $_has_many =  array(
			'regions' => array('model' => 'template_region'),
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
				'description' => array(
						array('max_length', array(':value', 65533)),
						array('min_length', array(':value', 1))
				),
				'file' => array(
						array('not_empty'),
						array('max_length', array(':value', 254)),
						array('min_length', array(':value', 1))
				),
				
				'admin_level' => array(array('not_empty'),),
				
				'decimals' => array(array('not_empty'),),				
				'lat' => array(array('not_empty'),),
				'lon' => array(array('not_empty'),),
				'zoom' => array(array('not_empty'),),
		);
	}//end function
	
	
	/**
	 * Update an existing template
	 *
	 * Example usage:
	 * ~~~
	 * $form = ORM::factory('Template', $id)->update_template($_POST);
	 * ~~~
	 *
	 * @param array $values
	 * @throws ORM_Validation_Exception
	 */
	public function update_template($values)
	{
	
		$expected = array('title', 'description', 'admin_level', 'file', 'decimals', 'lat', 'lon', 'zoom', 'user_id', 'is_official');
	
		$this->values($values, $expected);
		$this->check();
		$this->save();
	}//end function

	
	/**
	 * Delete a template
	 */
	public static function delete_template($id)
	{
		//get the template in question
		$template = ORM::factory('Template', $id);
		if(file_exists(DOCROOT.'uploads/templates/'.$template->file))
		{
			unlink(DOCROOT.'uploads/templates/'.$template->file);
		}
		if(file_exists(DOCROOT.'uploads/templates/'.$template->kml_file) AND strlen($template->kml_file) > 0)
		{
			unlink(DOCROOT.'uploads/templates/'.$template->kml_file);
		}
		$template->delete();
	}

	
} // End User Model