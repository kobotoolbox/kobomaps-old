<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_User extends Model_Auth_User {

	/**
	 * A user has many tokens and roles
	 *
	 * @var array Relationhips
	 */
	protected $_has_many =  array(
		'user_tokens' => array('model' => 'user_token'),
		'roles'       => array('model' => 'role', 'through' => 'roles_users'),
		'maps'		  => array('model'=>'map'),	
	);
	
	protected $_has_one = array(
			);


	/**
	 * Get the full name of a user
	 */
	public function full_name()
	{
		return $this->first_name . ' ' . $this->last_name;
	} 
	
	public function get_gender_possessive()
	{
		/*
		if($this->gender == '1')
		{
			return __('his');
		}
		return __('hers');
		*/
	}
	
} // End User Model