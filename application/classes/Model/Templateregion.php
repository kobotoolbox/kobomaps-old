<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Templateregion extends ORM {

	
	
	/**
	 * Set the name of the table
	 */
	protected  $_table_name = 'template_regions';
	
	/**
	 * A user has many tokens and roles
	 *
	 * @var array Relationhips
	 */
	protected $_has_many =  array(
	
	);
	
	protected $_has_one = array(
		'template' => array('model' => 'template'),
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
			
				'template_id' => array(array('not_empty'),),				
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
	public function update_template_region($values)
	{
	
		$expected = array('title', 'template_id');
	
		$this->values($values, $expected);
		$this->check();
		$this->save();
	}//end function

	
	
	/**
	 * Delete a region
	 */
	public static function delete_region($id)
	{
		//get the template in question
		$region = ORM::factory('Templateregion', $id);
		$region->delete();
	}
	
	/**
	 * Used to make a copy of a template for the given user
	 * @param int $template_id DB ID of the template to copy to
	 * @return db_obj The new template
	 */
	public function copy($template_id)
	{
		//copy the region
		$new_region = ORM::factory('Templateregion');
		$copy_array = $this->as_array();
		$copy_array['template_id'] = $template_id;
		$new_region->original_title = $this->original_title;
		$new_region->update_template_region($copy_array);
	
		return $new_region;
	}
	
	
	
	
	
} // End User Model