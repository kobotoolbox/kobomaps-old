<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Signup.php - Heler
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/

class Controller_Signup extends Controller_Main {

	
  	
	/**
	where users go to sign up
	*/
	public function action_index()
	{
		$data = array(
				'email'=>'',
				'username'=>'',
				'first_name'=>'',
				'last_name'=>'',
				'password'=>'',
				'password_confirm'=>'',
				'email_alerts'=>0,
				'email_warnings'=>0,
				'open_id_call'=>0,
				'open_id'=>''
				);
		
		//check if this is being called as a result of a open id call
		$sesh  = Session::instance(); 
		if($sesh->get_once('open_id_sign_up','0') == '1')
		{
			$data['open_id_call'] = 1;
			$data['email'] = $data['username'] = $sesh->get_once('email','');
			$data['password'] = $data['password_confirm'] = $sesh->get_once('password','');
			$data['last_name'] = $sesh->get_once('last_name','');
			$data['first_name'] = $sesh->get_once('first_name','');
			$data['open_id']= $sesh->get_once('open_id','');
			
		}
		
			
		//turn set focus to first UI form element
		$this->template->html_head->script_views[] = '<script type="text/javascript">$(document).ready(function() {$("input:text:visible:first").focus();});</script>';
		
		//turn on jquery UI
		$this->template->html_head->script_files[] = 'media/js/jquery-ui.min.js';
		$this->template->html_head->styles['media/css/jquery-ui.css'] = 'screen';
		
		//if they're already logged in then take them to their profile
		$auth = Auth::instance();		
		if( $auth->logged_in())
		{
			HTTP::redirect(Session::instance()->get_once('returnUrl','mymaps'));
		}
		$this->template->header->menu_page = "signup";
		$this->template->html_head->title = __("Sign Up");
		$this->template->content = View::factory('signup');
		$this->template->content->errors = array();
		$this->template->content->data = $data;
		
		if(!empty($_POST)) // They've submitted their registration form
		{
			try 
			{			
				//handle check boxes
				if(!isset($_POST['email_alerts']))
				{
					$_POST['email_alerts'] = 0;
				}
				if(!isset($_POST['email_warnings']))
				{
					$_POST['email_warnings'] = 0;
				}
				
				$this->template->content->data = $_POST;
			
				if(!isset($_POST['terms']))
				{
					$this->template->content->errors[] = __('Must agree to terms of use');
					return;
				}
				//conver the DOB to a format mysql recognizes
				$user = ORM::factory("User");
				$user->create_user($_POST, array('username','password','email', 'first_name','last_name', 'email_alerts', 'email_warnings', 'open_id'));
				// Add the login role to the user (add a row to the db)
				$login_role = new Model_Role(array('name' =>'login'));
            	$user->add('roles', $login_role);
            	
				// sign the user in
				if(!Auth::instance()->login($_POST['username'], $_POST['password'], true))
				{
					$this->template->content->errors[] = 'didn\'t log in successfully';
				}
				else
				{
					HTTP::redirect('/mymaps');
				}
			}
			catch (ORM_Validation_Exception $e)
			{
				$errors_temp = $e->errors('register');
				if(isset($errors_temp["_external"]))
				{
					$this->template->content->errors = array_merge($errors_temp["_external"], $this->template->content->errors);
				}				
				else
				{
					foreach($errors_temp as $error)
					{
						if(is_string($error))
						{
							//print_r($error.'    ');
							$start = strpos($error, '.');
							$end = strrpos($error, '.');
							
							$part = substr($error, $start + 1, $end-$start-1);
							$cause = substr($error, $end+1);
							$this->template->content->errors[] = __('The').' '.$part.' '.__('has already been used. Please choose another.');		
						}
					}
					//exit;
				}
			}	
		}
		else 
		{	//They're visiting for the first time		
		
		}
	}//end of action_index
	
	
	public function action_verify()
	{
		//turn set focus to first UI form element
		$this->template->html_head->script_views[] = '<script type="text/javascript">$(document).ready(function() {$("input:text:visible:first").focus();});</script>';
		
		//turn on jquery UI
		$this->template->html_head->script_files[] = 'media/js/jquery-ui.min.js';
		$this->template->html_head->styles['media/css/jquery-ui.css'] = 'screen';
		
		//if they're already logged in then take them to their profile
		$auth = Auth::instance();
		$user = null;
		//see if they're logged in
		if( $auth->logged_in() OR $auth->auto_login() OR isset($_GET['id']))
		{
			if($auth->logged_in() OR $auth->auto_login())
			{
				//if so get the user info 
				$user = ORM::factory('user',$auth->get_user());
				//has this user already verified their email
				if(intval($user->email_verified) == 1)
				{
					$this->request->redirect(Session::instance()->get_once('returnUrl','home'));
				}
			}
			else
			{
				$user = ORM::factory('user', $_GET['id']);
				//has this user already verified their email
				if(intval($user->email_verified) == 1)
				{
					$this->request->redirect(Session::instance()->get_once('returnUrl','home'));
				}
			}
		}
		else
		{
			//if they aren't logged in send them to the landing page
			$this->request->redirect('');
		}
		
		//Send an email to the user with the email verification key
		//but only do this if they aren't click on the the verify me link
		if(!isset($_GET['email_key']) AND empty($_POST))
		{
			//send email
			$token =  md5(uniqid(rand(), TRUE));
			$user->email_key = $token;
			$user->save();
			
			$message = __('Hello :name we are writing to confirm your email address. Please copy and paste the key :key below, or click on this link', 
					array(':name'=>$user->first_name,
					':key'=>$token));
				
			$email = Email::factory(__('Ekphora.com - Email verification'), 'blank')
			->to($user->email, $user->full_name())
			->from('email@ekphora.com', 'Ekphora.com - No Reply')
			->message($message, 'text/html')
			->send();
		}
		
		$this->template->header->menu_page = "verify email";
		$this->template->html_head->title = __("verify email");
		$this->template->content = View::factory('verifyemail');
		$this->template->content->errors = array();
		
		if(!empty($_POST) OR isset($_GET['email_key'])) // They've submitted their registration form
		{
				$key = '';
				if(isset($_GET['email_key']))
				{
					$key = $_GET['email_key'];
				}
				else 
				{
					$key = $_POST['email_key'];
				}

				//now we need to see if this key matches the key we have on file for the user.
				if(strcmp($key, $user->email_key) == 0)
				{
					//we have a match
					$user->email_verified = 1;
					$user->save();
					$this->template->content = View::factory('verifyemailthanks');
				}
				else
				{
					//the key did not match
					$this->request->redirect('/register/verify');
				}
		}
		else
		{	//They're visiting for the first time
			
		}
	}
} // End Welcome
