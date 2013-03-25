<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Mapsheet extends ORM {

	
	/**
	 * Set the name of the table
	 */
	protected  $_table_name = 'mapsheets';
	
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
				'map_id' => array(
						array('not_empty'),					
				),
				'position' => array(
						array('not_empty'),
				),
				
		);
	}//end function
	
	
	
	/**
	 * Make a full on copy of a map sheet for a map
	 * @param int $map_id - The DB ID of the map who will own the copied sheet
	 * @return ORM::Mapsheet that has been copied
	 */
	public function copy($map_id)
	{
		//copy the map database entry.
		$copy = ORM::factory('Mapsheet');

		$copy->map_id = $map_id;
		$copy->name = $this->name;
		$copy->position = $this->position;
		$copy->is_ignored = $this->is_ignored;
		$copy->save();
	
		//copy the rows
		$rows = ORM::factory('Row')
			->where('mapsheet_id', '=',$this->id)
			->find_all();
		foreach($rows as $row)
		{
			$row->copy($copy->id);
		}
		
		//copy the columns
		$columns = ORM::factory('Column')
			->where('mapsheet_id', '=',$this->id)
			->find_all();
		foreach($columns as $column)
		{
			$column->copy($copy->id);
		}
	
		return $copy;
	}
	
} // End mapsheet Model