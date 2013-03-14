<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Profile.php - Heler
* This software is copy righted by Kobo 2013
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-01-14
*************************************************************/

class Controller_Profile extends Controller_Loggedin {

	
  	
	/**
	where users go to sign up
	*/
	public function action_index()
	{
		
		$user = $this->user;
		$data = array(
				'email'=>$user->email,
				'username'=>$user->username,
				'first_name'=>$user->first_name,
				'last_name'=>$user->last_name,
				'password'=>'',
				'password_confirm'=>'',
				'email_alerts'=>$user->email_alerts,
				'email_warnings'=>$user->email_warnings,
				'open_id'=>$user->open_id,
				);
		//turn set focus to first UI form element
		$this->template->html_head->script_views[] = '<script type="text/javascript">$(document).ready(function() {$("input:text:visible:first").focus();});</script>';
		$this->template->html_head->script_views[] = View::factory('js/messages');
		//turn on jquery UI
		$this->template->html_head->script_files[] = 'media/js/jquery-ui.min.js';
		$this->template->html_head->styles['media/css/jquery-ui.css'] = 'screen';
		
		
		$this->template->header->menu_page = "profile";
		$this->template->html_head->title = __("Profile");
		$this->template->content = View::factory('profile');
		$this->template->content->errors = array();
		$this->template->content->messages = array();
		$this->template->content->data = $data;
		
		if(!empty($_POST)) // They've submitted their registration form
		{
			try 
			{
				if(!isset($_POST['email_alerts']))
				{
					$_POST['email_alerts'] = 0;
				}
				if(!isset($_POST['email_warnings']))
				{
					$_POST['email_warnings'] = 0;
				}
				$this->template->content->data = $_POST;				
				//conver the DOB to a format mysql recognizes
				$user->update_user($_POST, array('username','password','email', 'first_name','last_name', 'email_alerts', 'email_warnings'));
				            	
				$this->template->content->messages[] = __('Profile updated successfully');
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
							$this->template->content->errors[] = $error;							
						}
					}
				}
			}	
		}
		else 
		{	//They're visiting for the first time		
		
		}
	}//end of action_index
	
	
	
} // End Profile
