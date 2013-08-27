<?php defined('SYSPATH') or die('No direct access allowed.');
/***********************************************************
* Mainmenu.php - Helper
* This software is copy righted by Kobo 2012
* Writen by John Etherton <john@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-11-08
*************************************************************/

/**
* This class sets up the default menus and will handle the dynamic menus that admins can create
*/
class Helper_Menus
{
	public static function make_menu($page, $user)
	{
		$end_div = false;
		echo '<ul>';
		
		$auth = Auth::instance();
		$logged_in = $auth->logged_in() OR $auth->auto_login();
		if($logged_in)
		{
			$user = ORM::factory('user',$auth->get_user());
		}
		
		//Don't show the register link if the user is logged in
		if($user == null)
		{
			// home page
			if($page == "home")
			{
				echo '<li class="selected">';
			}
			else
			{
				echo '<li>';
			}
			echo '<a href="'.url::base().'home">'.__("Home").'</a></li>';
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
			
			//signup page
			if($page == "signup")
			{
				echo '<li class="selected">';
			}
			else
			{
				echo '<li>';
			}
			echo '<a href="'.url::base().'signup">'.__("Sign Up").'</a></li>';
			
			//login page
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
		
    //see if the given user is an admin, if so they can do super cool stuff
		$admin_role = ORM::factory('Role')->where("name", "=", "admin")->find();
    
		//if the user is logged in
		if($user != null)
		{
			$login_role = ORM::factory('Role')->where("name", "=", "login")->find();

			if($user->has('roles', $login_role) || $user->has('roles', $admin_role))
			{
				// home page
				if($page == "home")
				{
					echo '<li class="selected">';
				}
				else
				{
					echo '<li>';
				}
				echo '<a href="'.url::base().'home">'.__("Home").'</a></li>';
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
				if($page == "templates")
				{
					echo '<li class="selected">';
				}
				else
				{
					echo '<li>';
				}
				echo '<a href="'.url::base().'templates">'.__("Templates").'</a></li>';
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
				
				//Message center
				if($page == "message")
				{
					echo '<li class="selected">';
				}
				else
				{
					echo '<li>';
				}
        //finds out how many unread messages are in the center and displays them
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
		
	
			if($user->has('roles', $admin_role))
			{
				if($page == "custompage")
				{
					echo '<li class="selected adminmenu">';
				}
				else
				{
					echo '<li class="adminmenu">';
				}
				echo '<a href="'.url::base().'custompage">'.__("Custom Page").'</a></li>';		
								
				
			}
		}//end is logged in
		
		echo '</ul>';
		echo '<p style="clear:both;"></p>';
		
		
		
	}//end function
	
  
  
	/**
  	* contains the submenus for the pages, contained on the second line of menus, dynamically creates the ones created by admins using the database
  	*/
	public static function make_submenu($page, $user)
	{
		
		$current = request::current()->controller();
		echo '<ul>';
		
		//the custompage creation help page should not be available for non admins
		$auth = Auth::instance();
		$logged_in = $auth->logged_in() OR $auth->auto_login();
		//see if the given user is an admin, if so they can do super cool stuff
		$admin_role = ORM::factory('Role')->where('name', '=', 'admin')->find();
		if($logged_in)
		{
			$user = ORM::factory('user',$auth->get_user());
		}

		switch($page)
		{
			
		      case "custompage":
		      ?>
		      <li 
					<?php 
					if ($current == 'Custompage'){
						echo 'class="active"';
					}?>
				>
		        <a href="
		          <?php echo URL::base().'custompage/'?>">
		          <div>
		            <img class="customEdit" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Create pages');?>
		          </div>
		        </a>
		      </li>
		      <li 
					<?php if ($current == 'Menuedit' ){
							echo 'class="active"';				
					}?>
				>
		        <a href="<?php echo URL::base().'menuedit/'?>">
		        <div>
		          <img class="menuEdit" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Create submenus');?>
		        </div>
		        </a>
		      </li>
		      
		  	<?php
		      break;
		      
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
        
			case "help":
				$helpPage = Request::initial()->action();
				
					?>
						<li <?php echo ($helpPage == 'index' AND Request::initial()->controller() == 'Help') ? 'class="active"' : ''?>>
							<a href="<?php echo url::base().'help'?>">
									<div>
						            	<img class="<?php echo 'helpHome'?>" src="<?php echo URL::base();?>media/img/img_trans.gif" width="26" height="46"/><br/><?php echo __('Help Home');?>
						            					</br>
						            </div>
						    </a>            				
						</li>
						<li <?php echo ($helpPage == 'maphelp') ? 'class="active"' : ''?>>
							<a href="<?php echo url::base().'help/maphelp'?>">
									<div>
						            	<img class="<?php echo 'maphelp'?>" src="<?php echo URL::base();?>media/img/img_trans.gif" width="26" height="46"/><br/><?php echo __('Help Making Maps');?>
						            					</br>
						            </div>
						    </a>            				
						</li>
						<li <?php echo ($helpPage == 'templatehelp') ? 'class="active"' : ''?>>
							<a href="<?php echo url::base().'help/templatehelp'?>">
									<div>
						            	<img class="<?php echo 'templatehelp'?>" src="<?php echo URL::base();?>media/img/img_trans.gif" width="26" height="46"/><br/><?php echo __('Help Making Templates');?>
						            					</br>
						            </div>
						    </a>            				
						</li>
						<li <?php echo ($helpPage == 'stathelp') ? 'class="active"' : ''?>>
							<a href="<?php echo url::base().'help/stathelp'?>">
									<div>
						            	<img class="<?php echo 'stathelp'?>" src="<?php echo URL::base();?>media/img/img_trans.gif" width="26" height="46"/><br/><?php echo __('Statistics Help');?>
						            					</br>
						            </div>
						    </a>            				
						</li>
				<?php
					if($user != null AND $user->has('roles', $admin_role)){
					?>
						<li <?php echo ($helpPage == 'custompagehelp') ? 'class="active"' : ''?>>
							<a href="<?php echo url::base().'help/custompagehelp'?>">
									<div>
						            	<img class="<?php echo 'custompagehelp'?>" src="<?php echo URL::base();?>media/img/img_trans.gif" width="26" height="46"/><br/><?php echo __('Help Making Custom Pages');?>
						            					</br>
						            </div>
						    </a>            				
						</li>
						<li <?php echo ($helpPage == 'submenuhelp') ? 'class="active"' : ''?>>
							<a href="<?php echo url::base().'help/submenuhelp'?>">
									<div>
						            	<img class="<?php echo 'submenuhelp'?>" src="<?php echo URL::base();?>media/img/img_trans.gif" width="26" height="46"/><br/><?php echo __('Help Making Submenus');?>
						            					</br>
						            </div>
						    </a>            				
						</li>
					<?php }
				break;
			case "createmap":				
			case "mapview":

					$pageNumber = Request::initial()->action();
					$pageNumber = intval(str_replace('add', '', $pageNumber));

					//$end_div = false;
					$controller = Request::initial()->controller();
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
					
					<li class="<?php echo ($pageNumber == 6)? 'active':''; echo ($map_progress < 6 AND $pageNumber != 6)? 'greyedout':'';?>">
					
						<?php if($map_progress >= 5 AND $pageNumber != 6){?><a href="<?php echo URL::base();?>mymaps/add6?id=<?php echo $map_id;?>"> <?php } else {?><span><?php }?>
							<div>
								<img class="mapStyle" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('Map Style');?>
							</div>
						<?php if($map_progress >= 5 AND $pageNumber != 6){?></a><?php } else {?></span><?php }?>
					</li>
					
					<li class="<?php echo ($pageNumber == 0)? 'active':''; echo ($map_progress < 6 AND $pageNumber != 0)? 'greyedout':'';?>">
					
						<?php if($map_progress >= 6 AND $pageNumber != 0){?><a href="<?php echo URL::base();?><?php echo $map->slug;?>"> <?php } else {?><span><?php }?>
							<div>
								<img class="genMap" src="<?php echo URL::base();?>media/img/img_trans.gif" width="1" height="1"/><br/><?php echo __('View Map');?>
							</div>
						<?php if($map_progress >= 6 AND $pageNumber != 0){?></a><?php } else {?></span><?php }?>
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
		}//end switch statement

		
		//check if we're dealing with something custom
		if($current == "Dynamic")
		{
			$slug = request::current()->param('slug');
		
			$modified_slug = Model_Menuitem::flip(request::current()->param('slug'));

			$custompage = ORM::factory('Custompage')->
    			where('slug', '=', $modified_slug)->
    			find();
			
			if($custompage->loaded()){
				$m = ORM::factory('Menus',$custompage->menu_id);
				
				if($m->loaded()){
					if($m->id == 1){
						$helpPage = Request::initial()->action();

							?>
						<li <?php echo ($helpPage == 'index' AND Request::initial()->controller() == 'Help') ? 'class="active"' : ''?>>
							<a href="<?php echo url::base().'help'?>">
									<div>
						            	<img class="<?php echo 'helpHome'?>" src="<?php echo URL::base();?>media/img/img_trans.gif" width="26" height="46"/><br/><?php echo __('Help Home');?>
						            					</br>
						            </div>
						    </a>            				
						</li>
						<li <?php echo ($helpPage == 'maphelp') ? 'class="active"' : ''?>>
							<a href="<?php echo url::base().'help/maphelp'?>">
									<div>
						            	<img class="<?php echo 'maphelp'?>" src="<?php echo URL::base();?>media/img/img_trans.gif" width="26" height="46"/><br/><?php echo __('Help Making Maps');?>
						            					</br>
						            </div>
						    </a>            				
						</li>
						<li <?php echo ($helpPage == 'templatehelp') ? 'class="active"' : ''?>>
							<a href="<?php echo url::base().'help/templatehelp'?>">
									<div>
						            	<img class="<?php echo 'templatehelp'?>" src="<?php echo URL::base();?>media/img/img_trans.gif" width="26" height="46"/><br/><?php echo __('Help Making Templates');?>
						            					</br>
						            </div>
						    </a>            				
						</li>
						<li <?php echo ($helpPage == 'stathelp') ? 'class="active"' : ''?>>
							<a href="<?php echo url::base().'help/stathelp'?>">
									<div>
						            	<img class="<?php echo 'stathelp'?>" src="<?php echo URL::base();?>media/img/img_trans.gif" width="26" height="46"/><br/><?php echo __('Statistics Help');?>
						            					</br>
						            </div>
						    </a>            				
						</li>
				<?php
					if($user != null AND $user->has('roles', $admin_role)){
					?>
						<li <?php echo ($helpPage == 'custompagehelp') ? 'class="active"' : ''?>>
							<a href="<?php echo url::base().'help/custompagehelp'?>">
									<div>
						            	<img class="<?php echo 'custompagehelp'?>" src="<?php echo URL::base();?>media/img/img_trans.gif" width="26" height="46"/><br/><?php echo __('Help Making Custom Pages');?>
						            					</br>
						            </div>
						    </a>            				
						</li>
						<li <?php echo ($helpPage == 'submenuhelp') ? 'class="active"' : ''?>>
							<a href="<?php echo url::base().'help/submenuhelp'?>">
									<div>
						            	<img class="<?php echo 'submenuhelp'?>" src="<?php echo URL::base();?>media/img/img_trans.gif" width="26" height="46"/><br/><?php echo __('Help Making Submenus');?>
						            					</br>
						            </div>
						    </a>            				
						</li>
					<?php }
				
					}
					else{
						foreach($m->menu_items->find_all() as $menuitem){
							//help menu images are from a sprite
							
							if($user != null AND $menuitem->admin_only AND $user->has('roles', $admin_role)){
									?>
			              		<li 
									<?php if ($slug == $menuitem->item_url){
										echo 'class="active"';				
									}?>
								>
			                		<a href="<?php echo URL::base().$menuitem->item_url?>">
			                  		<div>
			                    		<img class="customMenus" src="<?php echo $menuitem->image_url?>" width="50" height="40"/><br/><?php echo $menuitem->text;?>
			                  		</div>
			                		</a>
			              		</li>
			              		<?php
			              		}
			              		if(!$menuitem->admin_only){
								?>
									<li 
										<?php if ($slug == $menuitem->item_url){
											echo 'class="active"';				
										}?>
									>
										<a href="<?php echo URL::base().$menuitem->item_url?>">
				         				 <div>
				            				<img class="customMenus" src="<?php echo $menuitem->image_url?>" width="50" height="40"/><br/><?php echo $menuitem->text;?>
				          				</div>
				       					 </a>
				      				</li>
				      			<?php 	
								}
		            	}//end for loop
		            }//end else	
			   }//if menu loaded

			}//if custom page loaded			
		}//if dynamic
    
    	
        
       
		echo '</ul>';
    //this helps make the divs float correctly
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
