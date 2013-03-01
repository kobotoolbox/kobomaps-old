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
				'content' => ''
		);
		
		$pages = ORM::factory('Custompage')->
		where('user_id', '=', $this->user->id)->
		find_all();
		
		$page_array = array();
		foreach($pages as $page){
			$page_array[$page->id] = $page->slug_id;
		}
		
		$this->template->header->menu_page = "custompage";
		$this->template->content = new View('custompage/main');
		$this->template->html_head->title = __("Custom Page");
		//$this->template->content->user = $this->user->id;
		$this->template->content->data = $data;
		$this->template->content->pages = $page_array;
		

		if(!empty($_POST)){
			$data['slug'] = $_POST['slug_id'];
			$data['content'] = $_POST['content'];
			
			$page = ORM::factory('Custompage')->
			where('slug_id', '=', $data['slug'])->
			where('user_id', '=', $this->user->id)->
			find();
			
			if($data['slug'] == '' || $data['content'] == ''){
				return;
			}
			
			//if the page already exists, update the content
			if(count($page) > 0){
				$page->slug_id = $data['slug'];
				$page->user_id = $this->user->id;
				$page->content = $data['content'];
				$page->save();
			}
			else{
				Model_Custompage::create_page($this->user->id, $data['slug'], $data['content']);
			}
		}
	}//end action_index
	
		 
	
}//end of class
