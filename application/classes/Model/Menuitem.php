<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Menuitem extends ORM {

	
	
	/**
	 * Set the name of the table
	 */
	protected  $_table_name = 'menu_items';
	
	/**
	 * A user has many tokens and roles
	 *
	 * @var array Relationhips
	 */
	
	protected $_has_one = array(
		'user' => array('model' => 'user'),
	);

	
	/**
	 * Rules function
	 * @see Kohana_ORM::rules()
	 */
	public function rules()
	{
		return array();
	}//end function
	
	/**
	* updates the menuitem
	* @param array $values that contains the info for the menuitem
	*/
	public function update_menuitem($values)
	{
	
		$expected = array('menu_id', 'text', 'image_url', 'item_url', 'admin_only');
	
		$this->values($values, $expected);
		$this->check();
		$this->save();
	}//end function
	

	/**
	* Used to create menuitems from the custompage
	* @param int $menu
	* @param string $text
	* @param string $image_url
	* @param string $item_url
	* @return ORM::Menuitem that was created
	*/
	public static function create_menuitem($menu, $text, $image_url, $item_url, $admin_only){
		
		$item = ORM::factory('Menuitem');
	
		if(!$item->loaded()){
			$item->menu = $menu;
			$item->text = $text;
			$item->image_url = $image_url;
			$item->item_url = $item_url;
			$item->admin_only = $admin_only;
		}
	
		$item->save();
		
		return $item;
	}
	
	/**
	* Deletes the menuitem with the id
	* @param int $item_id
	* @return string deleted
	*/
	public static function delete_menuitem($item_id){
		$item = ORM::factory('Menuitem', $item_id);
			$item->delete();
			return __('Deleted');
	}
	
	/**
	 * Used to convert static names of default custompages
	 * @param string $slug name to be converted
	 */
	public static function flip($slug){
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
}
