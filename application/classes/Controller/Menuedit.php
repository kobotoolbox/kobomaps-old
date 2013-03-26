<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Custompage.php - Controller
* This software is copy righted by Kobo 2013
* Writen by Dylan Gillespie, Etherton Technologies <http://ethertontech.com>
* Started on 2013-03-01
* Creating custom html script pages for the site
*************************************************************/

class Controller_Menuedit extends Controller_Loggedin {

	
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
						'text' => '',
						'image_url' => '',
						'item_url' => '',
						'id' => isset($_GET['id']) ? intval($_GET['id']) : '__HOME__',
						'menuString' => '',
				);
				
				$pages = ORM::factory('Custompage')->
				where('user_id', '=', $this->user->id)->
				find_all();

				$submenus = array();
				$menus = array();

				$menu = ORM::factory('Menus')->find_all();
				foreach($menu as $m){
					$menus[] = $m;
				}

				foreach($menus as $menu){
					$sub = ORM::factory('Menuitem')->
					where('menu', '=', $menu->id)->
					find_all();

					$submenuArray = array();

					foreach($sub as $s){
						$submenuArray[] = $s;
					}

					$submenus[$menu->id] = $submenuArray;
				}
				
				//grab all of the pages that are in the database from the start, such as main/about/help/support
				$default = ORM::factory('Custompage')->
				where('user_id', '=', 1)->
				find_all();

				//get the menus for the custompage
				$custompage = ORM::factory('Menus')->
				where('title', '=', 'custompage')->
				find_all();
				
				$page_array = array();
				foreach($default as $main){
					$page_array[$main->slug][] = __('New Submenu in').' '.$main->slug;
						$main->slug = $this->flip($main->slug);

						$menu = ORM::factory('Menus')->
						where('title', '=', $main->slug)->
						find();

						$sub = ORM::factory('Menuitem')->
						where('menu', '=', $menu->id)->
						find_all();

						$main->slug = $this->flip($main->slug);
						
						foreach($sub as $s){
							$page_array[$main->slug][] = $s->text;
						}
				}
				foreach($pages as $page){
					$page_array[$page->slug][] = __('New Submenu in').' '.$page->slug;
					$menu = ORM::factory('Menus')->
						where('title', '=', $page->slug)->
						find();

					$sub = ORM::factory('Menuitem')->
						where('menu', '=', $menu->id)->
						find_all();
					foreach($sub as $s){
							$page_array[$page->slug][] = $s->text;
					}
				}
				foreach($custompage as $custom){
					$page_array[$custom->title][] = __('New Submenu in').' '.$custom->title;
					$sub = ORM::factory('Menuitem')->
						where('menu', '=', $custom->id)->
						find_all();
					foreach($sub as $s){
							$page_array[$custom->title][] = $s->text;
					}
				}
				
				$this->template->header->menu_page = "custompage";
				//make messages roll up when done
				$this->template->html_head->messages_roll_up = true;
				$this->template->html_head->script_views[] = view::factory('js/messages');
				$this->template->content = new View('menuedit/main');
				$this->template->html_head->title = __("Menus Page");
				$this->template->html_head->script_views[] = new View('menuedit/main_js');
				$this->template->content->errors = array();
				$this->template->content->messages = array();
				$this->template->content->data = $data;
				$this->template->content->pages = $page_array;
				$this->template->content->menus = $menus;
				$this->template->content->submenus = $submenus;
			}
		}
	}
	/**
	* where users go to edit custom menus
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
			//if they are creating a new menu or menuitem
				if($_POST['text'] == '' || $_POST['item_url'] == ''){
					$this->template->content->errors[] = __('Title and link url cannot be empty.');
					$data['text'] = $_POST['text'];
					$data['item_url'] = $_POST['item_url'];
					$data['image_url'] = $_POST['image_url'];
					$data['id'] = $_POST['pages'];
					$data['menuString'] = '';
					$this->template->content->data = $data;
				}
				if($_POST['pages'] == 0){
					$length = strlen(__('New Submenu in '));
					//string will be the menu in which to place this
					$string = substr($_POST['menuString'], $length);
					
					$menu = ORM::factory('Menus')->
					where('title', '=', $string)->
					find();

					if($menu->loaded()){
						$sub = ORM::factory('Menuitem')->
						where('text', '=', $_POST['text'])->
						find();

						if(!$sub->loaded()){
							$sub->text = $_POST['text'];
							$sub->item_url = $_POST['item_url'];
							$sub->image_url = $_POST['image_url'];
							$sub->menu = $menu->id;

							$sub->save();

							$this->template->content->pages[$menu->title] = $sub->text;
							$this->template->content->messages[] = __('Saved submenu').' '.$sub->text;
						}
						else{
							$this->template->content->errors[] = $sub->text.__(' already exists.');
						}
					}
					else{
						$newMenu = ORM::factory('Menus');
						$newMenu->title = $this->flip($string);
						$newMenu->save();

						$sub = ORM::factory('Menuitem')->
						where('text', '=', $_POST['text'])->
						find();

						if(!$sub->loaded()){
							$sub->text = $_POST['text'];
							$sub->item_url = $_POST['item_url'];
							$sub->image_url = $_POST['image_url'];
							$sub->menu = $newMenu->id;
							
							$sub->save();

							$this->template->content->pages[$menu->title] = $sub->text;
							$this->template->content->messages[] = __('Saved submenu').' '.$sub->text;
						}
						else{
							$this->template->content->errors[] = $sub->text.__(' already exists.');
						}
					}
				}
			}
		}
	}//end action_index
	
		 
	/**
	* used by the Custompage controller to gather the data on the current page that was selected in the menu
	*/
	public function action_getmenu(){
		$this->auto_render = false;

		$sub = $_POST['sub'];
		$val = $_POST['val'];
		
		//if creating a new menu shouldn't return anything;
		if($val == 0){
			echo '{}';
			exit;
		}

		$menu = ORM::factory('Menuitem')->
		where('text', '=', $sub)->
		find();

		echo '{';
		echo '"text" : "'.$menu->text.'",';
		echo '"image" : "'.$menu->image_url.'",';
		echo '"url" : "'.$menu->item_url.'"';
		echo '}';
	}
	

	/**
	* Used to convert static names of default custompages
	* @param string $slug name to be converted
	*/
	public function flip($slug){
		if($slug == '__HOME__'){
				return __('home');
			} 
		if($slug == '__HELP__'){
				return __('help');
			}
		if($slug == '__ABOUT__'){
				return __('about');
			}
		if($slug == '__SUPPORT__'){
				return __('support');
			}
		if($slug == __('home') || ''){
			return '__HOME__';
		}
		if($slug == __('about')){
			return '__ABOUT__';
		}
		if($slug == __('support')){
			return '__SUPPORT__';
		}
		if($slug == __('help')){
			return '__HELP__';
		}
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
			$db_obj = ORM::factory('Menuitem');
		}
		else {
			$db_obj = ORM::factory('Menuitem')->where('id', '=', $_POST['id'])->find();
		}
		Helper_Slugs::check_slug($_POST['slug'], $db_obj);
	}
	
}//end of class
