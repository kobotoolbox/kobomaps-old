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
			// public maps page
			if($page == "publicmaps")
			{
				echo '<li class="selected">';
			}
			else
			{
				echo '<li>';
			}
			echo '<a href="'.url::base().'public/maps">'.__("Public Maps").'</a></li>';
			
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
				
				// public maps page
				if($page == "publicmaps")
				{
					echo '<li class="selected">';
				}
				else
				{
					echo '<li>';
				}
				echo '<a href="'.url::base().'public/maps">'.__("Public Maps").'</a></li>';
				
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
				/*
				if($page == "share")
				{
					echo '<li class="selected">';
				}
				else
				{
					echo '<li>';
				}
				echo '<a href="'.url::base().'mymaps/share">'.__("Share").'</a></li>';
				*/
				//statistics
				if($page == "statistics")
				{
					echo '<li class="selected">';
				}
				else
				{
					echo '<li>';
				}
				echo '<a href="'.url::base().'statistics">'.__("Statistics").'</a></li>';
				
				if($page == "templates")
				{
					echo '<li class="selected">';
				}
				else
				{
					echo '<li>';
				}
				echo '<a href="'.url::base().'templates">'.__("Templates").'</a></li>';
				
				//Message center
				if($page == "messages")
				{
					echo '<li class="selected">';
				}
				else
				{
					echo '<li>';
				}
				$unread = ORM::factory('Message')					
					->where('user_id','=',$user->id)
					->where('unread','=',1)
					->count_all();
				if($unread > 0)
				{
					$unread = '('.$unread.')';
				}
				else{
					$unread = '';
				}					
				echo '<a href="'.url::base().'message">'.__("Messages").$unread.'</a></li>';
				
			}
		
		
		
			
			//see if the given user is an admin, if so they can do super cool stuff
			$admin_role = ORM::factory('Role')->where("name", "=", "admin")->find();
			if($user->has('roles', $admin_role))
			{
				
				
					
									
				
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
					<a href="<?php echo URL::base();?>message">
					<div>
						<img class="message" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Message Center'); ?>
					</div>
					</a>
				</li>
				<?php 
				break;
				
			case "createmap":
				
			case "mapview":

					$pageNumber = Request::initial()->action();
					$pageNumber = intval(str_replace('add', '', $pageNumber));

					echo " page number ". $pageNumber;
					//$end_div = false;
					$controller = Request::initial()->controller();
					echo " controller ". $controller;
					$map_id = 0;
					if($controller == "Mymaps")
					{
						$map_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
						$map = ORM::factory('Map', $map_id);
					}
					elseif($controller == "Dynamic")
					{
						$slug = Request::initial()->param('slug');
						//see if this correlates to a map
						$map = ORM::factory('Map')
						->where('slug','=',$slug)
						->find();
						$map_id = $map->id;
					}
					
					$share = Model_Sharing::get_share($map->id, $user);
					if($map_id != 0)
					{
						$map_progress = $map->map_creation_progress;
						//make sure the user is the owner, otherwise don't show the edit stuff
						if($share->permission != Model_Sharing::$owner AND $share->permission != Model_Sharing::$edit)
						{
							return;
						}
					}
					else
					{
						$map_progress = 0;
					}
					?>
					
					<li class="<?php echo ($pageNumber == 1)? 'active':''; echo ($map_progress < 1 AND $pageNumber != 1)? 'greyedout':'';?>">
					
						<?php if($map_progress >= 1 AND $pageNumber != 1){?><a href="<?php echo URL::base();?>mymaps/add1?id=<?php echo $map_id;?>"> <?php } else {?><span><?php }?>
							<div>
								<img class="basicSetup" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Basic Set-up');?>
							</div>
						<?php if($map_progress >= 1 AND $pageNumber != 1){?></a><?php } else {?></span><?php }?>
					</li>
										
					
					<li class="<?php echo ($pageNumber == 2)? 'active':''; echo ($map_progress < 2 AND $pageNumber != 2)? 'greyedout':'';?>">
					
						<?php if($map_progress >= 1 AND $pageNumber != 2){?><a href="<?php echo URL::base();?>mymaps/add2?id=<?php echo $map_id;?>"> <?php } else {?><span><?php }?>
							<div>
								<img class="dataStruct" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Data Structure');?>
							</div>
						<?php if($map_progress >= 1 AND $pageNumber != 2){?></a><?php } else {?></span><?php }?>
					</li>

					<li class="<?php echo ($pageNumber == 3)? 'active':''; echo ($map_progress < 3 AND $pageNumber != 3)? 'greyedout':'';?>">
					
						<?php if($map_progress >= 2 AND $pageNumber != 3){?><a href="<?php echo URL::base();?>mymaps/add3?id=<?php echo $map_id;?>"> <?php } else {?><span><?php }?>
							<div>
								<img class="validation" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Validation');?>
							</div>
						<?php if($map_progress >= 2 AND $pageNumber != 3){?></a><?php } else {?></span><?php }?>
					</li>
					
					<li class="<?php echo ($pageNumber == 4)? 'active':''; echo ($map_progress < 4 AND $pageNumber != 4)? 'greyedout':'';?>">
					
						<?php if($map_progress >= 3 AND $pageNumber != 4){?><a href="<?php echo URL::base();?>mymaps/add4?id=<?php echo $map_id;?>"> <?php } else {?><span><?php }?>
							<div>
								<img class="geoSetup" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Geo Set-up');?>
							</div>
						<?php if($map_progress >= 3 AND $pageNumber != 4){?></a><?php } else {?></span><?php }?>
					</li>
					
					<li class="<?php echo ($pageNumber == 5)? 'active':''; echo ($map_progress < 5 AND $pageNumber != 5)? 'greyedout':'';?>">
					
						<?php if($map_progress >= 4 AND $pageNumber != 5){?><a href="<?php echo URL::base();?>mymaps/add5?id=<?php echo $map_id;?>"> <?php } else {?><span><?php }?>
							<div>
								<img class="geoMatch" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Geo Matching');?>
							</div>
						<?php if($map_progress >= 4 AND $pageNumber != 5){?></a><?php } else {?></span><?php }?>
					</li>
					
					<li class="<?php echo ($pageNumber == 0)? 'active':''; echo ($map_progress < 5 AND $pageNumber != 0)? 'greyedout':'';?>">
					
						<?php if($map_progress >= 5 AND $pageNumber != 0){?><a href="<?php echo URL::base();?><?php echo $map->slug;?>"> <?php } else {?><span><?php }?>
							<div>
								<img class="genMap" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('View Map');?>
							</div>
						<?php if($map_progress >= 5 AND $pageNumber != 0){?></a><?php } else {?></span><?php }?>
					</li>					
					<?php 

					break;
				case "templates":
					$action = Request::initial()->action();
				?>
					<li class="<?php echo $action=='index' ? 'active':'';?>">
						<?php if($action=='index'){?><span><?php }else{?><a href="<?php echo URL::base();?>templates"><?php }?>
						<div>
							<img class="allTemplates" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('All Templates');?>
						</div>
						<?php if($action=='index'){?></span><?php }else{?></a><?php }?>
					</li>
					<li class="<?php echo $action=='mine' ? 'active':'';?>">
						<?php if($action=='mine'){?><span><?php }else{?><a href="<?php echo URL::base();?>templates/mine"><?php }?>
						<div>
							<img class="myTemplates" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('My Templates');?>
						</div>
						<?php if($action=='mine'){?></span><?php }else{?></a><?php }?>
					</li>
					<li class="<?php echo ($action=='edit' AND !isset($_GET['id'])) ? 'active':'';?>">
						<?php if($action=='edit' AND !isset($_GET['id'])){?><span><?php }else{?><a href="<?php echo URL::base();?>templates/edit"><?php }?>
						<div>
							<img class="newTemplate" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Create Template');?>
						</div>
						<?php if($action=='edit' AND !isset($_GET['id'])){?></span><?php }else{?></a><?php }?>
					</li>
					<?php if($action=='edit' AND isset($_GET['id'])){?>
					<li class="active">
						<span>
						<div>
							<img class="newTemplate" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Edit Template');?>
						</div>
						</span>
					</li>						
					<?php }?>														
					
					<?php if($action=='view'){?>
					<li class="active">
						<span>
						<div>
							<img class="viewTemplate" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('View Template');?>
						</div>
						</span>
					</li>						
					<?php }?>														
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
			echo '<li><a href="'.url::base().'login">'.__('Log In').'</a></li>';
			echo '<li><a href="'.url::base().'signup">'.__('Sign Up').'</a></li>';
			echo '</ul>';
			echo '</li>';
		}
		echo '</ul>';
	}
}//end class
