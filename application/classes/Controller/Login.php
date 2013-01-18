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
		$user = ORM::factory('user')->and_where('email', '=', $email)->find();
		if(!$user->loaded())
		{
			echo __('no user found with email');
			return;
		}
		
		$this->_email_resetlink($email, $user->first_name, $user->last_name);
		echo __('reset email sent');
		
	 }//end action reset
	 
	 
	private function _email_resetlink( $email, $first_name, $last_name )
	{
		
		
		/*
		$secret = $auth->hash_password($user->email.$user->last_login);
		$secret_link = url::site('login/index/'.$user->id.'/'.$secret.'?reset');		
		*/
		$secret_link = "http://dfdfdfdfddf.com";
						
		$to = $email;
		$from = __('ui_admin.password_reset_from');
		$subject = __('ui_admin.password_reset_subject');		
		$message = "$first_name reset your password by following this link: " . $secret_link;
		email::send( $to, $from, $subject, $message, FALSE );

		//email details
		/*
		if( email::send( $to, $from, $subject, $message, FALSE ) == 1 )
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
		*/

	}
	
	
} // End Welcome
