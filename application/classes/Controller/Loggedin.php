<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* Loggedin.php - Controller
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/

class Controller_Loggedin extends Controller_Main {

	/**
	Set stuff up
	*/
	public function before()
	{
		parent::before();

		$this->auth = Auth::instance();
		//is the user logged in?
		if($this->auth->logged_in())
		{
			$this->user = ORM::factory('user',$this->auth->get_user());			
		}
		//if not send them to the login page
		else
		{
			//record where the user was trying to go
			$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
			Session::instance()->set('returnUrl',$url);			
			HTTP::redirect('/login');
		}
		
	}
	
	
  	
	/**
	where users go to sign up
	*/
	public function action_index()
	{
		
		//turn on bricking

		
		$this->template->html_head->title = __("home");
		$this->template->content = View::factory('home');		
		$this->template->header->menu_page = "home";
		$this->template->content->user = $this->user;
		
		//get top 5 last edited wishes
		$wishes = ORM::factory('wish')
			->where('user_id', '=', $this->user->id)
			->where('is_live', '=', '1')
			->order_by('date_modified')
			->limit(30)
			->find_all();
		$this->template->content->wishes = $wishes;
		
		//get updates
		$updates = ORM::factory('update')->
			where('user_id', '=', $this->user->id)->
			order_by('date_created', 'DESC')->
			limit(20)->
			find_all();
		$this->template->content->updates = $updates;
		
		//get all your friends
		$this->template->content->friends = Model_Friend::get_friends($this->user);
		
		$js_view = view::factory('home_js');
		$this->template->html_head->script_views[] = $js_view;
	}
	
	
} // End Welcome
