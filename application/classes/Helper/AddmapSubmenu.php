<?php defined('SYSPATH') or die('No direct access allowed.');
/***********************************************************
* Mainmenu.php - Helper
* This software is copy righted by Kobo 2012
* Written by Willy Douglas <willy@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2012-12-19
*************************************************************/

class Helper_AddmapSubmenu
{
	public static function make_addmap_menu($page)
	{
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
		
		//echo '<script type="text/javascript">alert('.$map->map_creation_progress.');</script>';
		
		echo '<ul id="addmap_menu">';
		
		echo '<h4>'.__("Add Map Menu").'</h4>';
		
		echo '<li id="page_1" '.(($page == 1)? 'class="active"':'').'>';
		echo '<a href="'.url::base().'mymaps/add1/?id='.$map_id.'">'.__("Page 1: Basic Info").'</a>';
		echo'</li>';
		
		echo '<li id="page_2" '.(($page == 2)? 'class="active"':'').'>';
		if($map_progress >= 1)
		{
			echo '<a href="'.url::base().'mymaps/add2/?id='.$map_id.'">'.__("Page 2: Data Structure").'</a>';
		}
		else 
		{
			echo __("Page 2: Data Structure");
		}
		echo'</li>';
		
		echo '<li id="page_3" '.(($page == 3)? 'class="active"':'').'>';
		if($map_progress >= 2)
		{
			echo '<a href="'.url::base().'mymaps/add3/?id='.$map_id.'">'.__("Page 3: Data Structure Verification").'</a>';
		}
		else 
		{
			echo __("Page 3: Data Structure Verification");
		}
		echo'</li>';
		
		echo '<li id="page_4" '.(($page == 4)? 'class="active"':'').'>';
		if($map_progress >= 2)
		{
			echo '<a href="'.url::base().'mymaps/add4/?id='.$map_id.'">'.__("Page 4: Map Setup").'</a>';
		}
		else 
		{
			echo __("Page 4: Map Setup");
		}
		echo'</li>';
		
		echo '<li id="page_5" '.(($page == 5)? 'class="active"':'').'>';
		if($map_progress >= 4)
		{
			echo '<a href="'.url::base().'mymaps/add5/?id='.$map_id.'">'.__("Page 5: Region Mapping").'</a>';
		}
		else 
		{
			echo __("Page 5: Region Mapping");
		}
		echo'</li>';
		
		echo '<li id="page_view">';
		if($map_progress >= 5)
		{
			echo '<a href="'.url::base().'public/view/?id='.$map_id.'">'.__("View Map").'</a>';
		}
		else
		{
			echo __("View Map");
		}
		echo'</li>';
		
		echo '</ul>';
		echo '<p style="clear:both;"></p>';
		
		
		
	}//end function
}//end class
