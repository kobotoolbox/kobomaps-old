<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Regionmapping extends ORM {

	
	
	/**
	 * Set the name of the table
	 */
	protected  $_table_name = 'regionmapping';
	
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
				'column_id' => array(
						array('not_empty'),
				),
			
				'template_region_id' => array(array('not_empty'),),				
		);
	}//end function
	
	
	/**
	 * Update an existing template
	 *
	 * Example usage:
	 * ~~~
	 * $form = ORM::factory('Regionmapping', $id)->update_regionmapping($_POST);
	 * ~~~
	 *
	 * @param array $values
	 * @throws ORM_Validation_Exception
	 */
	public function update_regionmapping($values)
	{
	
		$expected = array('column_id', 'template_region_id');
	
		$this->values($values, $expected);
		$this->check();
		$this->save();
	}//end function

} // End User Model