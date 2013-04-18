<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Custompage extends ORM {

	
	
	/**
	 * Set the name of the table
	 */
	protected  $_table_name = 'custompage';
	
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
		
		return array(
		'slug' => array(
				array('not_empty'),
				array('max_length', array(':value', 130))
				)
		);
	}//end function
	

	/**
	* creates a custompage
	* @param array of 'user_id', 'slug_id', 'content'
	*/
	public function update_custompage($values)
	{
		$expected = array('user_id', 'slug_id', 'content', 'menu_id');
		
		//if no slug is set
		if($values['slug_id'] == '')
		{
			$auth = $auth = Auth::instance();
				
			$hash = substr($auth->hash_password(microtime().$this->id), 0, 32);
			$values['slug_id'] = $hash;
			$page = ORM::factory('Custompage')->where('slug_id','=',$hash)->find();
				
			while($map->loaded()) //keep coming up with a new hash until we find a unique one.
			{
				$hash = substr($auth->hash_password(microtime().$this->id), 0, 32);
				$values['slug_id'] = $hash;
				$page = ORM::factory('Custompage')->where('slug_id','=',$hash)->find();
			}
		}
	
		$this->values($values, $expected);
		$this->check();
		$this->save();
	}//end function
	
	/**
	* Creates a Custompage
	* @param int $user_id is the id of the creator
	* @param int $slug_id is the title of the custompage
	* @param string $content is the HTML to create the page
	* @param int $menu_id is the id of the menu to use with this page
	* @return ORM::custompage the page that was created
	*/
	public static function create_page($user_id, $slug_id, $content, $menu_id){
		
		$page = ORM::factory('Custompage');
	
		if(!$page->loaded()){
			$page->user_id = $user_id;
			$page->slug = $slug_id;
			$page->content = $content;
			$page->menu_id = $menu_id;
		}
	
		$page->save();
		
		return $page;
	}
	
	/**
	* Attempts to delete the given page
	* @param int $page_id that is trying to be deleted
	* @return string message, status of deletion
	*/
	public static function delete_page($page_id){
	//special pages are created upon creation of database and can only be edited by their content
		$page = ORM::factory('Custompage', $page_id);
		if(!$page->special){
			$page->delete();
			return __('Deleted');
		}
		else{
			return __('That page cannot be deleted.');
		}
	}
	
	
}