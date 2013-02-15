<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Login.php - Controller
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/

class Controller_Login extends Controller_Main {

	
  	
	/**
	where users go to sign up
	*/
	public function action_index()
	{
		
		//if they're already logged in then take them to their profile
		$auth = Auth::instance();
		if( $auth->logged_in() OR $auth->auto_login())
		{
			HTTP::redirect(Session::instance()->get_once('returnUrl','mymaps'));
		}
		 
		$this->template->html_head->title = __("login");
		$this->template->content = View::factory('login');
		$this->template->header->menu_page = 'login';
		$this->template->content->errors = array();
		
		//set the focus on the username input box
		$this->template->html_head->script_views[] = '<script type="text/javascript">$(document).ready(function() {  $("#username").focus();});</script>';
		
		$this->template->html_head->script_files[] = 'media/js/jquery.tools.min.js';		
		//main JS view
		$this->template->html_head->script_views[] = view::factory('login_js');
		
		if(!empty($_POST)) // They've submitted their registration form
		{
			$auth->login($_POST['username'], $_POST['password'], true);
			if($auth->logged_in())
			{
				HTTP::redirect(Session::instance()->get_once('returnUrl','mymaps'));	
			}
			else
			{
				$this->template->content->errors[] = __("incorrect login");					
			}
	
		}
		else 
		{	//They're visiting for the first time		
		
		}
	}//end index action
	
	/**
	 * Called when a user wants to reset their password
	 */
	 public function action_reset()
	 {
		 //this function isn't participating in the auto render side of things
		$this->template = "";
		$this->auto_render = FALSE;
		
		//get the email.
		$email = null;
		if(isset($_GET['email']))
		{
			$email = urldecode($_GET['email']);
		}
		//if there's no email:
		if($email == null)
		{
			echo __('email null');
			return;
		}
		//get the user that corresponds to this email
		$user = ORM::factory('User')->and_where('email', '=', $email)->find();
		if(!$user->loaded())
		{
			echo __('no user found with email');
			return;
		}
		
		$this->_email_resetlink($user);
		echo __('reset email sent');
		
	 }//end action reset
	 
	 
	 /**
	  * Called when a user has received the password reset key and wants
	  * to now reset their password.
	  */
	 public function action_resetpassword()
	 {
	 	//make sure there's a key
	 	$hash = isset($_GET['key']) ? $_GET['key'] : '';
	 	
	 	//get the user that's requesting the reset
	 	$user = ORM::factory('User')
	 		->where('reset_hash','=',$hash)
	 		->find();
	 	
	 	//if this isn't valid get them out of here
	 	if(!$user->loaded())
	 	{
	 		HTTP::redirect('');
	 	}
	 	
	 	//get the expiration date of the key
	 	$expiration_date = strtotime($user->reset_expire);
	 	if($expiration_date < time())
	 	{
	 		HTTP::redirect('');
	 	}
	 	
	 	
	 	$this->template->html_head->title = __('Password Reset');
	 	$this->template->content = View::factory('password_reset');
	 	$this->template->header->menu_page = 'login';
	 	$this->template->content->errors = array();
	 	
	 	//set the focus on the username input box
	 	$this->template->html_head->script_views[] = '<script type="text/javascript">$(document).ready(function() {  $("#password").focus();});</script>';
	 	
	 	$this->template->html_head->script_files[] = 'media/js/jquery.tools.min.js';
		
	 	
	 	if(!empty($_POST)) // They've submitted their registration form
	 	{
	 		//check if the two passwords match
	 		if(isset($_POST['password']) AND isset($_POST['password_confirm']))
	 		{
	 			if($_POST['password'] == $_POST['password_confirm'])
	 			{	 				
	 				//reset the reset values in the DB and store this new password
	 				$auth = Auth::instance();
	 				$_POST['reset_hash'] = null;
	 				$_POST['reset_expire'] = null;
	 				$user->update_user($_POST, array('password','reset_hash','reset_expire'));
	 				//kick them out to the login page
	 				HTTP::redirect('login');
	 			}
	 			else
	 			{
	 				$this->template->content->errors[] = __('Your passwords don\'t match');
	 			}
	 		}
	 	}	 	
	 		
	 }
	 
	private function _email_resetlink( $user)
	{
		//first create a hash
		$auth = Auth::instance();
		$hash = $auth->hash_password(date('U').$user->password);
		$user->reset_hash = $hash;
		$user->reset_expire = date('Y-m-d G:i:s', time() + (2*60*60)); //give them two hours
		$user->save();
		//create the link
		$secret_link = '<a href="'.URL::site(NULL, 'http').'login/resetpassword?key='.$hash.'">'.URL::site(NULL, 'http').'login/resetpassword?key='.$hash.'</a>';
		//figure out the no reply email address
		$config = Kohana::$config->load('config');
		$no_reply = $config->get('no_reply_email');

		$to = array($user->email=>$user->first_name. ' '. $user->last_name);
		$from =array($no_reply=>__('KoboMaps System'));
		$subject = __('KoboMaps Password Reset');		
		$body = $user->first_name.' '.__('reset your password by following this link:').' '. $secret_link;		
		
		Helper_Email::send_email($to, $from, $subject, $body);

	}
	
	
} // End Welcome
