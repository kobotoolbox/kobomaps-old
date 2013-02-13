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
			
			$to = $user->email;
			$from = 'noreply@kobomaps.org';
			$subject = __('New alert from KoboMaps');
			$body = __('You have recieved a new message');
			
			require Kohana::find_file('swiftmailer', 'classes/lib/swift_required');
			//Create the Transport
			$transport = Swift_SmtpTransport::newInstance('localhost', 25);
			//Create the Mailer using your created Transport
			$mailer = Swift_Mailer::newInstance($transport);
			//Create a message
			$message = Swift_Message::newInstance('Email')
			->setSubject($subject)
			->setFrom(array($from => __('KoboMaps System')))
			->setTo(array($to))
			->setBody($body, 'text/html');
			//Send the message
			$result = $mailer->send($message);
			
				
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
			require Kohana::find_file('swiftmailer', 'classes/lib/swift_required');			
			$to = $user->email;
			$from = 'noreply@kobomaps.org';
			$subject = __('New warning from KoboMaps');
			$message = __('You have recieved a new message');
			Helper_Email::send( $to, $from, $subject, $message, FALSE );
		}
	
	
	}
	
	public static function delete_message($message_id){
		$mess_obj = ORM::factory('Message', $message_id);
		$mess_obj->delete();
	}

	
} // End User Model