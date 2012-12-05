<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Main.php - Heler
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/

class Controller_Main extends Controller_Template {

	public $template = 'main';
	
	/**
		Set stuff up
	*/
	public function before()
	{
		parent::before();
		
		

		$this->user = null; //not logged in
		$this->session = Session::instance();
		//if auto rendere set this up
		if ($this->auto_render)
		{
			// Initialize values
			$this->template->html_head = View::factory('html_head' );
			$this->template->html_head->title = "";
			$this->template->html_head->styles = array();
			$this->template->html_head->script_files = array();
			$this->template->html_head->script_views = array();
			
			$this->template->header = View::factory('header');
			$this->template->header->menu = "menu";
			$this->template->header->menu_page = "";
			$this->template->content = '';
			$this->template->footer = View::factory('footer');
			
			//add basic css and JS
			$this->template->html_head->styles['media/css/style.css'] = 'screen';
			if(isset($_GET['debug']))
			{
				$this->template->html_head->script_files[] = 'media/js/jquery.debug.min.js';
			}	
			else
			{			
				$this->template->html_head->script_files[] = 'media/js/jquery.min.js';
			}

		}
	}
  	
	
	public function action_index()
	{
		$this->template->content = View::factory('main_content');
	}
	
	/**
		Add whatever we need on the way out
	*/
	public function after()
	{
		if ($this->auto_render)
		{			
			
			$this->template->header->user = $this->user;
		}
		parent::after();
	}

} // End Welcome
