<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Menus extends ORM {

	
	
	/**
	 * Set the name of the table
	 */
	protected  $_table_name = 'menus';
	
	/**
	 * A user has many tokens and roles
	 *
	 * @var array Relationhips
	 */
	
	protected $_has_one = array(
		'user' => array('model' => 'user'),
	);

	protected $_has_many = array('menu_items' => array('model' => 'Menuitem', 'foreign_key' => 'menu'));
	
	/**
	 * Rules function
	 * @see Kohana_ORM::rules()
	 */
	public function rules()
	{
		return array();
	}//end function
	
	/**
	* updates the custom menus
	* @param array $values that contains the info for the menuitem
	*/
	public function update_menu($values)
	{
	
		$expected = array('title');
	
		$this->values($values, $expected);
		$this->check();
		$this->save();
	}//end function
	

	/**
	* Used to create menuitems from the custompage
	* @param string $title
	* @return ORM::Menu that was created
	*/
	public static function create_menu($title){
		
		$item = ORM::factory('Menus');
	
		if(!$item->loaded()){
			$item->title = $title;
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
}
