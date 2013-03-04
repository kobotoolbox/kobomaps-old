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
		return array();
	}//end function
	
	
	public function update_custompage($values)
	{
	
		$expected = array('user_id', 'slug_id', 'content');
		
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
	
	
	public static function create_page($user_id, $slug_id, $content){
		
		$page = ORM::factory('Custompage');
	
		if(!$page->loaded()){
			$page->user_id = $user_id;
			$page->slug_id = $slug_id;
			$page->content = $content;
		}
	
		$page->save();
	}
}