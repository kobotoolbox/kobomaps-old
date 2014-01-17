<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Help.php - Controller
* This software is copy righted by Kobo 2013
* Writen by Dylan Gillespie <dylan@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-08-21
*************************************************************/

class Controller_Help extends Controller_Main {

	
	/**
	 Set stuff up, mainly just check if the user is an admin or not
	 */
	public function before()
	{
		parent::before();
	
	}
	
	/**
	 * Main page for the Help section
	 */
	public function action_index()
	{
		/***** initialize stuff****/
		//The title to show on the browser
		$this->template->html_head->title = __("Help");
		//the name in the menu
		$this->template->header->menu_page = "help";
		$this->template->content = view::factory("help/main");

		//set the JS
		$js = view::factory('help/help_js');
		$this->template->html_head->script_views[] = $js;
		
		//set table variable
		$this->template->content->table = $this->_createToCTable();
		
	}//end action_index
	
	
	/**
	 * Gather tables out of custompage table and make a table of contents on the main help page
	 */
	private function _createToCTable(){
		$sections = array();
		$Kobo = array();
		
		//the custompage creation help page should not be available for non admins
		$auth = Auth::instance();
		$logged_in = $auth->logged_in() OR $auth->auto_login();
		//see if the given user is an admin, if so they can do super cool stuff
		$admin_role = ORM::factory('Role')->where('name', '=', 'admin')->find();
		if($logged_in)
		{
			$user = ORM::factory('User',$auth->get_user());
		}
		else{
			$user = null;
		}
		
		$Kobo[__('Help Making Maps')] = url::base().'help/maphelp';
		$Kobo[__('Help Making Templates')] = url::base().'help/templatehelp';
		$Kobo[__('Help With Statistics')] = url::base().'help/stathelp';
		
		if($user != null){
			$Kobo[__('Help Making Custom Pages')] = url::base().'help/custompagehelp';
			$Kobo[__('Help Making Submenus')] = url::base().'help/submenuhelp';
		}
		
		$sections[__('Kobo help pages')] = $Kobo;
		$customArray = array();
		
		$custom = ORM::factory('Custompage')
		->where('help', '=', '1')
		->find_all();
		
		foreach($custom as $page){
			$customArray[$page->title] = url::base().$page->slug; 
		}
		
		$sections[__('Custom Help')] = $customArray;
		
		return $sections;
	}
	
	
	/**
	 * Generates the map help page
	 * Creates the map edit/create form that is first seen upon clicking edit/create
	 */
	 public function action_maphelp()
	 {
	 		
		/***Now that we have the form, lets initialize the UI***/
		//The title to show on the browser
		$header =  __('Help Making Maps') ;
		$this->template->html_head->title = $header;		
		
		//the name in the menu
		$this->template->content = view::factory("help/maphelp");
		
		$this->template->content->header = $header;
		$this->template->header->menu_page = "help";
		//set the JS
		$js = view::factory('help/help_js');
		$this->template->html_head->script_views[] = $js;
		
		$this->template->content->table = $this->_createMapHelpTable();
		//get the status
		
	 }//end action_add1
	 
	 /**
	  * Helps make Table of contents
	  * @return array of strings and arrays of strings
	  */
	 private function _createMapHelpTable(){
	 	$sections = array();
	 	$style = array();
	 	$basic = array();
	 	$advanced = array();
	 	
	 	$basic['MapTitle'] = __('Map Title');
	 	$basic['MapSlug'] = __('Map Slug');
	 	$basic['MapDesc'] = __('Map Description');
	 	$basic['MapHidden'] = __('Map Hidden');
	 	$basic['MapData'] = __('Is the data source');
	 	$basic['MapSpread'] = __('Spreadsheet (.xls, .xlsx)');
	 	$basic['class'] = 'Basic';
	 	
	 	$sections['Basic Set-Up'] = $basic;
	 	
	 	$advanced['MapLabel'] = __('Map Label');
	 	$advanced['MapZoom'] = __('Map Zoom');
	 	$advanced['MapRegionFont'] = __('Map Region Font');
	 	$advanced['MapDataFont'] = __('Map Data Font');
	 	$advanced['MapBorderColor'] = __('Map Border Color');
	 	$advanced['MapRegionColor'] = __('Map Region Color');
	 	$advanced['MapGradient'] = __('Map Gradient');
	 	$advanced['MapShading'] = __('Map Shading');
	 	$advanced['MapSpread'] = __('Make map colors same');
	 	$advanced['MapBar'] = __('Map Bar');
	 	$advanced['MapSelected'] = __('Map Selected');
	 	$advanced['MapCSS'] = __('Map CSS');
	 	$advanced['class'] = 'Advanced';
	 	
	 	$sections['Advanced Options'] = $advanced;
	 	$sections['DataStructure'] = __('Data Structure');
	 	$sections['Validation'] = __('Validation');
	 	$sections['GeoSetup'] = __('Geo Set-up');
	 	$sections['GeoMatching'] = __('Geo Matching');
	 	
	 	$style['AdminProv'] = __('AdminProv');
	 	$style['AdminLoc'] = __('AdminLoc');
	 	$style['POI'] = __('POI');
	 	$style['Road'] = __('Road');
	 	$style['Landscape'] = __('Landscape');
	 	$style['Water'] = __('Water');
	 	$style['class'] = 'Style';
	 	
	 	$sections['Map Style'] = $style;
	 	
	 	return $sections;
	 }
	 
