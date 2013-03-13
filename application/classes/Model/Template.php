<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Template extends ORM {

	
	
	/**
	 * Set the name of the table
	 */
	protected  $_table_name = 'templates';
	
	/**
	 * A user has many tokens and roles
	 *
	 * @var array Relationhips
	 */
	protected $_has_many =  array(
			'regions' => array('model' => 'template_region'),
	);
	
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
				'title' => array(
						array('not_empty'),
						array('max_length', array(':value', 254)),
						array('min_length', array(':value', 1))
				),
				'description' => array(
						array('max_length', array(':value', 65533)),
						array('min_length', array(':value', 1))
				),
				'file' => array(
						array('not_empty'),
						array('max_length', array(':value', 254)),
						array('min_length', array(':value', 1))
				),
				
				'admin_level' => array(array('not_empty'),),
				
				'decimals' => array(array('not_empty'),),				
				'lat' => array(array('not_empty'),),
				'lon' => array(array('not_empty'),),
				'zoom' => array(array('not_empty'),),
		);
	}//end function
	
	
	/**
	 * Update an existing template
	 *
	 * Example usage:
	 * ~~~
	 * $form = ORM::factory('Template', $id)->update_template($_POST);
	 * ~~~
	 *
	 * @param array $values
	 * @throws ORM_Validation_Exception
	 */
	public function update_template($values)
	{
	
		$expected = array('title', 'description', 'admin_level', 'file', 'decimals', 'lat', 'lon', 'zoom', 'user_id', 'is_official','is_private');
	
		$this->values($values, $expected);
		$this->check();
		$this->save();
	}//end function

	
	/**
	 * Delete a template
	 */
	public static function delete_template($id)
	{
		//get the template in question
		$template = ORM::factory('Template', $id);
		if(file_exists(DOCROOT.'uploads/templates/'.$template->file))
		{
			unlink(DOCROOT.'uploads/templates/'.$template->file);
		}
		if(file_exists(DOCROOT.'uploads/templates/'.$template->kml_file) AND strlen($template->kml_file) > 0)
		{
			unlink(DOCROOT.'uploads/templates/'.$template->kml_file);
		}
		$template->delete();
	}
	
	/**
	 * Used to make a copy of a template for the given user
	 * @param int $user_id DB ID of the user you're making this copy for
	 * @return db_obj The new template
	 */
	public function copy($user_id)
	{
		//first copy the template itself
		$new_template = ORM::factory('Template');
		$copy_array = $this->as_array();
		$copy_array['user_id'] = $user_id;
		
		//these loops check to see if the title and slug contain __('Copy') already, if they do, take them out
		$unique = false;
		$count = 1;
		if(strrpos($copy_array['title'], '('.__('Copy')) !== false){
			$copy_array['title'] = substr($copy_array['title'], 0, strrpos($copy_array['title'], '('.__('Copy')));
		}
		$original_title = $copy_array['title'];
		//check the database until a new copy(count) isn't loaded, and then make the map title the title(copy)($count)
		while(!$unique){
			$checkTitle = ORM::factory('Template')->
			where('title', '=', $copy_array['title'])->
			find();
			if(!$checkTitle->loaded()){
				$copy_array['title'] = $original_title.'('.__('Copy').')('.$count.')';
				$unique = true;
			}
			else{
				$count ++;
				$copy_array['title'] = $original_title.'('.__('Copy').')('.$count.')';
			}
		}
		
		$new_template->update_template($copy_array);
		
		//now copy the regions
		$regions = ORM::factory('Templateregion')
			->where('template_id','=',$copy_array['id'])
			->find_all();
		foreach($regions as $region)
		{
			$region->copy($new_template->id);
		}
		
		//copy the json file
		$new_file = $new_template->id.'.json';
		copy(DOCROOT.'uploads/templates/'.$this->file,DOCROOT.'uploads/templates/'.$new_file);
		//copy the KML/KMZ file
		$extention = pathinfo($this->kml_file, PATHINFO_EXTENSION);
		$new_kml = $new_template->id.'.'.$extention;
		copy(DOCROOT.'uploads/templates/'.$this->kml_file,DOCROOT.'uploads/templates/'.$new_kml);
		//copy the new file locations
		$new_template->file = $new_file;
		$new_template->kml_file = $new_kml;
		$new_template->save();
		
		return $new_template;
	}

	
} // End User Model