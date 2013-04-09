<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Menuedit.php - Controller
* This software is copy righted by Kobo 2013
* Writen by Dylan Gillespie, Etherton Technologies <http://ethertontech.com>
* Started on 2013-03-22
* Creating custom menu items
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
						'admin_only' => '0',
				);
				
				$pages = ORM::factory('Custompage')->
				where('user_id', '=', $this->user->id)->
				find_all();

				$submenus = array();
				$menus = array();
				
				$m = ORM::factory('Menus')
				->find_all();
				
				foreach($m as $main){
					$sub = ORM::factory('Menuitem')->
					where('menu', '=', $main->id)->
					find_all();
					foreach($sub as $s){
						$menus[$main->title][$s->id] = $s;
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
				$sub = ORM::factory('Menuitem')->
				where('text', '=', $_POST['text'])->
				find();
				$response = Model_Menuitem::delete_menuitem($sub->id);
			
				$this->template->content->messages[] = __('Deleted the menu item').' '.$_POST['text'];
				unset($this->template->content->pages[$_POST['pages']]);
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
				
				//if submenu doesn't exist
				if($_POST['pages'] == 0){
					$length = strlen(__('New Submenu in')) + 1;
					//string will be the menu in which to place this
					$string = substr($_POST['menuString'], $length);

					$menu = ORM::factory('Menus')->
					where('title', '=', $this->flip($string))->
					find();

					if($menu->loaded()){
						$sub = ORM::factory('Menuitem')->
						where('text', '=', $_POST['text'])->
						find();

						if(!$sub->loaded()){
							//load the image
							$sub->text = $_POST['text'];
							$sub->item_url = URL::base(TRUE, TRUE).$_POST['item_url'];
							$sub->menu = $menu->id;
							$sub->save();
							
							$_POST['image_url'] = $_FILES['file']['name'];
							if($_FILES['file']['name'] != '')
							{
								$filename = $this->_save_file($_FILES['file'], $menu, $sub);
							}
							

							if($filename !== false){
								$sub->image_url = $filename;
							}
							$sub->save();

							$this->template->content->pages[$menu->title] = $sub->text;
							$this->template->content->messages[] = __('Saved submenu').' '.$sub->text;
						}
						
					}
					//if menu doesn't exist
					else{
						$newMenu = ORM::factory('Menus');
						$newMenu->title = $this->flip($string);
						$newMenu->save();

						$sub = ORM::factory('Menuitem')->
						where('text', '=', $_POST['text'])->
						find();

						if(!$sub->loaded()){
							$sub->text = $_POST['text'];
							$sub->item_url = URL::base(TRUE, TRUE).$_POST['item_url'];
							$sub->menu = $menu->id;
							
							$_POST['image_url'] = $_FILES['file']['name'];
							if($_FILES['file']['name'] != '')
							{
								$filename = $this->_save_file($_FILES['file'], $menu, $sub);
							}
							
							$sub->image_url = $filename;
							$sub->save();

							$this->template->content->pages[$menu->title] = $sub->text;
							$this->template->content->messages[] = __('Saved submenu').' '.$sub->text;
						}
						else{
							$this->template->content->errors[] = $sub->text.' '.__('already exists.');
						}
					}
				}
				else{
					//the submenu exists and is being edited
					
					$sub = ORM::factory('Menuitem', $_POST['pages']);
					$menu = ORM::factory('Menus', $sub->menu);
					
					if($menu->loaded()){
						$_POST['image_url'] = $_FILES['file']['name'];
						if($_FILES['file']['name'] != '')
						{
							$filename = $this->_save_file($_FILES['file'], $menu, $sub);
							if($filename !== false){
								$sub->image_url = $filename;
							}
						}
						$sub->text = $_POST['text'];
						$sub->item_url = URL::base(TRUE, TRUE).$_POST['item_url'];
						$sub->save();
						$this->template->content->messages[] = __('Saved submenu').' '.$sub->text;
					}
				}
				
			}
		}
	}//end action_index
	
		 
	/**
	* used by the Menuedit controller to gather the data on the current menu that was selected in the page
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

		$menuitem = ORM::factory('Menuitem', $val);
		$menu = ORM::factory('Menus', $menuitem->menu);
		
		//find only the end of the url, after kobomaps
		$pos = strpos($menuitem->item_url, '/kobomaps/');
		$len = strlen('/kobomaps/');
		
		$string = substr($menuitem->item_url, $pos + $len);

		echo '{';
		echo '"menu" : "'.$this->flip($menu->title).'",';
		echo '"text" : "'.$menuitem->text.'",';
		echo '"image" : "'.$menuitem->image_url.'",';
		echo '"url" : "'.$menuitem->item_url.'"';
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
		else return $slug;
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
	
	/**
	 * Grabs the file extention of a file
	 * @param string $file_name name of the file
	 * @return the exention of the file. So for 'about.txt' this function would return 'txt'
	 */
	protected function get_file_extension($file_name) {
		return substr(strrchr($file_name,'.'),1);
	}
	
	/**
	 * Saves a file from the temp upload area to the hard disk
	 * @param array $upload_file the $_FILES['<name>'] array for the given file
	 * @param obj $Menu Kohana ORM object for a menu, this is used in naming the file
	 * @param obj $Menuitem Kohana ORM object for a menuitem, this is used in naming the file
	 * @return boolean or filename
	 */
	protected function _save_file($upload_file, $menu, $sub)
	{

		if (
				! Upload::valid($upload_file) OR
				! Upload::not_empty($upload_file) OR
				! Upload::type($upload_file, array('png', 'jpeg', 'bmp', 'jpg')))
		{
			return FALSE;
		}
	
		$directory = DOCROOT.'uploads/images/';
	
		$extention = $this->get_file_extension($upload_file['name']);
		$filename = $menu->title.'-'.$sub->id.'.'.$extention;
		 
		if ($file = Upload::save($upload_file, $filename, $directory))
		{
			return URL::base(TRUE,TRUE).'uploads/images/'.$filename;
		}
	
		return FALSE;
	}
	
}//end of class
