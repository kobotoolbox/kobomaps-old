<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Column extends ORM {

	
	/**
	 * Set the name of the table
	 */
	protected  $_table_name = 'columns';
	
	
	
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
	 * Make a full on copy of a column for a map sheet
	 * @param int $mapsheet_id - The DB ID of the map sheet who will own the copied column
	 */
	public function copy($mapsheet_id)
	{
		//copy the map database entry.
		$copy = ORM::factory('Column');
	
		$copy->mapsheet_id = $mapsheet_id;
		$copy->name = $this->name;
		$copy->type = $this->type;
		$copy->template_region_id = $this->template_region_id;
		$copy->save();
	
		return $copy;
	}
	
} // End columns Model