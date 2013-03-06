<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Message.php - Controller
* This software is copy righted by Kobo 2013
* Written by Dylan Gillespie <dylan@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-02-06
*************************************************************/

class Controller_Message extends Controller_Loggedin {

	/**
	where users go to submit a comment
	*/
	public function action_submit()
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
				
		$view = new View('messages/submit_window');
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
		
		//get owner of the map
		$owner_id = ORM::factory('Sharing')
			->where('map_id','=',$_POST['map_id'])
			->where('permission','=',Model_Sharing::$owner)
			->find()
			->user_id;
		Model_Message::add_message($owner_id, $message, $name, $email);
		
		echo '{"status": "success"}';
	}
	
	
	
	//creates the inbox for users to view messages they have recieved for their maps
	public function action_index(){
		$view =  new View('messages/mymessages');
		$js = view::factory('messages/mymessages_js');
		
		
		if(!empty($_POST)) // They've submitted the form to update his/her wish
		{
			try
			{
				if($_POST['action'] == 'delete')
				{
					$message = ORM::factory('Message',$_POST['message_id']);
					
					$this->template->content->messages[] = __('Message Deleted');
					Model_Message::delete_message($_POST['message_id']);
						
				}
				if($_POST['action'] == 'delete_selected' AND isset($_POST['message_check']))
				{
					foreach($_POST['message_check'] as $message_id=>$value)
					{
						$mess = ORM::factory('Message', $message_id);
						$this->template->content->messages[] = __('Message Deleted').':'.substr($mess->message,0, 25);
						Model_Message::delete_message($message_id);		
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
		
		$messages = ORM::factory('Message')
			->where('user_id','=',$this->user->id)
			->order_by('date', 'DESC')
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
		$message = ORM::factory('Message', $message_id);
		//is this your map
		if($message->user_id != $this->user->id)
		{
			return;
		}
		
		//flip the status of the map
		$message->unread = 0;
		$message->save();
		
		$view = new View('messages/messageDetails');
		$view->message = $message;
		echo $view;
		return;
	}
		 
	
}//end of class
?>