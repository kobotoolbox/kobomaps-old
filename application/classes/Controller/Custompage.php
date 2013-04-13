<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Custompage.php - Controller
* This software is copy righted by Kobo 2013
* Writen by Dylan Gillespie, Etherton Technologies <http://ethertontech.com>
* Started on 2013-03-01
* Creating custom html script pages for the site
*************************************************************/

class Controller_Custompage extends Controller_Loggedin {

	
	public function before()
	{
		parent::before();	
		
		$auth = Auth::instance();
		//is the user logged in?
		if($auth->logged_in('admin'))
		{
			$this->session = Session::instance();
			//if auto rendered set this up
			if ($this->auto_render)
			{
				$data = array(
						'slug' => '',
						'content' => '',
						'id' => isset($_GET['id']) ? intval($_GET['id']) : 0,
				);
				
				$pages = ORM::factory('Custompage')->
				where('user_id', '=', $this->user->id)->
				find_all();
				
				//grab all of the pages that are in the database from the start, such as main/about/help/support
				$default = ORM::factory('Custompage')->
				where('user_id', '=', 1)->
				find_all();
				
				$page_array = array();
				$page_array[0] = __('New Page');
				foreach($default as $main){
					$page_array[$main->id] = $main->slug;
				}
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
			}
		}
	}
	/**
	* where users go to edit custom html
	*/
	public function action_index()
	{
		$auth = Auth::instance();
		//only admins should be allowed to see the page in the first place, and if not, are redirected to mymaps
		if(!$auth->logged_in('admin'))
		{
			HTTP::redirect('mymaps');
		}

		if(!empty($_POST)){

			if($_POST['action'] == 'delete'){
				$response = Model_Custompage::delete_page($_POST['pages']);
				if($response == __('That page cannot be deleted.')){
					$this->template->content->errors[] = $response;
					//reload the page with data being set to the page that was attempted to be deleted
					$data['id'] = $_POST['pages'];
					$data['slug'] = $_POST['slug'];
					$data['content'] = $_POST['content'];
					$this->template->content->data = $data;
				}
				else{
					$this->template->content->messages[] = __('Deleted the page ').$_POST['slug'];
					unset($this->template->content->pages[$_POST['pages']]);
				}
			}
			else{
			//if they submitted a save/create page request
				$data['slug'] = $_POST['slug'];
				$data['content'] = $_POST['content'];
	
			//throw an error of either are empty
				if($data['slug'] == '' || $data['content'] == ''){
					$this->template->content->errors[] = __('The title or the content was empty.');
					$this->template->content->data = $data;
					return;
				}
				
				//the select page bar on the page has default of 0 for new page
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
					else{
						$default = ORM::factory('Custompage')->
						where('user_id', '=', 1)->
						where('id', '=', $_POST['pages'])->
						find();
						
						$default->content = $data['content'];
						$default->slug = $data['slug'];
						$default->save();
						
						$data['id'] = $default->id;
						$this->template->content->pages[$default->id] = $default->slug;
						$this->template->content->data = $data;
						$this->template->content->messages[] = __('Saved page').' '.$data['slug'];
					}
				}
				return;
			}
		}
	}//end action_index
	
		 
	/**
	* used by the Custompage controller to gather the data on the current page that was selected in the menu
	*/
	public function action_getpage(){
		$this->auto_render = false;
		
		$page = ORM::factory('Custompage', $_POST['page']);

		echo $page->content;
	}
	
	//call the generic slug checker function
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
