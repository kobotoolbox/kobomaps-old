<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Dynamic.php - Controller
* This software is copy righted by Kobo 2013
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* This handles requests for maps by name
* Started on 2013-02-15
*************************************************************/

class Controller_Dynamic extends Controller_Main {

	
	/**
	 handles all requests
	 */
	public function action_index()
	{		
		$slug = $this->request->param('slug');

		//if any the pages are linked from the help page, redirect and keep the header the same
		$helpPages = array( 'maphelp' => 'maphelp',
							'templatehelp' => 'templatehelp',
							'custompagehelp' => 'custompagehelp',
							'submenuhelp' => 'submenuhelp',
							);
		
		foreach($helpPages as $help){
			if($help == $slug){
				$this->template->header->menu_page = 'help';
				$this->template->content = new View('help/'.$slug);
				return;
			}
		}
		
		//handle the default page requests
		if($slug == __('home') || ''){
			$slug = '__HOME__';
		}
		if($slug == __('about')){
			$slug = '__ABOUT__';
		}
		if($slug == __('support')){
			$slug = '__SUPPORT__';
		}
		if($slug == __('help')){
			$slug = '__HELP__';
		}
		
		$page = ORM::factory('Custompage')
			->where('slug', '=', $slug)
			->find();
		
		if($page->loaded()){
			$this->viewPage($page);
		}
		//see if this correlates to a map
		$map = ORM::factory('Map')
			->where('slug','=',$slug)
			->find();
			
		if($map->loaded()){
			$this->viewMap($map);
		}

		//if we couldn't find it bounce.
		if(!$map->loaded() && !$page->loaded())
		{
			throw new HTTP_Exception_404();
		}
		
	}//end action_index
	
	
	/**
	 * Use this to render Custom created pages
	 * @param ORM_obj $page ORM object of the page from the database
	 */
	public function viewPage($page){
		$this->auto_render = true;

		if($page->slug == '__HOME__'){
			$page->slug = __('home');
		} 
		if($page->slug == '__HELP__'){
			$page->slug = __('help');
		}
		if($page->slug == '__ABOUT__'){
			$page->slug = __('about');
		}
		if($page->slug == '__SUPPORT__'){
			$page->slug = __('support');
		}
	
		$this->template->header->menu_page = $page->slug;
		$this->template->content = $page->content;
	}
	
	
	/**
	 * Use this to render the map view page
	 * @param ORM_obj $map ORM object of the map from the database
	 */
	public function viewMap($map)
	{
		$this->auto_render = false;
	
		$user = null;
	
		//if the map isn't ready send it back to where it needs to go
		if(intval($map->map_creation_progress) != 5)
		{
			HTTP::redirect('mymaps/add'.$map->map_creation_progress.'?id='.$map->id);
		}
	
		$user = null;
		$auth = Auth::instance();
		//is the user logged in?
		if($auth->logged_in())
		{
	
			$user = ORM::factory('user',$auth->get_user());
		}
		 
		$share = Model_Sharing::get_share($map->id, $user);
		 
		if($map->is_private)
		{
			//if the map is private and they aren't logged in, bounce them
			if($user == null)
			{
				Session::instance()->set('returnUrl', $map->slug); 
				HTTP::redirect('login');
			}
			else  //they're logged in, see if the map is something they have access to
			{
				if($share->permission == null) //couldn't find anything giving the user permission
				{
					HTTP::redirect('mymaps');
				}
			}
			 
		}
		//checking if this is where the increment_visits should be included
		if($user == null){
			Model_Usagestatistics::increment_visit($map->id);
		}
		elseif($user != null AND $share->permission != Model_Sharing::$owner)
		{
			Model_Usagestatistics::increment_visit($map->id);
		}
		 
		$map_template = ORM::factory('Template', $map->template_id);
		$this->template = false;
		$this->auto_render = false;
	
		$view = view::factory("mapview/mapview");
		$view->map_id = $map->id;
		$view->map = $map;
		$js =  view::factory("mapview/mapview_js");
		$js->map = $map;
		$js->template = $map_template;
		$view->template = $map_template;
		$view->html_head = $js;
		 
		$view->menu_page = 'mapview';
		$view->user = $user;
		 
		echo $view;
		 
	
	
	
	}//end action_view()
	
		
}//end of class
