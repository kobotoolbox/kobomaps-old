<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Message extends ORM {

	
	
	/**
	 * Set the name of the table
	 */
	protected  $_table_name = 'message';
	
	/**
	 * A user has many tokens and roles
	 *
	 * @var array Relationhips
	 */
	
	protected $_has_one = array(
		'user_id' => array('model' => 'user'),
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
	public function update_message($values)
	{
	
		$expected = array('user_id', 'date', 'poster_name', 'poster_email', 'message');
	
		$this->values($values, $expected);
		$this->check();
		$this->save();
	}//end function

	
	
	public static function add_message($user_id, $message, $poster_name = '', $poster_email = '', $date = null){
		if($date == null){
			$date = time();
		}

		$date_str = date('Y-m-d H:i:s', $date);
		
		$mess_obj = ORM::factory('Message');
 
		if(!$mess_obj->loaded()){
			$mess_obj->date = $date_str;
			$mess_obj->user_id = $user_id;
			$mess_obj->poster_name = $poster_name;
			$mess_obj->poster_email = $poster_email;
			$mess_obj->message = $message;
		}

		$mess_obj->save();
		
		//email the user if they've set their options so
		$user = ORM::factory('User',$user_id);
		if($user->email_alerts)
		{
			
			$config = Kohana::$config->load('config');
			$no_reply = $config->get('no_reply_email');
			
			$to = array($user->email=>$user->first_name. ' '. $user->last_name);
			$from = array($no_reply=>__('KoboMaps System'));
			$subject = __('New alert from KoboMaps');
			$body = __('You have recieved a new message');
			
			Helper_Email::send_email($to, $from, $subject, $body);
			
				
		}
		
	}
	
	public static function add_alert($user_id, $message, $poster_name = '', $poster_email = '', $date = null){
		if($date == null){
			$date = time();
		}
	
		$date_str = date('Y-m-d H:i:s', $date);
	
		$mess_obj = ORM::factory('Message');
	
		if(!$mess_obj->loaded()){
			$mess_obj->date = $date_str;
			$mess_obj->user_id = $user_id;
			$mess_obj->poster_name = $poster_name;
			$mess_obj->poster_email = $poster_email;
			$mess_obj->message = $message;
		}
	
		$mess_obj->save();
		
		//email the user if they've set their options so
		$user = ORM::factory('User',$user_id);
		if($user->email_warnings)
		{			
			$config = Kohana::$config->load('config');
			$no_reply = $config->get('no_reply_email');
			
			$to = array($user->email=>$user->first_name. ' '. $user->last_name);
			$from = array($no_reply=>__('KoboMaps System'));
			$subject = __('New warning from KoboMaps');
			$body = __('You have recieved a new warning');					
			Helper_Email::send_email($to, $from, $subject, $body);
		}
	
	
	}
	
	public static function delete_message($message_id){
		$mess_obj = ORM::factory('Message', $message_id);
		$mess_obj->delete();
	}

	
} // End User Model