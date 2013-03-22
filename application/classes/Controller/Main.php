<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Main.php - Heler
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/

class Controller_Main extends Controller_Template {

	public $template = 'main';
	/** Stores the DB object of the current user, if one exists, else null*/
	public $user = null;
	/**
		Set stuff up
	*/
	public function before()
	{
		parent::before();
		
		

		$this->user = null; //not logged in
		$auth = Auth::instance();
		//is the user logged in?
		if($auth->logged_in())
		{
			$this->user = ORM::factory('user',$auth->get_user());
		}
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
			$this->template->html_head->styles['screen'] = 'media/css/style.css';
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
		//if they're logged in redirect them home:
		$auth = Auth::instance();
		$logged_in = $auth->logged_in() OR $auth->auto_login();
		if($logged_in)
		{
		
			HTTP::redirect('mymaps'); //send them to their maps page if they're logged in		
		}
		HTTP::redirect('home');
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
