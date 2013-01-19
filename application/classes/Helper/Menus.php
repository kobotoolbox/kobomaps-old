<?php defined('SYSPATH') or die('No direct access allowed.');
/***********************************************************
* Mainmenu.php - Helper
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/

class Helper_Menus
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
			echo '<a href="'.url::base().'signup">'.__("Sign Up").'</a></li>';
			
			//register page
			if($page == "login")
			{
				echo '<li class="selected">';
			}
			else
			{
				echo '<li>';
			}
			echo '<a href="'.url::base().'login">'.__("Log In").'</a></li>';
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
				if($page == "mapview")
				{
					echo '<li class="selected">';
					echo '<a href="" onclick="return false;">'.__("View Map").'</a></li>';
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
					<div>
						<img class="createNewMap" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Create New Map');?>
					</div>
					</a>
				</li>
				<li>
					<a href="<?php echo URL::base();?>mymaps/share">
					<div>
						<img class="share" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Share'); ?>
					</div>
					</a>
				</li>
				<li>
					<a href="<?php echo URL::base();?>mymaps/messages">
					<div>
						<img class="message" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Message Center'); ?>
					</div>
					</a>
				</li>
				<?php 
				break;
				
				case "createmap":

					$page = Request::initial()->action();
					$page = intval(str_replace('add', '', $page));
					//$end_div = false;
					$map_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
					if($map_id != 0)
					{
						$map = ORM::factory('Map', $map_id);
						$map_progress = $map->map_creation_progress;
					}
					else
					{
						$map_progress = 0;
					}
					?>
					
					<li class="<?php echo ($page == 1)? 'active':''; echo ($map_progress < 1 AND $page != 1)? 'greyedout':'';?>">
					
						<?php if($map_progress >= 1 AND $page != 1){?><a href="<?php echo URL::base();?>mymaps/add1?id=<?php echo $map_id;?>"> <?php } else {?><span><?php }?>
							<div>
								<img class="basicSetup" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Basic Set-up');?>
							</div>
						<?php if($map_progress >= 1 AND $page != 1){?></a><?php } else {?></span><?php }?>
					</li>
										
					
					<li class="<?php echo ($page == 2)? 'active':''; echo ($map_progress < 2 AND $page != 2)? 'greyedout':'';?>">
					
						<?php if($map_progress >= 1 AND $page != 2){?><a href="<?php echo URL::base();?>mymaps/add2?id=<?php echo $map_id;?>"> <?php } else {?><span><?php }?>
							<div>
								<img class="dataStruct" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Data Structure');?>
							</div>
						<?php if($map_progress >= 1 AND $page != 2){?></a><?php } else {?></span><?php }?>
					</li>

					<li class="<?php echo ($page == 3)? 'active':''; echo ($map_progress < 3 AND $page != 3)? 'greyedout':'';?>">
					
						<?php if($map_progress >= 2 AND $page != 3){?><a href="<?php echo URL::base();?>mymaps/add3?id=<?php echo $map_id;?>"> <?php } else {?><span><?php }?>
							<div>
								<img class="validation" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Validation');?>
							</div>
						<?php if($map_progress >= 2 AND $page != 3){?></a><?php } else {?></span><?php }?>
					</li>
					
					<li class="<?php echo ($page == 4)? 'active':''; echo ($map_progress < 4 AND $page != 4)? 'greyedout':'';?>">
					
						<?php if($map_progress >= 3 AND $page != 4){?><a href="<?php echo URL::base();?>mymaps/add4?id=<?php echo $map_id;?>"> <?php } else {?><span><?php }?>
							<div>
								<img class="geoSetup" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Geo Set-up');?>
							</div>
						<?php if($map_progress >= 3 AND $page != 4){?></a><?php } else {?></span><?php }?>
					</li>
					
					<li class="<?php echo ($page == 5)? 'active':''; echo ($map_progress < 5 AND $page != 5)? 'greyedout':'';?>">
					
						<?php if($map_progress >= 4 AND $page != 5){?><a href="<?php echo URL::base();?>mymaps/add5?id=<?php echo $map_id;?>"> <?php } else {?><span><?php }?>
							<div>
								<img class="geoMatch" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Geo Matching');?>
							</div>
						<?php if($map_progress >= 4 AND $page != 5){?></a><?php } else {?></span><?php }?>
					</li>
					
					<li class="<?php echo ($page == 0)? 'active':''; echo ($map_progress < 5 AND $page != 0)? 'greyedout':'';?>">
					
						<?php if($map_progress >= 5){?><a href="<?php echo URL::base();?>public/view?id=<?php echo $map_id;?>"> <?php } else {?><span><?php }?>
							<div>
								<img class="genMap" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('View Map');?>
							</div>
						<?php if($map_progress >= 5){?></a><?php } else {?></span><?php }?>
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
