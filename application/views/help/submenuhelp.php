<?php defined('SYSPATH') or die('No direct script access.');
/***********************************************************
* custompagehelp.php - view
* This software is copy righted by Kobo 2013
* Writen by Dylan Gillespie <dylan@ethertontech.com>, Etherton Technologies <http://ethertontech.com>
* Started on 2013-08-26
*************************************************************/


echo '<div class="helpSidebar  helpLink">';
	echo '<span class="ToC">'.__('ToC').'</span></br>';
		echo '<ul>';
	foreach($table as $key=>$section){
		if(is_array($section)){
			echo '<li><span  class="title">'.$key.'</span>';
			echo '<ul>';
			foreach($section as $label=>$part){
				if($label != 'class'){
					echo '<li><a href="#'.$label.'">'.$part.'</a></li>';
				}
			}
			echo '</ul></li>';
		}
		else{
			echo '<li><a href="#'.$key.'">'.$section.'</a></li>';
		}
	}
echo '</ul></div>';
	
echo '<div id="submenuHelp">';
	echo '<div class="sections">'.__('submenuHelp Title').'</div>';
	echo '</br>';
	echo '<div>'.__('submenuHelp Desc').'</div>';
	echo '</br></br>';
	
	echo __('submenuContent Title');
	echo '<img src="'.url::base().'media/img/Help/submenuContent.png"></br>';
	
	echo '<ul>';
		echo '<li><span class="title" id="submenuTitle">'.__('submenu Title').': </span>';
			echo __('submenu Desc');
		echo '</li>';
		echo '<li><span class="title" id="submenuItems">'.__('submenu Items').': </span>';
			echo __('items Desc');
		echo '</li>';
		echo '<li><span class="title" id="submenuActions">'.__('submenu Actions').': </span>';
			echo __('actions Desc');
		echo '</li>';
	echo '</ul></br></br></br>';
	
	echo '<div class="sections" id="createMenu">'.__('menu Below').'</div>';
	echo '<img src="'.url::base().'media/img/Help/submenuMenu.png"></br>';
	echo __('menu Desc');
	echo '</br></br></br>';
	
	echo '<div id="edit" class="title">'.__('submenu Edit').'</div></br>';
	echo '<img src="'.url::base().'media/img/Help/submenuPopup.png">';
		echo '<ul>';
			echo '<li>';
				echo '<span id="menuTitle" class="title">'.__('Title of menu item').': </span>';
				echo __('submenuTitle');
			echo '</li></br>';
			echo '<li>';
			echo '<span id="submenuUrl" class="title">'.__('Menu URL').': </span>';
				echo __('SubmenuUrl');
			echo '</li></br>';
			echo '<li>';
				echo '<span id="submenuIcon" class="title">'.__('Icon').': </span>';
				echo __('submenuIcon');
			echo '</li></br>';
			echo '<li>';
				echo '<span id="submenuAdmin" class="title">'.__('Admin only?').': </span>';
				echo __('submenuAdmin');
			echo '</li></ul></br></br></br>';
			
			echo '<div class="sections">'.__('submenuFinal').'</div>';
echo '</div>';
	?>

<div style="clear:both"></div>



