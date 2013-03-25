<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Sharing extends ORM {

	
	public static $allowed_permissions;
	public static $owner = 'owner';
	public static $edit = 'edit';
	public static $view = 'view';
	
	
		
	
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

	
	/**
	 * Use this to create an owner entry
	 * 
	 * @param int $map_id DB ID of the map
	 * @param int $user_id DB ID of the owner of the map
	 * @return ORM::Sharing sharing relation
	 */
	public static function create_owner($map_id, $user_id)
	{
		$share = ORM::factory('Sharing');
		$share->map_id = $map_id;
		$share->user_id = $user_id;
		$share->permission = Model_Sharing::$owner;
		$share->save();
		
		return $share;
	}
	
	/**
	 * Gets the map share object for a map and a user
	 * @param int $map_id DB ID of map
	 * @param mixed $user_id Could be a ORM DB object or the ID of the DB entry for a user
	 * @returns the Share object or false if it doesn't exist
	 */
	public static function get_share($map_id, $user)
	{
		
		$user_id = 0;
		//is it an int
		if(is_int($user))
		{			
			$user_id = intval($user);
		}
		elseif(is_string($user) AND intval($user) != 0)
		{
			$user_id = intval($user);
		}
		elseif(is_object($user) AND get_class($user) == 'Model_User')
		{
			$user_id = $user->id;
		}
		
		$share = ORM::factory('Sharing')
			->where('map_id','=',$map_id)
			->where('user_id','=',$user_id)
			->find();
		
		if($share->loaded())
		{
			return $share;
		}
		else
		{
			$share->permission = null;
			return $share;
		}
	}

	
} // End Sharing Model

Model_Sharing::$allowed_permissions = array(Model_Sharing::$view=>'view',Model_Sharing::$edit=>'edit');
