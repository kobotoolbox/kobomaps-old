<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Usagestatistics extends ORM {

	
	
	/**
	 * Set the name of the table
	 */
	protected  $_table_name = 'usagestatistics';
	
	/**
	 * A user has many tokens and roles
	 *
	 * @var array Relationhips
	 */
	
	protected $_has_one = array(
		'map_id' => array('model' => 'map'),
	);

	
	/**
	 * Rules function
	 * @see Kohana_ORM::rules()
	 */
	public function rules()
	{
		return array();
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
	public function update_usagestatistics($values)
	{
	
		$expected = array('date', 'map_id', 'visits');
	
		$this->values($values, $expected);
		$this->check();
		$this->save();
	}//end function


	/**
	* increments the visit counter of maps when they are visited
	* @param int $map_id
	* @param date $date
	*/
	public static function increment_visit($map_id, $date = null){
		if($date == null){
			$date = time();
		}
		$date_str = date('Y-m-d', $date);

		$stat_obj = ORM::factory('Usagestatistics')
			->where('date', '=', $date_str)
			->where('map_id', '=', $map_id)
			->find();
 
		if(!$stat_obj->loaded()){
			$stat_obj->date = $date_str;
			$stat_obj->map_id = $map_id;
		}
		$stat_obj->visits ++;
		$stat_obj->save();
	}

	
} // End Usagestatistics Model