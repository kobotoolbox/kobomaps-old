<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Row extends ORM {

	
	/**
	 * Set the name of the table
	 * in MySQL rows is protected, hence the rowss
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
	
	
	/**
	 * Make a full on copy of a row for a map sheet
	 * @param int $mapsheet_id - The DB ID of the map sheet who will own the copied row
	 */
	public function copy($mapsheet_id)
	{
		//copy the map database entry.
		$copy = ORM::factory('Row');
	
		$copy->mapsheet_id = $mapsheet_id;
		$copy->name = $this->name;
		$copy->type = $this->type;
		$copy->save();
		
		return $copy;
	}
	
	

	
} // End columns Model