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
				?>
				<li>
					<a href="<?php echo URL::base();?>mymaps/add1">
						<img class="createNewMap" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Create New Map');?>
					</a>
				</li>
				<li>
					<a href="<?php echo URL::base();?>mymaps/share">
						<img class="share" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Share'); ?>
					</a>
				</li>
				<li>
					<a href="<?php echo URL::base();?>mymaps/messages">
						<img class="message" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Message Center'); ?>
					</a>
				</li>
				<?php 
				break;
		}		
		
		echo '</ul>';
		echo '<p style="clear:both;"></p>';
	
	
	
	}//end function
	
	
	/**
	 * Creates the user menu, login, log out, profile, that sort of thing.
	 * @param string $page name of the page that we are currently rendering
	 * @param db_object $user user object for the user that is currenlty logged in
	 */
	public static function make_user_menu($page, $user)
	{
		echo '<ul>';
		echo '<li><a href="'.URL::base().'about">'.__('About KoboMap').'</a></li>';
		echo '<li><a href="'.URL::base().'support">'.__('Support & Feedback').'</a></li>';
		echo '<li><a class="helplink" href="'.URL::base().'help">'.__('Help').'</a></li>';
		
		$auth = Auth::instance();
		$logged_in = $auth->logged_in() OR $auth->auto_login();
		if($logged_in)
		{
			$user = ORM::factory('user',$auth->get_user());
			$user_name = $user->first_name. ' ' . $user->last_name;
			echo ' <li class="loginMenu">';
			echo '<a href="'.url::base().'mymaps">'.$user_name .'</a>';
			echo '<ul>';
			echo ' <li><a href="'.url::base().'profile">'.__('profile') .'</a></li>';
			echo ' <li><a href="'.url::base().'logout">'.__('logout').'</a></li>';
			echo '</ul>';
			echo '</li>';
			
		}
		else
		{

			echo ' <li class="loginMenu">';
			echo '<a href="'.url::base().'login">'.__('Login, Signup') .'</a>';
			echo '<ul>';
			echo '<li><a href="'.url::base().'login">'.__('login').'</a></li>';
			echo '<li><a href="'.url::base().'signup">'.__('signup').'</a></li>';
			echo '</ul>';
			echo '</li>';
		}
		echo '</ul>';
	}
}//end class