	 /**
	  * Generates template help page
	  */
	 public function action_templatehelp(){
	 	/***Now that we have the form, lets initialize the UI***/
	 	//The title to show on the browser
	 	$header =  __('Help Making Templates') ;
	 	$this->template->html_head->title = $header;
	 	
	 	//the name in the menu
	 	$this->template->content = view::factory("help/templatehelp");
	 	
	 	$this->template->content->header = $header;
	 	$this->template->header->menu_page = "help";
	 	//set the JS
	 	$js = view::factory('help/help_js');
	 	$this->template->html_head->script_views[] = $js;
	 	
	 	$this->template->content->table = $this->_createTempHelpTable();
	 	//get the status
	 }
	 
	 
	 /**
	  * Helps make Table of contents
	  * @return array of strings and arrays of strings
	  */
	 private function _createTempHelpTable(){
	 	$sections = array();
	 	
	 	$sections['NoTemp'] = __('No Template');
	 	$sections['AllTemps'] = __('AllTemplates');
	 	$sections['MyTemps'] = __('MyTemplates');
	 	
	 	$create = array();
	 	
	 	$create['TempTitle'] = __('Temp Title');
	 	$create['TempDesc'] = __('Temp Desc');
	 	$create['TempVis'] = __('Temp Vis');
	 	$create['TempFile'] = __('Temp File');
	 	$create['TempAdmin'] = __('Temp Admin');
	 	$create['TempDec'] = __('Temp Dec');
	 	$create['TempLat'] = __('Temp Lat');
	 	$create['TempLong'] = __('Temp Long');
	 	$create['TempZoom'] = __('Temp Zoom');
	 	 
	 	$sections['Create'] = $create;
	 	
	 	$sections['NoTemp'] = __('No Template');
	 	
	 	return $sections;
	 }
	 
	 /**
	  * Generates help page for statistics
	  */
	 public function action_stathelp(){
	 	/***Now that we have the form, lets initialize the UI***/
	 	//The title to show on the browser
	 	$header =  __('Help With Statistics');
	 	$this->template->html_head->title = $header;
	 
	 	//the name in the menu
	 	$this->template->content = view::factory("help/stathelp");
	 
	 	$this->template->content->header = $header;
	 	$this->template->header->menu_page = "help";
	 	//set the JS
	 	$js = view::factory('help/help_js');
	 	$this->template->html_head->script_views[] = $js;
	 
	 	$this->template->content->table = $this->_createStatHelpTable();
	 	//get the status
	 }
	 
	 
	 /**
	  * Helps make Table of contents
	  * Currently no ToC for Stat page
	  * @return array of strings and arrays of strings
	  */
	 private function _createStatHelpTable(){
	 	$sections = array();
	 	return $sections;
	 }
	 
	 /**
	  * Generates help page for custompages, admin only!
	  */
	 public function action_custompagehelp(){
	 	$auth = Auth::instance();
	 	//only admins should be allowed to see the page in the first place, and if not, are redirected to help home
	 	if(!$auth->logged_in('admin'))
	 	{
	 		HTTP::redirect('help');
	 	}
	 	/***Now that we have the form, lets initialize the UI***/
	 	//The title to show on the browser
	 	$header =  __('Help Making Custom Pages') ;
	 	$this->template->html_head->title = $header;
	 
	 	//the name in the menu
	 	$this->template->content = view::factory("help/custompagehelp");
	 
	 	$this->template->content->header = $header;
	 	$this->template->header->menu_page = "help";
	 	//set the JS
	 	$js = view::factory('help/help_js');
	 	$this->template->html_head->script_views[] = $js;
	 
	 	$this->template->content->table = $this->_createCustomHelpTable();
	 	//get the status
	 }
	 
	 
	 /**
	  * Helps make Table of contents
	  * @return array of strings and arrays of strings
	  */
	 private function _createCustomHelpTable(){
	 	$sections = array();
	 	
	 	$sections['currentPages'] = __('currentPages');
	 	$sections['customPageTitle'] = __('customPage Title');
	 	$sections['customPageSlug'] = __('customPage Slug');
	 	$sections['customPageSub'] = __('customPage Sub');
	 	$sections['customPageHelp'] = __('customPage Help');
	 	$sections['customPageContent'] = __('customPage Content');
	 	
	 	return $sections;
	 }
	 

	 /**
	  * Generates help page for submenus, admin only!
	  */
	 public function action_submenuhelp(){
	 	$auth = Auth::instance();
	 	//only admins should be allowed to see the page in the first place, and if not, are redirected to help home
	 	if(!$auth->logged_in('admin'))
	 	{
	 		HTTP::redirect('help');
	 	}
	 	/***Now that we have the form, lets initialize the UI***/
	 	//The title to show on the browser
	 	$header =  __('Help Making Submenus') ;
	 	$this->template->html_head->title = $header;
	 
	 	//the name in the menu
	 	$this->template->content = view::factory("help/submenuhelp");
	 
	 	$this->template->content->header = $header;
	 	$this->template->header->menu_page = "help";
	 	//set the JS
	 	$js = view::factory('help/help_js');
	 	$this->template->html_head->script_views[] = $js;
	 
	 	$this->template->content->table = $this->_createSubmenuHelpTable();
	 	//get the status
	 }
	 
	 
	 /**
	  * Helps make Table of contents
	  * @return array of strings and arrays of strings
	  */
	 private function _createSubmenuHelpTable(){
	 	$sections = array();
	 	$create = array();
	 	 
	 	$sections['submenuTitle'] = __('submenu Title');
	 	$sections['submenuItems'] = __('submenu Items');
	 	$sections['submenuActions'] = __('submenu Actions');
	 	
	 	$create['menuTitle'] = __('Title of menu item');
	 	$create['submenuUrl'] = __('Menu URL');
	 	$create['submenuIcon'] = __('Icon');
	 	$create['submenuAdmin'] = __('Admin only?');
	 	
	 	$sections[__('Create Menu')] = $create; 
	 	
	 	 
	 	return $sections;
	 }
	 
}//end of class