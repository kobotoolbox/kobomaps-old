<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Messagecenter extends ORM {

	
	
	/**
	 * Set the name of the table
	 */
	protected  $_table_name = 'message_center';
	
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
	public function update_messagecenter($values)
	{
	
		$expected = array('map_id', 'date', 'poster_name', 'poster_email', 'message');
	
		$this->values($values, $expected);
		$this->check();
		$this->save();
	}//end function

	public static function add_message($map_id, $message, $poster_name = '', $poster_email = '', $date = null){
		if($date == null){
			$date = time();
		}

		$date_str = date('Y-m-d', $date);
		
		$mess_obj = ORM::factory('Messagecenter');
 
		if(!$mess_obj->loaded()){
			$mess_obj->date = $date_str;
			$mess_obj->map_id = $map_id;
			$mess_obj->poster_name = $poster_name;
			$mess_obj->poster_email = $poster_email;
			$mess_obj->message = $message;
		}

		$mess_obj->save();
	}
	
	public static function delete_message($message_id){
		$mess_obj = ORM::factory('Messagecenter', $message_id);
		$mess_obj->delete();
	}

	
} // End User Model