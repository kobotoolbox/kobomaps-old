<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Row extends ORM {

	
	/**
	 * Set the name of the table
	 */
	protected  $_table_name = 'rowss';
	
	/**
	 * A user has many tokens and roles
	 *
	 * @var array Relationhips
	 */
	protected $_has_many =  array(
	
	);
	
	protected $_has_one = array(
		
	);

	
	/**
	 * Rules function
	 * @see Kohana_ORM::rules()
	 */
	public function rules()
	{
		return array(
				'name' => array(
						array('not_empty'),
						array('max_length', array(':value', 254)),
						array('min_length', array(':value', 1))
				),
				'mapsheet_id' => array(
						array('not_empty'),					
				),
				'type' => array(
						array('not_empty'),
						array('max_length', array(':value', 254)),
						array('min_length', array(':value', 1))
				),
				
		);
	}//end function
	
	

	
} // End columns Model