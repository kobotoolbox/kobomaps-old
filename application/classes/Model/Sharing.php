<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Sharing extends ORM {

	
	public static $allowed_permissions;
	
	
	
		
	
	/**
	 * Set the name of the table
	 */
	protected  $_table_name = 'sharing';
	
	/**
	 * A user has many tokens and roles
	 *
	 * @var array Relationhips
	 */
	protected $_has_many =  array(
	
	);
	
	protected $_has_one = array(
		'user' => array('model' => 'user'),
		'map' => array('model' => 'map'),
	);

	
	/**
	 * Rules function
	 * @see Kohana_ORM::rules()
	 */
	public function rules()
	{
		return array(
				'user_id' => array(
						array('not_empty'),
				),
				'map_id' => array(
						array('not_empty'),
				),
				'permission' => array(
						array('not_empty'),
						array('max_length', array(':value', 254)),
						array('min_length', array(':value', 1))
				),
			);
	}//end function
	
	
	/**
	 * Update an existing template
	 *
	 * Example usage:
	 * ~~~
	 * $form = ORM::factory('Sharing', $id)->update_sharing($_POST);
	 * ~~~
	 *
	 * @param array $values
	 * @throws ORM_Validation_Exception
	 */
	public function update_sharing($values)
	{
	
		$expected = array('user_id', 'map_id', 'permission');
	
		$this->values($values, $expected);
		$this->check();
		$this->save();
	}//end function

	
	

	
} // End User Model

Model_Sharing::$allowed_permissions = array('view'=>'view','edit'=>'edit');