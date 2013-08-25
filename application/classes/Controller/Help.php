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
		//$this->template->content->errors = array();
		//$this->template->content->messages = array();
		//$this->template->html_head->script_files[] = 'media/js/jquery.tools.min.js';
		//set the JS
		$js = view::factory('help/help_js');
		$this->template->html_head->script_views[] = $js;
		//$this->template->html_head->script_views[] = view::factory('js/messages');
		//$this->template->html_head->script_views[] = view::factory('js/shareEdit');
		//$this->template->html_head->script_views[] = view::factory('js/facebook');
		
		//$this->template->content->maps = $maps;
		
		
	}//end action_index
	
	
	
	/**
	 * the function for editing a form
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
	 	
	 	$sections['AllTemps'] = __('AllTemplates');
	 	$sections['MyTemps'] = __('MyTemplates');
	 	$sections['NoTemp'] = __('No Template');
	 	
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
}//end of class