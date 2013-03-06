<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Custompage.php - Controller
* This software is copy righted by Kobo 2013
* Writen by Dylan Gillespie, Etherton Technologies <http://ethertontech.com>
* Started on 2013-03-01
* Creating custom html scripts for the site
*************************************************************/

class Controller_Custompage extends Controller_Loggedin {

	/**
	* where users go to edit custom html
	*/
	public function action_index()
	{
		$data = array(
				'slug' => '',
				'content' => '',
				'id' => isset($_GET['id']) ? intval($_GET['id']) : 0,
		);
		
		$pages = ORM::factory('Custompage')->
		where('user_id', '=', $this->user->id)->
		find_all();
		
		$page_array = array();
		$page_array[0] = __('New Page');
		foreach($pages as $page){
			$page_array[$page->id] = $page->slug;
		}
		
		$this->template->header->menu_page = "custompage";
		//make messages roll up when done
		$this->template->html_head->messages_roll_up = true;
		$this->template->html_head->script_views[] = view::factory('js/messages');
		$this->template->content = new View('custompage/main');
		$this->template->html_head->title = __("Custom Page");
		$this->template->html_head->script_files[] = 'media/js/tiny_mce/jquery.tinymce.js';
		$this->template->html_head->script_views[] = new View('custompage/main_js');
		$this->template->content->errors = array();
		$this->template->content->messages = array();
		$this->template->content->data = $data;
		$this->template->content->pages = $page_array;
		

		if(!empty($_POST)){
			$data['slug'] = $_POST['slug'];
			$data['content'] = $_POST['content'];

			if($data['slug'] == '' || $data['content'] == ''){
				$this->template->content->errors[] = __('The title or the content was empty.');
				$this->template->content->data = $data;
				return;
			}
			
			if($_POST['pages'] == 0){
				$newPage = Model_Custompage::create_page($this->user->id, $data['slug'], $data['content']);
				$this->template->content->pages[$newPage->id] = $newPage->slug;
				$data['id'] = $newPage->id;
				$this->template->content->data = $data;
				$this->template->content->messages[] = __('Saved page').' '.$data['slug'];
			}
			
			else{
				$page = ORM::factory('Custompage')->
				where('id', '=', $_POST['pages'])->
				where('user_id', '=', $this->user->id)->
				find();
				
				//if the page already exists, update the content
				if($page->loaded()){
					$page->user_id = $this->user->id;
					$page->content = $data['content'];
					$page->slug = $data['slug'];
					$page->save();
					
					$data['id'] = $page->id;
					$this->template->content->pages[$page->id] = $page->slug;
					$this->template->content->data = $data;
					$this->template->content->messages[] = __('Saved page').' '.$data['slug'];
				}
			}
			return;
		}
	}//end action_index
	
		 
	public function action_getpage(){
		$this->auto_render = false;
		
		$page = ORM::factory('Custompage', $_POST['page']);

		echo $page->content;
	}
	
	public function action_checkslug(){
		$this->auto_render = false;
		$this->response->headers('Content-Type','application/json');
		
		if(!isset($_POST['slug'])){
			echo '{}';
			exit;
		}
		if($_POST['id'] == 0){
			$db_obj = ORM::factory('Custompage');
		}
		else {
			$db_obj = ORM::factory('Custompage')->where('id', '=', $_POST['id'])->find();
		}
		Helper_Slugs::check_slug($_POST['slug'], $db_obj);
	}
	
}//end of class
