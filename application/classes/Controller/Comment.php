<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Comment.php - Controller
* This software is copy righted by Kobo 2013
* Written by Dylan Gillespie <dylan@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-02-06
*************************************************************/

class Controller_Comment extends Controller_Loggedin {

	/**
	where users go to submit a comment
	*/
	public function action_window()
	{
		$this->auto_render = false;
		$this->template = null;				
		
		//grab the map ID
		//was an id given?
		$map_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		
		if($map_id == 0)
		{
			return;
		}
		
		//grab the map from the database
		$map = ORM::factory('Map',$map_id);
		
		if(!$map->loaded())
		{
			return;
		}
				
		$view = new View('comment/window');
		$view->map = $map;
		$view->user = $this->user;
		echo $view;
		
	}//end action_index
		
	public function action_submitmessage(){
		$this->auto_render = false;
		$this->response->headers('Content-Type','application/json');
		
		$name = $_POST['myName'];
		$email = $_POST['myEmail'];
		$message = $_POST['myMessage'];
		
		if(strlen($message) == 0){
			echo '{ "status": "error", "message": "'.__('Your comment is empty, please try again.').'" }';
			return;			
		}
		
		Model_Messagecenter::add_message($_POST['map_id'], $message, $name, $email);
		
		echo '{"status": "success"}';
	}
	
	
	
	//creates the inbox for users to view messages they have recieved for their maps
	public function action_index(){
		$view =  new View('messages/window');
		$js = view::factory('messages/window_js');
		
		
		if(!empty($_POST)) // They've submitted the form to update his/her wish
		{
			try
			{
				if($_POST['action'] == 'delete')
				{
					$message = ORM::factory('Messagecenter',$_POST['message_id']);
					
					$this->template->content->messages[] = __('Message Deleted');
					Model_Messagecenter::delete_message($_POST['message_id']);
						
				}
				if($_POST['action'] == 'delete_selected' AND isset($_POST['message_check']))
				{
					foreach($_POST['message_check'] as $message_id=>$value)
					{
						$mess = ORM::factory('Messagecenter', $message_id);
						$this->template->content->messages[] = __('Message Deleted').':'.substr($mess->message,0, 25);
						Model_Messagecenter::delete_message($message_id);		
					}
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
							$this->template->content->errors[] = $error;
						}
					}
				}
			}
		}
		
		$messages = ORM::factory('Messagecenter')
		->select('maps.title')
		->join('maps')
		->on('maps.id', '=', 'messagecenter.map_id')
		->where('maps.user_id','=',$this->user->id)
		->find_all();
		
		$view->messages = $messages;
		$this->template->header->menu_page = "comment";
		$this->template->html_head->title = __("Messages");
		$this->template->html_head->script_files[] = 'media/js/jquery.tools.min.js';
		$this->template->html_head->script_views[] = $js;
		$this->template->content = $view;
	}
	
	public function action_messageDetails(){
		$this->auto_render = false;
		
		//get the map id
		$message_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if($message_id == 0)
		{
			return;
		}
		//get the map
		$message = ORM::factory('Messagecenter', $message_id);
		$map = ORM::factory('Map', $message->map_id);
		//is this your map
		if($map->user_id != $this->user->id)
		{
			return;
		}
		
		//flip the status of the map
		$message->unread = 0;
		$message->save();
		
		$view = new View('messages/messageDetails');
		$view->message = $message;
		$view->map = $map;
		echo $view;
		return;
	}
		 
	
}//end of class
?>