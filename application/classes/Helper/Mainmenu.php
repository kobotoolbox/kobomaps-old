<?php defined('SYSPATH') or die('No direct access allowed.');
/***********************************************************
* Mainmenu.php - Helper
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/

class Helper_Mainmenu
{
	public static function make_menu($page, $user)
	{
		$end_div = false;
		echo '<ul>';
		
		//Don't show the register link if the user is logged in
		if($user == null)
		{
			//register page
			if($page == "signup")
			{
				echo '<li class="selected">';
			}
			else
			{
				echo '<li>';
			}
			echo '<a href="'.url::base().'signup">'.__("signup").'</a></li>';
		}
		
		//if the user is logged in
		if($user != null)
		{
			$login_role = ORM::factory('Role')->where("name", "=", "login")->find();

			if($user->has('roles', $login_role))
			{
				
				// home page
				if($page == "mymaps")
				{
					echo '<li class="selected">';
				}
				else
				{
					echo '<li>';
				}
				echo '<a href="'.url::base().'mymaps">'.__("My Maps").'</a></li>';
				
				// Create a map
				if($page == "createmap")
				{
					echo '<li class="selected">';
				}
				else
				{
					echo '<li>';
				}
				echo '<a href="'.url::base().'mymaps/add1">'.__("Create Map").'</a></li>';
				
				// View map
				if($page == "viewmap")
				{
					echo '<li class="selected">';
					echo '<a href="'.url::base().'mymaps/add1">'.__("View Map").'</a></li>';
				}
				
				//share
				if($page == "share")
				{
					echo '<li class="selected">';
				}
				else
				{
					echo '<li>';
				}
				echo '<a href="'.url::base().'mymaps/share">'.__("Share").'</a></li>';
				
				//statistics
				if($page == "statistics")
				{
					echo '<li class="selected">';
				}
				else
				{
					echo '<li>';
				}
				echo '<a href="'.url::base().'mymaps/statistics">'.__("Statistics").'</a></li>';
				
			
				
			}
		
		
		
			
			//see if the given user is an admin, if so they can do super cool stuff
			$admin_role = ORM::factory('Role')->where("name", "=", "admin")->find();
			if($user->has('roles', $admin_role))
			{
				
				if($page == "templates")
				{
					echo '<li class="adminmenu selected">';
				}
				else
				{
					echo '<li class="adminmenu">';
				}
				echo '<a href="'.url::base().'templates">'.__("Map Templates").'</a></li>';
					
									
				
			}
		}//end is logged in
		
		echo '</ul>';
		echo '<p style="clear:both;"></p>';
		
		
		
	}//end function
	
	
	public static function make_submenu($page, $user)
	{
		
		echo '<ul>';

		switch($page)
		{
			case "mymaps":
				echo '<li><a href="'.URL::base().'mymaps/add1">'.__('Create New Map').'</a></li>';
				echo '<li><a href="'.URL::base().'mymaps/share">'.__('Share').'</a></li>';
				echo '<li><a href="'.URL::base().'mymaps/messages">'.__('Message Center').'</a></li>';
				break;
		}		
		
		echo '</ul>';
		echo '<p style="clear:both;"></p>';
	
	
	
	}//end function
}//end class
